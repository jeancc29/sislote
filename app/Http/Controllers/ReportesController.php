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

    public function reporteJugadas(){
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }

        // return Response::json(["data" => $datos]);

        $consultaLoteria = isset($datos["loteria"]) ? " AND salesdetails.idLoteria = {$datos['loteria']['id']}" : '';
        $consultaSorteo = isset($datos["sorteo"]) ? " AND salesdetails.idSorteo = {$datos['sorteo']['id']}" : '';
        $consultaJugada = isset($datos["jugada"]) ? " AND salesdetails.jugada = {$datos['jugada']}" : '';
        $consultaLoteriaPremio = isset($datos["loteria"]) ? " AND awards.idLoteria = {$datos['loteria']['id']}" : '';
        $fechaInicial = $datos["fechaInicial"];
        $fechaFinal = $datos["fechaFinal"];
        $limite = isset($datos["limite"]) ? $datos["limite"] : 20;
        $idMoneda = isset($datos["moneda"]) ? $datos["moneda"]["id"] : \App\Coins::on($datos["servidor"])->orderBy("pordefecto", "desc")->first()->id;
        // $data = \DB::connection($datos["servidor"])->select("
        // SELECT 
        // s.jugada,
        // SUM(s.monto) AS monto,
        // SUM(s.premio) AS premio,
        // COUNT(s.jugada) AS cantidadVecesQueSeHaJugado,
        // IF(
        //     LENGTH(s.jugada) != 2,
        //     NULL,
        //     (SELECT 
        //         COUNT(awards.numeroGanador) 
        //     FROM awards 
        //     WHERE awards.created_at BETWEEN '$fechaInicial' and '$fechaFinal' AND awards.numeroGanador regexp CONCAT('(^', s.jugada, '|\d\d', s.jugada , '\d\d', '|\d\d\d\d', s.jugada, ')') $consultaLoteriaPremio)
        // ) AS cantidadVecesQueHaSalido,
        // s.idSorteo
        // FROM salesdetails AS s 
        // WHERE 
        //     s.idVenta in (SELECT sales.id FROM sales WHERE sales.status NOT IN(0, 5) AND sales.created_at BETWEEN '$fechaInicial' and '$fechaFinal')
        //     AND s.created_at BETWEEN '$fechaInicial' and '$fechaFinal'
        //     $consultaLoteria
        //     $consultaSorteo
        //     $consultaJugada
        // GROUP BY s.jugada, s.idSorteo
        // ORDER BY monto desc
        // ");


        $data = \DB::connection($datos["servidor"])->select("
            SELECT
            j.jugada,
                    j.idSorteo,
                    j.rankJugada,
                    j.monto,
                    j.premio,
                    j.cantidadVecesQueSeHaJugado,
                    IF(
                        j.idSorteo != 1,
                        NULL,
                        (SELECT 
                            COUNT(awards.numeroGanador) 
                        FROM awards 
                        WHERE awards.created_at BETWEEN '$fechaInicial' and '$fechaFinal' AND (awards.primera = j.jugada OR awards.segunda = j.jugada OR awards.tercera = j.jugada) $consultaLoteriaPremio)
                    ) AS cantidadVecesQueHaSalido
            FROM (
                SELECT 
                    j.jugada,
                    j.idSorteo,
                    j.rankJugada,
                    j.monto,
                    j.premio,
                    j.cantidadVecesQueSeHaJugado
                FROM (
                    select 
                    j.jugada,
                    j.idSorteo,
                    
                    j.monto,
                    j.premio,
                    j.cantidadVecesQueSeHaJugado,
                    Rank() over (Partition BY idSorteo ORDER BY j.monto DESC ) AS rankJugada
                    from (SELECT 
                        SUM(monto) AS monto, 
                        SUM(premio) AS premio, 
                        jugada,
                        idSorteo, 
                        COUNT(jugada) AS cantidadVecesQueSeHaJugado
                        
                        FROM salesdetails
                        WHERE 
                        salesdetails.idVenta in (SELECT sales.id FROM sales WHERE sales.status NOT IN(0, 5) AND sales.created_at BETWEEN '$fechaInicial' and '$fechaFinal' AND sales.idBanca IN(SELECT branches.id FROM branches WHERE branches.status = 1 AND branches.idMoneda = $idMoneda) )
                        AND created_at between '$fechaInicial' and '$fechaFinal' 
                        $consultaLoteria
                        $consultaSorteo
                        $consultaJugada
                        GROUP BY jugada, idSorteo
                        ) as j
                ) AS j
                WHERE rankJugada <= $limite
            ) AS j
        ");
        // $data = \DB::connection($datos["servidor"])->select("
        // SELECT
        // j.jugada,
        //         j.idSorteo,
        //         j.rankJugada,
        //         j.monto,
        //         j.premio,
        //         j.cantidadVecesQueSeHaJugado,
        //         IF(
        //             j.idSorteo != 1,
        //             NULL,
        //             (SELECT 
        //                 COUNT(awards.numeroGanador) 
        //             FROM awards 
        //             WHERE awards.created_at BETWEEN '2021-04-01 00:00' and '2021-04-12 23:00' AND awards.numeroGanador regexp CONCAT('(^', j.jugada, '|\d\d', j.jugada , '\d\d', '|\d\d\d\d', j.jugada, ')')   )
        //         ) AS cantidadVecesQueHaSalido
        // FROM (
        //     SELECT 
        //         j.jugada,
        //         j.idSorteo,
        //         j.rankJugada,
        //         j.monto,
        //         j.premio,
        //         j.cantidadVecesQueSeHaJugado
        //     FROM (
        //         SELECT 
        //             SUM(monto) AS monto, 
        //             SUM(premio) AS premio, 
        //             jugada,
        //             idSorteo, 
        //             COUNT(jugada) AS cantidadVecesQueSeHaJugado,
        //             Rank() over (Partition BY idSorteo ORDER BY id DESC ) AS rankJugada
        //             FROM salesdetails
        //             WHERE created_at between '2021-04-01 00:00' AND '2021-04-12 23:00' 
        //             GROUP BY jugada, idSorteo
        //             ORDER BY idSorteo ASC, monto desc
        //     ) AS j
        //     WHERE rankJugada <= 10
        // ) AS j
        
        // ");

        // $data = \DB::connection($datos["servidor"])->select("
        //     SELECT 
        //         draws.id,
        //         draws.descripcion,
        //         (
        //             SELECT
        //                 JSON_ARRAYAGG(
        //                     JSON_OBJECT(
        //                         'jugada', j.jugada
        //                     )
        //                 )
        //             FROM (
        //                 SELECT 
        //                 s.jugada,
        //                 SUM(s.monto) AS monto,
        //                 SUM(s.premio) AS premio,
        //                 COUNT(s.jugada) AS cantidadVecesQueSeHaJugado,
        //                 IF(
        //                     LENGTH(s.jugada) != 2,
        //                     NULL,
        //                     (SELECT 
        //                         COUNT(awards.numeroGanador) 
        //                     FROM awards 
        //                     WHERE awards.created_at BETWEEN '$fechaInicial' and '$fechaFinal' AND awards.numeroGanador regexp CONCAT('(^', s.jugada, '|\d\d', s.jugada , '\d\d', '|\d\d\d\d', s.jugada, ')') $consultaLoteriaPremio)
        //                 ) AS cantidadVecesQueHaSalido,
        //                 s.idSorteo
        //                 FROM salesdetails AS s 
        //                 WHERE 
        //                     s.idVenta in (SELECT sales.id FROM sales WHERE sales.status NOT IN(0, 5) AND sales.created_at BETWEEN '$fechaInicial' and '$fechaFinal')
        //                     AND s.created_at BETWEEN '$fechaInicial' and '$fechaFinal'
        //                     AND s.idSorteo = draws.id
        //                     $consultaLoteria
        //                     $consultaJugada
        //                 GROUP BY s.jugada, s.idSorteo
        //                 ORDER BY monto desc
        //                 LIMIT 50
        //             ) AS j
        //         ) AS jugadas
        //     FROM draws

        // ");

        return Response::json([
            "data" => $data,
            "sorteos" => ($datos["retornarSorteos"] == true) ? \App\Draws::on($datos["servidor"])->get() : [],
            "loterias" => ($datos["retornarLoterias"] == true) ? \App\Lotteries::on($datos["servidor"])->whereStatus(1)->get() : [],
            "monedas" => isset($datos["retornarMonedas"]) ? ($datos["retornarMonedas"] == true) ? \App\Coins::on($datos["servidor"])->orderBy("pordefecto", "desc")->get() : [] : []
        ]);

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

        // Salesdetails::on("valentin")
        // ->selectRaw(
        //     "
        //     sum(salesdetails.comision), 
        //     sum(salesdetails.monto), 
        //     sum(s.descuentoMonto), 
        //     sum(salesdetails.premio), 
        //     count(s.idTicket)
        //     "
        //     )
        //     ->join("sales as s", "s.id", "salesdetails.idVenta")
        //     ->whereBetween('s.created_at', array($fechaInicial, $fechaFinal))
        //     ->groupBy("s.idBanca")
        //     ->get();






            // Salesdetails::on("valentin")->selectRaw("sum(salesdetails.comision), sum(salesdetails.monto), sum(s.descuentoMonto), sum(salesdetails.premio), count(s.idTicket)")->join("sales as s", "s.id", "salesdetails.idVenta")->whereBetween('s.created_at', array($fechaInicial, $fechaFinal))->groupBy("s.idBanca")->get();
        
            //Query
            // \DB::connection("valentin")
            //     ->select(
            //         "
            //         select sum(s.descuentoMonto) as descuento, 
            //         sum(sd.comision) as comision, 
            //         sum(sd.monto) as monto, 
            //         sum(sd.premio) as premio, 
            //         (select count(id) from sales where sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') tickets, 
            //         (select count(id) from sales where sales.status = 1 and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') ticketsPendientes,
            //         (select count(id) from sales where sales.status = 2 and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') ticketsGanadores,
            //         (select count(id) from sales where sales.status = 3 and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') ticketsPerdedores, 
            //         s.idBanca,
            //         b.descripcion 
            //         from sales s 
            //         inner join salesdetails sd on s.id = sd.idVenta 
            //         inner join branches b on b.id = s.idBanca
            //         where s.status not in(0, 5) and s.created_at between '{$fechaInicial}' and '{$fechaFinal}' group by s.idBanca, b.descripcion
            //         union
            //         select 
            //         id as idBanca,
            //         descripcion
            //         from branches 
            //         where id not in(select idBanca from sales where status not in(0, 5) and created_at between '{$fechaInicial}' and '{$fechaFinal}' group by idBanca)");


        /**************** QUERY VIEJO */

        // $bancas = Branches::on($datos['servidor'])->whereStatus(1)->get();
        // $bancas = collect($bancas)->map(function($d) use($fechaInicial, $fechaFinal, $fechaActualCarbon, $fechaFinalSinHora, $datos){
        //     $ventas = Helper::ventasPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
        //     $descuentos = Helper::descuentosPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
        //     $premios = Helper::premiosPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
        //     $comisiones = Helper::comisionesPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
        //     $tickets = Helper::ticketsPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
        //     $ticketsPendientes = Helper::ticketsPendientesPorBanca($datos["servidor"], $d['id'], $fechaInicial, $fechaFinal);
        //     $totalNeto = $ventas - ($descuentos + $premios + $comisiones);
        //     $balance = Helper::saldoPorFecha($datos["servidor"], $d['id'], 1, $fechaFinalSinHora);
        //     $caidaAcumulada = Helper::saldoPorFecha($datos["servidor"], $d['id'], 3, $fechaFinalSinHora);

        //     // $sumarVentasNetas = false;

        //     // if($fechaFinalSinHora < $fechaActualCarbon->toDateString()){
        //     //     $sumarVentasNetas = false;
        //     // }
        //     // else if($fechaFinalSinHora == $fechaActualCarbon->toDateString() && ($fechaActualCarbon->hour == 23 && $fechaActualCarbon->minute > 54) == false){
        //     //     $sumarVentasNetas = true;
        //     // }
        //     //'balanceActual' => ($sumarVentasNetas == true) ? round(($balance + $totalNeto), 2) : $balance,

        //     $pendientes = Sales::on($datos['servidor'])
        //             ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //             ->whereStatus(1)
        //             ->where('idBanca', $d['id'])
        //             ->count();
            
        //         $ganadores = Sales::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //                     ->whereStatus('2')
        //                     ->where('idBanca', $d['id'])
        //                     ->count();
        //         // $premios = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //         //     ->whereStatus('2')
        //         //     ->where('idBanca', $d['id'])
        //         //     ->sum("premios");
                            
                        
        //         $perdedores = Sales::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //                     ->whereStatus('3')
        //                     ->where('idBanca', $d['id'])
        //                     ->count();

        //     return ['id' => $d['id'], 'descripcion' => strtoupper ($d['descripcion']), 
        //         'idMoneda' => $d['idMoneda'],
        //         'codigo' => $d['codigo'], 'ventas' => $ventas, 
        //         'descuentos' => $descuentos, 
        //         'premios' => $premios, 
        //         'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance, 
        //         'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes,
        //         'balanceActual' => round(($balance + $totalNeto), 2),
        //         'pendientes' => $pendientes,
        //         'ganadores' => $ganadores,
        //         'perdedores' => $perdedores,
        //         'monedas' => Coins::on($datos['servidor'])->orderBy('pordefecto', 1)->get()
        //     ];
        // });
        
      
        /************************** QUERY NUEVO *******************************/
        $bancas = [];
        $idMoneda = isset($datos["moneda"]) ? $datos["moneda"]["id"] : \App\Coins::on($datos["servidor"])->orderBy("pordefecto", "desc")->first()->id;
        if(isset($datos["opcion"]) == false){
            $limite = isset($datos["limite"]) ? $datos["limite"] : 30;
            $bancas = \DB::connection($datos["servidor"])
            ->select(
                "
                select
                (select sum(sales.descuentoMonto) from sales where sales.status not in(0, 5) and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') descuento, 
                sum(sd.comision) as comision, 
                sum(sd.monto) as monto, 
                sum(sd.premio) as premio, 
                (select count(id) from sales where sales.status not in(0, 5) and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') tickets, 
                (select count(id) from sales where sales.status = 1 and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') ticketsPendientes, 
                (select count(id) from sales where sales.status = 2 and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') ticketsGanadores, 
                (select count(id) from sales where sales.status = 3 and sales.idBanca = s.idBanca and sales.created_at between '{$fechaInicial}' and '{$fechaFinal}') ticketsPerdedores, 
                s.idBanca, 
                b.descripcion,
                b.idMoneda,
                b.codigo 
                from sales s 
                inner join salesdetails sd on s.id = sd.idVenta inner join branches b on b.id = s.idBanca 
                where s.status not in(0, 5) and 
                s.created_at between '{$fechaInicial}' and '{$fechaFinal}' 
                group by s.idBanca, b.descripcion, b.idMoneda, b.codigo 
                union 
                select 
                (select 0) as descuento, 
                (select 0) as comision, 
                (select 0) as monto, 
                (select 0) as premio, 
                (select 0) as tickets, 
                (select 0) as ticketsPendientes, 
                (select 0) as ticketsGanadores, 
                (select 0) as ticketsPerdedores,
                branches.id as idBanca, 
                branches.descripcion, 
                branches.idMoneda,
                branches.codigo 
                from branches 
                where 
                    id not in(select idBanca from sales where status not in(0, 5) and created_at between '{$fechaInicial}' and '{$fechaFinal}' group by idBanca) 
                    AND status = 1
                    limit $limite");
        }else{
            if($datos["opcion"] == "Sin ventas"){
                $bancas = \DB::connection($datos["servidor"])
                ->select("
                select 
                (select 0) as descuento, 
                (select 0) as comision, 
                (select 0) as monto, 
                (select 0) as premio, 
                (select 0) as tickets, 
                (select 0) as ticketsPendientes, 
                (select 0) as ticketsGanadores, 
                (select 0) as ticketsPerdedores,
                branches.id as idBanca, 
                branches.descripcion, 
                branches.idMoneda,
                branches.codigo 
                from branches 
                where 
                    id not in(select idBanca from sales where status not in(0, 5) and created_at between '{$fechaInicial}' and '{$fechaFinal}' group by idBanca)
                    AND branches.idMoneda = $idMoneda
                     limit {$datos['limite']}
                ");
            }else{
                $queryOpcion = "";
                if($datos["opcion"] == "Con premios")
                    $queryOpcion = "having premio > 0";
                if($datos["opcion"] == "Con tickets pendientes")
                    $queryOpcion = "having ticketsPendientes > 0";

                $bancas = \DB::connection($datos["servidor"])
                ->select(
                    "
                    select
                    (select sum(sales.descuentoMonto) from sales where sales.status not in(0, 5) and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') descuento, 
                    sum(sd.comision) as comision, 
                    sum(sd.monto) as monto, 
                    sum(sd.premio) as premio, 
                    (select count(id) from sales where sales.status not in(0, 5) and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') tickets, 
                    (select count(id) from sales where sales.status = 1 and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') ticketsPendientes, 
                    (select count(id) from sales where sales.status = 2 and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') ticketsGanadores, 
                    (select count(id) from sales where sales.status = 3 and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') ticketsPerdedores, 
                    s.idBanca, 
                    b.descripcion,
                    b.idMoneda,
                    b.codigo 
                    from sales s 
                    inner join salesdetails sd on s.id = sd.idVenta inner join branches b on b.id = s.idBanca 
                    where s.status not in(0, 5) 
                    AND s.idBanca in(SELECT branches.id FROM branches WHERE branches.idMoneda = $idMoneda)
                    and s.created_at between '$fechaInicial' AND '$fechaFinal' 
                    group by s.idBanca, b.descripcion, b.idMoneda, b.codigo 
                    $queryOpcion
                    limit {$datos['limite']}
                    ");
            }
            
        }

        

                $bancas = collect($bancas)->map(function($d) use($fechaInicial, $fechaFinal, $fechaActualCarbon, $fechaFinalSinHora, $datos){
                    $ventas = $d->monto;
                    $descuentos = $d->descuento;
                    $premios = $d->premio;
                    $comisiones = $d->comision;
                    $tickets = $d->tickets;
                    $ticketsPendientes = $d->ticketsPendientes;
                    $ticketsGanadores = $d->ticketsGanadores;
                    $ticketsPerdedores = $d->ticketsPerdedores;
                    $totalNeto = (double)$d->monto - ((double)$d->descuento + (double)$d->premio + (double)$d->comision);
                    $balance = Helper::saldoPorFecha($datos["servidor"], $d->idBanca, 1, $fechaFinalSinHora);
                    $caidaAcumulada = Helper::saldoPorFecha($datos["servidor"], $d->idBanca, 3, $fechaFinalSinHora);
        
                   
                    
        
                    return [
                        'id' => $d->idBanca, 
                        'descripcion' => strtoupper ($d->descripcion), 
                        'idMoneda' => $d->idMoneda,
                        'codigo' => $d->codigo, 'ventas' => $ventas, 
                        'descuentos' => $descuentos, 
                        'premios' => $premios, 
                        'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance, 
                        'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes,
                        'balanceActual' => round(($balance + $totalNeto), 2),
                        'pendientes' => $ticketsPendientes,
                        'ganadores' =>$ticketsGanadores,
                        'perdedores' => $ticketsPerdedores,
                        'monedas' => Coins::on($datos["servidor"])->orderBy('pordefecto', 1)->get()
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

    public function historicoApi()
    {
        $controlador = Route::getCurrentRoute()->getName();
        $fecha = getdate();
        $fechaActualCarbon = Carbon::now();


        $datos = request()->validate([
            'id' => '',
            'fechaDesde' => '',
            'fechaHasta' => '',
            'apiKey' => '',
            'idEmpresa' => '',
            'limite' => '',
        ]);


       
        try {
            $datos["servidor"] = "servidor303";

        if(isset($datos["apiKey"]) == false)
            $datos["apiKey"] = "";
        if($datos["apiKey"] != ".iOe5qtMLqUKUu_vK-ir2On2zILe4sXGRtHJCOSTlvws"){
            return Response::json(["error" => 12, "mensaje" => "apiKey incorrecta"]);
            return;
        }
    
        if(isset($datos['fechaHasta']) == false){
            $fechaHoyTmp = getdate();
            $datos['fechaHasta'] = $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'];
        }
        if(isset($datos['limite']) == false){
            $datos['limite'] = 50;
        }

        $fechaFinalSinHora = new Carbon($datos['fechaHasta']);
        $fechaFinalSinHora = $fechaFinalSinHora->toDateString();
        $fecha = getdate();
  
        if(isset($datos['fechaDesde']) == true && isset($datos['fechaHasta']) != null){
            $fecha = getdate(strtotime($datos['fechaDesde']));
            $fechaF = getdate(strtotime($datos['fechaHasta']));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
    

      
        /************************** QUERY NUEVO *******************************/
        $bancas = [];
        $idMoneda = isset($datos["moneda"]) ? $datos["moneda"]["id"] : \App\Coins::on($datos["servidor"])->orderBy("pordefecto", "desc")->first()->id;
        if(isset($datos["opcion"]) == false)
             $datos["opcion"] = "";

            if($datos["opcion"] == "Sin ventas"){
                $bancas = \DB::connection($datos["servidor"])
                ->select("
                select 
                (select 0) as descuento, 
                (select 0) as comision, 
                (select 0) as monto, 
                (select 0) as premio, 
                (select 0) as tickets, 
                (select 0) as ticketsPendientes, 
                (select 0) as ticketsGanadores, 
                (select 0) as ticketsPerdedores,
                branches.id as idBanca, 
                branches.descripcion, 
                branches.idMoneda,
                branches.codigo 
                from branches 
                where 
                    id not in(select idBanca from sales where status not in(0, 5) and created_at between '{$fechaInicial}' and '{$fechaFinal}' group by idBanca)
                    AND branches.idMoneda = $idMoneda
                     limit {$datos['limite']}
                ");
            }else{
                $queryOpcion = "";
                if(isset($datos["opcion"]) == false)
                    $queryOpcion = "";
                else if($datos["opcion"] == "Con premios")
                    $queryOpcion = "having premio > 0";
                else if($datos["opcion"] == "Con tickets pendientes")
                    $queryOpcion = "having ticketsPendientes > 0";

                

                $bancas = \DB::connection($datos["servidor"])
                ->select(
                    "
                    select
                    (select sum(sales.descuentoMonto) from sales where sales.status not in(0, 5) and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') descuento, 
                    sum(sd.comision) as comision, 
                    sum(sd.monto) as monto, 
                    sum(sd.premio) as premio, 
                    (select count(id) from sales where sales.status not in(0, 5) and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') tickets, 
                    (select count(id) from sales where sales.status = 1 and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') ticketsPendientes, 
                    (select count(id) from sales where sales.status = 2 and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') ticketsGanadores, 
                    (select count(id) from sales where sales.status = 3 and sales.idBanca = s.idBanca and sales.created_at between '$fechaInicial' AND '$fechaFinal') ticketsPerdedores, 
                    s.idBanca, 
                    b.descripcion,
                    b.idMoneda,
                    b.codigo 
                    from sales s 
                    inner join salesdetails sd on s.id = sd.idVenta inner join branches b on b.id = s.idBanca 
                    where s.status not in(0, 5) 
                    AND s.idBanca in(SELECT branches.id FROM branches WHERE branches.idMoneda = $idMoneda)
                    and s.created_at between '$fechaInicial' AND '$fechaFinal' 
                    group by s.idBanca, b.descripcion, b.idMoneda, b.codigo 
                    $queryOpcion
                    limit {$datos['limite']}
                    ");
            }
            

        

                $bancas = collect($bancas)->map(function($d) use($fechaInicial, $fechaFinal, $fechaActualCarbon, $fechaFinalSinHora, $datos){
                    $ventas = $d->monto;
                    $descuentos = $d->descuento;
                    $premios = $d->premio;
                    $comisiones = $d->comision;
                    $tickets = $d->tickets;
                    $ticketsPendientes = $d->ticketsPendientes;
                    $ticketsGanadores = $d->ticketsGanadores;
                    $ticketsPerdedores = $d->ticketsPerdedores;
                    $totalNeto = (double)$d->monto - ((double)$d->descuento + (double)$d->premio + (double)$d->comision);
                    $balance = Helper::saldoPorFecha($datos["servidor"], $d->idBanca, 1, $fechaFinalSinHora);
                    $caidaAcumulada = Helper::saldoPorFecha($datos["servidor"], $d->idBanca, 3, $fechaFinalSinHora);
        
                   
                    
        
                    return [
                        'descripcion' => strtoupper ($d->descripcion), 
                        'codigo' => $d->codigo, 'ventas' => $ventas, 
                        'descuentos' => $descuentos, 
                        'premios' => $premios, 
                        'comisiones' => $comisiones, 'totalNeto' => round($totalNeto, 2), 'balance' => $balance, 
                        'caidaAcumulada' => $caidaAcumulada, 'tickets' => $tickets, 'ticketsPendientes' => $ticketsPendientes,
                        'balanceActual' => round(($balance + $totalNeto), 2),
                        'pendientes' => $ticketsPendientes,
                        'ganadores' =>$ticketsGanadores,
                        'perdedores' => $ticketsPerdedores,
                    ];
                });
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json(["error" => 20, "mensaje" => $th->getMessage()]);
        }
    
        return Response::json([
            'bancas' => $bancas,
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

            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["data"]))
                $datos = $datos["data"];


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
            $fechaInicial = $fecha['year'].'-'. \App\Classes\Helper::toDosDigitos(strval($fecha['mon'])).'-'. \App\Classes\Helper::toDosDigitos(strval($fecha['mday'])) . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.\App\Classes\Helper::toDosDigitos(strval($fecha['mon'])).'-'.\App\Classes\Helper::toDosDigitos(strval($fecha['mday'])) . ' 23:50:00';
            $fechaParaImprimirChadreMovil = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'];
        }else{
            $fecha = getdate(strtotime($datos['fecha']));
            $fechaF = getdate(strtotime($datos['fechaFinal']));
            $fechaInicial = $fecha['year'].'-'.\App\Classes\Helper::toDosDigitos(strval($fecha['mon'])).'-'. \App\Classes\Helper::toDosDigitos(strval($fecha['mday'])) . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'. \App\Classes\Helper::toDosDigitos(strval($fechaF['mon'])) .'-'. \App\Classes\Helper::toDosDigitos(strval($fechaF['mday'])) . ' 23:50:00';
            $fechaParaImprimirChadreMovil = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'];

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
        
        ///NUEVO QUERY
        $dataVentas = \DB::connection($datos["servidor"])->select("
                SELECT
                s.pendientes,
                s.ganadores,
                s.perdedores,
                s.total,
                IF(s.descuentos IS NOT NULL, s.descuentos, 0) descuentos,
                IF(s.premios IS NOT NULL, s.premios, 0) premios,
                IF(s.ventas IS NOT NULL, s.ventas, 0) ventas
                FROM (
                    SELECT 
                        COUNT(IF(s.status = 1, s.id, NULL)) AS pendientes,
                        COUNT(IF((s.status = 1 OR s.status = 2) AND s.premios > 0, s.id, NULL)) AS ganadores,
                        COUNT(IF(s.status = 3, s.id, NULL)) AS perdedores,
                        COUNT(s.id) AS total,
                        SUM(s.descuentoMonto) AS descuentos,
                        SUM(s.premios) AS premios,
                        SUM(s.total) AS ventas
                    FROM sales s
                    WHERE 
                        s.status NOT IN(0, 5) 
                        AND s.idBanca = {$datos['idBanca']}
                        AND s.created_at BETWEEN '$fechaInicial' AND '$fechaFinal'
            ) AS s
        ");

        $pendientes = $dataVentas[0]->pendientes;
        $ganadores = $dataVentas[0]->ganadores;
        $perdedores = $dataVentas[0]->perdedores;
        $total = $dataVentas[0]->total;
        $premios = $dataVentas[0]->premios;
        $ventas = $dataVentas[0]->ventas;
        $descuentos = $dataVentas[0]->descuentos;
    
        // $pendientes = Sales::
        //     on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereStatus(1)
        //     ->where('idBanca', $datos['idBanca'])
        //     ->count();
    
        // $ganadores = Sales::on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereStatus('2')
        //     ->where('idBanca', $datos['idBanca'])
        //     ->count();
        // $premios = Sales::on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereStatus('2')
        //     ->where('idBanca', $datos['idBanca'])
        //     ->sum("premios");
                    
                
        // $perdedores = Sales::on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereStatus('3')
        //     ->where('idBanca', $datos['idBanca'])
        //     ->count();
    
        // $total = Sales::on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereIn('status', array(1,2,3))
        //     ->where('idBanca', $datos['idBanca'])
        //     ->count();
    
        // $ventas = Sales::on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereNotIn('status', [0,5])
        //     ->where('idBanca', $datos['idBanca'])
        //     ->sum('total');
    
        // //AQUI COMIENSA LAS COMISIONES

        // //AQUI TERMINAN LAS COMISIONES

        // $descuentos = Sales::on($datos['servidor'])
        //     ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereNotIn('status', [0,5])
        //     ->where('idBanca', $datos['idBanca'])
        //     ->sum('descuentoMonto');
    
                    

        // $idVentasPremios = Sales::on($datos['servidor'])
        //     ->select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //     ->whereIn('status', array(1,2))
        //     ->where('idBanca', $datos['idBanca'])
        //     ->get();

        // $idVentasPremios = collect($idVentasPremios)->map(function($v){
        //     return $v['id'];
        // });

        // $premios = Salesdetails::on($datos['servidor'])->whereIn('idVenta', $idVentasPremios)->sum('premio');
    
        //Obtener loterias con el monto total jugado y con los premios totales
    
        ///NUEVO QUERY
        $loterias = \DB::connection($datos["servidor"])->select("
            SELECT
                lo.id,
                lo.descripcion,
                lo.abreviatura,
                IF(lo.primera IS NOT NULL AND lo.primera != '' AND lo.primera != 'null', lo.primera, NULL) AS primera,
                IF(lo.segunda IS NOT NULL AND lo.segunda != '' AND lo.segunda != 'null', lo.segunda, NULL) AS segunda,
                IF(lo.tercera IS NOT NULL AND lo.tercera != '' AND lo.tercera != 'null', lo.tercera, NULL) AS tercera,
                IF(lo.pick3 IS NOT NULL AND lo.pick3 != '' AND lo.pick3 != 'null', lo.pick3, NULL) AS pick3,
                IF(lo.pick4 IS NOT NULL AND lo.pick4 != '' AND lo.pick4 != 'null', lo.pick4, NULL) AS pick4,
                IF(lo.ventas IS NOT NULL AND lo.ventas != '' AND lo.ventas != 'null', lo.ventas, 0) AS ventas,
                IF(lo.premios IS NOT NULL AND lo.premios != '' AND lo.premios != 'null', lo.premios, NULL) AS premios,
                IF(lo.comisiones IS NOT NULL AND lo.comisiones != '' AND lo.comisiones != 'null', lo.comisiones, 0) AS comisiones,
                IF(lo.neto IS NOT NULL AND lo.neto != '' AND lo.neto != 'null', lo.neto, 0) AS neto
            FROM (
                SELECT
                    lo.id,
                    lo.descripcion,
                    lo.abreviatura,
                    ROUND(JSON_UNQUOTE(JSON_EXTRACT(lo.dataVentas, '$.ventas')), 0) AS ventas,
                    ROUND(JSON_UNQUOTE(JSON_EXTRACT(lo.dataVentas, '$.premios')), 0) AS premios,
                    ROUND(JSON_UNQUOTE(JSON_EXTRACT(lo.dataVentas, '$.comisiones')), 0) AS comisiones,
                    SUM(ROUND(JSON_UNQUOTE(JSON_EXTRACT(lo.dataVentas, '$.ventas')), 0) - (ROUND(JSON_UNQUOTE(JSON_EXTRACT(lo.dataVentas, '$.comisiones')), 0) + ROUND(JSON_UNQUOTE(JSON_EXTRACT(lo.dataVentas, '$.premios')), 0) ) ) AS neto,
                    JSON_UNQUOTE(JSON_EXTRACT(lo.dataNumerosGanadores, '$.primera')) AS primera,
                    JSON_UNQUOTE(JSON_EXTRACT(lo.dataNumerosGanadores, '$.segunda')) AS segunda,
                    JSON_UNQUOTE(JSON_EXTRACT(lo.dataNumerosGanadores, '$.tercera')) AS tercera,
                    JSON_UNQUOTE(JSON_EXTRACT(lo.dataNumerosGanadores, '$.pick3')) AS pick3,
                    JSON_UNQUOTE(JSON_EXTRACT(lo.dataNumerosGanadores, '$.pick4')) AS pick4
                FROM (
                    SELECT
                        l.id,
                        l.descripcion,
                        l.abreviatura,
                        (
                            SELECT 
                                JSON_OBJECT(
                                    'ventas', SUM(sd.monto),
                                    'premios', SUM(sd.premio),
                                    'comisiones', SUM(sd.comision)
                                )
                            FROM salesdetails sd 
                            WHERE 
                                sd.idLoteria = l.id 
                                AND sd.idVenta IN(SELECT sales.id FROM sales WHERE sales.status NOT IN(0, 5) AND sales.created_at BETWEEN '$fechaInicial' AND '$fechaFinal' AND sales.idBanca = {$datos['idBanca']}) 
                        ) AS dataVentas,
                    (
                        SELECT
                            JSON_OBJECT(
                                'primera', awards.primera,
                                'segunda', awards.segunda,
                                'tercera', awards.tercera,
                                'pick3', awards.pick3,
                                'pick4', awards.pick4
                            )
                        FROM awards
                        WHERE awards.idLoteria = l.id AND awards.created_at BETWEEN '$fechaInicial' AND '$fechaFinal' limit 1
                    ) AS dataNumerosGanadores
                    FROM lotteries AS l
                    WHERE l.status = 1
                ) AS lo
                GROUP BY lo.id
            ) AS lo
        ");            
    
                    
        // $loterias = Lotteries::on($datos['servidor'])
        //     ->selectRaw('
        //         id, 
        //         descripcion, 
        //         abreviatura,
        //         (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as ventas,
        //         (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as premios,
        //         (select substring(numeroGanador, 1, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as primera,
        //         (select substring(numeroGanador, 3, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as segunda,
        //         (select substring(numeroGanador, 5, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as tercera,
        //         (select pick3 from awards where idLoteria = lotteries.id and created_at between ? and ?) as pick3,
        //         (select pick4 from awards where idLoteria = lotteries.id and created_at between ? and ?) as pick4
        //         ', [$datos['idBanca'], $fechaInicial, $fechaFinal, //Parametros para ventas
        //             $datos['idBanca'], $fechaInicial, $fechaFinal, //Parametros para premios
        //             $fechaInicial, $fechaFinal, //Parametros primera
        //             $fechaInicial, $fechaFinal, //Parametros segunda
        //             $fechaInicial, $fechaFinal, //Parametros tercera
        //             $fechaInicial, $fechaFinal, //Parametros pick3
        //             $fechaInicial, $fechaFinal //Parametros pick4
        //             ])
        //     ->where('lotteries.status', '=', '1')
        //     ->get();

        //             $loterias = collect($loterias)->map(function($d) use($datos, $fechaInicial, $fechaFinal){
        //                 $datosComisiones = Commissions::on($datos['servidor'])->where(['idBanca' => $datos['idBanca'], 'idLoteria' => $d['id']])->first();
        //                 $comisionesMonto = 0;
        //                 $idVentasDeEstaBanca = Sales::on($datos['servidor'])->select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $datos['idBanca'])->whereNotIn('status', [0,5])->get();
        //                 $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
        //                     return $id->id;
        //                 });
        //                 if($datosComisiones != null){
        //                     if($datosComisiones['directo'] > 0){
        //                         $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Directo')->first();
        //                         if($sorteo != null){
        //                             //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
        //                             $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //                                 ->whereIn('idVenta', $idVentasDeEstaBanca)
        //                                 ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
        //                                 ->sum('monto');
        //                         }
        //                     }
            
        //                     if($datosComisiones['pale'] > 0){
        //                         $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Pale')->first();
        //                         if($sorteo != null){
        //                             //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
        //                             $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //                                 ->whereIn('idVenta', $idVentasDeEstaBanca)
        //                                 ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
        //                                 ->sum('monto');
        //                         }
        //                     }
            
        //                     if($datosComisiones['tripleta'] > 0){
        //                         $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Tripleta')->first();
        //                         if($sorteo != null){
        //                             //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
        //                             $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //                                 ->whereIn('idVenta', $idVentasDeEstaBanca)
        //                                 ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
        //                                 ->sum('monto');
        //                         }
        //                     }
            
        //                     if($datosComisiones['superPale'] > 0){
        //                         $sorteo = Draws::on($datos['servidor'])->whereDescripcion('Super pale')->first();
        //                         if($sorteo != null){
        //                             //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
        //                             $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::on($datos['servidor'])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        //                                 ->whereIn('idVenta', $idVentasDeEstaBanca)
        //                                 ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
        //                                 ->sum('monto');
        //                         }
        //                     }
        //                 }
        //                 $comisionesMonto = round($comisionesMonto);
        //                 if($d->ventas == null)
        //                     $d->ventas = 0;
        //                 if($d->premios == null)
        //                     $d->premios = 0;
        //                 if($d->primera == null)
        //                     $d->primera = "";
        //                 if($d->segunda == null)
        //                     $d->segunda = "";
        //                 if($d->tercera == null)
        //                     $d->tercera = "";
        //                 return ['id' => $d->id, 'descripcion' => $d->descripcion, 'abreviatura' => $d->abreviatura, 'comisiones' => $comisionesMonto, 'ventas' => $d->ventas, 'premios' => $d->premios, 'primera' => $d->primera, 'segunda' => $d->segunda, 'tercera' => $d->tercera,'pick3' => $d->pick3, 'pick4' => $d->pick4, 'neto' => ($d->ventas) - ((int)$d->premios + $comisionesMonto)];
        //             });
    
      
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
            'fecha' => $fechaParaImprimirChadreMovil,
            'fechaInicial' => "$fechaInicial",
            'fechaFinal' => "$fechaFinal",
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
                    ->limit(100)
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
        $monitoreo = [];
        if(isset($datos['fechaFinal'])){
            $fecha = getdate(strtotime($datos['fecha']));
            $fechaF = getdate(strtotime($datos['fechaFinal']));
            $fechaInicial = $fecha['year'].'-'.\App\Classes\Helper::toDosDigitos(strval($fecha['mon'])).'-'. \App\Classes\Helper::toDosDigitos(strval($fecha['mday'])) . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'. \App\Classes\Helper::toDosDigitos(strval($fechaF['mon'])) .'-'. \App\Classes\Helper::toDosDigitos(strval($fechaF['mday'])) . ' 23:50:00';
        

            // $monitoreo = Sales::on($datos["servidor"])->select('id', 'idTicket', 'idBanca', 'total', 'status', 'premios', 'created_at')->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
            //         ->where('idBanca', $datos['idBanca'])
            //         ->where('status', '!=', '5')
            //         ->orderBy('id', 'desc')
            //         ->get();
            $consulta = "";
            $monitoreo = \DB::connection($datos["servidor"])->select("select 
            s.id, s.total, s.pagado, s.status, s.idTicket, s.created_at, 
            t.id, t.codigoBarra, s.idUsuario, u.usuario, b.codigo, sum(sd.premio) as premio, 
            sum(IF(sd.pagado = 0, sd.premio, 0)) as montoAPagar, 
            sum(IF(sd.pagado = 1, sd.premio, 0)) as montoPagado, 
            (select cancellations.razon from cancellations where cancellations.idTicket = s.idTicket) as razon, 
            (select users.usuario from users where users.id = (select cancellations.idUsuario from cancellations where cancellations.idTicket = s.idTicket)) as usuarioCancelacion, 
            (select cancellations.created_at from cancellations where cancellations.idTicket = s.idTicket) as fechaCancelacion 
            from sales s  inner join salesdetails sd on s.id = sd.idVenta 
            inner join users u on u.id = s.idUsuario 
            inner join tickets t on t.id = s.idTicket 
            inner join branches b on b.id = s.idBanca 
            where s.created_at between '{$fechaInicial}' and '{$fechaFinal}' and s.status != 5 and s.idBanca = {$datos['idBanca']} {$consulta} 
            group by s.id, s.total, s.pagado, s.status, s.idTicket, t.id, t.codigoBarra, s.idUsuario, u.usuario, b.codigo, razon, fechaCancelacion, usuarioCancelacion 
            order by s.created_at desc");
        
        }else{
            $monitoreo = Sales::on($datos["servidor"])->select('id', 'idTicket', 'idBanca', 'total', 'status', 'premios', 'created_at')->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->where('idBanca', $datos['idBanca'])
                ->where('status', '!=', '5')
                ->orderBy('id', 'desc')
                ->get();
        }
    
        
    
       // return $ventas;
    
       if(isset($datos['fechaFinal']) == false)
            $monitoreo = collect($monitoreo)->map(function($m) use($datos){
                $codigo = Branches::on($datos["servidor"])->select('codigo')->whereId($m['idBanca'])->first();
                $codigoBarra = Tickets::on($datos["servidor"])->whereId($m['idTicket'])->first();
                return ['id' =>$m['id'], 'total' =>$m['total'], 'status' =>$m['status'], 'idTicket' =>$m['idTicket'], 'premios' =>$m['premios'], 'created_at' => $m["created_at"], 'codigoBarra' =>$codigoBarra['codigoBarra'], 'idBanca' =>$m['idBanca'], 'codigo' =>$codigo['codigo']];
            });
    
        return Response::json([
            'monitoreo' => $monitoreo,
            'loterias' => Lotteries::on($datos["servidor"])->whereStatus(1)->get(),
            'bancas' => Branches::on($datos["servidor"])->select("id", "descripcion", "codigo")->whereStatus(1)->get(),
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
