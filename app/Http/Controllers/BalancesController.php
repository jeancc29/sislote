<?php

namespace App\Http\Controllers;

use App\Balances;
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
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Branches;
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Types;
use App\Coins;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BalancesController extends Controller
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

            
            $usuario = Users::whereId(session("idUsuario"))->first();
            if(!$usuario->tienePermiso("Ver lista de balances de bancas") == true){
                return redirect()->route('sinpermiso');
            }

            $monedas = Coins::orderBy('pordefecto', 1)->get();
            return view('balances.index', compact('controlador', 'usuario', 'monedas'));
        }



        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.fechaHasta' => 'required'
        ])['datos'];
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Ver lista de balances de bancas") == true){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }

        $bancas = DB::table('branches')
            ->select('branches.id', 'branches.descripcion', 'branches.idMoneda', 'branches.dueno', 'users.usuario')
            ->join('users', 'branches.idUsuario', '=', 'users.id')
            ->where('branches.status', 1)->get();
        
        $bancas = collect($bancas)->map(function($b) use($datos){
            return ['descripcion' => $b->descripcion, 'idMoneda' => $b->idMoneda, 'dueno' => $b->dueno, 'usuario' => $b->usuario, 'balance' => Helper::saldoPorFecha($b->id, 1, $datos['fechaHasta']), 'prestamo' => 0];
        });

        $monedas = Coins::orderBy('pordefecto', 1)->get();

        return Response::json([
            'errores' => 0,
            'bancas' => $bancas,
            'monedas' => $monedas,
        ], 201);
    }




    public function bancos()
    {
        $controlador = Route::getCurrentRoute()->getName();
        
        if(!strpos(Request::url(), '/api/')){
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }

            
            $usuario = Users::whereId(session("idUsuario"))->first();
            if(!$usuario->tienePermiso("Ver lista de balances de bancos") == true){
                return redirect()->route('sinpermiso');
            }
            return view('balances.bancos', compact('controlador', 'usuario'));
        }



        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.fechaHasta' => 'required'
        ])['datos'];
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Ver lista de balances de bancos") == true){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }

        $idTipoEntidad = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
        $bancas = DB::table('entities')
            ->select('entities.id', 'entities.nombre')
            ->join('types', 'entities.idTipo', '=', 'types.id')
            ->where(['entities.status' => 1, 'types.id' => $idTipoEntidad->id ])->get();
        
        $bancas = collect($bancas)->map(function($b) use($datos){
            return ['nombre' => $b->nombre, 'balance' => Helper::saldoPorFecha($b->id, 2, $datos['fechaHasta']), 'prestamo' => 0];
        });

        return Response::json([
            'errores' => 0,
            'bancas' => $bancas
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
     * @param  \App\Balances  $balances
     * @return \Illuminate\Http\Response
     */
    public function show(Balances $balances)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Balances  $balances
     * @return \Illuminate\Http\Response
     */
    public function edit(Balances $balances)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Balances  $balances
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Balances $balances)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Balances  $balances
     * @return \Illuminate\Http\Response
     */
    public function destroy(Balances $balances)
    {
        //
    }
}
