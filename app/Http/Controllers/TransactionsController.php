<?php

namespace App\Http\Controllers;
use App\Classes\Helper;
use Illuminate\Support\Facades\Redirect;

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
use App\Transactionscheduled;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\BranchesResourceSmall;
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
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar transacciones") == true){
                return redirect()->route('principal');
            }
            return view('transacciones.index', compact('controlador'));
        }


        
        $datos = request()->validate([
            'token' => ''
        ]);
       
        // $datos = \Helper::jwtDecode($datos["token"]);


        try {
            // $datos = JWT::decode($datos['token'], \config('data.apiKey'), array('HS256'));
            // $datos = json_decode(json_encode($datos), true);
            $datos = \Helper::jwtDecode($datos["token"]);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }
        

        $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
        // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
        
    
        $fechaDesde = Carbon::now();
        $fechaHasta = Carbon::now();

        $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
        $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

        $t = transactions::on($datos["servidor"])->whereBetween('created_at', array($fechaDesde, $fechaHasta))
        ->whereStatus(1)
        ->get();
        
        $tipos = Types::on($datos["servidor"])->where(['renglon' => 'entidad', 'status' => 1])->get();
        $tipos = collect($tipos)->map(function($d) use($datos){
            $entidades = null;
            if($d->descripcion == "Banca"){
                $entidades = Branches::on($datos["servidor"])->whereStatus(1)->get();
                $entidades = collect($entidades)->map(function($d){
                    return ['id' => $d->id, 'descripcion' => $d->descripcion];
                });
            }
            else if($d->descripcion == "Banco" || $d->descripcion == "Otros"){
                $entidades = Entity::on($datos["servidor"])->whereStatus(['status' => 1, 'idTipo' => $d->id])->get();
                $entidades = collect($entidades)->map(function($d){
                    return ['id' => $d->id, 'descripcion' => $d->nombre];
                });
            }
            return ['id' => $d['id'], 'descripcion' => $d['descripcion'],'renglon' => $d['renglon'], 'entidades' => $entidades];
        });

        return Response::json([
            'bancas' => Branches::on($datos["servidor"])->whereStatus(1)->get(),
            'entidades' => $tipos,
            'tipos' => Types::on($datos["servidor"])->whereRenglon('transaccion')->whereStatus(1)->get(),
            'transacciones' => TransactionsResource::collection($t)->servidor($datos["servidor"]),
            'usuarios' => Users::on($datos["servidor"])->whereStatus(1)->get()
        ], 201);
    }
    public function saldo()
    {
        // $datos = request()->validate([
        //     'datos.id' => 'required',
        //     'datos.es_banca' => 'required',
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $saldo_inicial = 0;

        // if($datos["es_banca"] == 1){
        //     $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        //     $debito = transactions::where(
        //         [
        //             'idEntidad1'=> $datos["id"], 
        //             'idTipoEntidad1' => $idTipoEntidad1->id, 
        //             'status' => 1
        //         ])->sum('debito');
        //     $credito =  transactions::where(
        //         [
        //             'idEntidad1'=> $datos["id"], 
        //             'idTipoEntidad1' => $idTipoEntidad1->id, 
        //             'status' => 1
        //         ])->sum('credito');
        //     $saldo_inicial = $debito - $credito;
        // }else{
        //     $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
        //     $debito = transactions::where(
        //         [
        //             'idEntidad2'=> $datos["id"],
        //             'idTipoEntidad2' => $idTipoEntidad2->id,  
        //             'status' => 1
        //         ])->sum('debito');
        //     $credito = transactions::where(
        //         [
        //             'idEntidad2'=> $datos["id"],
        //             'idTipoEntidad2' => $idTipoEntidad2->id,  
        //             'status' => 1
        //         ])->sum('credito');
        //     $saldo_inicial = $credito - $debito;
        // }

        if($datos["es_banca"] == 1)
            $saldo_inicial = Helper::saldo($datos["servidor"], $datos["id"], 1);
        else
            $saldo_inicial = Helper::saldo($datos["servidor"], $datos["id"], 2);

        return Response::json([
            'saldo_inicial' => $saldo_inicial,
            'tipos' => Types::on($datos["servidor"])->whereRenglon('entidad')->whereIn('descripcion', ['Banco', 'Otros'])->get()
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
            if(!(new Helper)->existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar transacciones") == true){
                return redirect()->route('principal');
            }
            return view('transacciones.grupo', compact('controlador'));
        }

        $datos = request()->validate([
            'token' => ''
        ]);

        try {
            // $datos = JWT::decode($datos['token'], \config('data.apiKey'), array('HS256'));
            // $datos = json_decode(json_encode($datos), true);
            $datos = \Helper::jwtDecode($datos["token"]);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }

        $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
        // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
        
    
        $fechaDesde = Carbon::now();
        $fechaHasta = Carbon::now();

        $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
        $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

        $t = Transactionsgroups::on($datos["servidor"])->whereBetween('created_at', array($fechaDesde, $fechaHasta))
        ->get();
        
    
        return Response::json([
            'bancas' => BranchesResourceSmall::collection(Branches::on($datos["servidor"])->whereStatus(1)->get()),
            'entidades' => Entity::on($datos["servidor"])->whereStatus(1)->get(),
            'tipos' => Types::on($datos["servidor"])->whereRenglon('transaccion')->whereIn('descripcion', ['Ajuste', 'Cobro', 'Pago', 'Descuento dias no laborados'])->get(),
            'grupos' => TransactionsgroupsResource::collection($t)->servidor($datos["servidor"])
        ], 201);
    }
    public function buscarTransaccion()
    {
        // $datos = request()->validate([
        //     'datos.fechaDesde' => 'required',
        //     'datos.fechaHasta' => 'required',
        //     'datos.idTipoEntidad' => 'required',
        //     'datos.idEntidad' => 'required',
        //     'datos.idTipo' => 'required',
        //     'datos.idUsuario' => 'required',
        // ])['datos'];

        $datos = request()['datos'];

        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }


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

        $t = transactions::on($datos["servidor"])->whereBetween('created_at', array($fechaDesde, $fechaHasta))
            ->where('idUsuario', $condicionUsuario, $datos['idUsuario'])
            ->where('idTipoEntidad1', $condicionTipoEntidad, $datos['idTipoEntidad'])
            ->where('idEntidad1', $condicionEntidad, $datos['idEntidad'])
            ->where('idTipo', $condicionTipo, $datos['idTipo'])
            ->whereStatus(1)
        ->get();

        return Response::json([
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'transacciones' => TransactionsResource::collection($t)->servidor($datos["servidor"])
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
        // $datos = request()->validate([
        //     'datos.addTransaccion' => 'required',
        //     'datos.idUsuario' => 'required',
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $idTipoEntidad1 = Types::on($datos["servidor"])->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first()->id;
        $idTipoEntidad2 = Types::on($datos["servidor"])->where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first()->id;


        $u = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        foreach($datos['addTransaccion'] as $t){
            if($t['tipo']['descripcion'] == "Ajuste"){
                if(!$u->tienePermiso("Crear ajustes") == true){
                    return Response::json([
                        'errores' => 1,
                        'mensajes' => "No tiene permisos para realizar esta accion",
                    ], 201);
                }
            }

            if($t['tipo']['descripcion'] == "Cobro"){
                if(!$u->tienePermiso("Crear cobros") == true){
                    return Response::json([
                        'errores' => 1,
                        'mensajes' => "No tiene permisos para realizar esta accion",
                    ], 201);
                }
            }

            if($t['tipo']['descripcion'] == "Pago"){
                if(!$u->tienePermiso("Crear pagos") == true){
                    return Response::json([
                        'errores' => 1,
                        'mensajes' => "No tiene permisos para realizar esta accion",
                    ], 201);
                }
            }


           
            
        }

        $c = 0;
        $colleccionNormal = null;
        $grupoNormal = null;
        $grupoProgramada = null;
        $colleccionProgramada = null;

        foreach($datos['addTransaccion'] as $t){
           
            if($t["tipoTransaccion"] == "Programada"){
                if($colleccionProgramada == null){
                    $grupoProgramada = Transactionsgroups::on($datos["servidor"])->create(['idUsuario' => $datos['idUsuario']]);                    
                }
                
                $fecha = new Carbon($t['fecha']);
                $fechaCarbon = Carbon::now();
                if($fecha->gt($fechaCarbon) == false){
                    Transactionsgroups::on($datos["servidor"])->whereId($grupoProgramada->id)->delete();
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => "La fecha debe ser mayor que el dia actual"
                    ], 201);
                }

                $transaccionProgramada = Transactionscheduled::on($datos["servidor"])->create([
                    'fecha' => $fecha->toDateString(),
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
                    $colleccionProgramada = collect([[
                        'idGrupo' => $grupoProgramada->id,
                        'idTransaccion' => $transaccionProgramada->id
                    ]]);
                }else{
                    $colleccionProgramada->push([
                        'idGrupo' => $grupoProgramada->id,
                        'idTransaccion' => $transaccionProgramada->id
                    ]);
                }
            }else{
                if($colleccionNormal == null){
                    $grupoNormal = Transactionsgroups::on($datos["servidor"])->create(['idUsuario' => $datos['idUsuario']]);                    
                }
                $transaccion = transactions::on($datos["servidor"])->create([
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
    
                 if($colleccionNormal == null){
                        $colleccionNormal = collect([[
                            'idGrupo' => $grupoNormal->id,
                            'idTransaccion' => $transaccion->id
                        ]]);
                    }else{
                        $colleccionNormal->push([
                            'idGrupo' => $grupoNormal->id,
                            'idTransaccion' => $transaccion->id
                        ]);
                    }
            }
            
          
           $c++;
        }

        
        if($colleccionNormal != null){
            $grupoNormal->transacciones()->detach();
            $colleccionNormal = collect($colleccionNormal)->map(function($c){
                return ['idGrupo' => $c['idGrupo'], 'idTransaccion' => $c['idTransaccion'] ];
            });
           $grupoNormal->transacciones()->attach($colleccionNormal);
        }
        

        if($colleccionProgramada != null){
            $grupoProgramada->transaccionesProgramadas()->detach();
            $colleccionProgramada = collect($colleccionProgramada)->map(function($c){
                return ['idGrupo' => $c['idGrupo'], 'idTransaccion' => $c['idTransaccion'] ];
            });
           $grupoProgramada->transaccionesProgramadas()->attach($colleccionProgramada);
        }



       $fechaDesde = Carbon::now();
       $fechaHasta = Carbon::now();

       $fechaDesde = $fechaDesde->year.'-'.$fechaDesde->month.'-'.$fechaDesde->day. " 00:00:00";
       $fechaHasta = $fechaHasta->year.'-'.$fechaHasta->month.'-'.$fechaHasta->day. " 23:59:00";

       $t = Transactionsgroups::on($datos["servidor"])->whereBetween('created_at', array($fechaDesde, $fechaHasta))
       ->get();
       

       return Response::json([
           'errores' => 0,
           'mensaje' => "Se ha guardado correctamente",
        'bancas' => Branches::on($datos["servidor"])->whereStatus(1)->get(),
        'entidades' => Entity::on($datos["servidor"])->whereStatus(1)->get(),
        'tipos' => Types::on($datos["servidor"])->whereRenglon('transaccion')->whereIn('descripcion', ['Ajuste', 'Cobro', 'Pago'])->get(),
        'grupos' => TransactionsgroupsResource::collection($t)->servidor($datos["servidor"])
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
