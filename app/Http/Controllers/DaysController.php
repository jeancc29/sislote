<?php

namespace App\Http\Controllers;

use App\Days;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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
        //
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
