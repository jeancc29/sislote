<?php

namespace App\Http\Controllers;

use App\Blocksplays;
use App\Blocksplaysgenerals;
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
use App\Realtime;
use App\Coins;
use App\Classes\Helper;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use App\Http\Requests\BlocksplaysRequest;


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
            'datos.bancas' => 'required',
            'datos.jugadas' => 'required',
            'datos.fechaDesde' => 'required',
            'datos.fechaHasta' => 'required',
            'datos.idMoneda' => 'required',
        ])['datos'];
    
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                return $l['seleccionado'] == true;
            });

            $loterias = collect($loterias_seleccionadas)->map(function($d){
                return $d['id'];
            });

            $loterias_seleccionadas = Lotteries::whereIn('id', $loterias)->whereStatus(1)->get();
    
            $fechaDesde = getdate(strtotime($datos['fechaDesde']));
            $fechaHasta = getdate(strtotime($datos['fechaHasta']));
            $fecha = getdate();

            // $fechaDesdeCarbon = new Carbon($datos['fechaDesde']);
            // $fechaHastaCarbon = new Carbon($datos['fechaHasta']);
            $fechaDesdeCarbon = new Carbon($fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday']);
            $fechaHastaCarbon = new Carbon($fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday']);
            $fechaActualCarbon = Carbon::now();
    
            $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();

    foreach($datos['bancas'] as $banca):
        foreach($loterias_seleccionadas as $l):
            if(Branches::whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                    continue;

            foreach($datos['jugadas'] as $j):
           
            
                $j['idSorteo'] = Helper::determinarSorteo($j['jugada'], $l);
                $j['jugada'] = Helper::quitarUltimoCaracter($j['jugada'], $j['idSorteo']);
                   
    
                    $bloqueo = Blocksplays::where(
                        [
                            'idBanca' => $banca['id'], 
                            'idLoteria' => $l['id'], 
                            'jugada' => $j['jugada'],
                            'idSorteo' => $j['idSorteo'],
                            'status' => 1,
                            'idMoneda' => $datos['idMoneda']
                        ])
                        ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
    
                    if($bloqueo != null){
                        $bloqueo['monto'] = $j['monto'];
                        $bloqueo['fechaDesde'] = $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00';
                        $bloqueo['fechaHasta'] = $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00';
                        $bloqueo->save();

                        Realtime::create([
                            'idAfectado' => $bloqueo['id'],
                            'tabla' => 'blocksplays'
                        ]);

                        // return Response::json([
                        //     'errores' => 1,
                        //     'mensaje' => 'Monto vendido ',
                        //     'fechaDesdeCarbon' => $fechaDesdeCarbon->toDateString(),
                        //     'fechaActualCarbon' => $fechaActualCarbon->toDateString(),
                        //     'fechaDesde' => $fechaDesde
                        // ], 201);

                        if($fechaDesdeCarbon->toDateString() <= $fechaActualCarbon->toDateString() && $fechaHastaCarbon->toDateString() >= $fechaActualCarbon->toDateString())
                        {
                            $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idBanca' => $banca['id'], 
                                    'idLoteria' => $l['id'], 
                                    'jugada' => $j['jugada'],
                                    'idSorteo' => $j['idSorteo'],
                                    'esGeneral' => 0,
                                    'idMoneda' => $datos['idMoneda']
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaHastaCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                // return Response::json([
                                //     'errores' => 1,
                                //     'mensaje' => 'Monto vendido '
                                // ], 201);

                                foreach($stocksJugadasDelDiaActual as $s)
                                {
                                    $montoVendido = $s['montoInicial'] - $s['monto'];
                                    // return Response::json([
                                    //     'errores' => 1,
                                    //     'mensaje' => 'Monto vendido ',
                                    //     'a_montoVendido' => $montoVendido,
                                    //     'a_montoInicial' => $s['montoInicial']
                                    // ], 201);
                                    $s['montoInicial'] = $j['monto'];
                                    $s['monto'] = $j['monto'] - $montoVendido;
                                    $s['esBloqueoJugada'] = 1;
                                    $s->save();

                                    Realtime::create([
                                        'idAfectado' => $s['id'],
                                        'tabla' => 'stocks'
                                    ]);
                                }
                        }
                    }else{
                        $b = Blocksplays::create([
                            'idBanca' => $banca['id'],
                            'idLoteria' => $l['id'],
                            'idSorteo' => $j['idSorteo'],
                            'jugada' => $j['jugada'],
                            'montoInicial' => $j['monto'],
                            'monto' => $j['monto'],
                            'fechaDesde' => $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00',
                            'fechaHasta' => $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00',
                            'idUsuario' => $datos['idUsuario'],
                            'status' => 1,
                            'idMoneda' => $datos['idMoneda']
                        ]);

                        Realtime::create([
                            'idAfectado' => $b['id'],
                            'tabla' => 'blocksplays'
                        ]);

                        if($fechaDesdeCarbon->toDateString() <= $fechaActualCarbon->toDateString() && $fechaHastaCarbon->toDateString() >= $fechaActualCarbon->toDateString())
                        {
                            // return Response::json([
                            //     'errores' => 1,
                            //     'mensaje' => 'Monto vendido '
                            // ], 201);

                            $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idBanca' => $banca['id'], 
                                    'idLoteria' => $l['id'], 
                                    'jugada' => $j['jugada'],
                                    'idSorteo' => $j['idSorteo'],
                                    'esGeneral' => 0,
                                    'idMoneda' => $datos['idMoneda']
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaHastaCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                foreach($stocksJugadasDelDiaActual as $s)
                                {
                                    
                                    $montoVendido = $s['montoInicial'] - $s['monto'];
                                    // return Response::json([
                                    //     'errores' => 1,
                                    //     'mensaje' => 'Monto vendido ',
                                    //     'a_montoVendido' => $montoVendido,
                                    //     'a_montoInicial' => $s['montoInicial']
                                    // ], 201);
                                    $s['montoInicial'] = $j['monto'];
                                    $s['monto'] = $j['monto'] - $montoVendido;
                                    $s['esBloqueoJugada'] = 1;
                                    $s->save();

                                    Realtime::create([
                                        'idAfectado' => $s['id'],
                                        'tabla' => 'stocks'
                                    ]);
                                }
                        }
                    }
                   
                endforeach;
            endforeach; //End foreahc loterias
        endforeach; //End foreach banca
    
        $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::all(),
            'dias' => Days::all(),
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }


    //CODIGO GUARDAR GENERAL
    
    public function storeGeneral(Request $request)
    {
        $datos = request()->validate([
            'datos.loterias' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idMoneda' => 'required',
            'datos.jugadas' => 'required',
            'datos.fechaDesde' => 'required',
            'datos.fechaHasta' => 'required',
            'datos.ignorarDemasBloqueos' => ''
        ])['datos'];
    
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                return $l['seleccionado'] == true;
            });

            $loterias = collect($loterias_seleccionadas)->map(function($d){
                return $d['id'];
            });

            $loterias_seleccionadas = Lotteries::whereIn('id', $loterias)->whereStatus(1)->get();
    
            $fechaDesde = getdate(strtotime($datos['fechaDesde']));
            $fechaHasta = getdate(strtotime($datos['fechaHasta']));
            $fecha = getdate();

            // $fechaDesdeCarbon = new Carbon($datos['fechaDesde']);
            // $fechaHastaCarbon = new Carbon($datos['fechaHasta']);
            $fechaDesdeCarbon = new Carbon($fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday']);
            $fechaHastaCarbon = new Carbon($fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday']);
            $fechaActualCarbon = Carbon::now();
    
            $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();

 
        foreach($loterias_seleccionadas as $l):
           
            foreach($datos['jugadas'] as $j):
           
            
                $j['idSorteo'] = Helper::determinarSorteo($j['jugada'], $l);
                $j['jugada'] = Helper::quitarUltimoCaracter($j['jugada'], $j['idSorteo']);
                   
    
                    $bloqueo = Blocksplaysgenerals::where(
                        [
                            'idLoteria' => $l['id'], 
                            'jugada' => $j['jugada'],
                            'idSorteo' => $j['idSorteo'],
                            'status' => 1,
                            'idMoneda' => $datos['idMoneda']
                        ])
                        ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
    
                    if($bloqueo != null){
                        $bloqueo['monto'] = $j['monto'];
                        $bloqueo['ignorarDemasBloqueos'] = $datos['ignorarDemasBloqueos'];
                        $bloqueo['fechaDesde'] = $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00';
                        $bloqueo['fechaHasta'] = $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00';
                        $bloqueo->save();

                        Realtime::create([
                            'idAfectado' => $bloqueo['id'],
                            'tabla' => 'blocksplaysgenerals'
                        ]);

                        if($fechaDesdeCarbon->toDateString() <= $fechaActualCarbon->toDateString() && $fechaHastaCarbon->toDateString() >= $fechaActualCarbon->toDateString())
                        {
                            $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idLoteria' => $l['id'], 
                                    'jugada' => $j['jugada'],
                                    'idSorteo' => $j['idSorteo'],
                                    'esGeneral' => 1,
                                    'idMoneda' => $datos['idMoneda']
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaHastaCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                foreach($stocksJugadasDelDiaActual as $s)
                                {
                                    $montoVendido = $s['montoInicial'] - $s['monto'];
                                    $s['montoInicial'] = $j['monto'];
                                    $s['monto'] = $j['monto'] - $montoVendido;
                                    $s['esBloqueoJugada'] = 1;
                                    $s['esGeneral'] = 1;
                                    $s['ignorarDemasBloqueos'] = $datos['ignorarDemasBloqueos'];
                                    $s->save();

                                    Realtime::create([
                                        'idAfectado' => $s['id'],
                                        'tabla' => 'stocks'
                                    ]);
                                }
                        }
                    }else{
                        $b = Blocksplaysgenerals::create([
                            'idLoteria' => $l['id'],
                            'idSorteo' => $j['idSorteo'],
                            'jugada' => $j['jugada'],
                            'montoInicial' => $j['monto'],
                            'monto' => $j['monto'],
                            'fechaDesde' => $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00',
                            'fechaHasta' => $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00',
                            'idUsuario' => $datos['idUsuario'],
                            'status' => 1,
                            'ignorarDemasBloqueos' => $datos['ignorarDemasBloqueos'],
                            'idMoneda' => $datos['idMoneda']
                        ]);

                        Realtime::create([
                            'idAfectado' => $b['id'],
                            'tabla' => 'blocksplaysgenerals'
                        ]);

                        if($fechaDesdeCarbon->toDateString() <= $fechaActualCarbon->toDateString() && $fechaHastaCarbon->toDateString() >= $fechaActualCarbon->toDateString())
                        {
                            $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idLoteria' => $l['id'], 
                                    'jugada' => $j['jugada'],
                                    'idSorteo' => $j['idSorteo'],
                                    'esGeneral' => 1,
                                    'idMoneda' => $datos['idMoneda']
                                ])
                                ->whereBetween('created_at', array($fechaActualCarbon->toDateString() . ' 00:00:00', $fechaHastaCarbon->toDateString() . ' 23:50:00'))
                                ->get();

                                foreach($stocksJugadasDelDiaActual as $s)
                                {
                                    $montoVendido = $s['montoInicial'] - $s['monto'];
                                    $s['montoInicial'] = $j['monto'];
                                    $s['monto'] = $j['monto'] - $montoVendido;
                                    $s['esBloqueoJugada'] = 1;
                                    $s['esGeneral'] = 1;
                                    $s['ignorarDemasBloqueos'] = $datos['ignorarDemasBloqueos'];
                                    $s->save();

                                    Realtime::create([
                                        'idAfectado' => $s['id'],
                                        'tabla' => 'stocks'
                                    ]);
                                }
                        }
                    }
                   
                endforeach;
            endforeach; //End foreahc loterias
    
    
        $loterias = Lotteries::whereStatus(1)->get();
            $bancas = Branches::whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::all(),
            'dias' => Days::all(),
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }

    //CODIGO GUARDAR VIEJO
    public function storeViejo(Request $request)
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
                        ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
    
                    if($bloqueo != null){
                        $bloqueo['monto'] = $datos['monto'];
                        $bloqueo['fechaDesde'] = $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00';
                        $bloqueo['fechaHasta'] = $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00';
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

    public function eliminar(BlocksplaysRequest $request)
    {
        
        $validated = $request->validated();
        Blocksplays::whereId($validated["datos"]["idBloqueo"])->delete();

    
        return Response::json([
            'mensaje' => "Se ha eliminado correctamente"
        ], 201);
    }
}
