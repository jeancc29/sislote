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
use App\Classes\Helper;

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
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }

            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar reglas") == true){
                return redirect()->route('sinpermiso');
            }

            return view('bloqueos.index', compact('controlador'));
        }

        $datos = request()->validate([
            'token' => ''
        ]);

        try {
            $datos = \Helper::jwtDecode($datos["token"]);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }
        

        $loterias = Lotteries::on($datos["servidor"])->select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
        $bancas = Branches::on($datos["servidor"])->select('id', 'descripcion')->whereStatus(1)->get();


        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::on($datos["servidor"])->get(),
            'dias' => Days::on($datos["servidor"])->get(),
            'monedas' => Coins::on($datos["servidor"])->get(),
        ], 201);
    }

    public function buscar(Request $request)
    {
        // $datos = request()->validate([
        //     'datos.bancas' => '',
        //     'datos.dias' => '',
        //     'datos.idUsuario' => 'required',
        //     'datos.idTipoBloqueo' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }
    
    
        

        if($datos['idTipoBloqueo'] == 1){
            $idDias = collect($datos['dias'])->map(function($d){
                return $d['id'];
            });
            $dias = Days::on($datos["servidor"])->whereIn('id', $idDias)->get();
            //COLLECT DIAS
            $dias = collect($dias)->map(function($d) use($datos){
                $loterias = collect(Lotteries::on($datos["servidor"])->whereStatus(1)->get())->map(function($l) use($d, $datos){
                    //COLLECT SORTEOS
                    $sorteos = collect($l['sorteos'])->map(function($s) use($d, $l, $datos){
                        $bloqueo = Blocksgenerals::on($datos["servidor"])->where(['idLoteria' => $l['id'], 'idSorteo' => $s['id'], 'idDia' => $d['id'], "idMoneda" => $datos["idMoneda"]])->first();
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

            $dias = Days::on($datos["servidor"])->whereIn('id', $idDias)->get();
            //COLLECT DIAS
            $dias = collect($dias)->map(function($d) use($idBancas, $datos){
                $bancas = BranchesResource::collection($d->bancas()->wherePivotIn('idBanca', $idBancas)->get())->servidor($datos["servidor"]);
                //COLLECT BANCAS
                $bancas = collect($bancas)->map(function($b) use($d, $datos){
                    //COLLECT LOTERIAS
                    $loterias = collect($b['loterias'])->map(function($l) use($b, $d, $datos){
                        //COLLECT SORTEOS
                        $sorteos = collect($l['sorteos'])->map(function($s) use($d, $b, $l, $datos){
                            $bloqueo = Blockslotteries::on($datos["servidor"])->where(['idBanca' => $b['id'], 'idLoteria' => $l['id'], 'idSorteo' => $s['id'], 'idDia' => $d['id'], "idMoneda" => $datos["idMoneda"]])->first();
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
            $loterias = collect(Lotteries::on($datos["servidor"])->whereStatus(1)->get())->map(function($l) use($datos){
                //COLLECT JUGADAS
                $fecha = Carbon::now();
                $jugadas = Blocksplaysgenerals::on($datos["servidor"])->where(['idLoteria' => $l['id'], "idMoneda" => $datos["idMoneda"]])
                ->whereRaw('date(blocksplaysgenerals.fechaHasta) >= ? ', [$fecha->toDateString()])
                ->get();
                $jugadas = collect($jugadas)->map(function($j) use($datos){
                    return ["id" => $j["id"], "jugada" => $j["jugada"], "monto" => $j["monto"], "idSorteo" => $j["idSorteo"], "sorteo" => Draws::on($datos["servidor"])->whereId($j["idSorteo"])->first()->descripcion, "fechaDesde" => $j["fechaDesde"], "fechaHasta" => $j["fechaHasta"],  "idBloqueo" => $j["id"]];
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

            
            
                $bancas = BranchesResource::collection(Branches::on($datos["servidor"])->whereIn('id', $idBancas)->get())->servidor($datos["servidor"]);
                //COLLECT BANCAS
                $bancas = collect($bancas)->map(function($b) use($datos){
                    //COLLECT LOTERIAS
                    $loterias = collect($b['loterias'])->map(function($l) use($b, $datos){
                        //COLLECT SORTEOS
                        $fecha = Carbon::now();
                        $jugadas = Blocksplays::on($datos["servidor"])->where(['idLoteria' => $l['id'], "idBanca" => $b['id'], "idMoneda" => $datos["idMoneda"]])
                        ->whereRaw('date(blocksplays.fechaHasta) >= ? ', [$fecha->toDateString()])
                        ->get();
                        $jugadas = collect($jugadas)->map(function($j) use($datos){
                            return ["id" => $j["id"], "jugada" => $j["jugada"], "monto" => $j["monto"], "idSorteo" => $j["idSorteo"], "sorteo" => Draws::on($datos["servidor"])->whereId($j["idSorteo"])->first()->descripcion, "fechaDesde" => $j["fechaDesde"], "fechaHasta" => $j["fechaHasta"],  "idBloqueo" => $j["id"]];
                        });
                        return ["id" => $l['id'], "descripcion" => $l['descripcion'], "jugadas" => $jugadas, "cantidadDeBloqueos" => count($jugadas)];
                    
                    });
                    return ["id" => $b['id'], "descripcion" => $b['descripcion'], "loterias" => $loterias];
                });

                return Response::json([
                    'bancas' => $bancas
                ], 201);
           
        }

        else if($datos['idTipoBloqueo'] == 5){
            
            $loterias = collect(Lotteries::on($datos["servidor"])->whereStatus(1)->get())->map(function($l) use($datos){
                //COLLECT SORTEOS
                $sorteos = collect($l['sorteos'])->map(function($s) use($l, $datos){
                    $bloqueo = \App\Blocksdirtygenerals::on($datos["servidor"])->where(['idLoteria' => $l['id'], 'idSorteo' => $s['id'], "idMoneda" => $datos["idMoneda"]])->first();
                    $montoBloqueo = 0;
                    if($bloqueo != null)
                        $montoBloqueo = $bloqueo['cantidad'];
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

            return Response::json([
                'loterias' => $loterias
            ], 201);
           
        }

        return Response::json([
            'dias' => $dias,
            "idMoneda" => $datos["idMoneda"]
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
        // $datos = request()->validate([
        //     'datos.loterias' => 'required',
        //     'datos.sorteos' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.bancas' => 'required',
        //     'datos.ckbDias' => 'required',
        //     'datos.idMoneda' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }
    
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l) use($datos){
                return $l['seleccionado'] == true && Lotteries::on($datos["servidor"])->where(['id' => $l['id'], 'status' => 1])->first() != null;
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
           
            if(Branches::on($datos["servidor"])->whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                    continue;
    
                   
           
            foreach($sorteos_seleccionadas as $s):
                if(Lotteries::on($datos["servidor"])->whereId($l['id'])->first()->sorteos()->wherePivot('idSorteo', $s['id'])->first() == null)
                    continue;
    
                   
    
                foreach($dias_seleccionadas as $d):
                    
                    $bloqueo = Blockslotteries::on($datos["servidor"])->where(
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
                        
                        
                            $dia = Days::on($datos["servidor"])->where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                            if($dia != null){
                                $stocksJugadasDelDiaActual = Stock::on($datos["servidor"])->where(
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
                        $b = Blockslotteries::on($datos["servidor"])->create([
                            'idBanca' => $banca['id'],
                            'idLoteria' => $l['id'],
                            'idSorteo' => $s['id'],
                            'idDia' => $d['id'],
                            'monto' => $s['monto'],
                            'idMoneda' => $datos['idMoneda']
                        ]);

                        

                        $dia = Days::on($datos["servidor"])->where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                        if($dia != null){
                            $stocksJugadasDelDiaActual = Stock::on($datos["servidor"])->where(
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
    
        $loterias = Lotteries::on($datos["servidor"])->select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
            $bancas = Branches::on($datos["servidor"])->select('id', 'descripcion')->whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::on($datos["servidor"])->get(),
            'dias' => Days::on($datos["servidor"])->get(),
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }


    public function storeGeneral(Request $request)
    {
        // $datos = request()->validate([
        //     'datos.loterias' => 'required',
        //     'datos.sorteos' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.ckbDias' => 'required',
        //     'datos.idMoneda' => 'required',
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }
    
            $loterias = collect($datos['loterias']);
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l) use($datos){
                return $l['seleccionado'] == true && Lotteries::on($datos["servidor"])->where(['id' => $l['id'], 'status' => 1])->first() != null;
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
                if(Lotteries::on($datos["servidor"])->whereId($l['id'])->first()->sorteos()->wherePivot('idSorteo', $s['id'])->first() == null)
                    continue;
    
                   
    
                foreach($dias_seleccionadas as $d):
                    
                    $bloqueo = Blocksgenerals::on($datos["servidor"])->where(
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

                        

                        
                            $dia = Days::on($datos["servidor"])->where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                            if($dia != null){
                                $stocksJugadasDelDiaActual = Stock::on($datos["servidor"])->where(
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
                        $b = Blocksgenerals::on($datos["servidor"])->create([
                            'idLoteria' => $l['id'],
                            'idSorteo' => $s['id'],
                            'idDia' => $d['id'],
                            'monto' => $s['monto'],
                            'idMoneda' => $datos['idMoneda']
                        ]);

                        

                        $dia = Days::on($datos["servidor"])->where(['id' => $d['id'], 'wday' => $fecha['wday']])->first();
                        if($dia != null){
                            $stocksJugadasDelDiaActual = Stock::on($datos["servidor"])->where(
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
       
    
        $loterias = Lotteries::on($datos["servidor"])->select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
        $bancas = Branches::on($datos["servidor"])->select('id', 'descripcion')->whereStatus(1)->get();
    
    
        return Response::json([
            'loterias' => $loterias,
            'bancas' => $bancas,
            'sorteos' => Draws::on($datos["servidor"])->get(),
            'dias' => Days::on($datos["servidor"])->get(),
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
