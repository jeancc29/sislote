<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;

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
use App\Coins;
use App\Classes\Helper;
use App\Classes\TicketToHtmlClass;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\SalesdetailsResource;
use App\Http\Resources\SalesImageResource;
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

        
        // $datos = request()->validate([
        //     'datos.idLoteria' => '',
        //     'datos.fecha' => '',
        //     'datos.bancas' => ''
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
            ], 201);
        }
    
        $fecha = getdate(strtotime($datos['fecha']));
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = collect();
    
    
        if($datos['idLoteria'] != null && $datos['fecha'] != null && $datos['bancas'] != null){
            //Obtenemos todos los id bancas
            $idBancas = collect($datos['bancas'])->map(function($id){
                return $id['id'];
            });


            $idVentas = Sales::on($datos["servidor"])->select('id')
                ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereNotIn('status', [0,5])
                ->whereIn('idBanca', $idBancas)
                ->get();
    
            $idVentas = collect($idVentas)->map(function($id){
                return $id->id;
            });
        
            $jugadas = collect();
            $sorteos = Draws::on($datos["servidor"])->get();
            foreach ($sorteos as $sorteo) {
                // if($jugadas == null)
                //     $jugadas = Salesdetails::
                //     on($datos["servidor"])
                //     ->where('idLoteria', $datos['idLoteria'])
                //     ->whereIn('idVenta', $idVentas)
                //     ->where('idSorteo', $sorteo->id)
                //     ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                //     ->orderBy("monto", "desc")
                //     ->limit(20)
                //     ->get();
                // else{
                //     $jugadasPorSorteo = Salesdetails::
                //     on($datos["servidor"])
                //     ->where('idLoteria', $datos['idLoteria'])
                //     ->whereIn('idVenta', $idVentas)
                //     ->where('idSorteo', $sorteo->id)
                //     ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                //     ->orderBy("monto", "desc")
                //     ->limit(20)
                //     ->get();

                    

                //     if($jugadasPorSorteo->count() > 0){
                //         $jugadas->merge($jugadasPorSorteo);
                //         return Response::json([
                //             "jugadasPorSorteo" => $jugadasPorSorteo,
                //             "jugadas" => $jugadas,
                //         ]);
                //     }
                        
                // }

                $jugadasPorSorteo = Salesdetails::
                    on($datos["servidor"])
                    ->where('idLoteria', $datos['idLoteria'])
                    ->whereIn('idVenta', $idVentas)
                    ->where('idSorteo', $sorteo->id)
                    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                    ->orderBy("monto", "desc")
                    ->limit(20)
                    ->get();

                if($jugadasPorSorteo->count() > 0){
                    $jugadasPorSorteo = SalesdetailsResource::collection($jugadasPorSorteo);
                    $jugadas = $jugadas->merge($jugadasPorSorteo);
                    // return Response::json([
                    //     "jugadasPorSorteo" => $jugadasPorSorteo,
                    //     "jugadas" => $jugadas,
                    // ]);
                }
            }
            
        }   
    
        
    
        
      
        
    
        return Response::json([
            'jugadas' => $jugadas,
            'errores' => $errores,
            'mensaje' => $mensaje,
            'loterias' => Lotteries::on($datos["servidor"])->whereStatus(1)->get(),
            // 'bancas' => Branches::whereStatus(1)->get()
        ], 201);
    }



    public function historico()
    {
        $controlador = Route::getCurrentRoute()->getName();
        $fecha = getdate();
        $fechaActualCarbon = Carbon::now();
       

        
        if(!strpos(Request::url(), '/api/')){
            // return "<h1>Dentro reporte jugadas: $controlador </h1>";
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }

            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Ver historico ventas") == true){
                return redirect()->route('sinpermiso');
            }

            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
            $fechaFinalSinHora = Carbon::now();
            $fechaFinalSinHora = $fechaFinalSinHora->toDateString();
            // $datos = request()->validate([
            //     'token' => ''
            // ]);

            // try {
            //     $datos = \Helper::jwtDecode($datos['token']);
            // } catch (\Throwable $th) {
            //     //throw $th;
            //     return Response::json([
            //         'errores' => 1,
            //         'mensaje' => 'Token incorrecto',
            //     ], 201);
            // }
           
            // $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            // $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
            // $fechaFinalSinHora = Carbon::now();
            // $fechaFinalSinHora = $fechaFinalSinHora->toDateString();
            
            // $bancas = Branches::whereStatus(1)->get();
            // $bancas = collect($bancas)->map(function($d) use($fechaActualCarbon, $fechaFinalSinHora, $fechaInicial, $fechaFinal){
            //     $ventas = Helper::ventasPorBanca($d['id']);
            //     $descuentos = Helper::descuentosPorBanca($d['id']);
            //     $premios = Helper::premiosPorBanca($d['id']);
            //     $comisiones = Helper::comisionesPorBanca($d['id']);
            //     $tickets = Helper::ticketsPorBanca($d['id']);
            //     $ticketsPendientes = Helper::ticketsPendientesPorBanca($d['id']);
            //     $totalNeto = $ventas - ($descuentos + $premios + $comisiones);
            //     $balance = Helper::saldo($d['id'], 1);
            //     $caidaAcumulada = Helper::saldo($d['id'], 3);

            //     // $sumarVentasNetas = false;
                
            //     // if($fechaFinalSinHora < $fechaActualCarbon->toDateString()){
            //     //     $sumarVentasNetas = false;
            //     // }
            //     // else if($fechaFinalSinHora == $fechaActualCarbon->toDateString() && ($fechaActualCarbon->hour == 23 && $fechaActualCarbon->minute > 54) == false){
            //     //     $sumarVentasNetas = true;
            //     // }
            //     //'balanceActual' => ($sumarVentasNetas == true) ? round(($balance + $totalNeto), 2) : $balance

            //     $pendientes = Sales::
            //         whereBetween('created_at', array($fechaInicial, $fechaFinal))
            //         ->whereStatus(1)
            //         ->where('idBanca', $d['id'])
            //         ->count();
            
            //     $ganadores = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            //                 ->whereStatus('2')
            //                 ->where('idBanca', $d['id'])
            //                 ->count();
            //     // $premios = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            //     //     ->whereStatus('2')
            //     //     ->where('idBanca', $d['id'])
            //     //     ->sum("premios");
                            
                        
            //     $perdedores = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            //                 ->whereStatus('3')
            //                 ->where('idBanca', $d['id'])
            //                 ->count();

            //     return ['id' => $d['id'], 'descripcion' => strtoupper ($d['descripcion']), 'codigo' => $d['codigo'], 
            //         'idMoneda' => $d['idMoneda'], 'ventas' => $ventas, 
            //         'descuentos' => $descuentos, 
            //         'premios' => $premios, 
            //         'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance, 
            //         'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes,
            //         'balanceActual' => round(($balance + $totalNeto), 2),
            //         'pendientes' => $pendientes,
            //         'ganadores' => $ganadores,
            //         'perdedores' => $perdedores
            //     ];
            // });

            // $monedas = Coins::orderBy('pordefecto', 1)->get();

           return view('reportes.historico', compact('controlador'));
        }

        
        
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.fechaDesde' => '',
        //     'datos.fechaHasta' => ''
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
            ], 201);
        }
    
        $fechaFinalSinHora = new Carbon($datos['fechaHasta']);
        $fechaFinalSinHora = $fechaFinalSinHora->toDateString();
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
    
        // $loterias = Lotteries::
        //                     selectRaw('
        //                         id, 
        //                         descripcion, 
        //                         (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and  s.idBanca = ? and s.created_at between ? and ?) as ventas,
        //                         (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as premios,
        //                         (select substring(numeroGanador, 1, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as primera,
        //                         (select substring(numeroGanador, 3, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as segunda,
        //                         (select substring(numeroGanador, 5, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as tercera
        //                         ', [$datos["idBanca"], $fechaInicial, $fechaFinal, //Parametros para ventas
        //                             $datos["idBanca"], $fechaInicial, $fechaFinal, //Parametros para premios
        //                             $fechaInicial, $fechaFinal, //Parametros primera
        //                             $fechaInicial, $fechaFinal, //Parametros segunda
        //                             $fechaInicial, $fechaFinal //Parametros tercera
        //                             ])
        //                     ->where('lotteries.status', '=', '1')
        //                     ->get();
        
        //Query para optimizar
        //Salesdetails::on("valentin")->selectRaw("sum(salesdetails.comision), sum(salesdetails.monto), sum(s.descuentoMonto), sum(salesdetails.premio), count(s.idTicket)")->join("sales as s", "s.id", "salesdetails.idVenta")->groupBy("s.idBanca")->get();
        // Salesdetails::on("valentin")->where("jugada", "01")
        
        $bancas = Branches::on($datos['servidor'])->whereStatus(1)->get();
        $bancas = collect($bancas)->map(function($d) use($fechaInicial, $fechaFinal, $fechaActualCarbon, $fechaFinalSinHora, $datos){
            $ventas = Helper::ventasPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
            $descuentos = Helper::descuentosPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
            $premios = Helper::premiosPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
            $comisiones = Helper::comisionesPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
            $tickets = Helper::ticketsPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
            $ticketsPendientes = Helper::ticketsPendientesPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
            $totalNeto = $ventas - ($descuentos + $premios + $comisiones);
            $balance = Helper::saldoPorFecha($datos["servidor"], $d['id'], 1, $fechaFinalSinHora);
            $caidaAcumulada = Helper::saldoPorFecha($datos["servidor"], $d['id'], 3, $fechaFinalSinHora);

            // $sumarVentasNetas = false;

            // if($fechaFinalSinHora < $fechaActualCarbon->toDateString()){
            //     $sumarVentasNetas = false;
            // }
            // else if($fechaFinalSinHora == $fechaActualCarbon->toDateString() && ($fechaActualCarbon->hour == 23 && $fechaActualCarbon->minute > 54) == false){
            //     $sumarVentasNetas = true;
            // }
            //'balanceActual' => ($sumarVentasNetas == true) ? round(($balance + $totalNeto), 2) : $balance,

            $pendientes = Sales::on($datos['servidor'])
                    ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    ->whereStatus(1)
                    ->where('idBanca', $d['id'])
                    ->count();
            
                $ganadores = Sales::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereStatus('2')
                            ->where('idBanca', $d['id'])
                            ->count();
                // $premios = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                //     ->whereStatus('2')
                //     ->where('idBanca', $d['id'])
                //     ->sum("premios");
                            
                        
                $perdedores = Sales::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereStatus('3')
                            ->where('idBanca', $d['id'])
                            ->count();

            return ['id' => $d['id'], 'descripcion' => strtoupper ($d['descripcion']), 
                'idMoneda' => $d['idMoneda'],
                'codigo' => $d['codigo'], 'ventas' => $ventas, 
                'descuentos' => $descuentos, 
                'premios' => $premios, 
                'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance, 
                'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes,
                'balanceActual' => round(($balance + $totalNeto), 2),
                'pendientes' => $pendientes,
                'ganadores' => $ganadores,
                'perdedores' => $perdedores,
                'monedas' => Coins::on($datos['servidor'])->orderBy('pordefecto', 1)->get()
            ];
        });
        
      
        
    
        return Response::json([
            'monedas' => Coins::on($datos['servidor'])->orderBy('pordefecto', 1)->get(),
            'bancas' => $bancas,
            'fechaInicial' => $fechaInicial,
            'fechaFinal' => $fechaFinal,
            'a' => $datos['fechaDesde'], 
            'sumar' => ($fechaFinalSinHora == $fechaActualCarbon->toDateString() && ($fechaActualCarbon->hour == 23 && $fechaActualCarbon->minute > 54) == false),
            'sin' => $fechaFinalSinHora == $fechaActualCarbon->toDateString(),
            'af' =>$fechaFinalSinHora,
            'af1' =>$fechaActualCarbon->toDateString(),
        ], 201);
    }


    public function ventasporfecha()
    {
        $controlador = Route::getCurrentRoute()->getName();
        
        if(!strpos(Request::url(), '/api/')){
            // return "<h1>Dentro reporte jugadas: $controlador </h1>";
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }

            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Ver ventas") == true){
                return redirect()->route('sinpermiso');
            }

            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

            $idMonedaPordefecto = Coins::on(session("servidor"))->wherePordefecto(1)->first()->id;
            $idBancas = Branches::on(session("servidor"))->where(['status' => 1, 'idMoneda' => $idMonedaPordefecto])->get();
            $idBancas = collect($idBancas)->map(function($b){
                return $b->id;
            });
            // $fecha = getdate();
            // $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            // $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

            // $idMonedaPordefecto = Coins::wherePordefecto(1)->first()->id;
            // $idBancas = Branches::where(['status' => 1, 'idMoneda' => $idMonedaPordefecto])->get();
            // $idBancas = collect($idBancas)->map(function($b){
            //     return $b->id;
            // });

            // $idVentas = Sales::select(DB::raw('sales.id'))
            // ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            // ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            // ->whereNotIn('sales.status', [0,5])
            // ->whereIn('sales.idBanca', $idBancas)
            // // ->groupBy('fecha')
            // //->orderBy('created_at', 'asc')
            // ->get();
            
            // $idVentas = collect($idVentas)->map(function($v){
            //     return $v['id'];
            // });

            // $fechasVentas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            // sum(sales.subTotal) subTotal, 
            // sum(sales.total) total, 
            // sum(sales.premios) premios, 
            // sum(descuentoMonto)  as descuentoMonto,
            // sum(salesdetails.comision) as comisiones'))
            // ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            // ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            // ->whereNotIn('sales.status', [0,5])
            // ->groupBy('fecha')
            // //->orderBy('created_at', 'asc')
            // ->get();

            // $ventas = collect($fechasVentas)->map(function($f) use($idVentas){
            //     $comision = Salesdetails::whereRaw('date(created_at) = date(?)', [$f['fecha']])->whereIn('idVenta', $idVentas)->sum('comision');
                
            //     $ventas = Sales::select(DB::raw('
            //     sum(sales.subTotal) subTotal, 
            //     sum(sales.total) total, 
            //     sum(sales.premios) premios, 
            //     sum(descuentoMonto)  as descuentoMonto'))
            //     ->whereRaw('date(sales.created_at) = ? ', [$f['fecha']])
            //     ->whereIn('sales.id', $idVentas)
            //     ->get();

            //     // $total = Sales::
            //     // whereRaw('date(sales.created_at) = ? ', [$f['fecha']])
            //     // ->whereIn('sales.id', $idVentas)
            //     // ->sum('total');

            //     return ['fecha' => $f['fecha'], 'total' => $ventas[0]->total, 'premios' => $ventas[0]->premios, 'descuentoMonto' => $ventas[0]->descuentoMonto, 'comisiones' => $comision, ];
            // });
       
            // $ventas = collect($ventas)->map(function($d){
              
            //     $totalNeto = $d['total'] - ($d['descuentoMonto'] + $d['premios']  + $d['comisiones']);
    
            //     return ['fecha' => $d['fecha'], 'ventas' => $d['total'], 
            //         'descuentos' => $d['descuentoMonto'], 
            //         'premios' => $d['premios'], 
            //         'comisiones' => $d['comisiones'], 
            //         'totalNeto' => round($totalNeto, 2)];
            // });

            // $monedas = Coins::orderBy('pordefecto', 1)->get();
            // $bancas = Branches::select('id', 'descripcion', 'idMoneda')->whereStatus(1)->get();

           return view('reportes.ventasporfecha', compact('controlador'));
        }

        
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.idMoneda' => 'required',
        //     'datos.bancas' => '',
        //     'datos.fechaDesde' => '',
        //     'datos.fechaHasta' => ''
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }
    
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
            $datos['bancas'] = collect($datos['bancas'])->map(function($b){
                return $b['id'];
            });
            // $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            // sum(sales.subTotal) subTotal, 
            // sum(sales.total) total, 
            // sum(sales.premios) premios, 
            // sum(descuentoMonto)  as descuentoMonto,
            // sum(salesdetails.comision) as comisiones'))
            // ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            // ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            // ->whereNotIn('sales.status', [0,5])
            // ->whereIn('sales.idBanca', $datos['bancas'])
            // ->groupBy('fecha', 'sales.idBanca')
            // //->orderBy('created_at', 'asc')
            // ->distinct('idBanca')
            // ->get();

            // $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            // sum(sales.subTotal) subTotal, 
            // sum(sales.total) total, 
            // sum(sales.premios) premios, 
            // sum(descuentoMonto)  as descuentoMonto,
            // sum(salesdetails.comision) as comisiones'))
            // ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            // ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            // ->whereNotIn('sales.status', [0,5])
            // ->whereIn('sales.idBanca', $datos['bancas'])
            // ->groupBy('fecha')
            // //->orderBy('created_at', 'asc')
            // ->get();

            $idVentas = Sales::on($datos["servidor"])->select(DB::connection($datos["servidor"])->raw('sales.id'))
            ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('sales.status', [0,5])
            ->whereIn('sales.idBanca', $datos['bancas'])
            // ->groupBy('fecha', 'sales.idBanca')
            //->orderBy('created_at', 'asc')
            
            ->get();


            

            $idVentas = collect($idVentas)->map(function($v){
                return $v['id'];
            });


            

            $fechasVentas = Sales::on($datos["servidor"])->select(DB::connection($datos["servidor"])->raw('DATE(sales.created_at) as fecha, 
            sum(sales.subTotal) subTotal, 
            sum(sales.total) total, 
            sum(sales.premios) premios, 
            sum(descuentoMonto)  as descuentoMonto,
            sum(salesdetails.comision) as comisiones'))
            ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('sales.status', [0,5])
            ->whereIn('sales.idBanca', $datos['bancas'])
            // ->groupBy('fecha', 'sales.idBanca')
            ->groupBy('fecha')
            //->orderBy('created_at', 'asc')
            ->get();

            $ventas = collect($fechasVentas)->map(function($f) use($idVentas, $datos){
                $comision = Salesdetails::on($datos["servidor"])->whereRaw('date(created_at) = date(?)', [$f['fecha']])->whereIn('idVenta', $idVentas)->sum('comision');
                
                $ventas = Sales::on($datos["servidor"])->select(DB::connection($datos["servidor"])->raw('
                sum(sales.subTotal) subTotal, 
                sum(sales.total) total, 
                sum(sales.premios) premios, 
                sum(descuentoMonto)  as descuentoMonto'))
                ->whereRaw('date(sales.created_at) = ? ', [$f['fecha']])
                ->whereIn('sales.id', $idVentas)
                ->get();

                // $total = Sales::
                // whereRaw('date(sales.created_at) = ? ', [$f['fecha']])
                // ->whereIn('sales.id', $idVentas)
                // ->sum('total');

                return ['fecha' => $f['fecha'], 'total' => $ventas[0]->total, 'premios' => $ventas[0]->premios, 'descuentoMonto' => $ventas[0]->descuentoMonto, 'comisiones' => $comision, ];
            });

            
        }else{
            // $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            // sum(sales.subTotal) subTotal, 
            // sum(sales.total) total, 
            // sum(sales.premios) premios, 
            // sum(descuentoMonto)  as descuentoMonto,
            // sum(salesdetails.comision) as comisiones'))
            // ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            // ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            // ->whereNotIn('sales.status', [0,5])
            // ->groupBy('fecha')
            // //->orderBy('created_at', 'asc')
            // ->get();

            // $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
            // sum(sales.subTotal) subTotal, 
            // sum(sales.total) total, 
            // sum(sales.premios) premios, 
            // sum(descuentoMonto)  as descuentoMonto,
            // (select sum(salesdetails.comision) from salesdetails where idVenta = sales.id) as comisiones'))
            // ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            // ->whereNotIn('sales.status', [0,5])
            // ->groupBy('fecha')
            // //->orderBy('created_at', 'asc')
            // ->get();

            $moneda = Coins::on($datos["servidor"])->whereId($datos["idMoneda"])->first();
            if($moneda == null)
                $moneda = Coins::on($datos["servidor"])->where("pordefecto", 1)->first();

            $idBancas = Branches::on($datos["servidor"])->where(['status' => 1, 'idMoneda' => $moneda->id])->get();
            $idBancas = collect($idBancas)->map(function($b){
                return $b->id;
            });

            $idVentas = Sales::on($datos["servidor"])->select(DB::connection($datos["servidor"])->raw('sales.id'))
            ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('sales.status', [0,5])
            ->whereIn('sales.idBanca', $idBancas)
            // ->groupBy('fecha')
            //->orderBy('created_at', 'asc')
            ->get();

            
            $idVentas = collect($idVentas)->map(function($v){
                return $v['id'];
            });


            

            $fechasVentas = Sales::on($datos["servidor"])->select(DB::connection($datos["servidor"])->raw('DATE(sales.created_at) as fecha, 
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

            $ventas = collect($fechasVentas)->map(function($f) use($idVentas, $datos){
                $comision = Salesdetails::on($datos["servidor"])->whereRaw('date(created_at) = date(?)', [$f['fecha']])->whereIn('idVenta', $idVentas)->sum('comision');
                
                $ventas = Sales::on($datos["servidor"])->select(DB::connection($datos["servidor"])->raw('
                sum(sales.subTotal) subTotal, 
                sum(sales.total) total, 
                sum(sales.premios) premios, 
                sum(descuentoMonto)  as descuentoMonto'))
                ->whereRaw('date(sales.created_at) = ? ', [$f['fecha']])
                ->whereIn('sales.id', $idVentas)
                ->get();

                // $total = Sales::
                // whereRaw('date(sales.created_at) = ? ', [$f['fecha']])
                // ->whereIn('sales.id', $idVentas)
                // ->sum('total');

                return ['fecha' => $f['fecha'], 'total' => $ventas[0]->total, 'premios' => $ventas[0]->premios, 'descuentoMonto' => $ventas[0]->descuentoMonto, 'comisiones' => $comision, ];
            });

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
            'a' => $falso,
            'bancas' => $datos['bancas'],
            'monedas' => Coins::on($datos["servidor"])->orderBy('pordefecto', 1)->get(),
            'bancas' => Branches::on($datos["servidor"])->select('id', 'descripcion', 'idMoneda')->whereStatus(1)->get()
        ], 201);
    }

    public function ventas()
    {
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => '',
        //     'datos.fecha' => 'required',
        //     'datos.fechaFinal' => '',
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
            ], 201);
        }
    
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
        

        
    

        $usuario = Users::on($datos['servidor'])->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Ver ventas")){
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
            $datos['idBanca'] = Branches::on($datos['servidor'])->where(['id' => $datos['idBanca'], 'status' => 1])->first();
            if($datos['idBanca'] != null)
                $datos['idBanca'] = $datos['idBanca']->id;
        }else{
            $datos['idBanca'] = Branches::on($datos['servidor'])->where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first()->id;
        }
    
        //$fecha = getdate(strtotime($datos['fecha']));
        
    
        $pendientes = Sales::
            on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereStatus(1)
            ->where('idBanca', $datos['idBanca'])
            ->count();
    
        $ganadores = Sales::on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereStatus('2')
            ->where('idBanca', $datos['idBanca'])
            ->count();
        $premios = Sales::on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereStatus('2')
            ->where('idBanca', $datos['idBanca'])
            ->sum("premios");
                    
                
        $perdedores = Sales::on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereStatus('3')
            ->where('idBanca', $datos['idBanca'])
            ->count();
    
        $total = Sales::on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereIn('status', array(1,2,3))
            ->where('idBanca', $datos['idBanca'])
            ->count();
    
        $ventas = Sales::on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $datos['idBanca'])
            ->sum('total');
    
        //AQUI COMIENSA LAS COMISIONES

        //AQUI TERMINAN LAS COMISIONES

        $descuentos = Sales::on($datos['servidor'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $datos['idBanca'])
            ->sum('descuentoMonto');
    
                    // $premios = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                    // ->whereIn('status', array(1,2))
                    // ->where('idBanca', $datos['idBanca'])
                    // ->sum('premios');

        $idVentasPremios = Sales::on($datos['servidor'])
            ->select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereIn('status', array(1,2))
            ->where('idBanca', $datos['idBanca'])
            ->get();

        $idVentasPremios = collect($idVentasPremios)->map(function($v){
            return $v['id'];
        });

        $premios = Salesdetails::on($datos['servidor'])->whereIn('idVenta', $idVentasPremios)->sum('premio');
    
        //Obtener loterias con el monto total jugado y con los premios totales
    
                    
    
                    
        $loterias = Lotteries::on($datos['servidor'])
            ->selectRaw('
                id, 
                descripcion, 
                (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as ventas,
                (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as premios,
                (select substring(numeroGanador, 1, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as primera,
                (select substring(numeroGanador, 3, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as segunda,
                (select substring(numeroGanador, 5, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as tercera,
                (select pick3 from awards where idLoteria = lotteries.id and created_at between ? and ?) as pick3,
                (select pick4 from awards where idLoteria = lotteries.id and created_at between ? and ?) as pick4
                ', [$datos['idBanca'], $fechaInicial, $fechaFinal, //Parametros para ventas
                    $datos['idBanca'], $fechaInicial, $fechaFinal, //Parametros para premios
                    $fechaInicial, $fechaFinal, //Parametros primera
                    $fechaInicial, $fechaFinal, //Parametros segunda
                    $fechaInicial, $fechaFinal, //Parametros tercera
                    $fechaInicial, $fechaFinal, //Parametros pick3
                    $fechaInicial, $fechaFinal //Parametros pick4
                    ])
            ->where('lotteries.status', '=', '1')
            ->get();

                    $loterias = collect($loterias)->map(function($d) use($datos, $fechaInicial, $fechaFinal){
                        $datosComisiones = Commissions::on($datos['servidor'])->where(['idBanca' => $datos['idBanca'], 'idLoteria' => $d['id']])->first();
                        $comisionesMonto = 0;
                        $idVentasDeEstaBanca = Sales::on($datos['servidor'])->select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $datos['idBanca'])->whereNotIn('status', [0,5])->get();
                        $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
                            return $id->id;
                        });
                        if($datosComisiones != null){
                            if($datosComisiones['directo'] > 0){
                                $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Directo')->first();
                                if($sorteo != null){
                                    //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                    $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                        ->whereIn('idVenta', $idVentasDeEstaBanca)
                                        ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                        ->sum('monto');
                                }
                            }
            
                            if($datosComisiones['pale'] > 0){
                                $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Pale')->first();
                                if($sorteo != null){
                                    //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                    $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                        ->whereIn('idVenta', $idVentasDeEstaBanca)
                                        ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                        ->sum('monto');
                                }
                            }
            
                            if($datosComisiones['tripleta'] > 0){
                                $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Tripleta')->first();
                                if($sorteo != null){
                                    //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                    $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                        ->whereIn('idVenta', $idVentasDeEstaBanca)
                                        ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                        ->sum('monto');
                                }
                            }
            
                            if($datosComisiones['superPale'] > 0){
                                $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Super pale')->first();
                                if($sorteo != null){
                                    //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                    $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                        ->whereIn('idVenta', $idVentasDeEstaBanca)
                                        ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                        ->sum('monto');
                                }
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
                        return ['id' => $d->id, 'descripcion' => $d->descripcion, 'comisiones' => $comisionesMonto, 'ventas' => $d->ventas, 'premios' => $d->premios, 'primera' => $d->primera, 'segunda' => $d->segunda, 'tercera' => $d->tercera,'pick3' => $d->pick3, 'pick4' => $d->pick4, 'neto' => ($d->ventas) - ((int)$d->premios + $comisionesMonto)];
                    });
    
      
        $ticketsGanadores = Sales::on($datos['servidor'])
            ->select('salesdetails.idVenta')
            ->join('salesdetails', 'salesdetails.idVenta', 'sales.id')
            ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            ->where(['sales.idBanca'=> $datos['idBanca'], 'sales.pagado' =>0])
            ->where('salesdetails.premio', '>', 0)
            ->whereNotIn('sales.status', [0, 5])
            ->get();

            $ticketsGanadores = collect($ticketsGanadores)->map(function($t){
                return $t['idVenta'];
            });

            

            $ticketsGanadores = Sales::on($datos['servidor'])->whereIn('id', $ticketsGanadores)->get();
    
            $comisionesMonto = (new Helper)->comisionesPorBanca($datos['servidor'], $datos['idBanca'], $fechaInicial, $fechaFinal);
            

            $fechaFinalSinHora = explode(' ', $fechaFinal);
            $fechaFinalSinHora = new Carbon($fechaFinalSinHora[0]);
            $fechaFinalSinHora = $fechaFinalSinHora->toDateString();
            $balanceHastaLaFecha = (new Helper)->saldoPorFecha($datos['servidor'], $datos['idBanca'], 1, $fechaFinalSinHora);
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
            'neto' => round($neto, 2),
            'loterias' => $loterias,
            'ticketsGanadores' => SalesResource::collection($ticketsGanadores)->servidor($datos['servidor']),
            'banca' => Branches::on($datos['servidor'])->whereId($datos['idBanca'])->first(),
            'bancas' => Branches::on($datos['servidor'])->select('id', 'descripcion')->whereStatus(1)->get(),
            'premios' => $premios,
            'balanceActual' => round(($balanceHastaLaFecha + $neto), 2),
            'comisiones' => round($comisionesMonto, 2)
        ], 201);
    }

    public function monitoreo()
    {
        // $datos = request()->validate([
        //     'datos.fecha' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => '',
        //     'datos.layout' => ''
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
    
        $usuario = Users::on($datos['servidor'])->whereId($datos['idUsuario'])->first();
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
            $datos['idBanca'] = Branches::on($datos['servidor'])->where(['id' => $datos['idBanca'], 'status' => 1])->first();
            if($datos['idBanca'] != null)
                $datos['idBanca'] = $datos['idBanca']->id;
        }else{
            $datos['idBanca'] = Branches::on($datos['servidor'])->where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first()->id;
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
    
    
        $monitoreo = Sales::on($datos['servidor'])->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                    ->where('idBanca', $datos['idBanca'])
                    ->where('status', '!=', '5')
                    ->orderBy('id', 'desc')
                    ->get();
    
       // return $ventas;
        
    
        return Response::json([
            'monitoreo' => SalesResource::collection($monitoreo)->servidor($datos['servidor']),
            'loterias' => Lotteries::on($datos['servidor'])->whereStatus(1)->get(),
            'caracteristicasGenerales' =>  Generals::on($datos['servidor'])->get(),
            'total_ventas' => Sales::on($datos['servidor'])->sum('total'),
            'total_jugadas' => Salesdetails::on($datos['servidor'])->count('jugada'),
            'errores' => 0,
        ], 201);
    }


    public function monitoreoMovil()
    {
        // $datos = request()->validate([
        //     'datos.fecha' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => '',
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
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
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
            $datos['idBanca'] = Branches::on($datos["servidor"])->where(['id' => $datos['idBanca'], 'status' => 1])->first();
            if($datos['idBanca'] != null)
                $datos['idBanca'] = $datos['idBanca']->id;
        }else{
            $datos['idBanca'] = Branches::on($datos["servidor"])->where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first()->id;
        }
    
        $fecha = getdate(strtotime($datos['fecha']));
    
    
    
        $monitoreo = Sales::on($datos["servidor"])->select('id', 'idTicket', 'idBanca', 'total', 'status')->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                    ->where('idBanca', $datos['idBanca'])
                    ->where('status', '!=', '5')
                    ->orderBy('id', 'desc')
                    ->get();
    
       // return $ventas;
        
       $monitoreo = collect($monitoreo)->map(function($m) use($datos){
           $codigo = Branches::on($datos["servidor"])->select('codigo')->whereId($m['idBanca'])->first();
           $codigoBarra = Tickets::on($datos["servidor"])->whereId($m['idTicket'])->first();
           return ['id' =>$m['id'], 'total' =>$m['total'], 'status' =>$m['status'], 'idTicket' =>$m['idTicket'], 'codigoBarra' =>$codigoBarra['codigoBarra'], 'idBanca' =>$m['idBanca'], 'codigo' =>$codigo['codigo']];
       });
    
        return Response::json([
            'monitoreo' => $monitoreo,
            'loterias' => Lotteries::on($datos["servidor"])->whereStatus(1)->get(),
            'caracteristicasGenerales' =>  Generals::on($datos["servidor"])->get(),
            'total_ventas' => Sales::on($datos["servidor"])->sum('total'),
            'total_jugadas' => Salesdetails::on($datos["servidor"])->count('jugada'),
            'errores' => 0
        ], 201);
    }


    public function getTicketById()
    {
        // $datos = request()->validate([
        //     'datos.idTicket' => 'required',
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
            ], 201);
        }
       

        $ticket = Sales::on($datos["servidor"])->where('idTicket', $datos['idTicket'])->first();
        if($ticket == null){
            return Response::json([
                'errores' => 0,
                'mensaje' => "El ticket no existe",
            ], 201);
        }
    
        $ticket = (new SalesImageResource($ticket))->servidor($datos["servidor"]);
        
        return Response::json([
            'ticket' => $ticket,
            'errores' => 0,
            'mensaje' => "El ticket no existe",
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
        // $datos = request()->validate([
        //     'datos.fecha' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => '',
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
            ], 201);
        }

        if($datos['idBanca'] == 0){
            $bancas = Branches::on($datos["servidor"])->whereStatus(1)->get();
            $datos['idBanca'] = collect($bancas)->map(function($b){
                return $b['id'];
            });
        }else{
            $datos['idBanca'] = [$datos['idBanca']];
        }

        if($datos['fecha'] == "Todas las fechas"){
            $ticketsPendientesDePago = Sales::
            on($datos["servidor"])
            ->select('sales.id')
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

           

            $ticketsPendientesDePago = Sales::on($datos["servidor"])->select('sales.id')
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

        $ticketsPendientesDePago = Sales::on($datos["servidor"])->whereIn('id', $ticketsPendientesDePago)->get();
        return Response::json([
            'bancas' => Branches::on($datos["servidor"])->whereStatus(1)->get(),
            'ticketsPendientesDePago' => SalesResource::collection($ticketsPendientesDePago)->servidor($datos["servidor"])
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
