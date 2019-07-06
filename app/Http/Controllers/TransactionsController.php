<?php

namespace App\Http\Controllers;
use App\Classes\Helper;

use App\transactions;
use Request;

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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
use App\Types;
use App\Entity;
use App\Transactionsgroups;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;
use App\Http\Resources\EntityResource;
use App\Http\Resources\TransactionsResource;
use App\Http\Resources\TransactionsgroupsResource;

use Illuminate\Support\Facades\Crypt;

class TransactionsController extends Controller
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
            $u = Users::whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar transacciones") == true){
                return Redirect::back();
            }
            return view('transacciones.index', compact('controlador'));
        }


        
       
        

        $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
        // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
        
    
        $fechaDesde = Carbon::now();
        $fechaHasta = Carbon::now();

        $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
        $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

        $t = transactions::whereBetween('created_at', array($fechaDesde, $fechaHasta))
        ->whereStatus(1)
        ->get();
        
        $tipos = Types::where(['renglon' => 'entidad', 'status' => 1])->get();
        $tipos = collect($tipos)->map(function($d){
            $entidades = null;
            if($d->descripcion == "Banca"){
                $entidades = Branches::whereStatus(1)->get();
                $entidades = collect($entidades)->map(function($d){
                    return ['id' => $d->id, 'descripcion' => $d->descripcion];
                });
            }
            else if($d->descripcion == "Banco" || $d->descripcion == "Otros"){
                $entidades = Entity::whereStatus(['status' => 1, 'idTipo' => $d->id])->get();
                $entidades = collect($entidades)->map(function($d){
                    return ['id' => $d->id, 'descripcion' => $d->nombre];
                });
            }
            return ['id' => $d['id'], 'descripcion' => $d['descripcion'],'renglon' => $d['renglon'], 'entidades' => $entidades];
        });

        return Response::json([
            'bancas' => Branches::whereStatus(1)->get(),
            'entidades' => $tipos,
            'tipos' => Types::whereRenglon('transaccion')->whereStatus(1)->get(),
            'transacciones' => TransactionsResource::collection($t),
            'usuarios' => Users::whereStatus(1)->get()
        ], 201);
    }
    public function saldo()
    {
        $datos = request()->validate([
            'datos.id' => 'required',
            'datos.es_banca' => 'required',
        ])['datos'];

        $saldo_inicial = 0;

        if($datos["es_banca"] == 1){
            $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $debito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])->sum('debito');
            $credito =  transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])->sum('credito');
            $saldo_inicial = $debito - $credito;
        }else{
            $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
            $debito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])->sum('debito');
            $credito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])->sum('credito');
            $saldo_inicial = $credito - $debito;
        }

        return Response::json([
            'saldo_inicial' => $saldo_inicial,
            'tipos' => Types::whereRenglon('entidad')->whereIn('descripcion', ['Banco', 'Otros'])->get()
        ], 201);
    }

    public function buscar()
    {
        $datos = request()->validate([
            'datos.fechaDesde' => 'required',
            'datos.fechaHasta' => 'required',
        ])['datos'];

        $fechaDesde = new Carbon($datos['fechaDesde']);
        $fechaHasta = new Carbon($datos['fechaHasta']);

        $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
        $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

        $t = Transactionsgroups::whereBetween('created_at', array($fechaDesde, $fechaHasta))
        ->get();

        return Response::json([
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'grupos' => TransactionsgroupsResource::collection($t)
        ], 201);
    }

    public function grupo()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        if(!strpos(Request::url(), '/api/')){
            return view('transacciones.grupo', compact('controlador'));
        }

        

        $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
        // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
        
    
        $fechaDesde = Carbon::now();
        $fechaHasta = Carbon::now();

        $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
        $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

        $t = Transactionsgroups::whereBetween('created_at', array($fechaDesde, $fechaHasta))
        ->get();
        
    
        return Response::json([
            'bancas' => Branches::whereStatus(1)->get(),
            'entidades' => Entity::whereStatus(1)->get(),
            'tipos' => Types::whereRenglon('transaccion')->whereIn('descripcion', ['Ajuste', 'Cobro', 'Pago'])->get(),
            'grupos' => TransactionsgroupsResource::collection($t)
        ], 201);
    }
    public function buscarTransaccion()
    {
        $datos = request()->validate([
            'datos.fechaDesde' => 'required',
            'datos.fechaHasta' => 'required',
            'datos.idTipoEntidad' => 'required',
            'datos.idEntidad' => 'required',
            'datos.idTipo' => 'required',
            'datos.idUsuario' => 'required',
        ])['datos'];

        $condicionTipoEntidad = '=';
        $condicionEntidad = '=';
        $condicionTipo = '=';
        $condicionUsuario = '=';

        $fechaDesde = new Carbon($datos['fechaDesde']);
        $fechaHasta = new Carbon($datos['fechaHasta']);

        $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
        $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

        if($datos['idTipoEntidad'] == 0)
            $condicionTipoEntidad = '!=';
        if($datos['idEntidad'] == 0)
            $condicionEntidad = '!=';
        if($datos['idTipo'] == 0)
            $condicionTipo = '!=';
        if($datos['idUsuario'] == 0)
            $condicionUsuario = '!=';

        $t = transactions::whereBetween('created_at', array($fechaDesde, $fechaHasta))
            ->where('idUsuario', $condicionUsuario, $datos['idUsuario'])
            ->where('idTipoEntidad1', $condicionTipoEntidad, $datos['idTipoEntidad'])
            ->where('idEntidad1', $condicionEntidad, $datos['idEntidad'])
            ->where('idTipo', $condicionTipo, $datos['idTipo'])
            ->whereStatus(1)
        ->get();

        return Response::json([
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'transacciones' => TransactionsResource::collection($t)
        ], 201);
    }

    // public function grupo()
    // {
    //     $controlador = Route::getCurrentRoute()->getName(); 
    //     if(!strpos(Request::url(), '/api/')){
    //         return view('transacciones.grupo', compact('controlador'));
    //     }

        

    //     $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
    //     // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
        
    
    //     $fechaDesde = Carbon::now();
    //     $fechaHasta = Carbon::now();

    //     $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
    //     $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

    //     $t = Transactionsgroups::whereBetween('created_at', array($fechaDesde, $fechaHasta))
    //     ->get();
        
    
    //     return Response::json([
    //         'bancas' => Branches::whereStatus(1)->get(),
    //         'entidades' => Entity::whereStatus(1)->get(),
    //         'tipos' => Types::whereRenglon('transaccion')->whereIn('descripcion', ['Ajuste', 'Cobro', 'Pago'])->get(),
    //         'grupos' => TransactionsgroupsResource::collection($t)
    //     ], 201);
    // }

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
            'datos.addTransaccion' => 'required',
            'datos.idUsuario' => 'required',
        ])['datos'];

        $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first()->id;
        $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first()->id;

        $grupo = Transactionsgroups::create(['idUsuario' => $datos['idUsuario']]);

        

        $c = 0;
        foreach($datos['addTransaccion'] as $t){
            $transaccion = transactions::create([
                'idUsuario' => $datos['idUsuario'],
                'idTipo' => $t['tipo']['id'],
                'idTipoEntidad1' => $idTipoEntidad1,
                'idTipoEntidad2' => $idTipoEntidad2,
                'idEntidad1' => $t['entidad1']['id'],
                'idEntidad2' => $t['entidad2']['id'],
                'entidad1_saldo_inicial' => $t['entidad1_saldo_inicial'],
                'entidad2_saldo_inicial' => $t['entidad2_saldo_inicial'],
                'debito' => $t['debito'],
                'credito' => $t['credito'],
                'entidad1_saldo_final' => $t['entidad1_saldo_final'],
                'entidad2_saldo_final' => $t['entidad2_saldo_final'],
                'nota' => $t['nota'],
                'nota_grupo' => $t['nota_grupo']
            ]);

             if($c == 0){
                    $colleccion = collect([[
                        'idGrupo' => $grupo->id,
                        'idTransaccion' => $transaccion->id
                    ]]);
                }else{
                    $colleccion->push([
                        'idGrupo' => $grupo->id,
                        'idTransaccion' => $transaccion->id
                    ]);
                }
          
           $c++;
        }

        $grupo = Transactionsgroups::whereId($grupo->id)->first();
        $grupo->transacciones()->detach();
        $colleccion = collect($colleccion)->map(function($c){
            return ['idGrupo' => $c['idGrupo'], 'idTransaccion' => $c['idTransaccion'] ];
        });
       $grupo->transacciones()->attach($colleccion);




       $fechaDesde = Carbon::now();
       $fechaHasta = Carbon::now();

       $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
       $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

       $t = Transactionsgroups::whereBetween('created_at', array($fechaDesde, $fechaHasta))
       ->get();
       

       return Response::json([
        'bancas' => Branches::whereStatus(1)->get(),
        'entidades' => Entity::whereStatus(1)->get(),
        'tipos' => Types::whereRenglon('transaccion')->whereIn('descripcion', ['Ajuste', 'Cobro', 'Pago'])->get(),
        'grupos' => TransactionsgroupsResource::collection($t)
    ], 201);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function show(transactions $transactions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function edit(transactions $transactions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, transactions $transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\transactions  $transactions
     * @return \Illuminate\Http\Response
     */
    public function destroy(transactions $transactions)
    {
        //
    }
}
