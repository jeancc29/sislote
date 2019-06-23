<?php

namespace App\Http\Controllers;

use App\Awards;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;


use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksplays;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Draws;
use App\Branches;
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Classes\AwardsClass;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class AwardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        if(!strpos(Request::url(), '/api/')){
            return view('premios.index', compact('controlador'));
        }


        // $datos = request()->validate([
        //     'datos.codigoBarra' => 'required',
        //     'datos.razon' => 'required',
        //     'datos.idUsuario' => 'required'
        // ])['datos'];


        $fecha = getdate();
        $fechaDesde = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';



        $loterias = Lotteries::whereStatus(1)->get();

        $loterias = collect($loterias)->map(function($l) use($fechaDesde, $fechaHasta){
            $primera = null;
            $segunda = null;
            $tercera = null;
            $premios = Awards::whereBetween('created_at', array($fechaDesde , $fechaHasta))
                            ->where('idLoteria', $l['id'])
                            ->first();

            if($premios != null){
                $primera = $premios->primera;
                $segunda = $premios->segunda;
                $tercera = $premios->tercera;
            }
            return [
                    'id' => $l['id'],
                    'descripcion' => $l['descripcion'],
                    'abreviatura' => $l['abreviatura'],
                    'primera' => $primera,
                    'segunda' => $segunda,
                    'tercera' => $tercera,
                    'sorteos' => $l->sorteos
                ];
        });

        return Response::json([
            'loterias' => $loterias
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = request()->validate([
            //'datos.idLoteria' => 'required',
            //'datos.numerosGanadores' => 'required|min:2|max:6',
            'datos.idUsuario' => 'required',
            'datos.loterias' => 'required',
            'datos.idBanca' => 'required',
        ])['datos'];

        
    
        $fecha = getdate();
        $errores = 0;
        $mensaje = '';
        $idBanca = Branches::whereId($datos['idBanca'])->whereStatus(1)->first();
        if($idBanca == null){
            $idBanca = Branches::
                where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
                ->first()->id;         
        }else{
            $idBanca = $idBanca->id;
        }

        
    
    foreach($datos['loterias'] as $l):
        $awardsClass = new AwardsClass($l['id']);
        $awardsClass->idUsuario = $datos['idUsuario'];
        $awardsClass->primera = $l['primera'];
        $awardsClass->segunda = $l['segunda'];
        $awardsClass->tercera = $l['tercera'];
        $awardsClass->numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];

        
    
    
        if($awardsClass->combinacionesNula() == true){
            continue;
        }
        if(!is_numeric($awardsClass->numerosGanadores)){
            return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
        }
        if(!$awardsClass->loteriaAbreDiaActual()){
            return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $awardsClass->getLoteriaDescripcion() .' no abre este dia '], 201);
        }  
        if($awardsClass->insertarPremio() == false){
            return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
        }


           
            $c = 0;
            $colleccion = null;
            
            foreach($awardsClass->getJugadasDeHoy($l['id']) as $j){
    
                $j['premio'] = 0;
                $contador = 0;
                $busqueda1 = false;
                $busqueda2 = false;
                $busqueda3 = false;


    
                
                if(strlen($j['jugada']) == 2){
                    $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if(strlen($j['jugada']) == 4){
                    $j['premio'] = $awardsClass->paleBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], $j['idSorteo']);
                }
                else if(strlen($j['jugada']) == 6){
                    $j['premio'] = $awardsClass->tripletaBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
    
    
                $j['status'] = 1;
                $j->save();
    
    
                // if($c == 0){
                //     $colleccion = collect([
                //         'busqueda_false' => gettype($busqueda),
                //         'busqueda' => $busqueda,
                //         'jugada' => $j['jugada'], 
                //         'premio' => $j['premio'], 
                //         'monto' => $j['monto'],
                //         'contador' => $contador,
                //         'busqueda1' => $busqueda1,
                //         'busqueda2' => $busqueda2,
                //         'busqueda3' => $busqueda3
                //     ]);
                // }else{
                //     $colleccion->push([
                //         'busqueda_false' => gettype($busqueda),
                //         'busqueda' => $busqueda,
                //         'jugada' => $j['jugada'], 
                //         'premio' => $j['premio'], 
                //         'monto' => $j['monto'],
                //         'contador' => $contador,
                //         'busqueda1' => $busqueda1,
                //         'busqueda2' => $busqueda2,
                //         'busqueda3' => $busqueda3
                //     ]);
                // }
    
                $c++;
            }
    
        endforeach;
    
    
    
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->get();
    
            foreach($ventas as $v){
                $todas_las_jugadas = Salesdetails::where(['idVenta' => $v['id']])->count();
                $todas_las_jugadas_salientes = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->count();
                $cantidad_premios = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                

                
    
                if($todas_las_jugadas == $todas_las_jugadas_salientes)
                {
                    if($cantidad_premios > 0)
                    {
                        $montoPremios = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
                        $v['premios'] = $montoPremios;
                        $v['status'] = 2;
                    }
                        
                    else{
                        $v['premios'] = 0;
                        $v['status'] = 3;
                    }
                        
    
                    $v->save();
                }
            }
    
    
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            //'colleccon' => $colleccion
        ], 201);
    }



    public function erase(Request $request)
    {
        $datos = request()->validate([
            //'datos.idLoteria' => 'required',
            //'datos.numerosGanadores' => 'required|min:2|max:6',
            'datos.idUsuario' => 'required',
            'datos.idLoteria' => 'required',
            'datos.idBanca' => 'required',
        ])['datos'];

        
    
        $fecha = getdate();
        $errores = 0;
        $mensaje = '';
        $idBanca = Branches::whereId($datos['idBanca'])->whereStatus(1)->first();
        if($idBanca == null){
            $idBanca = Branches::
                where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
                ->first()->id;         
        }else{
            $idBanca = $idBanca->id;
        }

        $awardsClass = new AwardsClass($datos['idLoteria']);
        $awardsClass->idUsuario = $datos['idUsuario'];
        $awardsClass->primera = "";
        $awardsClass->segunda = "";
        $awardsClass->tercera = "";
        $awardsClass->numerosGanadores = "";
        if($awardsClass->insertarPremio() == false){
            return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
        }

            foreach($awardsClass->getJugadasDeHoy($datos['idLoteria']) as $j){
                $j['premio'] = 0;
                $j['status'] = 0;
                $j->save();
            }
    
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->get();
    
            foreach($ventas as $v){
                $todas_las_jugadas_realizadas = Salesdetails::where(['idVenta' => $v['id']])->count();
                $todas_las_jugadas_que_ya_salieron = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->count();
                $cantidad_premios = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                
                //Si la cantidad de jugadas realizadas es la que misma que la cantidad que jugadas que se 
                //han marcado como que ya salieron los premios entonces la venta debe cambiar de status pendiente a ganadores o perdedores
                if($todas_las_jugadas_realizadas == $todas_las_jugadas_que_ya_salieron)
                {
                    if($cantidad_premios > 0)
                    {
                        $montoPremios = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
                        $v['premios'] = $montoPremios;
                        $v['status'] = 2;
                    }    
                    else{
                        $v['premios'] = 0;
                        $v['status'] = 3;
                    }
                        
    
                    $v->save();
                }else{
                    $v['premios'] = 0;
                    $v['status'] = 1;
                    $v->save();
                }
            }
    
    
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha eliminado correctamente',
            //'colleccon' => $colleccion
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Awards  $awards
     * @return \Illuminate\Http\Response
     */
    public function show(Awards $awards)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Awards  $awards
     * @return \Illuminate\Http\Response
     */
    public function edit(Awards $awards)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Awards  $awards
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Awards $awards)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Awards  $awards
     * @return \Illuminate\Http\Response
     */
    public function destroy(Awards $awards)
    {
        //
    }
}
