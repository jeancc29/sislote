<?php

namespace App\Http\Controllers;

use App\Blockslotteries;
use Request;

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;


use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
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

use Illuminate\Support\Facades\Crypt;

class BlockslotteriesController extends Controller
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
            return view('bloqueos.index', compact('controlador'));
        }
        

        $loterias = Lotteries::whereStatus(1)->get();
        $bancas = Branches::whereStatus(1)->get();


        return Response::json([
            'loterias' => LotteriesResource::collection($loterias),
            'bancas' => BranchesResource::collection($bancas),
            'sorteos' => Draws::all(),
            'dias' => Days::all()
        ], 201);
    }

    public function buscar(Request $request)
    {
        $datos = request()->validate([
            'datos.bancas' => 'required',
            'datos.dias' => 'required',
            'datos.idUsuario' => 'required'
        ])['datos'];
    
    
        $idDias = collect($datos['dias'])->map(function($d){
            return $d['id'];
        });
        $idBancas = collect($datos['bancas'])->map(function($d){
            return $d['id'];
        });

        $dias = Days::whereIn('id', $idDias)->get();
        //COLLECT DIAS
        $dias = collect($dias)->map(function($d) use($idBancas){
            $bancas = BranchesResource::collection($d->bancas()->wherePivotIn('idBanca', $idBancas)->get());
            //COLLECT BANCAS
            $bancas = collect($bancas)->map(function($b) use($d){
                //COLLECT LOTERIAS
                $loterias = collect($b['loterias'])->map(function($l) use($b, $d){
                    //COLLECT SORTEOS
                    $sorteos = collect($l['sorteos'])->map(function($s) use($d, $b, $l){
                        $bloqueo = Blockslotteries::where(['idBanca' => $b['id'], 'idLoteria' => $l['id'], 'idSorteo' => $s['id'], 'idDia' => $d['id']])->first();
                        if($bloqueo != null)
                            $bloqueo = $bloqueo['monto'];
                        else
                            $bloqueo = null;
                        return ["id" => $s['id'], "descripcion" => $s['descripcion'], "bloqueo" => $bloqueo];
                    });
                    return ["id" => $l['id'], "descripcion" => $l['descripcion'], "sorteos" => $sorteos];
                });
                return ["id" => $b['id'], "descripcion" => $b['descripcion'], "loterias" => $loterias];
            });
            return ["id" => $d->id, "descripcion" => $d->descripcion, "wday" => $d->wday, "bancas" => $bancas];
        });

        
    
        return Response::json([
            'dias' => $dias
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
            'datos.sorteos' => 'required',
            'datos.idUsuario' => 'required',
            'datos.bancas' => 'required',
            'datos.ckbDias' => 'required'
        ])['datos'];
    
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
            });
    
            $sorteos = collect($datos['sorteos']);
            list($sorteos_seleccionadas, $no) = $sorteos->partition(function($l){
                return $l['monto'] != null && $l['monto'] >= 0 && isset($l['monto']);
            });
    
            $dias = collect($datos['ckbDias']);
            list($dias_seleccionadas, $no) = $dias->partition(function($l){
                return $l['existe'] == true;
            });
           
        
          $fecha = getdate();
          $fechaActualCarbon = Carbon::now();
    
    foreach($datos['bancas'] as $banca):
        foreach($loterias_seleccionadas as $l):
           
            if(Branches::whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                    continue;
    
                   
           
            foreach($sorteos_seleccionadas as $s):
                if(Lotteries::whereId($l['id'])->first()->sorteos()->wherePivot('idSorteo', $s['id'])->first() == null)
                    continue;
    
                   
    
                foreach($dias_seleccionadas as $d):
                    
                    $bloqueo = Blockslotteries::where(
                            [
                                'idBanca' => $banca['id'], 
                                'idLoteria' => $l['id'], 
                                'idSorteo' => $s['id'],
                                'idDia' => $d['id']
                            ])->get()->first();
    
                    if($bloqueo != null){
                        $bloqueo['monto'] = $s['monto'];
                        $bloqueo->save();

                        
                            $dia = Days::where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                            if($dia != null){
                                $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idBanca' => $banca['id'], 
                                    'idLoteria' => $l['id'], 
                                    'idSorteo' => $s['id'],
                                    'esBloqueoJugada' => 0
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaActualCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                foreach($stocksJugadasDelDiaActual as $sj)
                                {
                                    $montoVendido = $sj['montoInicial'] - $sj['monto'];
                                    $sj['montoInicial'] = $s['monto'];
                                    $sj['monto'] = $s['monto'] - $montoVendido;
                                    $sj->save();
                                }
                            }
                            
                        
                    }else{
                        Blockslotteries::create([
                            'idBanca' => $banca['id'],
                            'idLoteria' => $l['id'],
                            'idSorteo' => $s['id'],
                            'idDia' => $d['id'],
                            'monto' => $s['monto']
                        ]);

                        $dia = Days::where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                        if($dia != null){
                            $stocksJugadasDelDiaActual = Stock::where(
                            [
                                'idBanca' => $banca['id'], 
                                'idLoteria' => $l['id'], 
                                'idSorteo' => $s['id'],
                                'esBloqueoJugada' => 0
                            ])
                            ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaActualCarbon->toDateString() . ' 23:50:00'))
                            ->get();

                            foreach($stocksJugadasDelDiaActual as $sj)
                            {
                                $montoVendido = $sj['montoInicial'] - $sj['monto'];
                                $sj['montoInicial'] = $s['monto'];
                                $sj['monto'] = $s['monto'] - $montoVendido;
                                $sj->save();
                            }
                        }
                    }
                endforeach; //End foreach dias
            endforeach;//End foreach sorteos
            endforeach; //End foreahc loterias
        endforeach; //End foreach banca
    
        $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => LotteriesResource::collection($loterias),
            'bancas' => BranchesResource::collection($bancas),
            'sorteos' => Draws::all(),
            'dias' => Days::all(),
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return \Illuminate\Http\Response
     */
    public function show(Blockslotteries $blockslotteries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return \Illuminate\Http\Response
     */
    public function edit(Blockslotteries $blockslotteries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blockslotteries  $blockslotteries
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blockslotteries $blockslotteries)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blockslotteries $blockslotteries)
    {
        //
    }
}
