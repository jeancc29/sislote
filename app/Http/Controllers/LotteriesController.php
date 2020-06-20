<?php

namespace App\Http\Controllers;

use App\Lotteries;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;


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

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;
use App\Classes\Helper;

class LotteriesController extends Controller
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

            $u = Users::whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar loterias") == true){
                return redirect()->route('sinpermiso');
            }

            return view('loterias.index', compact('controlador'));
        }

        

        $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
        // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
        
    
        $cadena = "060829";
        $buscar = "99";
    
        
    
        return Response::json([
            'loterias' => LotteriesResource::collection(Lotteries::whereIn('status', [1,0])->get()),
            'dias' => Days::all(),
            'sorteos' => Draws::all()
        ], 201);

        
    }

    public function bloqueos()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        // $route = Route();
        //echo $controlador;

       // dd($controlador);

        
        return view('loterias.bloqueos', compact('controlador'));
        //return view('loterias.index');
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
            'datos.id' => 'required',
            'datos.descripcion' => 'required',
            'datos.abreviatura' => 'required|min:1|max:10',
            'datos.status' => 'required',
            // 'datos.horaCierre' => 'required',
            'datos.sorteos' => 'required',
            'datos.loterias' => '',
    
        ])['datos'];


        //Validamos que cuando el sorteo super pale este seleccionado entonces no se permiten mas sorteos
        $sorteoCollection = collect($datos['sorteos']);
        $es_superpale = false;
        foreach($sorteoCollection as $s){
            if($s['descripcion'] == "Super pale" || $s['id'] == 4)
                $es_superpale = true;
        }

        if($es_superpale == true && count($sorteoCollection) > 1){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Cuando el sorteo Super pale esta seleccionado no se permiten mas sorteos'
            ], 201);
        }
    
        $errores = 0;
        $mensaje = '';
    
        $loterias = collect($datos['loterias']);
        list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
            return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
        });

        
        foreach($loterias_seleccionadas as $s){
            //Verificamos que las loterias seleccionada para super pale deben tener el sorteo pale asignados a ellas
            if(Lotteries::whereId($s['id'])->first()->sorteos()->whereDescripcion('Pale')->first() == null){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Debe asignarle el sorteo pale a la loteria ' . Lotteries::whereId($s['id'])->first()->descripcion
                ], 201);
            }
        }

       
    
    
        $loteria = Lotteries::whereId($datos['id'])->get()->first();
        if($loteria != null){
            $loteria['descripcion'] = $datos['descripcion'];
            $loteria['abreviatura'] = $datos['abreviatura'];
            $loteria['status'] = $datos['status'];
            // $loteria['horaCierre'] = $datos['horaCierre'];
            $loteria->save();
    
    
            $loteria->sorteos()->detach();
            $sorteos = collect($datos['sorteos'])->map(function($s) use($loteria){
                
                return ['idSorteo' => $s['id'], 'idLoteria' => $loteria['id'] ];
            });
            $loteria->sorteos()->attach($sorteos);

            foreach($datos['sorteos'] as $s){
                $sorteo = Draws::whereId($s['id'])->first();
                if($sorteo != null){
                    if($sorteo['descripcion'] == "Super pale"){
                        $loteria->drawRelations()->detach();
                        // $loterias = collect($datos['loterias']);
                        // list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                        //     return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
                        // });

                        
                        $loterias_seleccionadas = collect($loterias_seleccionadas)->map(function($d) use($sorteo, $loteria){
                            return ['idSorteo' => $sorteo['id'], 'idLoteriaPertenece' => $loteria['id'], 'idLoteria' => $d['id'] ];
                        });

                        $loteria->drawRelations()->attach($loterias_seleccionadas);
                    }
                }
            }
            
            // foreach($datos['sorteos'] as $s){
            //     $sorteo = Draws::whereId($s['id'])->first();
            //     if($sorteo != null){
            //         if($sorteo['descripcion'] == "Super pale"){
            //             DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            //             $sorteo->loteriasRelacionadas()->where('idLoteriaPertenece', $loteria['id'])->delete();
            //             DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            //             $loteriasRelacionadas = collect($datos['loterias'])->map(function($d) use($sorteo, $loteria){
                
            //                 return ['idSorteo' => $sorteo['id'], 'idLoteriaPertenece' => $loteria['id'], 'idLoteria' => $d['id'] ];
            //             });
            //             $sorteo->loteriasRelacionadas()->attach($loteriasRelacionadas);
            //         }
            //     }
            // }
    
        }else{
            $loteria = Lotteries::create([
                'descripcion' => $datos['descripcion'],
                'abreviatura' => $datos['abreviatura'],
                // 'horaCierre' => $datos['horaCierre'],
                'status' => $datos['status'],
            ]);
    
          
    
            $loteria->sorteos()->detach();
            $sorteos = collect($datos['sorteos'])->map(function($s) use($loteria){
                return ['idSorteo' => $s['id'], 'idLoteria' => $loteria['id'] ];
            });
            $loteria->sorteos()->attach($sorteos);

           

            foreach($datos['sorteos'] as $s){
                $sorteo = Draws::whereId($s['id'])->first();
                if($sorteo != null){
                    if($sorteo['descripcion'] == "Super pale"){
                        $loteria->drawRelations()->detach();
                        // $loterias = collect($datos['loterias']);
                        // list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                        //     return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
                        // });

                        $loterias_seleccionadas = collect($loterias_seleccionadas)->map(function($d) use($sorteo, $loteria){
                            return ['idSorteo' => $sorteo['id'], 'idLoteriaPertenece' => $loteria['id'], 'idLoteria' => $d['id'] ];
                        });
                        $loteria->drawRelations()->attach($loterias_seleccionadas);
                    }
                }
            }

            // foreach($datos['sorteos'] as $s){
            //     $sorteo = Draws::whereId($s['id'])->first();
            //     if($sorteo != null){
            //         if($sorteo['descripcion'] == "Super pale"){
            //             DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            //             $sorteo->loteriasRelacionadas()->where('idLoteriaPertenece', $loteria['id'])->delete();
            //             DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            //             $loteriasRelacionadas = collect($datos['loterias'])->map(function($d) use($sorteo, $loteria){
                
            //                 return ['idSorteo' => $sorteo['id'], 'idLoteriaPertenece' => $loteria['id'], 'idLoteria' => $d['id'] ];
            //             });
            //             $sorteo->loteriasRelacionadas()->attach($loteriasRelacionadas);
            //         }
            //     }
            // }
            
        }
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'loterias' => LotteriesResource::collection(Lotteries::whereIn('status', [1,0])->get()),
            'dias' => Days::all(),
            'sorteos' => Draws::all(),
            'aa' => $sorteos
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lotteries  $lotteries
     * @return \Illuminate\Http\Response
     */
    public function show(Lotteries $lotteries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lotteries  $lotteries
     * @return \Illuminate\Http\Response
     */
    public function edit(Lotteries $lotteries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lotteries  $lotteries
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lotteries $lotteries)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lotteries  $lotteries
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lotteries $lotteries)
    {
        $datos = request()->validate([
            'datos.id' => 'required',
            'datos.descripcion' => 'required',
            'datos.abreviatura' => 'required',
            'datos.status' => 'required'
        ])['datos'];

        $loteria = Lotteries::whereId($datos['id'])->first();
        if($loteria != null){
            $loteria->status = 2;
            $loteria->save();

            return Response::json([
                'errores' => 0,
                'mensaje' => 'Se ha eliminado correctamente'
            ], 201);
        }

        return Response::json([
            'errores' => 1,
            'mensaje' => 'Error al eliminar loteria'
        ], 201);
    }
}
