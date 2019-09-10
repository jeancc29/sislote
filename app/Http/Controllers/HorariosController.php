<?php

namespace App\Http\Controllers;

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
            return view('horarios.index', compact('controlador'));
        }
        
        
        
        return Response::json([
            'loterias' => LotteriesResource::collection(Lotteries::whereStatus(1)->get()),
            'dias' => Days::all()
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
        $datos = request()->validate([
            'datos.loterias' => 'required',
            'datos.idUsuario' => 'required'
        ])['datos'];
    
        // return Response::json([
        //     'errores' => 0,
        //     'mensaje' => $datos['loterias']
        // ], 201);
    
    
        /********************* DIAS ************************/
            //Eliminamos los dias para luego agregarlos nuevamentes
           
            foreach($datos['loterias'] as $d){
                $loteria = Lotteries::whereId($d["id"])->first();
                $loteria->dias()->detach();
                $loteria->save();
            }
    
            foreach($datos['loterias'] as $d){
                $loteria = Lotteries::whereId($d["id"])->first();
    
                if($d["lunes"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Lunes")->first()->id, 'horaApertura' => $d["lunes"]["aperturaGuardar"], 'horaCierre' => $d["lunes"]["cierreGuardar"]];
                    $loteria->dias()->attach([$horario]);
                }
                
                if($d["martes"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Martes")->first()->id, 'horaApertura' => $d["martes"]["aperturaGuardar"], 'horaCierre' => $d["martes"]["cierreGuardar"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["miercoles"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Miercoles")->first()->id, 'horaApertura' => $d["miercoles"]["aperturaGuardar"], 'horaCierre' => $d["miercoles"]["cierreGuardar"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["jueves"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Jueves")->first()->id, 'horaApertura' => $d["jueves"]["aperturaGuardar"], 'horaCierre' => $d["jueves"]["cierreGuardar"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["viernes"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Viernes")->first()->id, 'horaApertura' => $d["viernes"]["aperturaGuardar"], 'horaCierre' => $d["viernes"]["cierreGuardar"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["sabado"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Sabado")->first()->id, 'horaApertura' => $d["sabado"]["aperturaGuardar"], 'horaCierre' => $d["sabado"]["cierreGuardar"]];
                    $loteria->dias()->attach([$horario]);
                }
    
                if($d["domingo"]["status"] == 1){
                    $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Domingo")->first()->id, 'horaApertura' => $d["domingo"]["aperturaGuardar"], 'horaCierre' => $d["domingo"]["cierreGuardar"]];
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
