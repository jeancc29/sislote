<?php

namespace App\Http\Controllers;
use App\Userssesions;
use App\Users;
use Request;
use Illuminate\Support\Facades\Route; 
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
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Branches;
use App\Roles;
use App\Commissions;
use App\Permissions;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Classes\Helper;

class UserssesionsController extends Controller
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
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Ver inicios de sesion") == true){
                return redirect()->route('sinpermiso');
            }
            return view('usuarios.sesiones', compact('controlador'));
        }
    }


    public function buscar()
    {
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.fecha' => 'required'
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

        $fecha = getdate(strtotime($datos['fecha']));
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

        
        $usuarios = Userssesions::on($datos["servidor"])->select('idUsuario')->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        ->groupBy('idUsuario')
        ->get();

        $usuarios = collect($usuarios)->map(function($d){
            return $d->idUsuario;
        });

        $usuarios = Users::on($datos["servidor"])->whereIn('id', $usuarios)->get();

        $sesiones = collect($usuarios)->map(function($d) use($fechaInicial, $fechaFinal, $datos){
           
            $primerInicioSesionPC = Userssesions::on($datos["servidor"])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                ->where(['idUsuario' => $d['id'], 'esCelular' => 0])->min('created_at');
            $ultimoInicioSesionPC = Userssesions::on($datos["servidor"])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                ->where(['idUsuario' => $d['id'], 'esCelular' => 0])->max('created_at');
            $primerInicioSesionCelular = Userssesions::on($datos["servidor"])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                ->where(['idUsuario' => $d['id'], 'esCelular' => 1])->min('created_at');
            $ultimoInicioSesionCelular = Userssesions::on($datos["servidor"])->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                ->where(['idUsuario' => $d['id'], 'esCelular' => 1])->max('created_at');
            
            
            if($primerInicioSesionPC == null){
                $primerInicioSesionPC = "-";
                $ultimoInicioSesionPC = "-";
            }else{
                $fecha = new Carbon($primerInicioSesionPC);
                $hora = $fecha->format('g:i A');
                $fecha = $fecha->toDateString();
                $primerInicioSesionPC = $fecha . " " . $hora;

                $fecha = new Carbon($ultimoInicioSesionPC);
                $hora = $fecha->format('g:i A');
                $fecha = $fecha->toDateString();
                $ultimoInicioSesionPC = $fecha . " " . $hora;
            }

            if($primerInicioSesionCelular == null){
                $primerInicioSesionCelular = "-";
                $ultimoInicioSesionCelular = "-";
            }else{
                $fecha = new Carbon($primerInicioSesionCelular);
                $hora = $fecha->format('g:i A');
                $fecha = $fecha->toDateString();
                $primerInicioSesionCelular = $fecha . " " . $hora;

                $fecha = new Carbon($ultimoInicioSesionCelular);
                $hora = $fecha->format('g:i A');
                $fecha = $fecha->toDateString();
                $ultimoInicioSesionCelular = $fecha . " " . $hora;
            }

           $banca = Branches::on($datos["servidor"])->where('idUsuario', $d['id'])->first();
           if($banca != null){
               $banca = $banca->descripcion;
           }else{
            $banca = "-";
           }
            return ['usuario' => $d['usuario'], 'banca' => $banca, 'primerInicioSesionPC' => $primerInicioSesionPC, 'ultimoInicioSesionPC' => $ultimoInicioSesionPC, 'primerInicioSesionCelular' => $primerInicioSesionCelular, 'ultimoInicioSesionCelular' => $ultimoInicioSesionCelular];

            
        });

        return Response::json([
            'sesiones' => $sesiones,
            'fecha' => $fecha
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
     * @param  \App\Userssesions  $userssesions
     * @return \Illuminate\Http\Response
     */
    public function show(Userssesions $userssesions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Userssesions  $userssesions
     * @return \Illuminate\Http\Response
     */
    public function edit(Userssesions $userssesions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Userssesions  $userssesions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Userssesions $userssesions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Userssesions  $userssesions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Userssesions $userssesions)
    {
        //
    }
}
