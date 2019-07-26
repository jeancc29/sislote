<?php

namespace App\Http\Controllers;

use App\Sales;




use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\TicketClass;
use Illuminate\Support\Facades\DB;


// use Faker\Generator as Faker;
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

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        // $route = Route();
        //echo $controlador;
        
        
        // if(!strpos(Request::url(), '/api/')){
        //     return view('principal.index', compact('controlador', 'usuario'));
        // }

        
        $fechaActual = Carbon::now();
        $fecha6DiasAtras = $fechaActual->copy()->subDays(6);

        $fechaActual = $fechaActual->toDateString() . ' 23:00:00';
        $fecha6DiasAtras = $fecha6DiasAtras->toDateString() . ' 00:00:00';

        $daysSpanish = [
            1 => 'lun',
            2 => 'mar',
            3 => 'mié',
            4 => 'jue',
            5 => 'vie',
            6 => 'sáb',
            0 => 'dom',
        ];

        //VENTAS AGRUPADAS POR DIA PARA LA GRAFICA
        $ventasGrafica = Sales::select(DB::raw('DATE(created_at) as date, sum(subTotal) subTotal, sum(total) total, sum(premios) premios'))
            ->whereBetween('created_at', array($fecha6DiasAtras, $fechaActual))
            ->whereNotIn('status', [0,5])
            ->groupBy('date')
            //->orderBy('created_at', 'asc')
            ->get();

       
        $ventasGrafica = collect($ventasGrafica)->map(function($d) use($daysSpanish){
            $fecha = new Carbon($d['date']);
            $dia = $daysSpanish[$fecha->dayOfWeek] . ' ' . $fecha->day;
            return ["total" => $d['subTotal'], "neto" => $d['total'] - $d['premios'], "dia" => $dia];
        });

        // var_dump($ventasGrafica);
        // return ;

        //VENTAS Y PREMIOS AGRUPADOS POR LOTERIA
        $fechaInicial = Carbon::now();
        $fechaFinal = $fechaInicial->toDateString() . ' 23:00:00';
        $fechaInicial = $fechaInicial->toDateString() . ' 00:00:00';
        $loterias = Lotteries::
            selectRaw('
                id, 
                descripcion, 
                (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status not in(0,5) and sd.idLoteria = lotteries.id and s.created_at between ? and ?) as ventas,
                (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status not in(0,5) and sd.idLoteria = lotteries.id and s.created_at between ? and ?) as premios
                ', [$fechaInicial, $fechaFinal, //Parametros para ventas
                    $fechaInicial, $fechaFinal //Parametros para premios
                    ])
            ->where('lotteries.status', '=', '1')
            ->get();

        //Jugadas con mayores montos jugados en loterías disponibles
        $fecha = getdate();
        $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])->get();

        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
        $sorteos = Draws::all();
        $sorteos = collect($sorteos)->map(function($d) use($idVentas){
            $jugadasConMayoresMontesJugadosEnLoterias = Salesdetails::whereIn('idVenta', $idVentas)->where('idSorteo', $d['id'])->orderBy('monto', 'desc')->take(5)->get();
            $jugadasConMayoresMontesJugadosEnLoterias = collect($jugadasConMayoresMontesJugadosEnLoterias)->map(function($j){
                $loteria = Lotteries::whereId($j['idLoteria'])->first();
                return ['descripcion' => $loteria['descripcion'], 'abreviatura' => $loteria['abreviatura'], 'jugada' => $j['jugada'], 'monto' => $j['monto']];
            });
            return ['descripcion' => $d['descripcion'], 'jugadas' => $jugadasConMayoresMontesJugadosEnLoterias];
        });

        $idBancas = collect($ventas)->map(function($id){
            return $id['idBanca'];
        });

        $bancasConVentas = Branches::whereIn('id', $idBancas)->count();
        $bancasSinVentas = Branches::whereNotIn('id', $idBancas)->count();

        return view('dashboard.index', compact('controlador', 'ventasGrafica', 'loterias', 'sorteos', 'bancasConVentas', 'bancasSinVentas'));
        
    }


    public function indexPost()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        // $route = Route();
        //echo $controlador;
        
        

        
        $fechaActual = Carbon::now();
        $fecha6DiasAtras = $dt->copy()->subDays(6);

        $fechaActual = $fechaActual->toDateString();
        $fecha6DiasAtras = $fecha6DiasAtras->toDateString();

        $daysSpanish = [
            0 => 'lun',
            1 => 'mar',
            2 => 'mié',
            3 => 'jue',
            4 => 'vie',
            5 => 'sáb',
            6 => 'dom',
        ];

        $ventasGrafica = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->groupBy('created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        $ventasGrafica = collect($ventasGrafica)->map(function($d) use($daysSpanish){
            $fecha = new Carbon($d['created_at']);
            $dia = $daysSpanish[$fecha->dayOfWeek] . $fecha->day;
            return ["subTotal" => $d['subTotal'], "total" => $d['total'], "dia" => $dia];
        });
        
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
