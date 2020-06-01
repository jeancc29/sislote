<?php

namespace App\Http\Controllers;

use App\Awards;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;


use Faker\Generator as Faker;
use Carbon\Carbon;
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
use App\Classes\Helper;

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
        $datos = request()->validate([
            'layout' => ''
        ]);

        
        if(!strpos(Request::url(), '/api/')){
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar transacciones") == true){
                return redirect()->route('principal');
            }

            

            return view('premios.index', compact('controlador'));
        }


        $datos = request()->validate([
            'token' => 'required'
        ]);

        try {
            $datos = \Helper::jwtDecode($datos["token"]);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $layout = isset($datos['layout']) ? $datos['layout'] : null;
        $loterias = AwardsClass::getLoterias($datos["servidor"], $layout);

        return Response::json([
            'loterias' => $loterias
        ], 201);
    }



    public function buscarPorFecha()
    {
       


        // $datos = request()->validate([
        //     'datos.fecha' => 'required',
        //     'datos.idUsuario' => 'required'
        // ])['datos'];
        $datos = request()['datos'];


        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        

        $fecha = getdate(strtotime($datos['fecha']));
        $fechaDesde = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';


        $loterias = Lotteries::on($datos["servidor"])->whereStatus(1)->has('sorteos')->get();
        // == "vistaPremiosModal"
        if(isset($datos['layout'])){
            
            
            // if($datos['layout'] != "vistaPremiosModal")
                
            $loterias = collect($loterias)->map(function($l) use($datos, $fechaDesde, $fechaHasta){
                $primera = null;
                $segunda = null;
                $tercera = null;
                $pick3 = null;
                $pick4 = null;
                $premios = Awards::on($datos["servidor"])->whereBetween('created_at', array($fechaDesde , $fechaHasta))
                                ->where('idLoteria', $l['id'])
                                ->first();
    
                if($premios != null){
                    $primera = $premios->primera;
                    $segunda = $premios->segunda;
                    $tercera = $premios->tercera;
                    $pick3 = $premios->pick3;
                    $pick4 = $premios->pick4;
                }
                return [
                        'id' => $l['id'],
                        'descripcion' => $l['descripcion'],
                        'abreviatura' => $l['abreviatura'],
                        'primera' => $primera,
                        'segunda' => $segunda,
                        'tercera' => $tercera,
                        'pick3' => $pick3,
                        'pick4' => $pick4,
                        'sorteos' => $l->sorteos
                    ];
            });
    
            // $loterias = collect($datos['loterias']);
            list($loterias, $no) = $loterias->partition(function($l) use($datos){
                return Helper::loteriaTienePremiosRegistradosHoy($datos["servidor"], $l['id']) != true;
            });
        }else{
            $loterias = collect($loterias)->map(function($l) use($datos, $fechaDesde, $fechaHasta){
                $primera = null;
                $segunda = null;
                $tercera = null;
                $pick3 = null;
                $pick4 = null;
                $premios = Awards::on($datos["servidor"])->whereBetween('created_at', array($fechaDesde , $fechaHasta))
                                ->where('idLoteria', $l['id'])
                                ->first();
    
                if($premios != null){
                    $primera = $premios->primera;
                    $segunda = $premios->segunda;
                    $tercera = $premios->tercera;
                    $pick3 = $premios->pick3;
                    $pick4 = $premios->pick4;
                }
                return [
                        'id' => $l['id'],
                        'descripcion' => $l['descripcion'],
                        'abreviatura' => $l['abreviatura'],
                        'primera' => $primera,
                        'segunda' => $segunda,
                        'tercera' => $tercera,
                        'pick3' => $pick3,
                        'pick4' => $pick4,
                        'sorteos' => $l->sorteos
                    ];
            });
        }

        //La funcion partition retorna los objetos que cumplan la condicion pero esta tambien retornara su mismo index, en algunos
        //casos no se retorna el index cero porque el elemento en esta posicion no ha sido incluido, entonces lo que hace la funcion values()
        //es empezar la collection desde su indice cero
        $loterias = $loterias->values();


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
        // $datos = request()->validate([
        //     //'datos.idLoteria' => 'required',
        //     //'datos.numerosGanadores' => 'required|min:2|max:6',
        //     'datos.fecha' => '',
        //     'datos.layout' => '',
        //     'datos.idUsuario' => 'required',
        //     'datos.loterias' => 'required',
        //     'datos.idBanca' => 'required',
        // ])['datos'];

        $datos = request()['datos'];


        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $fecha = getdate();

        if($datos['layout'] == "vistaSencilla" || $datos['layout'] == "vistaPremiosModal"){

            $fechaRequest = new Carbon($datos['fecha']);
            $fechaActual = Carbon::now();
      
            if($fechaRequest->greaterThan($fechaActual)){
                return Response::json(['errores' => 1,'mensaje' => 'No está permitido actualizar resultados para fechas en el futuro'], 201);
            }else{
                $fecha = getdate(strtotime($datos['fecha']));
            }
        }
    
        
        $errores = 0;
        $mensaje = '';
        $idBanca = Branches::on($datos["servidor"])->whereId($datos['idBanca'])->whereStatus(1)->first();
        if($idBanca == null){
            $idBanca = Branches::
                on($datos["servidor"])
                ->where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
                ->first()->id;         
        }else{
            $idBanca = $idBanca->id;
        }

        
    
    foreach($datos['loterias'] as $l):
        $awardsClass = new AwardsClass($datos["servidor"], $l['id']);
        $awardsClass->fecha = $fecha;
        $awardsClass->idUsuario = $datos['idUsuario'];
        $awardsClass->primera = $l['primera'];
        $awardsClass->segunda = $l['segunda'];
        $awardsClass->tercera = $l['tercera'];
        $awardsClass->pick3 = $l['pick3'];
        $awardsClass->pick4 = $l['pick4'];
        $awardsClass->numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];

        
    
    
        
        if($awardsClass->combinacionesNula() == true){
            continue;
        }
        if($awardsClass->datosValidos() == false){
            return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);
        }
        if(!$awardsClass->loteriaAbreDiaActual()){
            return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $awardsClass->getLoteriaDescripcion() .' no abre este dia '], 201);
        }  
        if($awardsClass->insertarPremio() == false){
            return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
        }


           
            $c = 0;
            $colleccion = null;
            
            foreach($awardsClass->getJugadasDeFechaDada($l['id']) as $j){
    
                $j['premio'] = 0;
                $contador = 0;
                $busqueda1 = false;
                $busqueda2 = false;
                $busqueda3 = false;

                $sorteo = Draws::on($datos["servidor"])->whereId($j['idSorteo'])->first();

                
    
                
                if($sorteo->descripcion == "Directo"){
                    if(!is_numeric($awardsClass->numerosGanadores)){
                        return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                    }
                    $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if($sorteo->descripcion == "Pale" || $sorteo->descripcion == "Super pale"){
                    if(!is_numeric($awardsClass->numerosGanadores)){
                        return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                    }
                    $j['premio'] = $awardsClass->paleBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], $j['idSorteo']);
                }
                else if($sorteo->descripcion == "Tripleta"){
                    if(!is_numeric($awardsClass->numerosGanadores)){
                        return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                    }
                    $j['premio'] = $awardsClass->tripletaBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if($sorteo->descripcion == "Pick 3 Straight"){
                    $j['premio'] = $awardsClass->pick3BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if($sorteo->descripcion == "Pick 3 Box"){
                    $j['premio'] = $awardsClass->pick3BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], false);
                }
                else if($sorteo->descripcion == "Pick 4 Straight"){
                    $j['premio'] = $awardsClass->pick4BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if($sorteo->descripcion == "Pick 4 Box"){
                    $j['premio'] = $awardsClass->pick4BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], false);
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
    
    
    
            $ventas = Sales::on($datos["servidor"])->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])
            ->get();
    
            foreach($ventas as $v){
                $todas_las_jugadas = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id']])->count();
                $todas_las_jugadas_salientes = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->count();
                $cantidad_premios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                

                
    
                if($todas_las_jugadas == $todas_las_jugadas_salientes)
                {
                    if($cantidad_premios > 0)
                    {
                        $montoPremios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
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
    
        $loterias = AwardsClass::getLoterias($datos["servidor"]);

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'loterias' => $loterias
            //'colleccon' => $colleccion
        ], 201);
    }



    public function erase(Request $request)
    {
        // $datos = request()->validate([
        //     //'datos.idLoteria' => 'required',
        //     //'datos.numerosGanadores' => 'required|min:2|max:6',
        //     'datos.idUsuario' => 'required',
        //     'datos.idLoteria' => 'required',
        //     'datos.idBanca' => 'required',
        //     'datos.fecha' => '',
        // ])['datos'];

        $datos = request()['datos'];


        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
            
                // return Response::json([
                //     'errores' => 1,
                //     'mensaje' => 'Token incorrecto',
                //     'token' => $datos
                // ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }
    
            $fecha = getdate();
            $fechaRequest = new Carbon($datos['fecha']);
            $fechaActual = Carbon::now();
      
            if($fechaRequest->greaterThan($fechaActual)){
                return Response::json(['errores' => 1,'mensaje' => 'No está permitido actualizar resultados para fechas en el futuro'], 201);
            }else{
                if(isset($datos['fecha'])){
                    $fecha = getdate(strtotime($datos['fecha']));
                }
            }

        $errores = 0;
        $mensaje = '';
        $idBanca = Branches::on($datos["servidor"])->whereId($datos['idBanca'])->whereStatus(1)->first();
        if($idBanca == null){
            $idBanca = Branches::
                on($datos["servidor"])
                ->where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
                ->first()->id;         
        }else{
            $idBanca = $idBanca->id;
        }

        $awardsClass = new AwardsClass($datos["servidor"], $datos['idLoteria']);
        $awardsClass->fecha = $fecha;
        $awardsClass->idUsuario = $datos['idUsuario'];
        // $awardsClass->primera = "";
        // $awardsClass->segunda = "";
        // $awardsClass->tercera = "";
        // $awardsClass->numerosGanadores = "";
        if($awardsClass->existenTicketsMarcadoComoPagado($datos['idLoteria']) == true){
            return Response::json(['errores' => 1,'mensaje' => 'Error: existen tickets marcados como pagados para esta loteria'], 201);
        }
        if($awardsClass->eliminarPremio() == false){
            return Response::json(['errores' => 1,'mensaje' => 'Error al eliminar numeros ganadores'], 201);
        }

            foreach($awardsClass->getJugadasDeFechaDada($datos['idLoteria']) as $j){
                $j['premio'] = 0;
                $j['status'] = 0;
                $j->save();
            }


            //Aqui buscaremos todos los tickets creados en el dia de hoy y vamos a 
            //asignarles el estado pendiente a los tickets en los cuales sus loterias aun no han salido
    
            $ventas = Sales::on($datos["servidor"])->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])
            ->get();
    
            foreach($ventas as $v){
                $todas_las_jugadas_realizadas = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id']])->count();
                $todas_las_jugadas_que_ya_salieron = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->count();
                $cantidad_premios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                
                //Si la cantidad de jugadas realizadas es la que misma que la cantidad que jugadas que se 
                //han marcado como que ya salieron los premios, entonces la venta debe cambiar de status pendiente a ganadores o perdedores
                if($todas_las_jugadas_realizadas == $todas_las_jugadas_que_ya_salieron)
                {
                    if($cantidad_premios > 0)
                    {
                        $montoPremios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
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
                    $v['pagado'] = 0;
                    $v['status'] = 1;
                    $v->save();
                }
            }
    
    
        $loterias = AwardsClass::getLoterias($datos["servidor"]);
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha eliminado correctamente',
            'loterias' => $loterias
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
