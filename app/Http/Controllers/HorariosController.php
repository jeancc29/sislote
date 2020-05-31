<?php

namespace App\Http\Controllers;
use App\Classes\Helper;
use App\Days;
use Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Response;


use Faker\Generator as Faker;
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


class HorariosController extends Controller
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
            if(!(new Helper)->existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar horarios de loterias") == true){
                return redirect()->route('principal');
            }
            return view('horarios.index', compact('controlador'));
        }
        
        $datos = request()->validate([
            'token' => '',
        ]);
        // $datos = \Helper::jwtDecode($datos["token"]);
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
        
        return Response::json([
            'loterias' => LotteriesResource::collection(Lotteries::on($datos["servidor"])->whereStatus(1)->get())->servidor($datos["servidor"]),
            'dias' => Days::on($datos["servidor"])->get()
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
        //     'datos.loterias' => 'required',
        //     'datos.idUsuario' => 'required'
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
    
    
        /********************* DIAS ************************/
            //Eliminamos los dias para luego agregarlos nuevamentes
           
            foreach($datos['loterias'] as $d){
                $loteria = Lotteries::on($datos["servidor"])->whereId($d["id"])->first();
                $loteria->dias()->detach();
                $loteria->save();
            }
    
            foreach($datos['loterias'] as $d){
                $loteria = Lotteries::on($datos["servidor"])->whereId($d["id"])->first();
    
                if($d["lunes"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Lunes")->first()->id, 'horaApertura' => $d["lunes"]["aperturaGuardar"], 'horaCierre' => $d["lunes"]["cierreGuardar"], 'minutosExtras' => $d["lunes"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
                
                if($d["martes"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Martes")->first()->id, 'horaApertura' => $d["martes"]["aperturaGuardar"], 'horaCierre' => $d["martes"]["cierreGuardar"], 'minutosExtras' => $d["martes"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["miercoles"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Miercoles")->first()->id, 'horaApertura' => $d["miercoles"]["aperturaGuardar"], 'horaCierre' => $d["miercoles"]["cierreGuardar"], 'minutosExtras' => $d["miercoles"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["jueves"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Jueves")->first()->id, 'horaApertura' => $d["jueves"]["aperturaGuardar"], 'horaCierre' => $d["jueves"]["cierreGuardar"], 'minutosExtras' => $d["jueves"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["viernes"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Viernes")->first()->id, 'horaApertura' => $d["viernes"]["aperturaGuardar"], 'horaCierre' => $d["viernes"]["cierreGuardar"], 'minutosExtras' => $d["viernes"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["sabado"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Sabado")->first()->id, 'horaApertura' => $d["sabado"]["aperturaGuardar"], 'horaCierre' => $d["sabado"]["cierreGuardar"], 'minutosExtras' => $d["sabado"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["domingo"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::on($datos["servidor"])->whereDescripcion("Domingo")->first()->id, 'horaApertura' => $d["domingo"]["aperturaGuardar"], 'horaCierre' => $d["domingo"]["cierreGuardar"], 'minutosExtras' => $d["domingo"]["minutosExtras"]];
                    $loteria->dias()->attach([$horario]);
                }
            }
    
            
    
    
         
        return Response::json([
            'errores' => 0,
            'mensaje' => "Se ha guardado correctamente"
        ], 201);
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
