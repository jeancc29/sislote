<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 

use App\Sales;
use Request;


use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
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
use App\Classes\Helper;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\SalesSmallResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class MonitoreoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function jugadas()
    {
        $controlador = Route::getCurrentRoute()->getName();
        if(!strpos(Request::url(), '/api/')){
            // return "<h1>Dentro reporte jugadas: $controlador </h1>";
            
           return view('reportes.jugadas', compact('controlador'));
        }

        
        $datos = request()->validate([
            'datos.idLoteria' => '',
            'datos.fecha' => '',
            'datos.bancas' => ''
        ])['datos'];
    
        $fecha = getdate(strtotime($datos['fecha']));
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
    
        if($datos['idLoteria'] != null && $datos['fecha'] != null && $datos['bancas'] != null){
            //Obtenemos todos los id bancas
            $idBancas = collect($datos['bancas'])->map(function($id){
                return $id['id'];
            });


            $idVentas = Sales::select('id')
                ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereNotIn('status', [0,5])
                ->whereIn('idBanca', $idBancas)
                ->get();
    
            $idVentas = collect($idVentas)->map(function($id){
                return $id->id;
            });
        
        
            $jugadas = Salesdetails::
                        where('idLoteria', $datos['idLoteria'])
                        ->whereIn('idVenta', $idVentas)
                        ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                        ->get();
        }
    
        
    
        
      
        
    
        return Response::json([
            'jugadas' => $jugadas,
            'errores' => $errores,
            'mensaje' => $mensaje,
            'loterias' => Lotteries::whereStatus(1)->get(),
            'bancas' => Branches::whereStatus(1)->get()
        ], 201);
    }


    public function ventas()
    {
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.idBanca' => '',
            'datos.fecha' => 'required',
            'datos.fechaFinal' => '',
            'datos.layout' => ''
        ])['datos'];
    
        if(!isset($datos['fechaFinal'])){
            $fecha = getdate(strtotime($datos['fecha']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }else{
            $fecha = getdate(strtotime($datos['fecha']));
            $fechaF = getdate(strtotime($datos['fechaFinal']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }
        

        
    

        $usuario = Users::on(session("servidor"))->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Monitorear ticket")){
            //Datos['layout'] es un parametro que me indicara si se esta accediendo 
            // desde la ventana principal o desde otra venta, si es de la ventana principal entonces
            // se verifica que la variable $datos['layout'] este definida y que su valor sea igual a 'Principal', 
            // de lo contrario no tendra permisos
            if(!isset($datos['layout'])){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'No tiene permisos para realizar esta accion'
                ], 201);
            }
            else if($datos['layout'] != 'Principal'){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'No tiene permisos para realizar esta accion'
                ], 201);
            }
        }

        if(isset($datos['idBanca'])){
            $datos['idBanca'] = Branches::where(['id' => $datos['idBanca'], 'status' => 1])->first();
            if($datos['idBanca'] != null)
                $datos['idBanca'] = $datos['idBanca']->id;
        }else{
            $datos['idBanca'] = Branches::where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first()->id;
        }
    
        //$fecha = getdate(strtotime($datos['fecha']));
        
    
        $pendientes = Sales::
                    whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereStatus(1)
                    ->where('idBanca', $datos['idBanca'])
                    ->count();
    
        $ganadores = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereStatus('2')
                    ->where('idBanca', $datos['idBanca'])
                    ->count();
        $premios = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereStatus('2')
            ->where('idBanca', $datos['idBanca'])
            ->sum("premios");
                    
                
        $perdedores = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereStatus('3')
                    ->where('idBanca', $datos['idBanca'])
                    ->count();
    
        $total = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereIn('status', array(1,2,3))
                    ->where('idBanca', $datos['idBanca'])
                    ->count();
    
                    $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereNotIn('status', [0,5])
                    ->where('idBanca', $datos['idBanca'])
                    ->sum('total');
    
                    //AQUI COMIENSA LAS COMISIONES
    
                    //AQUI TERMINAN LAS COMISIONES
    
                    $descuentos = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereNotIn('status', [0,5])
                    ->where('idBanca', $datos['idBanca'])
                    ->sum('descuentoMonto');
    
                    $premios = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereIn('status', array(1,2))
                    ->where('idBanca', $datos['idBanca'])
                    ->sum('premios');
    
                    //Obtener loterias con el monto total jugado y con los premios totales
    
                    
    
                    
                    $loterias = Lotteries::
                            selectRaw('
                                id, 
                                descripcion, 
                                (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as ventas,
                                (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as premios,
                                (select substring(numeroGanador, 1, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as primera,
                                (select substring(numeroGanador, 3, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as segunda,
                                (select substring(numeroGanador, 5, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as tercera
                                ', [$datos['idBanca'], $fechaInicial, $fechaFinal, //Parametros para ventas
                                    $datos['idBanca'], $fechaInicial, $fechaFinal, //Parametros para premios
                                    $fechaInicial, $fechaFinal, //Parametros primera
                                    $fechaInicial, $fechaFinal, //Parametros segunda
                                    $fechaInicial, $fechaFinal //Parametros tercera
                                    ])
                            ->where('lotteries.status', '=', '1')
                            ->get();

                    $loterias = collect($loterias)->map(function($d) use($datos, $fechaInicial, $fechaFinal){
                        $datosComisiones = Commissions::where(['idBanca' => $datos['idBanca'], 'idLoteria' => $d['id']])->first();
                        $comisionesMonto = 0;
                        $idVentasDeEstaBanca = Sales::select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $datos['idBanca'])->whereNotIn('status', [0,5])->get();
                        $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
                            return $id->id;
                        });
                        if($datosComisiones['directo'] > 0){
                            $sorteo = Draws::whereDescripcion('Directo')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
        
                        if($datosComisiones['pale'] > 0){
                            $sorteo = Draws::whereDescripcion('Pale')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
        
                        if($datosComisiones['tripleta'] > 0){
                            $sorteo = Draws::whereDescripcion('Tripleta')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
        
                        if($datosComisiones['superPale'] > 0){
                            $sorteo = Draws::whereDescripcion('Super pale')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
                        $comisionesMonto = round($comisionesMonto);
                        if($d->ventas == null)
                            $d->ventas = 0;
                        if($d->premios == null)
                            $d->premios = 0;
                        if($d->primera == null)
                            $d->primera = "";
                        if($d->segunda == null)
                            $d->segunda = "";
                        if($d->tercera == null)
                            $d->tercera = "";
                        return ['id' => $d->id, 'descripcion' => $d->descripcion, 'comisiones' => $comisionesMonto, 'ventas' => $d->ventas, 'premios' => $d->premios, 'primera' => $d->primera, 'segunda' => $d->segunda, 'tercera' => $d->tercera, 'neto' => ($d->ventas) - ((int)$d->premios + $comisionesMonto)];
                    });
    
      
        $ticketsGanadores = Sales::
            whereStatus(2)
            ->wherePagado(0)
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->where('idBanca', $datos['idBanca'])
            ->get();
    
            $comisionesMonto = 0;
            $datosComisiones = Commissions::where('idBanca', $datos['idBanca'])->get();
            $idVentasDeEstaBanca = Sales::select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $datos['idBanca'])->whereNotIn('status', [0,5])->get();
            $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
                return $id->id;
            });
            foreach($datosComisiones as $d){
                if($d['directo'] > 0){
                    $sorteo = Draws::whereDescripcion('Directo')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pale'] > 0){
                    $sorteo = Draws::whereDescripcion('Pale')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['tripleta'] > 0){
                    $sorteo = Draws::whereDescripcion('Tripleta')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['superPale'] > 0){
                    $sorteo = Draws::whereDescripcion('Super pale')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }
            }

            $balanceHastaLaFecha = (new Helper)->saldo($datos['idBanca'], 1);
            $comisiones = 0;
            $neto = $ventas -  ($premios + $descuentos + $comisionesMonto);

        return Response::json([
            'errores' => 0,
            'balanceHastaLaFecha' => $balanceHastaLaFecha,
            'pendientes' => $pendientes,
            'perdedores' => $perdedores,
            'ganadores' => $ganadores,
            'total' => $total,
            'ventas' => $ventas,
            'descuentos' => $descuentos,
            'premios' => $premios,
            'neto' => round($neto),
            'loterias' => $loterias,
            'ticketsGanadores' => SalesResource::collection($ticketsGanadores),
            'banca' => Branches::whereId($datos['idBanca'])->first(),
            'premios' => $premios,
            'balanceActual' => round(($balanceHastaLaFecha + $neto)),
            'comisiones' => round($comisionesMonto)
        ], 201);
    }

    public function tickets()
    {
        $controlador = Route::getCurrentRoute()->getName();
        
        if(!strpos(Request::url(), '/api/')){
           
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }

            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Monitorear ticket") == true){
                return redirect()->route('sinpermiso');
            }
            // if(!$u->tienePermiso("Manejar transacciones") == true){
            //     return redirect()->route('principal');
            // }
            $usuario = Users::on(session("servidor"))->whereId(session('idUsuario'))->first();
            $bancas = Branches::on(session("servidor"))->whereStatus(1)->get()->toJson();
            $loterias = Lotteries::on(session("servidor"))->whereStatus(1)->get()->toJson();
            $sorteos = Draws::on(session("servidor"))->whereStatus(1)->get()->toJson();
            return view('monitoreo.tickets', compact('controlador', 'usuario', 'bancas', 'loterias', 'sorteos'));
        }else{
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
            // $usuario = Users::on(session("servidor"))->whereId(session('idUsuario'))->first();
            $bancas = Branches::on($datos["servidor"])->whereStatus(1)->get()->toJson();
            $loterias = Lotteries::on($datos["servidor"])->whereStatus(1)->get()->toJson();
            $sorteos = Draws::on($datos["servidor"])->whereStatus(1)->get()->toJson();
            return Response::json([
                'bancas' => $bancas,
                'loterias' => $loterias,
                'sorteos' => $sorteos,
                'errores' => 0
            ], 201);
        }

    }

    public function monitoreo()
    {
        // $datos = request()->validate([
        //     'datos.fecha' => 'required',
        //     'datos.idUsuario' => '',
        //     'datos.idBanca' => '',
        //     'datos.idLoteria' => '',
        //     'datos.idSorteo' => '',
        //     'datos.jugada' => '',
        //     'datos.layout' => ''
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
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        
       
        // if(!$usuario->tienePermiso("Monitorear ticket")){
          
        //     // return Response::json([
        //     //     'errores' => 1,
        //     //     'mensaje' => 'No tiene permisos para realizar esta accion'
        //     // ], 201);
        //      //Datos['layout'] es un parametro que me indicara si se esta accediendo 
        //     // desde la ventana principal o desde otra venta, si es de la ventana principal entonces
        //     // se verifica que la variable $datos['layout'] este definida y que su valor sea igual a 'Principal', 
        //     // de lo contrario no tendra permisos
        //     if(!isset($datos['layout'])){
        //         return Response::json([
        //             'errores' => 1,
        //             'mensaje' => 'No tiene permisos para realizar esta accion'
        //         ], 201);
        //     }
        //     else if($datos['layout'] != 'Principal'){
        //         return Response::json([
        //             'errores' => 1,
        //             'mensaje' => 'No tiene permisos para realizar esta accion'
        //         ], 201);
        //     }
        // }

        
    
        $fecha = getdate(strtotime($datos['fecha']));

        // \DB::connection("valentin")->select(" select s.id, s.total, s.pagado, s.status, s.idTicket, t.id, s.idUsuario, u.usuario, sum(sd.premio) as premio, (select cancellations.razon from cancellations where cancellations.idTicket = s.idTicket) as razon, (select JSON_OBJECT('id', users.id, 'usuario', users.usuario) from users where users.id = (select cancellations.idUsuario from cancellations where cancellations.idTicket = s.idTicket)) as usuarioCancelacion, (select cancellations.created_at from cancellations where cancellations.idTicket = s.idTicket) as fechaCancelacion from sales s  inner join salesdetails sd on s.id = sd.idVenta inner join users u on u.id = s.idUsuario inner join tickets t on t.id = s.idTicket where s.created_at between '{$fechaInicial}' and '{$fechaFinal}' {$consulta} group by s.id, s.total, s.pagado, s.status, s.idTicket, t.id, s.idUsuario, u.usuario, razon, fechaCancelacion, usuarioCancelacion ");
    
        // $monitoreo = Sales::join('tickets', 'sales.idTicket', '=', 'tickets.id')
        //             ->join('branches', 'sales.idBanca', '=', 'branches.id')
        //             ->join('users', 'sales.idUsuario', '=', 'users.id')
        //             //->join('salesdetails', 'sales.id', '=', 'salesdetails.idVenta')
        //             ->leftJoin('cancellations', 'sales.idTicket', '=', 'cancellations.idTicket')
                    
        //             ->selectRaw('
        //                 sales.*, tickets.codigoBarra, branches.codigo, 
        //                 users.usuario, 
        //                 (select sum(premio) from salesdetails where idVenta = sales.id) as premio,
        //                 cancellations.razon,
        //                 cancellations.created_at as fechaCancelacion
        //                 ')
        //             // ->groupBy('sales.id')
        //             //->sum('sales.id')
        //             ->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
        //             ->get();
        $consultaVentas = array();
        $consultaVentasDetalles = array();
        $consulta = "";
        if(isset($datos['idBanca'])){
            $consultaVentas['idBanca'] = $datos['idBanca'];
            $consulta .= " and s.idBanca = " . $datos['idBanca'];
        }
        if(isset($datos['idLoteria'])){
            $consultaVentasDetalles['idLoteria'] = $datos['idLoteria'];
            $consulta .= " and sd.idLoteria = " . $datos['idLoteria'];
        }
        if(isset($datos['idSorteo'])){
            $consultaVentasDetalles['idSorteo'] = $datos['idSorteo'];
            $consulta .= " and sd.idSorteo = " . $datos['idSorteo'];
        }
        if(isset($datos['jugada'])){
            $consultaVentasDetalles['jugada'] = $datos['jugada'];
        }

        // \DB::connection($datos["servidor"])
        //     ->select("
        //         select 
        //         s.id,
        //         s.total,
        //         s.pagado,
        //         s.status,
        //         s.idTicket,
        //         t.id,
        //         s.idUsuario,
        //         u.usuario,
        //         sum(sd.premio) as premio,
        //         (select cancellations.razon from cancellations where cancellations.idTicket = s.idTicket) as razon,
        //         (select users.usuario from users where users.id = (select cancellations.idUsuario from cancellations where cancellations.idTicket = s.idTicket)) as usuarioCancelacion
        //         (select cancellations.created_at from cancellations where cancellations.idTicket = s.idTicket) as fechaCancelacion,
        //         from sales s 
        //         inner join salesdetails sd on s.id = sd.idVenta
        //         inner join users u on u.id = s.idUsuario
        //         inner join tickets t on t.id = s.idTicket
        //         where s.created_at between '{$fechaInicial}' and '{$fechaFinal} {$consulta} group by s.id' 
        //     ");

        /************************** QUERY VIEJO ********************************/
    //     $idVentas = Sales::on($datos["servidor"])->select('id')
    //                 ->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
    //                 ->where($consultaVentas)
    //                 ->where('status', '!=', '5') //Eliminado
    //                 ->orderBy('id', 'desc')
    //                 ->get();


    //     $idVentas = collect($idVentas)->map(function($d){
    //         return $d['id'];
    //     });

    //     $ventasDetalles = Salesdetails::on($datos["servidor"])->whereIn('idVenta', $idVentas)
    //                 ->where($consultaVentasDetalles)
    //                 ->orderBy('id', 'desc')
    //                 ->get();
    //    // return $ventas;

    //    $idVentas = collect($ventasDetalles)->map(function($d){
    //         return $d['idVenta'];
    //     });

    //     $monitoreo = Sales::on($datos["servidor"])->whereIn('id', $idVentas)->orderBy('id', 'desc')->get();


        /********************************** QUERY NUEVO **********************************/
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        //https://www.sqlservercentral.com/forums/topic/order-by-based-on-condition
        //https://stackoverflow.com/questions/19529864/mysql-using-sum-and-case
        //https://stackoverflow.com/questions/6878090/mysql-sum-with-case-statement
        $monitoreo = \DB::connection($datos["servidor"])->select("select 
        s.id, s.total, s.pagado, s.status, s.idTicket, DATE_FORMAT(s.created_at, '%d/%m/%Y %h:%i %p') created_at, 
        t.codigoBarra, s.idUsuario, u.usuario, b.codigo, sum(sd.premio) as premio, 
        sum(IF(sd.pagado = 0, sd.premio, 0)) as montoAPagar, 
        sum(IF(sd.pagado = 1, sd.premio, 0)) as montoPagado, 
        (select cancellations.razon from cancellations where cancellations.idTicket = s.idTicket) as razon, 
        (select users.usuario from users where users.id = (select cancellations.idUsuario from cancellations where cancellations.idTicket = s.idTicket)) as usuarioCancelacion, 
        (select DATE_FORMAT(cancellations.created_at, '%d/%m/%Y %h:%i %p') from cancellations where cancellations.idTicket = s.idTicket) as fechaCancelacion 
        from sales s  inner join salesdetails sd on s.id = sd.idVenta 
        inner join users u on u.id = s.idUsuario 
        inner join tickets t on t.id = s.idTicket 
        inner join branches b on b.id = s.idBanca 
        where s.created_at between '{$fechaInicial}' and '{$fechaFinal}' and s.status != 5 {$consulta} group by s.id, s.total, s.pagado, s.status, s.idTicket, t.id, t.codigoBarra, s.idUsuario, u.usuario, b.codigo, razon, fechaCancelacion, usuarioCancelacion ");
        // (select sum(premio) from salesdetails where salesdetails.pagado = 0 and salesdetails.idVenta = s.id) montoAPagar,
    
        return Response::json([
            // 'monitoreo' => SalesSmallResource::collection($monitoreo),
            'monitoreo' => $monitoreo,
            'loterias' => Lotteries::on($datos["servidor"])->whereStatus(1)->get(),
            'bancas' => Branches::on($datos["servidor"])->whereStatus(1)->get(),
            'sorteos' => Draws::on($datos["servidor"])->get(),
            'caracteristicasGenerales' =>  Generals::on($datos["servidor"])->get(),
            // 'total_ventas' => Sales::on($datos["servidor"])->whereIn('id', $idVentas)->sum('total'),
            // 'total_jugadas' => Salesdetails::on($datos["servidor"])->whereIn('idVenta', $idVentas)->count('jugada'),
            'errores' => 0
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
        //
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
