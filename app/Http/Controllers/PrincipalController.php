<?php

namespace App\Http\Controllers;


use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\TicketPrintClass;


// use Faker\Generator as Faker;
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
use App\Awards;
use App\Draws;
use App\Branches;
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class PrincipalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName();
        $usuario = Users::whereId(session('idUsuario'))->first();
        if(!strpos(Request::url(), '/api/')){
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Vender tickets") == true){
                return redirect()->route('principal');
            }
            return view('principal.index', compact('controlador', 'usuario'));
        }

        $idBanca = 0;
        

        $datos = request()->validate([
            'datos.idUsuario' => ''
        ])['datos'];

        if(isset($datos['idUsuario'])){
            $idBanca = Branches::where(['id' => $datos['idUsuario'], 'status' => 1])->first();
            if($idBanca != null)
                $idBanca = $idBanca->id;
        }
       
        $fecha = getdate();
   
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
    
    
    
        return Response::json([
            'loterias' => Helper::loteriasOrdenadasPorHoraCierre(),
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas),
            'bancas' => Branches::whereStatus(1)->get()
        ], 201);


        
        
    }

    public function indexPost()
    {
        $idBanca = 0;
        

        $datos = request()->validate([
            'datos.idUsuario' => 'required'
        ])['datos'];

        if(isset($datos['idUsuario'])){
            $idBanca = Helper::getIdBanca($datos['idUsuario']);
            // $idBanca = Branches::where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first();
            // if($idBanca != null)
            //     $idBanca = $idBanca->id;
        }

        if($idBanca == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No hay bancas registradas'
            ], 201);
        }
       
        $fecha = getdate();
   
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
    
    
    
        return Response::json([
            'idVenta' => Helper::createIdVentaTemporal($idBanca),
            'loterias' => Helper::loteriasOrdenadasPorHoraCierre(),
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas),
            'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            'idUsuario' => $datos['idUsuario'],
            'idBanca' => $idBanca
        ], 201);
    }

    public function ticket()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        
        return view('principal.ticket', compact('controlador'));
    }

    public function pruebahttp(Request $request)
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        if(!strpos(Request::url(), '/api/')){
            return view('principal.pruebahttp', compact('controlador'));
        }

        
       
        
        $saldo = (new Helper)->_sendSms("+18294266800", "Hola jean como estas");

        $fechaActual = getdate();

        $cadena = "100121";
        $buscar = "10";

        $bancas = Branches::whereIn('status', array(0, 1))->get();

        $coleccion = collect([
            ["nombre" => "jean", "edad" => 24, "status" => true],
            ["nombre" => "pedro", "edad" => 24, "status" => true],
            ["nombre" => "juana", "edad" => 24, "status" => false],
        ]);

        list($loterias_seleccionadas, $no) = $coleccion->partition(function($l){
            return $l['status'] == true;
        });
        

  

       
        $fechaCarbon = Carbon::now();
        /***  ADD PARA AGREGAR Y SUB PARA QUITAR  */
        // $fechaCarbon->addSecond()
        // $fechaCarbon->addSeconds(10)
        // $fechaCarbon->addMinute()
        // $fechaCarbon->addMinutes(5)
        // $fechaCarbon->addHour()
        // $fechaCarbon->addHours(5)
        // $fechaCarbon->addDays(5)
        // $fechaCarbon->addWeek()
        // $fechaCarbon->addWeeks(5)
        // $fechaCarbon->addYear()
        // $fechaCarbon->addYears(3)
        // $fechaCarbon->subYears(3)
        $fechaCarbon2 = Carbon::now()->addDay();
        $a = new Carbon('2019-01-30 5:23');

        return Response::json([
            'ventas' => SalesResource::collection(Sales::whereId(2)->get()),
            'coleccion' => $loterias_seleccionadas,
            'bancas' => BranchesResource::collection($bancas),
            'busqueda' => strpos($cadena, $buscar),
            'fechaActual' => $fechaActual,
            // 'timezone' => $timezone,
            'a' => $fechaCarbon,
            'a1' => $fechaCarbon2 ,
            'a2' => $a->addMonthNoOverflow(),
            'a3' => $a->copy()->addMonthNoOverflow(),
            'a4' => $saldo
        ], 201);
    }

    public function duplicar()
    {
        $codigoBarra = request()->validate([
            'datos.codigoBarra' => '',
            'datos.codigoQr' => ''
        ])['datos'];

        
        if(isset($codigoBarra['codigoBarra'])){
            if(!(new Helper)->isNumber($codigoBarra['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
            if(strlen($codigoBarra['codigoBarra']) != 10){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
        }
        else if(isset($codigoBarra['codigoQr']) && !isset($codigoBarra['codigoBarra'])){
            $codigoBarra['codigoBarra'] = base64_decode($codigoBarra['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        $idTicket = Tickets::where('codigoBarra', $codigoBarra['codigoBarra'])->value('id');
        $idVenta = Sales::where('idTicket', $idTicket)->whereNotIn('status', [0,5])->value('id');
        
        if(strlen($codigoBarra['codigoBarra']) == 10 && is_numeric($codigoBarra['codigoBarra']) == true){
            if($idVenta != null){
                $idLoterias = Salesdetails::distinct()->select('idLoteria')->where('idVenta', $idVenta)->get();
                $idLoterias = collect($idLoterias)->map(function($id){
                    return $id->idLoteria;
                });
    
                $loterias = Lotteries::whereIn('id', $idLoterias)->whereStatus(1)->get();
                // $jugadas = Salesdetails::where('idVenta', $idVenta)->get();
                
                $jugadas = collect(Salesdetails::where('idVenta', $idVenta)->get())->map(function($d){
                    $sorteo = Draws::whereId($d['idSorteo'])->first()->descripcion;
                    return ['id' => $d['id'], 'idVenta' => $d['idVenta'], 'jugada' => $d['jugada'], 'idLoteria' => $d['idLoteria'], 'idSorteo' => $d['idSorteo'], 'monto' => $d['monto'], 'premio' => $d['premio'], 'status' => $d['status'], 'sorteo' => $sorteo];
                });
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }
        //$fecha = getdate(strtotime($fecha['fecha']));
    
        
    
        // $jugadas = collect($jugadas)->map(function($d){
        //     $loteria = Lotteries::whereId($d['idLoteria'])->first();
        //     return ['id' => $d['id'], 'idVenta' => $d['idVenta'], 
        //             'idLoteria' => $d['idLoteria'], 'idSorteo' => $d['idSorteo'], 
        //             'jugada' => $d['jugada'], 'monto' => $d['monto'],
        //             'premio' => $d['premio'],
        //             'status' => $d['status'],
        //             ]
        // });
      
        
    
        return Response::json([
            'loterias' => $loterias,
            'jugadas' => $jugadas,
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }

    public function pagar()
    {
        $datos = request()->validate([
            'datos.codigoBarra' => '',
            'datos.codigoQr' => '',
            'datos.idUsuario' => 'required'
        ])['datos'];
    
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Marcar ticket como pagado")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }

        if(isset($datos['codigoBarra'])){
            if(!(new Helper)->isNumber($datos['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
            if(strlen($datos['codigoBarra']) != 10){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
        }
        else if(isset($datos['codigoQr']) && !isset($datos['codigoBarra'])){
            $datos['codigoBarra'] = base64_decode($datos['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
        $venta = null;
        
    
    
        if(strlen($datos['codigoBarra']) == 10 && is_numeric($datos['codigoBarra'])){
            $idTicket = Tickets::where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::where('idTicket', $idTicket)->whereIn('status', [1,2])->wherePagado(0)->wherePagado(0)->get()->first();
    
            if($venta != null){
                // $venta['pagado'] = 1;
                // $venta->save();
    
                if(Helper::pagar($venta->id, $datos['idUsuario'])){
                    $mensaje = "El ticket se ha pagado correctamente";
                }
                else{
                    $errores = 1;
                    $mensaje = "El ticket no existe, no esta premiado o ya ha sido pagado";
                }
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe, no esta premiado o ya ha sido pagado";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje,
            'venta' => ($venta != null) ? new SalesResource($venta) : null,
        ], 201);
    }


    public function buscarTicketAPagar()
    {
        $datos = request()->validate([
            'datos.codigoBarra' => '',
            'datos.codigoQr' => '',
            'datos.idUsuario' => 'required'
        ])['datos'];
    
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Marcar ticket como pagado")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }

        if(isset($datos['codigoBarra'])){
            if(!(new Helper)->isNumber($datos['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
            if(strlen($datos['codigoBarra']) != 10){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
        }
        else if(isset($datos['codigoQr']) && !isset($datos['codigoBarra'])){
            $datos['codigoBarra'] = base64_decode($datos['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
    
        if(strlen($datos['codigoBarra']) == 10 && is_numeric($datos['codigoBarra'])){
            $idTicket = Tickets::where('codigoBarra', $datos['codigoBarra'])->value('id');
            //->wherePagado(0)
            $venta = Sales::where('idTicket', $idTicket)->whereStatus(2)->get()->first();
            
            if($venta != null){
                
                if(Helper::verificarTicketHaSidoPagado($venta->id)){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'El ticket ya ha sido pagado'
                    ], 201);
                }else{
                    return Response::json([
                        'errores' => 0,
                        'mensaje' => '',
                        'venta' =>  new SalesResource($venta)
                    ], 201);
                }
    
            }else{
                
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El ticket no existe, no esta premiado'
                ], 201);
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }


    public function cancelar()
    {
        $datos = request()->validate([
            'datos.codigoBarra' => 'required',
            'datos.razon' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idBanca' => 'required'
        ])['datos'];
    
        $usuario = Users::whereId($datos['idUsuario'])->first();
        // if(!$usuario->tienePermiso("Eliminar ticket")){
        //     return Response::json([
        //         'errores' => 1,
        //         'mensaje' => 'No tiene permisos para realizar esta accion'
        //     ], 201);
        // }
    
       //return $datos;
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
    
        if(strlen($datos['codigoBarra']) == 10 && is_numeric($datos['codigoBarra'])){
            //Obtenemos el ticket
            $idTicket = Tickets::where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::where('idTicket', $idTicket)->whereNotIn('status', [0, 5])->get()->first();
    
            
            
            if($venta != null){
                $banca = Branches::whereId($datos['idBanca'])->first();
                $minutoTicketJugado =  getdate(strtotime($venta['created_at']));
                $minutoActual = $fecha['minutes'];
    
                if(!$usuario->tienePermiso("Cancelar tickets en cualquier momento")){
                    if($minutoTicketJugado['year'] != $fecha['year']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar",
                            'ticket' => $minutoTicketJugado
                        ], 201);
                    }
                    if($minutoTicketJugado['mon'] != $fecha['mon']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                    if($minutoTicketJugado['mday'] != $fecha['mday']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                    if($minutoTicketJugado['hours'] != $fecha['hours']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                }
               // return ($minutoActual - $minutoTicketJugado) . " - " .$banca[' minutosCancelarTicket'];
    
                if(($minutoActual - $minutoTicketJugado['minutes']) < $banca['minutosCancelarTicket'] || $usuario->tienePermiso("Cancelar tickets en cualquier momento")){
                    $venta['pagado'] = 0;
                    $venta['status'] = 0;
                    $venta->save();

                    $ventasDetalles = Salesdetails::where('idVenta', $venta['id'])->get();
                    foreach($ventasDetalles as $v){
                        $v['premio'] = 0;
                        $v['status'] = 0;
                        $v->save();
                    }
    
                    Cancellations::create([
                        'idTicket' => $venta['idTicket'],
                        'idUsuario' => $datos['idUsuario'],
                        'razon' => $datos['razon']
                    ]);
    
                    $mensaje = "El ticket se ha cancelado correctamente";
                }else{
                    $errores = 1;
                    $mensaje = "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar";
                    //$mensaje = "minutos actual: $minutoActual, minutosticket: $minutoTicketJugado";
                }
                
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe o ya ha sido cancelado";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }

        $fecha = getdate();
        $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereNotIn('status', [0,5])->get();
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
    
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje,

            'loterias' => Lotteries::whereStatus(1)->get(),
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas),
            'bancas' => Branches::whereStatus(1)->get()
        ], 201);
    }



    public function eliminar()
    {
        $datos = request()->validate([
            'datos.codigoBarra' => 'required',
            'datos.razon' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idBanca' => 'required'
        ])['datos'];
    
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Eliminar ticket")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }
    
       //return $datos;
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
    
        if(strlen($datos['codigoBarra']) == 10 && is_numeric($datos['codigoBarra'])){
            //Obtenemos el ticket
            $idTicket = Tickets::where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::where('idTicket', $idTicket)->whereNotIn('status', [5])->get()->first();
    
            
            
            if($venta != null){
                $banca = Branches::whereId($datos['idBanca'])->first();
            
    
               
               // return ($minutoActual - $minutoTicketJugado) . " - " .$banca[' minutosCancelarTicket'];
    
                    $venta['status'] = 5;
                    $venta['pagado'] = 0;
                    $venta->save();

                    $ventasDetalles = Salesdetails::where('idVenta', $venta['id'])->get();
                    foreach($ventasDetalles as $v){
                        $v['premio'] = 0;
                        $v['status'] = 0;
                        $v->save();
                    }
    
                    Cancellations::create([
                        'idTicket' => $venta['idTicket'],
                        'idUsuario' => $datos['idUsuario'],
                        'razon' => $datos['razon']
                    ]);
    
                    $mensaje = "El ticket se ha eliminado correctamente";
               
                
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe para eliminar";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }

       
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }




    public function copiarTicket()
    {
        $idBanca = 0;
        

        $datos = request()->validate([
            'datos.idTicket' => 'required'
        ])['datos'];

        $idVenta = Sales::where('idTicket', $datos['idTicket'])->first();
        if($idVenta == null){
            $idVenta = $idVenta->id;
        }else{
            return Response::json([
                'errores' => 1,
                'mensaje' => 'El ticket no existe',
                'img' => null
            ], 201);
        }

       
    
        $img = new TicketClass($sale->id);
        $img = $img->generate();
    
    
        return Response::json([
            'errores' => 0,
            'mensaje' => '',
            'img' => $img
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
            'datos.idUsuario' => 'required',
            'datos.idBanca' => 'required',
            'datos.idVenta' => 'required',
            'datos.descuentoMonto' => 'required',
            'datos.hayDescuento' => 'required',
            'datos.total' => 'required',
            'datos.subTotal' => 'required',
    
            'datos.loterias' => 'required',
            'datos.jugadas' => 'required',
        ])['datos'];

        $datos['idVenta'] = Helper::getIdVentaTemporal($datos['idVenta']);
        if($datos['idVenta'] == 0){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Error de seguridad: Idventa incorrecto'
            ], 201);
        }

        $sale = Sales::where(['id' => $datos['idVenta'], 'idBanca' => $datos['idBanca']])->whereNotIn('status', [0, 5])->first();
        if($sale != null){
            //En caso de que la venta ya exista eso quiere decir que 
            //la venta se guardo pero hubo un error antes de terminar de guardar todo por lo tanto es mejor borrarla y volver a crearla
            Helper::borrarVentaErronea($sale);
            // $img = new TicketPrintClass($sale->id);
            // $img = $img->generate();
            // return Response::json([
            //     'idVenta' => Helper::createIdVentaTemporal($datos['idBanca']),
            //     'errores' => 0,
            //     'mensaje' => 'Se ha guardado Correctamente',
            //     'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            //     'loterias' => Helper::loteriasOrdenadasPorHoraCierre(),
            //     'venta' => ($sale != null) ? new SalesResource($sale) : null,
            //     'img' => $img
            // ], 201);
        }

   
        
    
        $fecha = getdate();
        $idSorteo = 0;
        $idTicket = 0;
        $codigoBarraCorrecto = false;
        $errores = 0;
        $mensaje = '';
        $codigoBarra = '';
        $sale = null;
        $idDia = Days::whereWday($fecha['wday'])->first()->id;
    
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso('Vender tickets')){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }
    
        if(!$usuario->esBancaAsignada($datos['idBanca'])){
            if(!$usuario->tienePermiso('Jugar como cualquier banca')){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'No tiene permisos para realizar esta accion'
                ], 201);
            }
        }
    
    
        if(!Branches::whereId($datos['idBanca'])->first()->abierta()){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'La banca aun no ha abierto'
            ], 201);
        }
    
        if(Branches::whereId($datos['idBanca'])->first()->cerrada()){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'La banca ha cerrado'
            ], 201);
        }

        if(Branches::whereId($datos['idBanca'])->first()->limiteVenta($datos['total'])){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'A excedido el limite de ventas de la banca'
            ], 201);
        }
    
        
    
       
    
        //Generamos y guardamos codigo de barra
        while($codigoBarraCorrecto != true){
            // $codigoBarra = $faker->isbn10;
            $codigoBarra = rand(1111111111, getrandmax());
            //return 'codiog: ' . $codigoBarra . ' faker: ' . $faker->isbn10;
            //Verificamos de que el codigo de barra no exista
            if(Tickets::where('codigoBarra', $codigoBarra)->get()->first() == null){
                if(is_numeric($codigoBarra)){
                    Tickets::create(['idBanca' => $datos['idBanca'], 'codigoBarra' => $codigoBarra]);
                    $idTicket = Tickets::where('codigoBarra', $codigoBarra)->value('id');
                    $codigoBarraCorrecto = true;
                    break;
                }
            }
        }
    
        
        /***************** Validamos la existencia de la jugada ***********************/
        //foreach($datos['loterias'] as $l){
            foreach($datos['jugadas'] as $d){

               
                
                $loteria = Lotteries::whereId($d['idLoteria'])->first();
                 //Confirmamos de que la loteria no tenga premios registrados en el dia de hoy
               //si es asi entonces no puede realizar la jugada y en caso de querer hacer jugadas
               //enonces debe borrar los premios de dicha loteria
               if(Helper::loteriaTienePremiosRegistradosHoy($d['idLoteria']) == true){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'Error: La loteria ' . $loteria->descripcion . ' ya tiene numeros ganadores registrados'
                    ], 201);
                }

                

               $idSorteo = (new Helper)->determinarSorteo($d['jugada'], $loteria->id);
               $sorteo = Draws::whereId($idSorteo)->first();
               if(Helper::decimalesDelMontoJugadoSonValidos($d['monto'], $loteria, $sorteo) == false){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Error: El monto de la jugada ' . $d['jugada'] . ' es incorrecto'
                ], 201);
            }
               

            $banca = Branches::whereId($datos['idBanca'])->first();
            
                if(!$banca->loteriaExisteYTienePagoCombinaciones($d['idLoteria'], $idSorteo)){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria ' . $loteria->descripcion . ' no esta permitida en esta banca, o no tiene combinaciones para el sorteo ' . $sorteo->descripcion
                    ], 201);
                }
    
               if(!$loteria->sorteoExiste($idSorteo)){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'El sorteo no existe para la loteria' . $loteria->descripcion
                    ], 201);
                }
    
               if(!$loteria->abreHoy()){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria' . $loteria->descripcion . ' no habre hoy'
                    ], 201);
                }
    
               if(!$loteria->abierta()){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria' . $loteria->descripcion . ' aun no ha abierto'
                    ], 201);
                }
            
                if($loteria->cerrada()){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria' . $loteria->descripcion . ' ha cerrado'
                    ], 201);
                }
        
    
               //Obtenemos los datos correspondiente a la loteria y jugada dada que estan en la tabla inventario (stcoks)
               $stock = Stock::where([
                    'idBanca' => $datos['idBanca'], 
                    'idLoteria' => $d['idLoteria'], 
                    'jugada' => $d['jugada']])
                    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
                   
                if((new Helper)->montodisponible($d['jugada'], $d['idLoteria'], $datos['idBanca']) < $d['monto']){
                        $errores = 1;
                        $mensaje = 'No hay existencia suficiente para la jugada ' . $d['jugada'] .' en la loteria ' . $d['descripcion'];
                        break;
                }
            
               
            }
        //}
    
    if($errores == 0){
    
        /***************** Insertar la venta y obtener el idVenta ***********************/
        
       $sale = Sales::create([
           'id' => $datos['idVenta'],
           'idUsuario' => $datos['idUsuario'],
           'idBanca' => $datos['idBanca'],
           'total' => $datos['total'],
           'subTotal' => $datos['subTotal'],
           'descuentoMonto' => $datos['descuentoMonto'],
           'hayDescuento' => $datos['hayDescuento'],
           'idTicket' => $idTicket
       ]);

       //Hago esto porque al momento de guardar me retorna el id 0 entonces no es correcto
       $sale = Sales::whereId($datos['idVenta'])->first();
    
    
       /***************** Insertar el datelle ventas ***********************/
    
   
    $coleccionJugadas = collect($datos['jugadas']);
        // list($jugadasLoterias, $no) = $coleccionJugadas->partition(function($j) use($l){
        //     return $j['idLoteria'] == $l['id'];
        // });

        foreach($datos['jugadas'] as $d){

            $loteria = Lotteries::whereId($d['idLoteria'])->first();
            
           $idSorteo = (new Helper)->determinarSorteo($d['jugada'], $loteria->id);
           $d['jugada'] = Helper::quitarUltimoCaracter($d['jugada'], $idSorteo);
    
           $stock = Stock::where(['idLoteria' => $d['idLoteria'],'idBanca' => $datos['idBanca'], 'jugada' => $d['jugada'], 'idSorteo' => $idSorteo])
                    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
                    
               //Verificamos que la variable $stock no sea nula
               if($stock != null){
                   //Restamos el monto jugado a la tabla stock y guardamos
                   $stock['monto'] = $stock['monto'] - $d['monto'];
                   $stock->save();
               }else{
                   //Obtenemos el stock de la tabla bloqueo jugadas
                    $stock = Blocksplays::where(
                        ['idBanca' => $datos['idBanca'], 
                        'idLoteria' => $d['idLoteria'],
                        'idSorteo' => $idSorteo,
                        'jugada' => $d['jugada'], 
                        'status' => 1])
                        ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
                        ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->first();
                
                    //Verificamos que no sea nulo
                    if($stock != null){
                        //Si el monto en la variable $stock es mejor que el monto que se jugara entonces no hay exitencia suficiente
                        Stock::create([
                            'idBanca' => $datos['idBanca'],
                            'idLoteria' => $d['idLoteria'],
                            'idSorteo' => $idSorteo,
                            'jugada' => $d['jugada'],
                            'montoInicial' => $stock['monto'],
                            'monto' => $stock['monto'] - $d['monto']
                        ]);
                    }else{
                        $stock = Blockslotteries::where([
                            'idBanca' => $datos['idBanca'], 
                            'idLoteria' => $d['idLoteria'], 
                            'idSorteo' => $idSorteo
                        ])->first();
    
                        Stock::create([
                            'idBanca' => $datos['idBanca'],
                            'idLoteria' => $d['idLoteria'],
                            'idSorteo' => $idSorteo,
                            'jugada' => $d['jugada'],
                            'montoInicial' => $stock['monto'],
                            'monto' => $stock['monto'] - $d['monto']
                        ]);
                    }
               }
    
         Salesdetails::create([
             'idVenta' => Sales::where('idTicket', $idTicket)->value('id'),
             'idLoteria' => $d['idLoteria'],
             'idSorteo' => $idSorteo,
             'jugada' => $d['jugada'],
             'monto' => $d['monto'],
             'premio' => 0,
             'monto' => $d['monto'],
             'comision' => Helper::comision($datos['idBanca'], $d['idLoteria'], $idSorteo, $d['monto'])
         ]);

         
    
        }

        if($errores == 0)
            $mensaje = "Se ha guardado correctamente";

    
    } //END if validacion si hay errores

        $img = null;
        if($sale != null){
            // $img = new TicketClass($sale->id);\
            
            $img = new TicketPrintClass($sale->id);
            $img = $img->generate();
        }
    
        return Response::json([
            'idVenta' => Helper::createIdVentaTemporal($datos['idBanca']),
            'errores' => $errores,
            'mensaje' => $mensaje,
            'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            'loterias' => Helper::loteriasOrdenadasPorHoraCierre(),
            'venta' => ($sale != null) ? new SalesResource($sale) : null,
            'img' => $img,
            'ventas' => SalesResource::collection(Helper::getVentasDeHoy($datos['idBanca'])),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function montodisponible(Request $request)
    {
        $datos = request()->validate([
            'datos.jugada' => 'required|min:2|max:6',
            'datos.idLoteria' => 'required',
            'datos.idBanca' => 'required'
        ])['datos'];
    
        $fecha = getdate();
        $idSorteo = 0;
        $bloqueo = 0;
    
        $idDia = Days::whereWday($fecha['wday'])->first()->id;
    
        $loteria = Lotteries::whereId($datos['idLoteria'])->first();
    
    //    if(strlen($datos['jugada']) == 2){
    //         $idSorteo = 1;
    //    }
    //    else if(strlen($datos['jugada']) == 4){
    //         if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
    //             $idSorteo = 2;
    //         else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
    //             $idSorteo = 4;
    //    }
    //    else if(strlen($datos['jugada']) == 6){
    //         $idSorteo = 3;
    //    }

  
        $idSorteo = (new Helper)->determinarSorteo($datos['jugada'], $loteria->id);
    
       $bloqueo = Stock::where([   
           'idLoteria' => $datos['idLoteria'], 
           'idBanca' => $datos['idBanca'], 
           'jugada' => $datos['jugada'],
           'idSorteo' => $idSorteo,
        ])
       ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->value('monto');
       
    //Verificamos que la variable $stock no sea nula
    // if($bloqueo == null){
    //     $bloqueo = Blocksplays::where(
    //         [
    //             'idBanca' => $datos['idBanca'],
    //             'idLoteria' => $datos['idLoteria'], 
    //             'jugada' => $datos['jugada'],
    //             'idSorteo' => $idSorteo,
    //             'status' => 1
    //         ])
    //         ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
    //         ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->value('monto');
    
    //     if($bloqueo == null){
    //         $bloqueo = Blockslotteries::where([
    //             'idBanca' => $datos['idBanca'], 
    //             'idLoteria' => $datos['idLoteria'], 
    //             'idDia' => $idDia,
    //             'idSorteo' => $idSorteo
    //         ])->value('monto');
    //     }
    // }

    $bloqueo = (new Helper)->montodisponible($datos['jugada'], $datos['idLoteria'], $datos['idBanca']);
    
       
    
       if($bloqueo == null) $bloqueo = 0;
    
        return Response::json([
            'monto' => $bloqueo
        ], 201);
    }


    public function imagen(Request $request)
    {
            $datos = request()->validate([
            'datos.imagen' => 'required',
            'lastModifiedDate' => '',
            'datos.nombre' => '',
            'name' => '',
            'size' => '',
            'type' => ''
        ])['datos'];

        

        // $lastModifiedDate = $datos['imagen']['lastModifiedDate'];
        // $name = request()->datos['name'];
        // $type = request()->datos['type'];
        // $size = request()->datos['size'];

        // $img = collect($datos['imagen'])->map(function($s) use($lastModifiedDate, $name, $type, $size){
                
        //     return ['name' => $name, 'lastModifiedDate' => $lastModifiedDate, 'type' => $type, 'size' => $size ];
        // });

        // $img = str_replace('data:image/png;base64,', '', $datos['imagen']);
        // $img = str_replace(' ', '+', $img);
        // $fileData  = base64_decode($img);
        // $mensaje = 'Funciona conversion';
        // if($fileData == false){
        //     $mensaje = "Error base64 conversion";
        // }





    // $image = file_put_contents(public_path().'/img/'.'test2.png',$img);
    // $output_file = "fotoo.png";
    // $output_file = public_path().'/ticket/'. $datos['nombre'] . ".png";
    
    $output_file = public_path() . "\\assets\\ticket\\" . $datos['nombre'] . ".png";
    $file = fopen($output_file, "wb");
    $imagenBase64SinComaillas = explode(',', $datos['imagen']);
    fwrite($file, base64_decode($imagenBase64SinComaillas[1]));
    fclose($file);

    $ticket = Tickets::where('codigoBarra', $datos['nombre'])->first();
    if($ticket != null){
        $ticket->imageBase64 = $imagenBase64SinComaillas[1];
        $ticket->save();
    }

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'imagenBase64' => $imagenBase64SinComaillas[1],
            'nombre' => $datos['nombre'],
            'blob' => $output_file,
            'request' => request()->all(),
            'base_decode' => $output_file
        ], 201);

        $image = file_put_contents(public_path().'/img/'.'test.png',$datos['imagen']);
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'temp' => $image,
            'blob' => $datos['imagen']
        ], 201);

        // ruta de las imagenes guardadas
    $ruta = public_path().'/img/';

    // recogida del form
    $imagenOriginal = $datos['imagen'];

    // crear instancia de imagen
    $imagen = Image::make($imagenOriginal);

    // generar un nombre aleatorio para la imagen
    $temp_name = $this->random_string() . '.' . $imagenOriginal->getClientOriginalExtension();

    $imagen->resize(300,300);

    // guardar imagen
    // save( [ruta], [calidad])
    $imagen->save($ruta . $temp_name, 100);






        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'temp' => $temp_name
        ], 201);
    }



    public function sms(Request $request)
    {
            $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.codigoBarra' => 'required',
            'datos.sms' => 'required',
            'datos.whatsapp' => 'required',
            'datos.numSms' => '',
            'datos.numWhatsapp' => '',
        ])['datos'];

        $colleccion = [];


        if($datos["sms"] == 1){
            $arreglo = (new Helper)->_sendSms($datos["numSms"], $datos["codigoBarra"]);
            $colleccion = collect([$arreglo ]);
        }
        if($datos["whatsapp"] == 1){
            $arreglo = (new Helper)->_sendSms($datos["numSms"], $datos["codigoBarra"], false);
            if($colleccion == null){
                $colleccion = collect([$arreglo ]);
            }else{
                $colleccion->push($arreglo);
            }
        }



        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            "errrores" => $colleccion
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sales $sales)
    {
        //
    }
}
