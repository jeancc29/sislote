<?php

namespace App\Http\Controllers;

use App\Days;
use Illuminate\Support\Facades\DB;


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

class DaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public function index()
    {
        // $ventas = Sales::select(DB::raw('DATE(sales.created_at) as fecha, 
        //             sum(sales.subTotal) subTotal, 
        //             sum(sales.total) total, 
        //             sum(sales.premios) premios, 
        //             sum(descuentoMonto)  as descuentoMonto,
        //             sum(salesdetails.comision) as comisiones'))
        //     ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
        //     ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
        //     ->whereNotIn('sales.status', [0,5])
        //     ->groupBy('fecha')
        //     //->orderBy('created_at', 'asc')
        //     ->get();
    }

    public function test1()
    {
        $time_start = $this->microtime_float();

        // Sleep for a while
        $d = Days::whereId(1);
    
       
        
        $time_end = $this->microtime_float();
        $time = $time_end - $time_start;

        return $time;
    }

    
    public function test2()
    {
        $time_start = $this->microtime_float();

        // Sleep for a while
        // $d = DB::raw('select * from days where id = 1');
        $d = DB::table('users')->select('id');
    
       
        echo 
        
        $time_end = $this->microtime_float();
        $time = $time_end - $time_start;

        return $time;
    }

    
    public function test3()
    {
        // $time_start = $this->microtime_float();
        $tiempo_inicial = microtime(true);
        $idBanca = 0;
        

        

        
            $idBanca = Helper::getIdBanca(1);
            // $idBanca = Branches::where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first();
            // if($idBanca != null)
            //     $idBanca = $idBanca->id;
        
        

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
    
    
        $usuario = Users::where(['id' => 1, 'status' => 1])->first();
    
            $arreglo = array(
            'idVenta' => Helper::createIdVentaTemporal($idBanca),
            'loterias' => ($usuario != null) ? Helper::loteriasOrdenadasPorHoraCierre($usuario) : [],
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas),
            'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            'idUsuario' => 1,
            'idBanca' => $idBanca );


        //     $time_end = $this->microtime_float();
        // $time = $time_end - $time_start;

        $tiempo_final = microtime(true);
	$tiempo = $tiempo_final - $tiempo_inicial;

        return $tiempo;
        
    }

    public function test4()
    {
        // $time_start = $this->microtime_float();
 
        $tiempo_inicial = microtime(true);

        $idBanca = 0;
        

      

        $data = Helper::indexPost(1);
        

        $arreglo = array(
            'idVenta' => $data[0]->idVentaHash,
            'loterias' => ($data[0]->loterias != null) ? json_decode($data[0]->loterias) : [],
            'caracteristicasGenerales' =>  ($data[0]->caracteristicasGenerales != null) ? json_decode($data[0]->caracteristicasGenerales) : [],
            'total_ventas' => $data[0]->total_ventas,
            'total_jugadas' => $data[0]->total_jugadas,
            'ventas' => ($data[0]->ventas != null) ? json_decode($data[0]->ventas) : [],
            'bancas' => ($data[0]->bancas != null) ? json_decode($data[0]->bancas) : [],
            'idUsuario' => 1,
            'idBanca' => $data[0]->idBanca
        );


        // $time_end = $this->microtime_float();
        // $time = $time_end - $time_start;

        $tiempo_final = microtime(true);
	$tiempo = $tiempo_final - $tiempo_inicial;

        return $tiempo;
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
     * @param  \App\Days  $days
     * @return \Illuminate\Http\Response
     */
    public function show(Days $days)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Days  $days
     * @return \Illuminate\Http\Response
     */
    public function edit(Days $days)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Days  $days
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Days $days)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Days  $days
     * @return \Illuminate\Http\Response
     */
    public function destroy(Days $days)
    {
        //
    }
}
