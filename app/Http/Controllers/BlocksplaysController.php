<?php

namespace App\Http\Controllers;

use App\Blocksplays;
use Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;


use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
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

class BlocksplaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $datos = request()->validate([
            'datos.loterias' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idSorteo' => 'required',
            'datos.bancas' => 'required',
            'datos.jugada' => 'required',
            'datos.monto' => 'required',
            'datos.fechaDesde' => 'required',
            'datos.fechaHasta' => 'required',
        ])['datos'];
    
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
            });
    
            $fechaDesde = getdate(strtotime($datos['fechaDesde']));
            $fechaHasta = getdate(strtotime($datos['fechaHasta']));
            $fecha = getdate();

            $fechaDesdeCarbon = new Carbon($datos['fechaDesde']);
            $fechaHastaCarbon = new Carbon($datos['fechaHasta']);
            $fechaActualCarbon = Carbon::now();
    
            $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();
    
    foreach($datos['bancas'] as $banca):
        foreach($loterias_seleccionadas as $l):
           
            if(Branches::whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                    continue;
    
                   
    
                    $bloqueo = Blocksplays::where(
                        [
                            'idBanca' => $banca['id'], 
                            'idLoteria' => $l['id'], 
                            'jugada' => $datos['jugada'],
                            'idSorteo' => $datos['idSorteo'],
                            'status' => 1
                        ])
                        ->where('fechaDesde', '<=', $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00')
                        ->where('fechaHasta', '>=', $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00')->first();
    
    
                    if($bloqueo != null){
                        $bloqueo['monto'] = $datos['monto'];
                        $bloqueo->save();

                        if($fechaDesdeCarbon->toDateString() <= $fechaActualCarbon->toDateString() && $fechaHastaCarbon->toDateString() >= $fechaActualCarbon->toDateString())
                        {
                            $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idBanca' => $banca['id'], 
                                    'idLoteria' => $l['id'], 
                                    'jugada' => $datos['jugada'],
                                    'idSorteo' => $datos['idSorteo']
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaHastaCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                foreach($stocksJugadasDelDiaActual as $s)
                                {
                                    $montoVendido = $s['montoInicial'] - $s['monto'];
                                    $s['montoInicial'] = $datos['monto'];
                                    $s['monto'] = $datos['monto'] - $montoVendido;
                                    $s['esBloqueoJugada'] = 1;
                                    $s->save();
                                }
                        }
                    }else{
                        Blocksplays::create([
                            'idBanca' => $banca['id'],
                            'idLoteria' => $l['id'],
                            'idSorteo' => $datos['idSorteo'],
                            'jugada' => $datos['jugada'],
                            'montoInicial' => $datos['monto'],
                            'monto' => $datos['monto'],
                            'fechaDesde' => $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00',
                            'fechaHasta' => $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00',
                            'idUsuario' => $datos['idUsuario'],
                            'status' => 1
                        ]);

                        if($fechaDesdeCarbon->toDateString() <= $fechaActualCarbon->toDateString() && $fechaHastaCarbon->toDateString() >= $fechaActualCarbon->toDateString())
                        {
                            $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idBanca' => $banca['id'], 
                                    'idLoteria' => $l['id'], 
                                    'jugada' => $datos['jugada'],
                                    'idSorteo' => $datos['idSorteo']
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaHastaCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                foreach($stocksJugadasDelDiaActual as $s)
                                {
                                    $montoVendido = $s['montoInicial'] - $s['monto'];
                                    $s['montoInicial'] = $datos['monto'];
                                    $s['monto'] = $datos['monto'] - $montoVendido;
                                    $s['esBloqueoJugada'] = 1;
                                    $s->save();
                                }
                        }
                    }
                   
          
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


    public function buscar(Request $request)
    {
        $datos = request()->validate([
            'datos.bancas' => 'required',
            'datos.dias' => 'required',
            'datos.idUsuario' => 'required'
        ])['datos'];
    
    
        $idDias = collect($datos['dias'])->map(function($d){
            return $d->id;
        });
        $idBancas = collect($datos['bancas'])->map(function($d){
            return $d->id;
        });

        $dias = Days::whereIn('id', $idDias)->get();
        $dias = collect($dias)->map(function($d){
            return ["id" => $d->id, "descripcion" => $d->descripcion, "wday" => $d->wday, "bancas" => BranchesResource::collection($d->bancas()->wherePivotIn('idBanca', [1,2])->get())];
        });

        
    
        return Response::json([
            'dias' => $dias
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return \Illuminate\Http\Response
     */
    public function show(Blocksplays $blocksplays)
    {
        $datos = request()->validate([
            'datos.loterias' => 'required',
            'datos.idUsuario' => 'required',
            'datos.bancas' => 'required',
            'datos.jugada' => 'required',
            'datos.monto' => 'required',
            'datos.fechaDesde' => 'required',
            'datos.fechaHasta' => 'required',
        ])['datos'];
    
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
            });
    
            $fechaDesde = getdate(strtotime($datos['fechaDesde']));
            $fechaHasta = getdate(strtotime($datos['fechaHasta']));
            $fecha = getdate();
    
            $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();
    
    foreach($datos['bancas'] as $banca):
        foreach($loterias_seleccionadas as $l):
           
            if(Branches::whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                    continue;
    
                   
    
                    $bloqueo = Blocksplays::where(
                        [
                            'idBanca' => $banca['id'], 
                            'idLoteria' => $l['id'], 
                            'jugada' => $datos['jugada'],
                            'status' => 1
                        ])
                        ->where('fechaDesde', '<=', $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00')
                        ->where('fechaHasta', '>=', $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00')->first();
    
    
                    if($bloqueo != null){
                        $bloqueo['monto'] = $datos['monto'];
                        $bloqueo->save();
                    }else{
                        Blocksplays::create([
                            'idBanca' => $banca['id'],
                            'idLoteria' => $l['id'],
                            'idSorteo' => 1,
                            'jugada' => $datos['jugada'],
                            'montoInicial' => $datos['monto'],
                            'monto' => $datos['monto'],
                            'fechaDesde' => $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00',
                            'fechaHasta' => $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00',
                            'idUsuario' => $datos['idUsuario'],
                            'status' => 1
                        ]);
                    }
                   
          
            endforeach; //End foreahc loterias
        endforeach; //End foreach banca
    
        $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => LotteriesResource::collection($loterias),
            'bancas' => BranchesResource::collection($bancas),
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return \Illuminate\Http\Response
     */
    public function edit(Blocksplays $blocksplays)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blocksplays  $blocksplays
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blocksplays $blocksplays)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blocksplays $blocksplays)
    {
        //
    }
}
