<?php

namespace App\Http\Controllers;

use App\Realtime;
use App\Users;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

use App\Userssesions;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksgenerals;
use App\Blocksplays;
use App\Blocksplaysgenerals;
use App\Branches;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Frecuency;
use App\Automaticexpenses;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;
use App\Classes\Helper;

class RealtimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.maximoIdRealtime' => 'required',
        ])['datos'];
       // dd($data);

        

        $u = Users::where(['id' => $datos['idUsuario'], 'status' => 1])->first();


        if($u == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Usuario o contraseña incorrectos'
            ], 201);
            // return redirect('login')->withErrors([
            //     'usuario' => 'Usuario o contraseña incorrectos'
            // ]);
        }

        $realtime = Realtime::where('id', '>', $datos['maximoIdRealtime'])->get();
        $stocks = [];
        $blockslotteries = [];
        $Blocksgenerals = [];
        $blocksplays = [];
        $blocksplaysgenerals = [];
        if(count($realtime) > 0){
            $realtime = collect($realtime);
            list($stocks, $noStocks) = $realtime->partition(function($r){
                    return $r['tabla'] == 'stocks'; 
            });
            list($blockslotteries, $noBlockslotteries) = $realtime->partition(function($r){
                     return $r['tabla'] == 'blockslotteries'; 
             });
             list($Blocksgenerals, $noBlocksgenerals) = $realtime->partition(function($r){
                     return $r['tabla'] == 'blocksgenerals'; 
             });
             list($blocksplays, $noBlocksplays) = $realtime->partition(function($r){
                return $r['tabla'] == 'blocksplays'; 
             });

             list($blocksplaysgenerals, $noBlocksplaysgenerals) = $realtime->partition(function($r){
                return $r['tabla'] == 'blocksplaysgenerals'; 
            });

            $stocks = (count($stocks) > 0) ? $stocks->map(function($s){return $s['idAfectado'];}) : $stocks;
            $blockslotteries = (count($blockslotteries) > 0) ? $blockslotteries->map(function($s){return $s['idAfectado'];}) : $blockslotteries;
            $Blocksgenerals = (count($Blocksgenerals) > 0) ? $Blocksgenerals->map(function($s){return $s['idAfectado'];}) : $Blocksgenerals;
            $blocksplays = (count($blocksplays) > 0) ? $blocksplays->map(function($s){return $s['idAfectado'];}) : $blocksplays;
            $blocksplaysgenerals = (count($blocksplaysgenerals) > 0) ? $blocksplaysgenerals->map(function($s){return $s['idAfectado'];}) : $blocksplaysgenerals;

            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

            $stocks = Stock::whereIn('id', $stocks)->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get();
            $blockslotteries = Blockslotteries::whereIn('id', $blockslotteries)->get();
            $Blocksgenerals = Blocksgenerals::whereIn('id', $Blocksgenerals)->get();
            $blocksplays = Blocksplays::whereIn('id', $blocksplays)
            ->where('fechaDesde', '<=', $fechaInicial)
            ->where('fechaHasta', '>=', $fechaFinal)->get();
            $blocksplaysgenerals = Blocksplaysgenerals::whereIn('id', $blocksplaysgenerals)
            ->where('fechaDesde', '<=', $fechaInicial)
            ->where('fechaHasta', '>=', $fechaFinal)->get();
        }

        // if(Crypt::decryptString($u->password) != $datos['password']){
        //     return Response::json([
        //         'errores' => 1,
        //         'mensaje' => 'Contraseña incorrecta'
        //     ], 201);
        //     // return redirect('login')->withErrors([
        //     //     'password' => 'Contraseña incorrecta'
        //     // ]);
        // }

        
      
       return Response::json([
        'errores' => 0,
        'mensaje' => '',
        'maximoIdRealtime' => Realtime::max('id'),
        'hayCambios' => count($realtime) > 0 ? true : false,
        'stocks' => count($stocks) > 0 ? $stocks : null,
        'blockslotteries' => count($blockslotteries) > 0 ? $blockslotteries : null,
        'blocksgenerals' => count($Blocksgenerals) > 0 ? $Blocksgenerals : null,
        'blocksplays' => count($blocksplays) > 0 ? $blocksplays : null,
        'blocksplaysgenerals' => count($blocksplaysgenerals) > 0 ? $blocksplaysgenerals : null,
        ], 201);
    }

    public function todos()
    {
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.maximoIdRealtime' => 'required',
        ])['datos'];
       // dd($data);

        

        $u = Users::where(['id' => $datos['idUsuario'], 'status' => 1])->first();


        if($u == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Usuario o contraseña incorrectos'
            ], 201);
            // return redirect('login')->withErrors([
            //     'usuario' => 'Usuario o contraseña incorrectos'
            // ]);
        }

            $fecha = getdate();
  
       
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        

        $maxId = Realtime::max('id');
        $stocks = Stock::whereBetween('created_at', array($fechaInicial, $fechaFinal))->get();
        $blockslotteries = Blockslotteries::all();
        $Blocksgenerals = Blocksgenerals::all();
        $blocksplays = Blocksplays::whereStatus(1)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->get();
        $blocksplaysgenerals = Blocksplaysgenerals::whereStatus(1)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->get();
        $draws = Draws::whereStatus(1)->get();
        
      
       return Response::json([
        'errores' => 0,
        'mensaje' => '',
        'hayCambios' => true,
        'maximoIdRealtime' => $maxId,
        'stocks' => count($stocks) > 0 ? $stocks : null,
        'blockslotteries' => count($blockslotteries) > 0 ? $blockslotteries : null,
        'blocksgenerals' => count($Blocksgenerals) > 0 ? $Blocksgenerals : null,
        'blocksplays' => count($blocksplays) > 0 ? $blocksplays : null,
        'blocksplaysgenerals' => count($blocksplaysgenerals) > 0 ? $blocksplaysgenerals : null,
        'draws' => count($draws) > 0 ? $draws : null,
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
     * @param  \App\Realtime  $realtime
     * @return \Illuminate\Http\Response
     */
    public function show(Realtime $realtime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Realtime  $realtime
     * @return \Illuminate\Http\Response
     */
    public function edit(Realtime $realtime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Realtime  $realtime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Realtime $realtime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Realtime  $realtime
     * @return \Illuminate\Http\Response
     */
    public function destroy(Realtime $realtime)
    {
        //
    }
}
