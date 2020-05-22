<?php

namespace App\Http\Controllers;

use App\Sales;




use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\DashboardClass;
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
use App\Coins;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;
use Firebase\JWT\JWT;


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
        
        if(!strpos(Request::url(), '/api/')){

            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }
            
            $sorteos = Draws::all();

            //VENTAS AGRUPADAS POR DIA PARA LA GRAFICA
            $dashboard = new DashboardClass(null,session("servidor"), session("idUsuario"), null);
            $ventasGrafica = $dashboard->ventasGrafica();
            
            //VENTAS Y PREMIOS AGRUPADOS POR LOTERIA
            $loterias = $dashboard->ventasPremiosPorLoteria();

            //Jugadas con mayores montos jugados en loterías disponibles
            $loteriasJugadasDashboard = $dashboard->todasLasJugadasPorLoteria();
            if($loteriasJugadasDashboard == null){
                $ventas = null;
                $loteriasJugadasDashboard = null;
                $sorteos = collect($sorteos)->map(function($d){
                    return ['descripcion' => $d['descripcion'], 'jugadas' => []];
                });
            }
        
            //Bancas con y sin ventas
           $bancasArray = $dashboard->bancaConYSinVentas();
           $bancasConVentas = $bancasArray['bancasConVentas'];
           $bancasSinVentas = $bancasArray['bancasSinVentas'];

            //Total premios y ventas
            $ventasYPremiosArray = $dashboard->totalVentasYPremiosLoterias($loterias);
            $totalVentasLoterias = $ventasYPremiosArray['totalVentasLoterias'];
            $totalPremiosLoterias = $ventasYPremiosArray['totalPremiosLoterias'];

            return view('dashboard.index', compact('controlador', 'ventasGrafica', 'loterias', 'sorteos', 'bancasConVentas', 'bancasSinVentas', 'totalVentasLoterias', 'totalPremiosLoterias', 'loteriasJugadasDashboard'));
        }
        else{
            $datos = request()->validate([
                // 'fecha' => 'required',
                // 'idUsuario' => 'required',
                // 'idMoneda' => 'required',
                // 'servidor' => 'required',
                'token' => ''
            ]);

            try {
                // $datos = JWT::decode($datos['token'], \config('data.apiKey'), array('HS256'));
                // $datos = json_decode(json_encode($datos), true);
                $datos = \Helper::jwtDecode($datos["token"]);
            } catch (\Throwable $th) {
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Token incorrecto'
                ], 201);
            }

            
            $sorteos = Draws::on($datos["servidor"])->get();

            //VENTAS AGRUPADAS POR DIA PARA LA GRAFICA
            $dashboard = new DashboardClass($datos['fecha'], $datos["servidor"], $datos['idUsuario'], $datos['idMoneda']);
            $ventasGrafica = $dashboard->ventasGrafica();
            
            //VENTAS Y PREMIOS AGRUPADOS POR LOTERIA
            $loterias = $dashboard->ventasPremiosPorLoteria();

            //Jugadas con mayores montos jugados en loterías disponibles
            $loteriasJugadasDashboard = $dashboard->todasLasJugadasPorLoteria();
            if($loteriasJugadasDashboard == null){
                $ventas = null;
                $loteriasJugadasDashboard = null;
                $sorteos = collect($sorteos)->map(function($d){
                    return ['descripcion' => $d['descripcion'], 'jugadas' => []];
                });
            }
        
            //Bancas con y sin ventas
           $bancasArray = $dashboard->bancaConYSinVentas();
           $bancasConVentas = $bancasArray['bancasConVentas'];
           $bancasSinVentas = $bancasArray['bancasSinVentas'];

            //Total premios y ventas
            $ventasYPremiosArray = $dashboard->totalVentasYPremiosLoterias($loterias);
            $totalVentasLoterias = $ventasYPremiosArray['totalVentasLoterias'];
            $totalPremiosLoterias = $ventasYPremiosArray['totalPremiosLoterias'];

            // return view('dashboard.index', compact('controlador', 'ventasGrafica', 'loterias', 'sorteos', 'bancasConVentas', 'bancasSinVentas', 'totalVentasLoterias', 'totalPremiosLoterias', 'loteriasJugadasDashboard'));

            return Response::json([
                'ventasGrafica' => $ventasGrafica,
                'loterias' => $loterias,
                'sorteos' => $sorteos,
                'bancasConVentas' => $bancasConVentas,
                'bancasSinVentas' => $bancasSinVentas,
                'totalVentasLoterias' => $totalVentasLoterias,
                'totalPremiosLoterias' => $totalPremiosLoterias,
                'loteriasJugadasDashboard' => $loteriasJugadasDashboard,
                'monedas' => Coins::on($datos["servidor"])->orderBy('pordefecto', 1)->get(),
            ], 201);
        }
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
