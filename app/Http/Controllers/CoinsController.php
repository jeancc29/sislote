<?php

namespace App\Http\Controllers;

use App\Coins;
use Request;

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;


use Faker\Generator as Faker;
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
use App\Http\Resources\EntityResource;

use Illuminate\Support\Facades\Crypt;
use App\Classes\Helper;

class CoinsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        if(!strpos(Request::url(), '/api/')){
            return view('monedas.index', compact('controlador'));
        }

        $datos = request()->validate([
            "token" => ''
        ]);

        try {
            $datos = \Helper::jwtDecode($datos["token"]);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
        // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
    
        return Response::json([
            'monedas' => Coins::on($datos["servidor"])->get(),
        ], 201);
    }

    public function pordefecto(Coins $coins)
    {
        // $datos = request()->validate([
        //     'datos.id' => 'required'
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

        Coins::on($datos["servidor"])->where('id', '>', 0)->update(['pordefecto' => 0]);
        $coin = Coins::on($datos["servidor"])->whereId($datos['id'])->first();
        $coin->pordefecto = 1;
        $coin->save();

        // if($entidad != null){
        //     $entidad->status = 2;
        //     $entidad->save();
        // }

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha establecido por defecto correctamente',
            'monedas' => Coins::on($datos["servidor"])->get(),
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
        // $datos = request()->validate([
        //     'datos.id' => 'required',
        //     'datos.descripcion' => 'required',
        //     'datos.abreviatura' => 'required',
        //     'datos.permiteDecimales' => 'required',
        //     'datos.equivalenciaDeUnDolar' => 'required',
        //     'datos.color' => 'required',
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

        $entidad = Coins::on($datos["servidor"])->whereId($datos['id'])->first();

        if($entidad != null){
            $entidad->descripcion = $datos['descripcion'];
            $entidad->abreviatura = $datos['abreviatura'];
            $entidad->permiteDecimales = $datos['permiteDecimales'];
            $entidad->equivalenciaDeUnDolar = $datos['equivalenciaDeUnDolar'];
            $entidad->color = $datos['color'];
            $entidad->save();
        }else{
            Coins::on($datos["servidor"])->create([
                'descripcion' => $datos['descripcion'],
                'abreviatura' => $datos['abreviatura'],
                'permiteDecimales' => $datos['permiteDecimales'],
                'equivalenciaDeUnDolar' => $datos['equivalenciaDeUnDolar'],
                'color' => $datos['color']
            ]);
        }
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'monedas' => Coins::on($datos["servidor"])->get(),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coins  $coins
     * @return \Illuminate\Http\Response
     */
    public function show(Coins $coins)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coins  $coins
     * @return \Illuminate\Http\Response
     */
    public function edit(Coins $coins)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coins  $coins
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coins $coins)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coins  $coins
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coins $coins)
    {
        // $datos = request()->validate([
        //     'datos.id' => 'required'
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

        $entidad = Coins::on($datos["servidor"])->whereId($datos['id'])->delete();

        // if($entidad != null){
        //     $entidad->status = 2;
        //     $entidad->save();
        // }
        

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha eliminado correctamente',
            'monedas' => Coins::on($datos["servidor"])->get(),
        ], 201);
    }
}
