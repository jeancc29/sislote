<?php

namespace App\Http\Controllers;

use App\Blockslotteries;
use Request;
// use Illuminate\Http\Request;
use App\Http\Requests\IdBloqueoRequest;

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
use App\Blocksgenerals;
use App\Blocksplaysgenerals;
use App\Coins; 

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
        

        $loterias = Lotteries::select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
        $bancas = Branches::select('id', 'descripcion')->whereStatus(1)->get();


        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::all(),
            'dias' => Days::all(),
            'monedas' => Coins::all(),
        ], 201);
    }

    public function buscar(Request $request)
    {
        $datos = request()->validate([
            'datos.bancas' => '',
            'datos.dias' => '',
            'datos.idUsuario' => 'required',
            'datos.idTipoBloqueo' => 'required'
        ])['datos'];
    
    
        

        if($datos['idTipoBloqueo'] == 1){
            $idDias = collect($datos['dias'])->map(function($d){
                return $d['id'];
            });
            $dias = Days::whereIn('id', $idDias)->get();
            //COLLECT DIAS
            $dias = collect($dias)->map(function($d){
                $loterias = collect(Lotteries::whereStatus(1)->get())->map(function($l) use($d){
                    //COLLECT SORTEOS
                    $sorteos = collect($l['sorteos'])->map(function($s) use($d, $l){
                        $bloqueo = Blocksgenerals::where(['idLoteria' => $l['id'], 'idSorteo' => $s['id'], 'idDia' => $d['id']])->first();
                        $montoBloqueo = 0;
                        if($bloqueo != null)
                            $montoBloqueo = $bloqueo['monto'];
                        else
                            $bloqueo = null;
                        return ["id" => $s['id'], "descripcion" => $s['descripcion'], "bloqueo" => $montoBloqueo, "idBloqueo" => ($bloqueo != null) ? $bloqueo['id'] : $bloqueo];
                    });
                    //VALIDAMOS DE QUE EL BLOQUEO EXISTE PARA ELLO NOS ASEGURAMOS DE QUE EL IDBLOQUE NO SEA NULO
                    list($sorteos_seleccionadas, $no) = $sorteos->partition(function($l){
                        return $l['idBloqueo'] != null;
                    });
                    return ["id" => $l['id'], "descripcion" => $l['descripcion'], "sorteos" => $sorteos_seleccionadas, "cantidadDeBloqueos" => count($sorteos_seleccionadas)];
                });
                return ["id" => $d->id, "descripcion" => $d->descripcion, "wday" => $d->wday, "loterias" => $loterias];
            });
        }
        else if($datos['idTipoBloqueo'] == 3){
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
                            $montoBloqueo = 0;
                            if($bloqueo != null)
                                $montoBloqueo = $bloqueo['monto'];
                            else
                                $bloqueo = null;
                            return ["id" => $s['id'], "descripcion" => $s['descripcion'], "bloqueo" => $montoBloqueo, "idBloqueo" => ($bloqueo != null) ? $bloqueo['id'] : $bloqueo];
                        });
                         //VALIDAMOS DE QUE EL BLOQUEO EXISTE PARA ELLO NOS ASEGURAMOS DE QUE EL IDBLOQUE NO SEA NULO
                        list($sorteos_seleccionadas, $no) = $sorteos->partition(function($l){
                            return $l['idBloqueo'] != null;
                        });
                        return ["id" => $l['id'], "descripcion" => $l['descripcion'], "sorteos" => $sorteos_seleccionadas, "cantidadDeBloqueos" => count($sorteos_seleccionadas)];
                    });
                    return ["id" => $b['id'], "descripcion" => $b['descripcion'], "loterias" => $loterias];
                });
                return ["id" => $d->id, "descripcion" => $d->descripcion, "wday" => $d->wday, "bancas" => $bancas];
            });
        }

        else if($datos['idTipoBloqueo'] == 2){
            $loterias = collect(Lotteries::whereStatus(1)->get())->map(function($l){
                //COLLECT JUGADAS
                $fecha = Carbon::now();
                $jugadas = Blocksplaysgenerals::where('idLoteria', $l['id'])
                ->whereRaw('date(blocksplaysgenerals.fechaHasta) >= ? ', [$fecha->toDateString()])
                ->get();
                $jugadas = collect($jugadas)->map(function($j){
                    return ["id" => $j["id"], "jugada" => $j["jugada"], "monto" => $j["monto"], "idSorteo" => $j["idSorteo"], "sorteo" => Draws::whereId($j["idSorteo"])->first()->descripcion, "fechaDesde" => $j["fechaDesde"], "fechaHasta" => $j["fechaHasta"],  "idBloqueo" => $j["id"]];
                });
                return ["id" => $l['id'], "descripcion" => $l['descripcion'], "jugadas" => $jugadas, "cantidadDeBloqueos" => count($jugadas)];
            });

            return Response::json([
                'loterias' => $loterias
            ], 201);
        }

        else if($datos['idTipoBloqueo'] == 4){
            
            $idBancas = collect($datos['bancas'])->map(function($d){
                return $d['id'];
            });

            
            
                $bancas = BranchesResource::collection(Branches::whereIn('id', $idBancas)->get());
                //COLLECT BANCAS
                $bancas = collect($bancas)->map(function($b){
                    //COLLECT LOTERIAS
                    $loterias = collect($b['loterias'])->map(function($l) use($b){
                        //COLLECT SORTEOS
                        $fecha = Carbon::now();
                        $jugadas = Blocksplays::where(['idLoteria' => $l['id'], "idBanca" => $b['id']])
                        ->whereRaw('date(blocksplays.fechaHasta) >= ? ', [$fecha->toDateString()])
                        ->get();
                        $jugadas = collect($jugadas)->map(function($j){
                            return ["id" => $j["id"], "jugada" => $j["jugada"], "monto" => $j["monto"], "idSorteo" => $j["idSorteo"], "sorteo" => Draws::whereId($j["idSorteo"])->first()->descripcion, "fechaDesde" => $j["fechaDesde"], "fechaHasta" => $j["fechaHasta"],  "idBloqueo" => $j["id"]];
                        });
                        return ["id" => $l['id'], "descripcion" => $l['descripcion'], "jugadas" => $jugadas, "cantidadDeBloqueos" => count($jugadas)];
                    
                    });
                    return ["id" => $b['id'], "descripcion" => $b['descripcion'], "loterias" => $loterias];
                });

                return Response::json([
                    'bancas' => $bancas
                ], 201);
           
        }
        

        
    
        return Response::json([
            'dias' => $dias
        ], 201);
    }

    //ELIMINAR BLOQUEOS
    public function eliminar(IdBloqueoRequest $request)
    {
        
        $validated = $request->validated();
        Blockslotteries::whereId($validated["datos"]["idBloqueo"])->first()->delete();

    
        return Response::json([
            'mensaje' => "Se ha eliminado correctamente"
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
            'datos.ckbDias' => 'required',
            'datos.idMoneda' => 'required'
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
                                'idDia' => $d['id'],
                                'idMoneda' => $datos['idMoneda']
                            ])->get()->first();
    
                    //ACTUALIZAR BLOQUEO SI EXISTE
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
                                    'esBloqueoJugada' => 0,
                                    'esGeneral' => 0,
                                    'idMoneda' => $datos['idMoneda']
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
                        //CREAR BLOQUEO
                        $b = Blockslotteries::create([
                            'idBanca' => $banca['id'],
                            'idLoteria' => $l['id'],
                            'idSorteo' => $s['id'],
                            'idDia' => $d['id'],
                            'monto' => $s['monto'],
                            'idMoneda' => $datos['idMoneda']
                        ]);

                        

                        $dia = Days::where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                        if($dia != null){
                            $stocksJugadasDelDiaActual = Stock::where(
                            [
                                'idBanca' => $banca['id'], 
                                'idLoteria' => $l['id'], 
                                'idSorteo' => $s['id'],
                                'esBloqueoJugada' => 0,
                                'esGeneral' => 0,
                                'idMoneda' => $datos['idMoneda']
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
    
        $loterias = Lotteries::select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
            $bancas = Branches::select('id', 'descripcion')->whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::all(),
            'dias' => Days::all(),
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }


    public function storeGeneral(Request $request)
    {
        $datos = request()->validate([
            'datos.loterias' => 'required',
            'datos.sorteos' => 'required',
            'datos.idUsuario' => 'required',
            'datos.ckbDias' => 'required',
            'datos.idMoneda' => 'required',
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
    
  
        foreach($loterias_seleccionadas as $l):
           
           
    
                   
           
            foreach($sorteos_seleccionadas as $s):
                if(Lotteries::whereId($l['id'])->first()->sorteos()->wherePivot('idSorteo', $s['id'])->first() == null)
                    continue;
    
                   
    
                foreach($dias_seleccionadas as $d):
                    
                    $bloqueo = Blocksgenerals::where(
                            [ 
                                'idLoteria' => $l['id'], 
                                'idSorteo' => $s['id'],
                                'idDia' => $d['id'],
                                'idMoneda' => $datos['idMoneda']
                            ])->get()->first();
    
                    //ACTUALIZAR BLOQUEO SI EXISTE
                    if($bloqueo != null){
                        $bloqueo['monto'] = $s['monto'];
                        $bloqueo->save();

                        

                        
                            $dia = Days::where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                            if($dia != null){
                                $stocksJugadasDelDiaActual = Stock::where(
                                [
                                    'idLoteria' => $l['id'], 
                                    'idSorteo' => $s['id'],
                                    'esBloqueoJugada' => 0,
                                    'esGeneral' => 1,
                                    'idMoneda' => $datos['idMoneda']
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
                        //CREAR BLOQUEO
                        $b = Blocksgenerals::create([
                            'idLoteria' => $l['id'],
                            'idSorteo' => $s['id'],
                            'idDia' => $d['id'],
                            'monto' => $s['monto'],
                            'idMoneda' => $datos['idMoneda']
                        ]);

                        

                        $dia = Days::where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                        if($dia != null){
                            $stocksJugadasDelDiaActual = Stock::where(
                            [
                                'idLoteria' => $l['id'], 
                                'idSorteo' => $s['id'],
                                'esBloqueoJugada' => 0,
                                'esGeneral' => 1,
                                'idMoneda' => $datos['idMoneda']
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
       
    
        $loterias = Lotteries::select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
        $bancas = Branches::select('id', 'descripcion')->whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
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
