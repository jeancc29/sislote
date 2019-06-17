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

        
    //$numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];
    
        //Validar combinaciones no sean nulas
        // $es_superpale = Lotteries::whereId($l['id'])->first()->sorteos()->whereDescripcion('Super pale')->first();
        // if($es_superpale == null){
        //     //Si uno de estos campos es nulo entonces eso quiere decir que esta loteria no se insertara, asi que pasaremos a la siguiente loteria
        //     if($l['primera'] == null || $l['segunda'] == null || $l['tercera'] == null)
        //     continue;
        // }else{
        //     if($l['primera'] == null || $l['segunda'] == null)
        //     continue;
        // }
    
        if($awardsClass->combinacionesNula() == true){
            continue;
        }

        if(!is_numeric($awardsClass->numerosGanadores)){
            return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
        }

       
    
        /************* VALIDAMOS DIAS DE LA LOTERIA ***************/
        // $loteria = Lotteries::whereId($l['id'])->get()->first();
    
        // $loteriaWday = $loteria->dias()->whereWday(getdate()['wday'])->get()->first();
        // if($loteriaWday == null){
        //     return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $loteria['descripcion'] .' no abre este dia '], 201);
        // }
        if(!$awardsClass->loteriaAbreDiaActual()){
            return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $awardsClass->getLoteriaDescripcion() .' no abre este dia '], 201);
        }
        /************* END VALIDAMOS DIAS DE LA LOTERIA ***************/
    
    
        
            // $fechaActual = getdate();
           
            // $numeroGanador = Awards::where('idLoteria', $l['id'])
            // ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->get()->first();

            //     //Si es diferente de nulo entonces existe, asi que debo actualizar los numeros ganadores
            //     if($numeroGanador != null){
            //         $numeroGanador['numeroGanador'] = $numerosGanadores;
            //         $numeroGanador['primera'] =  $l['primera'];
            //         $numeroGanador['segunda'] = $l['segunda'];
            //         $numeroGanador['tercera'] =  $l['tercera'];
            //         $numeroGanador->save();
    
                    
            //         $mensaje = "Los numeros ganadores se han guardado correctamente";
            //     }else{
            //         Awards::create([
            //             'idUsuario' => $datos['idUsuario'],
            //             'idLoteria' => $l['id'],
            //             'numeroGanador' => $numerosGanadores,
            //             'primera' => $l['primera'],
            //             'segunda' => $l['segunda'],
            //             'tercera' => $l['tercera']
            //         ]);
    
            //         $mensaje = "Los numeros ganadores se han guardado correctamente";
            //     }
            if($awardsClass->insertarPremio() == false){
                return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
            }


            // }else{
            //     $errores = 1;
            //     $mensaje = "La loteria aun no ha cerrado";
            // }
            // }else{
            //     $errores = 1;
            //     $mensaje = "Los numeros ganadores no son correctos";
            // }
    
            //Obtenemos todas las jugadas pertenecientes a dicha loteria y utilizamos un Foreach para actualizar premios de la tabla SalesDetails
            // $idVentas = Sales::select('sales.id')
            //     ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            //     ->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            //     ->where('salesdetails.idLoteria', $l['id'])->where('sales.status', '!=', 0)->get();

            // $jugadas = Salesdetails::whereIn('idVenta', $idVentas)
            //             ->orderBy('jugada', 'asc')
            //             ->get();
            
                        // return Response::json(['errores' => 1,'mensaje' => $jugadas], 201);
            $c = 0;
            $colleccion = null;
            
            foreach($awardsClass->getJugadasDeHoy($l['id']) as $j){
    
                $j['premio'] = 0;
                $contador = 0;
                $busqueda1 = false;
                $busqueda2 = false;
                $busqueda3 = false;


    
                // return Response::json(['errores' => 1,'mensaje' => strlen($j['jugada'])], 201);
                if(strlen($j['jugada']) == 2){
                    // $busqueda = strpos($numerosGanadores, $j['jugada']);
                    
                    
                    // if(gettype($busqueda) == "integer"){
                    //     $venta = Sales::whereId($j['idVenta'])->first()
                    //     $idBanca = Branches::whereId()
                    //     if($busqueda == 0) $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primera');
                    //     else if($busqueda == 2) $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('segunda');
                    //     else if($busqueda == 4) $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('tercera');
                    // }
                    // else
                    //     $j['premio'] = 0;
                    //return Response::json(['busqueda' => $busqueda,'jugada' => $j['jugada'], 'premio' => $j['premio'], 'monto' => $j['monto'], 'segunda' => Payscombinations::where('idLoteria',$datos['idLoteria'])->value('segunda')], 201);
                    $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if(strlen($j['jugada']) == 4){
                //    // return Response::json(['numGanador' => $numeroGanador['numeroGanador'],'juada' => substr('jean', 0, 2)], 201);
                //     $busqueda1 = strpos($numerosGanadores, substr($j['jugada'], 0, 2));
                //     $busqueda2 = strpos($numerosGanadores, substr($j['jugada'], 2, 2));
    
                //    $sorteo = Draws::whereId($j['idSorteo'])->first();
    
                //    //Si el sorteo es diferente de super pale entonces es un pale normal
                //     if($sorteo['descripcion'] != "Super pale"){
                //         //Verificamos que los tipos de datos de las busquedas sean enteros
                //         if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer"){
                //             //Primera y segunda
                //             if($busqueda1 == 0 && $busqueda2 == 2 || $busqueda2 == 0 && $busqueda1 == 2){
                //                 $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primeraSegunda');
                //             }
                //             //Primera y tercera
                //             else if($busqueda1 == 0 && $busqueda2 == 4 || $busqueda2 == 0 && $busqueda1 == 4){
                //                 $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primeraTercera');
                //             }
                //             //Segunda y tercera
                //             else if($busqueda1 == 2 && $busqueda2 == 4 || $busqueda2 == 2 && $busqueda1 == 4){
                //                 $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('segundaTercera');
                //             }
                //         }else $j['premio'] = 0;
                //     }else{
                //         //Verificamos que los tipos de datos de las busquedas sean enteros
                //         if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer"){
                //             if($busqueda1 == 0 && $busqueda2 == 2 || $busqueda2 == 0 && $busqueda1 == 2){
                //                 $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primerPago');
                //             }
                //         }else $j['premio'] = 0;

                       
                        
                //     }

                    // return Response::json([
                    //     'a' =>  $busqueda1,
                    //     'a1' =>  $busqueda2,
                    //     'a2' =>  $numerosGanadores,
                    //     'a3' =>  $sorteo,
                    //     'errores' => 0,
                    //     'mensaje' => 'Se ha guardado correctamente',
                    //     //'colleccon' => $colleccion
                    // ], 201);
    
                    // if($j['premio'] > 0)
                    //  {
                    //     $j['status'] = 1;
                    //     $j->save();
                    //  }

                    $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], $j['idSorteo']);
                }
                else if(strlen($j['jugada']) == 6){
                    // $contador = 0;
                    // $busqueda1 = strpos($numerosGanadores, substr($j['jugada'], 0, 2));
                    // $busqueda2 = strpos($numerosGanadores, substr($j['jugada'], 2, 2));
                    // $busqueda3 = strpos($numerosGanadores, substr($j['jugada'], 4, 2));
    
                    // if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer" && gettype($busqueda3) == "integer"){
                    //     $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('tresNumeros');
                    // }
                    // else{
                    //     if(gettype($busqueda1) == "integer")
                    //         $contador++;
                    //     if(gettype($busqueda2) == "integer")
                    //         $contador++;
                    //     if(gettype($busqueda3) == "integer")
                    //         $contador++;
                        
                    //     //Si el contador es = 2 entonces hay premio
                    //     if($contador == 2)
                    //         $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('dosNumeros');
                    //     else
                    //         $j['premio'] = 0;
                    // }
                    
                    $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
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
