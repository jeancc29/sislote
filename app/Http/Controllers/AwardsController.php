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
use \App\Events\LotteriesEvent;


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
            if(!$u->tienePermiso("Manejar resultados") == true){
                return redirect()->route('sinpermiso');
            }
            // $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            // if(!$u->tienePermiso("Manejar transacciones") == true){
            //     return redirect()->route('principal');
            // }

            

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

        

        $fecha = getdate(strtotime($datos['fecha']));
        $fechaDesde = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:59:00';


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
            // $fechaActual = new Carbon("2020-12-15 23:59:00");
      
            if($fechaRequest->greaterThan($fechaActual)){
                if($fechaRequest->day > $fechaActual->day || $fechaRequest->month > $fechaActual->month || $fechaRequest->year > $fechaActual->year)
                    return Response::json(['errores' => 1,'mensaje' => 'No está permitido actualizar resultados para fechas en el futuro'], 201);
                else
                    $fecha = getdate(strtotime($datos['fecha']));
            }else{
                $fecha = getdate(strtotime($datos['fecha']));
            }
        }

        // return Response::json(['errores' => 1,'mensaje' => "Error al insertar premio: {$fecha['year']}-{$fecha['mon']}-{$fecha['mday']} {$fecha['hours']}:{$fecha['minutes']}:{$fecha['seconds']}"], 201);

    
        
        $errores = 0;
        $mensaje = '';
        $idBanca = Branches::on($datos["servidor"])->whereId($datos['idBanca'])->whereStatus(1)->first();
        if($idBanca == null){
            // $idBanca = Branches::
            //     on($datos["servidor"])
            //     ->where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
            //     ->first()->id;    
            $idBanca = Branches::
                on($datos["servidor"])
                ->where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
                ->first();    
            if($idBanca != null){
                $idBanca = $idBanca->id;
            }else{
                $idBanca = Branches::
                on($datos["servidor"])
                ->where(['status' => 1])
                ->first(); 
                if($idBanca != null)
                    $idBanca = $idBanca->id;
                else
                    $idBanca = 0;
            }     
        }else{
            $idBanca = $idBanca->id;
        }

        // \App\Jobs\AwardsJob::dispatch($datos, $fecha);
        // return Response::json([
        //     'errores' => 0,
        //     'mensaje' => 'Se ha guardado correctamente',
        //     'loterias' => "datos"
        //     //'colleccon' => $colleccion
        // ], 201);
        
    $servers = \App\Server::on("mysql")->get();
    $a = collect();
    foreach($servers as $server):
    $datos["servidor"] = $server->descripcion;
    
    foreach($datos['loterias'] as $l):
        $loteria = \App\Lotteries::on($datos["servidor"])->whereDescripcion(strtolower($l["descripcion"]))->first();
        $l['id'] = $loteria->id;
        $a->push(["lotieria" => $loteria->descripcion, "id" => $loteria->id, "servidor" => $datos["servidor"]]);
        // abort(404, "Lotieria: " . $loteria->id . " id: " . $loteria->id . " servidor: " . $datos["servidor"]);
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
        // if($awardsClass->datosValidos() == false){
        //     return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);
        // }
        if(!$awardsClass->loteriaAbreDiaActual()){
            continue;
            // return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $awardsClass->getLoteriaDescripcion() .' no abre este dia '], 201);
        
        }  
        if($awardsClass->insertarPremio() == false){
            return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
        }

           
            $c = 0;
            $colleccion = null;

            // return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);

            
            foreach($awardsClass->getJugadasDeFechaDada($l['id']) as $j){
    
                $j['premio'] = 0;
                $contador = 0;
                $busqueda1 = false;
                $busqueda2 = false;
                $busqueda3 = false;

                // abort(404, $j["jugada"]);

                $sorteo = Draws::on($datos["servidor"])->whereId($j['idSorteo'])->first();

            // return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);
                
    
                
                if($sorteo->descripcion == "Directo"){
                    if(!is_numeric($awardsClass->numerosGanadores)){
                        return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                    }
                    $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                }
                else if($sorteo->descripcion == "Pale"){
                    if(!is_numeric($awardsClass->numerosGanadores)){
                        return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                    }
                    $j['premio'] = $awardsClass->paleBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], $j['idSorteo']);
                }
                // else if($sorteo->descripcion == "Super pale"){
                //     if(!is_numeric($awardsClass->numerosGanadores)){
                //         return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                //     }
                //     $j['premio'] = $awardsClass->superPaleBuscarPremio($j['idVenta'], $l['id'], $j['idLoteriaSuperpale'], $j['jugada'], $j['monto'], $j['idSorteo']);
                // }
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


            //Buscar jugadas super pale de esa loteria, ya sea esta la loteria primaria o loteria superpale de la tabla salesdetails
            // return Response::json(['errores' => 1,'mensaje' => $awardsClass->getJugadasSuperpaleDeFechaDada($l['id'])], 201);
            foreach($awardsClass->getJugadasSuperpaleDeFechaDada($l['id']) as $j){

                
                if($l['id'] != $j["idLoteria"] && $l['id'] != $j["idLoteriaSuperpale"])
                    continue;
    
                $j['premio'] = 0;
                $contador = 0;
                $busqueda1 = false;
                $busqueda2 = false;
                $busqueda3 = false;

                if(!is_numeric($awardsClass->numerosGanadores)){
                    return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                }

                //Si el premio superpale es igual a -1 entonces eso quiere decir que la otra loteria no ha salido, 
                //por lo tanto el status de la jugada seguira siendo igual a cero, indicando que todavia la jugada estara pendiente
                $premioSuperpale = $awardsClass->superPaleBuscarPremio($j['idVenta'], $l['id'], $j);
                // return Response::json(['errores' => 1,'mensaje' => "Dentro jugadas super pale premio: {$premioSuperpale}"], 201);
                if($premioSuperpale != -1){
                    $j['premio'] = $premioSuperpale;
                    $j['status'] = 1;
                    $j->save();
                }else{
                    $j['premio'] = 0;
                    $j['status'] = 0;
                    $j->save();
                }
                
    
                $c++;
            }
    
        endforeach;


        foreach($datos['loterias'] as $l):
            // $ventas = Sales::on($datos["servidor"])
            // ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            // ->whereNotIn('status', [0,5])
            // ->get();


            /************** MANERA NUEVA DE CAMBIAR STATUS DE LOS TICKETS ***********/
            $loteria = \App\Lotteries::on($datos["servidor"])->whereDescripcion(strtolower($l["descripcion"]))->first();
            $l['id'] = $loteria->id;

            AwardsClass::actualizarStatusDelTicket($datos["servidor"], $l["id"], $fecha);

            // if($datos["servidor"] == "servidor3"){
            //     $fechaActual = $fecha;
            //     $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
            //     $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
            //     $data = \DB::connection($datos["servidor"])->select("select IF(ticketsInfo.todas_las_jugadas = ticketsInfo.todas_las_jugadas_salientes and ticketsInfo.premios > 0, 2, IF(ticketsInfo.todas_las_jugadas = ticketsInfo.todas_las_jugadas_salientes, 3, 1)) as status, ticketsInfo.premios from (select sd.idVenta as id, count(IF(sd.status = 1, 1, null)) as todas_las_jugadas_salientes, count(sd.id) as todas_las_jugadas, sum(sd.premio) as premios from salesdetails sd where sd.idVenta in (select sales.id from sales inner join salesdetails on salesdetails.idVenta = sales.id where salesdetails.idLoteria = {$l['id']} and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}' and sales.status not in(0, 5) group by sales.id) group by sd.idVenta) as ticketsInfo inner join sales s on s.id = ticketsInfo.id ");
            //     return Response::json([
            //         "fechaInicial" => $fechaInicial, 
            //         "fechaFinal" => $fechaFinal, 
            //         "data" => $data,
            //         "loteria" => $l["id"]
            //     ]);
            // }



            /********* MANERA VIEJA DE CAMBIAR STATUS DE LOS TICKETS */
            // $ventas = AwardsClass::getVentasDeFechaDada($datos["servidor"], $l["id"], $fecha);
    
            // foreach($ventas as $v){
            //     $todas_las_jugadas = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id']])->count();
            //     $todas_las_jugadas_salientes = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->count();
            //     $cantidad_premios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                
                
            //     // abort(404, "Error todas: {$todas_las_jugadas} salientes: {$todas_las_jugadas_salientes} status: {$v['status']}");
    
            //     if($todas_las_jugadas == $todas_las_jugadas_salientes)
            //     {

            //         if($cantidad_premios > 0)
            //         {
            //             $montoPremios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
            //             $v['premios'] = $montoPremios;
            //             $v['status'] = 2;
            //         }                        
            //         else{
            //             $v['premios'] = 0;
            //             $v['status'] = 3;
            //         }
                        
    
            //         $v->save();
            //     }
            // }

        endforeach;
        $loteriasOrdenadasPorHoraCierre = Helper::loteriasOrdenadasPorHoraCierre($datos["servidor"], Users::on($datos["servidor"])->whereId($datos["idUsuario"])->first());
        event(new LotteriesEvent($datos["servidor"], $loteriasOrdenadasPorHoraCierre));

        endforeach;
    
        $loterias = AwardsClass::getLoterias($datos["servidor"]);
        // $loterias = LotteriesSmallResource::collection($loterias);

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'loterias' => $loterias,
            "guardar" => $a
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
            // $idBanca = Branches::
            //     on($datos["servidor"])
            //     ->where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
            //     ->first()->id;     
            $idBanca = Branches::
                on($datos["servidor"])
                ->where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
                ->first();    
            if($idBanca != null){
                $idBanca = $idBanca->id;
            }else{
                $idBanca = Branches::
                on($datos["servidor"])
                ->where(['status' => 1])
                ->first(); 
                if($idBanca != null)
                    $idBanca = $idBanca->id;
                else
                    $idBanca = 0;
            }
        }else{
            $idBanca = $idBanca->id;
        }

        $loteriaDescripcion = \App\Lotteries::on($datos["servidor"])->whereId($datos["idLoteria"])->first();
        $loteriaDescripcion = ($loteriaDescripcion != null) ? $loteriaDescripcion->descripcion : "Real";

        $servers = \App\Server::on("mysql")->get();
        foreach($servers as $server):
        $datos["servidor"] = $server->descripcion;
        $loteria = \App\Lotteries::on($datos["servidor"])->whereDescripcion(strtolower($loteriaDescripcion))->first();
        $datos["idLoteria"] = $loteria->id;

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


        $awardsClass->setStatusAndPremioOfPlaysToPendientes($datos['idLoteria']);
        \App\Classes\AwardsClass::actualizarStatusDelTicket($datos["servidor"], $datos["idLoteria"], $fecha);
        
        $loteriasOrdenadasPorHoraCierre = Helper::loteriasOrdenadasPorHoraCierre($datos["servidor"], Users::on($datos["servidor"])->whereId($datos["idUsuario"])->first());
        // $loterias = LotteriesSmallResource::collection($loterias);
        event(new LotteriesEvent($datos["servidor"], $loteriasOrdenadasPorHoraCierre));
        
        endforeach;
            // foreach($awardsClass->getJugadasDeFechaDada($datos['idLoteria']) as $j){
            //     $j['premio'] = 0;
            //     $j['status'] = 0;
            //     $j->save();
            // }

            // foreach($awardsClass->getJugadasSuperpaleDeFechaDada($datos['idLoteria']) as $j){
            //     $j['premio'] = 0;
            //     $j['status'] = 0;
            //     $j->save();
            // }


            //Aqui buscaremos todos los tickets creados en el dia de hoy y vamos a 
            //asignarles el estado pendiente a los tickets en los cuales sus loterias aun no han salido
    
            // $ventas = Sales::on($datos["servidor"])->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            // ->whereNotIn('status', [0,5])
            // ->get();

            // $ventas = AwardsClass::getVentasDeFechaDada($datos["servidor"], $datos["idLoteria"], $fecha);
    
            // foreach($ventas as $v){
            //     $todas_las_jugadas_realizadas = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id']])->count();
            //     $todas_las_jugadas_que_ya_salieron = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->count();
            //     $cantidad_premios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                
            //     //Si la cantidad de jugadas realizadas es la que misma que la cantidad que jugadas que se 
            //     //han marcado como que ya salieron los premios, entonces la venta debe cambiar de status pendiente a ganadores o perdedores
            //     if($todas_las_jugadas_realizadas == $todas_las_jugadas_que_ya_salieron)
            //     {
            //         if($cantidad_premios > 0)
            //         {
            //             $montoPremios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
            //             $v['premios'] = $montoPremios;
            //             $v['status'] = 2;
            //         }    
            //         else{
            //             $v['premios'] = 0;
            //             $v['status'] = 3;
            //         }
                        
    
            //         $v->save();
            //     }else{
            //         $v['premios'] = 0;
            //         $v['pagado'] = 0;
            //         $v['status'] = 1;
            //         $v->save();
            //     }
            // }
    
    
        $loterias = AwardsClass::getLoterias($datos["servidor"]);
        $loteriasOrdenadasPorHoraCierre = Helper::loteriasOrdenadasPorHoraCierre($datos["servidor"], Users::on($datos["servidor"])->whereId($datos["idUsuario"])->first());
        // $loterias = LotteriesSmallResource::collection($loterias);
        event(new LotteriesEvent($datos["servidor"], $loteriasOrdenadasPorHoraCierre));
    
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
