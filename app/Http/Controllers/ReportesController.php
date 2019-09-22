<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 

use App\Sales;
use Request;
use Illuminate\Support\Facades\DB;


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
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class ReportesController extends Controller
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



    public function historico()
    {
        $controlador = Route::getCurrentRoute()->getName();
        
        if(!strpos(Request::url(), '/api/')){
            // return "<h1>Dentro reporte jugadas: $controlador </h1>";
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
            $bancas = Branches::whereStatus(1)->get();
            $bancas = collect($bancas)->map(function($d){
                $ventas = Helper::ventasPorBanca($d['id']);
                $descuentos = Helper::descuentosPorBanca($d['id']);
                $premios = Helper::premiosPorBanca($d['id']);
                $comisiones = Helper::comisionesPorBanca($d['id']);
                $tickets = Helper::ticketsPorBanca($d['id']);
                $ticketsPendientes = Helper::ticketsPendientesPorBanca($d['id']);
                $totalNeto = $ventas - ($descuentos + $premios + $comisiones);
                $balance = Helper::saldo($d['id'], 1);
                $caidaAcumulada = Helper::saldo($d['id'], 3);

                return ['id' => $d['id'], 'descripcion' => strtoupper ($d['descripcion']), 'codigo' => $d['codigo'], 'ventas' => $ventas, 
                    'descuentos' => $descuentos, 
                    'premios' => $premios, 
                    'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance + round($totalNeto, 2), 
                    'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes];
            });

           return view('reportes.historico', compact('controlador', 'bancas'));
        }

        
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.fechaDesde' => '',
            'datos.fechaHasta' => ''
        ])['datos'];
    
        $fecha = getdate();
  
        if($datos['fechaDesde'] != null && $datos['fechaHasta'] != null){
            $fecha = getdate(strtotime($datos['fechaDesde']));
            $fechaF = getdate(strtotime($datos['fechaHasta']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
    
    
        
    
        $bancas = Branches::whereStatus(1)->get();
        $bancas = collect($bancas)->map(function($d) use($fechaInicial, $fechaFinal){
            $ventas = Helper::ventasPorBanca($d['id'], $fechaInicial, $fechaFinal);
            $descuentos = Helper::descuentosPorBanca($d['id'], $fechaInicial, $fechaFinal);
            $premios = Helper::premiosPorBanca($d['id'], $fechaInicial, $fechaFinal);
            $comisiones = Helper::comisionesPorBanca($d['id'], $fechaInicial, $fechaFinal);
            $tickets = Helper::ticketsPorBanca($d['id'], $fechaInicial, $fechaFinal);
            $ticketsPendientes = Helper::ticketsPendientesPorBanca($d['id'], $fechaInicial, $fechaFinal);
            $totalNeto = $ventas - ($descuentos + $premios + $comisiones);
            $balance = Helper::saldo($d['id'], 1);
            $caidaAcumulada = Helper::saldo($d['id'], 3);

            return ['id' => $d['id'], 'descripcion' => strtoupper ($d['descripcion']), 'codigo' => $d['codigo'], 'ventas' => $ventas, 
                'descuentos' => $descuentos, 
                'premios' => $premios, 
                'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance + round($totalNeto, 2), 
                'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes];
        });
        
      
        
    
        return Response::json([
            'bancas' => $bancas,
            'fechaInicial' => $fechaInicial,
            'fechaFinal' => $fechaFinal,
            'a' => $datos['fechaDesde']
        ], 201);
    }


    public function ventasporfecha()
    {
        $controlador = Route::getCurrentRoute()->getName();
        
        if(!strpos(Request::url(), '/api/')){
            // return "<h1>Dentro reporte jugadas: $controlador </h1>";
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
            
            $bancas = Branches::whereStatus(1)->get();
            $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
                    sum(sales.subTotal) subTotal, 
                    sum(sales.total) total, 
                    sum(sales.premios) premios, 
                    sum(descuentoMonto)  as descuentoMonto,
                    sum(salesdetails.comision) as comisiones'))
            ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('sales.status', [0,5])
            ->groupBy('fecha')
            //->orderBy('created_at', 'asc')
            ->get();

       
            $ventas = collect($ventas)->map(function($d){
              
                $totalNeto = $d['total'] - ($d['descuentoMonto'] + $d['premios']  + $d['comisiones']);
    
                return ['fecha' => $d['fecha'], 'ventas' => $d['total'], 
                    'descuentos' => $d['descuentoMonto'], 
                    'premios' => $d['premios'], 
                    'comisiones' => $d['comisiones'], 
                    'totalNeto' => round($totalNeto, 2)];
            });


           return view('reportes.ventasporfecha', compact('controlador', 'bancas', 'ventas'));
        }

        
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.bancas' => '',
            'datos.fechaDesde' => '',
            'datos.fechaHasta' => ''
        ])['datos'];
    
        $fecha = getdate();
  
        if($datos['fechaDesde'] != null && $datos['fechaHasta'] != null){
            $fecha = getdate(strtotime($datos['fechaDesde']));
            $fechaF = getdate(strtotime($datos['fechaHasta']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }

        $falso = false;

        if(isset($datos['bancas']) == true){
            $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            sum(sales.subTotal) subTotal, 
            sum(sales.total) total, 
            sum(sales.premios) premios, 
            sum(descuentoMonto)  as descuentoMonto,
            sum(salesdetails.comision) as comisiones'))
            ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('sales.status', [0,5])
            ->whereIn('sales.idBanca', $datos['bancas'])
            ->groupBy('fecha')
            //->orderBy('created_at', 'asc')
            ->get();
        }else{
            $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            sum(sales.subTotal) subTotal, 
            sum(sales.total) total, 
            sum(sales.premios) premios, 
            sum(descuentoMonto)  as descuentoMonto,
            sum(salesdetails.comision) as comisiones'))
            ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('sales.status', [0,5])
            ->groupBy('fecha')
            //->orderBy('created_at', 'asc')
            ->get();

            $false = true;
        }

        
      
        $ventas = collect($ventas)->map(function($d){
              
            $totalNeto = $d['total'] - ($d['descuentoMonto'] + $d['premios']  + $d['comisiones']);

            return ['fecha' => $d['fecha'], 'ventas' => $d['total'], 
                'descuentos' => $d['descuentoMonto'], 
                'premios' => $d['premios'], 
                'comisiones' => $d['comisiones'], 
                'totalNeto' => round($totalNeto, 2)];
        });
        
    
        return Response::json([
            'ventas' => $ventas,
            'fechaInicial' => $fechaInicial,
            'fechaFinal' => $fechaFinal,
            'a' => $falso
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
        

        
    

        $usuario = Users::whereId($datos['idUsuario'])->first();
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

    public function monitoreo()
    {
        $datos = request()->validate([
            'datos.fecha' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idBanca' => '',
            'datos.layout' => ''
        ])['datos'];
    
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Monitorear ticket")){
            // return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'No tiene permisos para realizar esta accion'
            // ], 201);
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
    
        $fecha = getdate(strtotime($datos['fecha']));
    
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
    
    
        $monitoreo = Sales::whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                    ->where('idBanca', $datos['idBanca'])
                    ->where('status', '!=', '5')
                    ->orderBy('id', 'desc')
                    ->get();
    
       // return $ventas;
        
    
        return Response::json([
            'monitoreo' => SalesResource::collection($monitoreo),
            'loterias' => Lotteries::whereStatus(1)->get(),
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::sum('total'),
            'total_jugadas' => Salesdetails::count('jugada'),
            'errores' => 0
        ], 201);
    }

    public function ticketsPendientesDePago()
    {
        $datos = request()->validate([
            'datos.fecha' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idBanca' => '',
            'datos.layout' => ''
        ])['datos'];

        if($datos['idBanca'] == 0){
            $bancas = Branches::whereStatus(1)->get();
            $datos['idBanca'] = collect($bancas)->map(function($b){
                return $b['id'];
            });
        }else{
            $datos['idBanca'] = [$datos['idBanca']];
        }

        if($datos['fecha'] == "Todas las fechas"){
            $ticketsPendientesDePago = Sales::
            select('sales.id')
            ->join('salesdetails', 'salesdetails.idVenta', 'sales.id')
            ->whereNotIn('sales.status', [0, 5])
            ->where(['salesdetails.status' => 1, 'salesdetails.pagado' => 0])
            ->where('salesdetails.premio', '>', 0)
            ->groupBy('sales.id')
            ->whereIn('sales.idBanca', $datos['idBanca'])
            ->get();
        }else{
            $fecha = getdate(strtotime($datos['fecha']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

           

            $ticketsPendientesDePago = Sales::select('sales.id')
            ->join('salesdetails', 'salesdetails.idVenta', 'sales.id')
            ->whereNotIn('sales.status', [0, 5])
            ->where(['salesdetails.status' => 1, 'salesdetails.pagado' => 0])
            ->where('salesdetails.premio', '>', 0)
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->groupBy('sales.id')
            ->whereIn('sales.idBanca', $datos['idBanca'])
            ->get();
        }



        $ticketsPendientesDePago = collect($ticketsPendientesDePago)->map(function($t){
            return $t['id'];
        });

        $ticketsPendientesDePago = Sales::whereIn('id', $ticketsPendientesDePago)->get();
        return Response::json([
            'bancas' => Branches::whereStatus(1)->get(),
            'ticketsPendientesDePago' => SalesResource::collection($ticketsPendientesDePago)
        ], 201);
        
    }



    public function ticketsPendientesDePagoIndex()
    {
        $datos = request()->validate([
            'datos.fecha' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idBanca' => '',
            'datos.layout' => ''
        ])['datos'];

        if($datos['idBanca'] == 0){
            $bancas = Branches::whereStatus(1)->get();
            $datos['idBanca'] = collect($bancas)->map(function($b){
                return $b['id'];
            });
        }else{
            $datos['idBanca'] = [$datos['idBanca']];
        }

        if($datos['fecha'] == "Todas las fechas"){
            $ticketsPendientesDePago = Sales::
            select('sales.id')
            ->join('salesdetails', 'salesdetails.idVenta', 'sales.id')
            ->whereNotIn('sales.status', [0, 5])
            ->where(['salesdetails.status' => 1, 'salesdetails.pagado' => 0])
            ->where('salesdetails.premio', '>', 0)
            ->groupBy('sales.id')
            ->whereIn('sales.idBanca', $datos['idBanca'])
            ->get();
        }else{
            $fecha = getdate(strtotime($datos['fecha']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

           

            $ticketsPendientesDePago = Sales::select('sales.id')
            ->join('salesdetails', 'salesdetails.idVenta', 'sales.id')
            ->whereNotIn('sales.status', [0, 5])
            ->where(['salesdetails.status' => 1, 'salesdetails.pagado' => 0])
            ->where('salesdetails.premio', '>', 0)
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->groupBy('sales.id')
            ->whereIn('sales.idBanca', $datos['idBanca'])
            ->get();
        }



        $ticketsPendientesDePago = collect($ticketsPendientesDePago)->map(function($t){
            return $t['id'];
        });

        $ticketsPendientesDePago = Sales::whereIn('id', $ticketsPendientesDePago)->get();
        return Response::json([
            'bancas' => Branches::whereStatus(1)->get(),
            'ticketsPendientesDePago' => SalesResource::collection($ticketsPendientesDePago)
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
