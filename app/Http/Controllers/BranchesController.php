<?php

namespace App\Http\Controllers;

use App\Branches;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Classes\Helper;


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
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Frecuency;
use App\Automaticexpenses;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class BranchesController extends Controller
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
            return view('bancas.index', compact('controlador'));
        }
        
       


        
        $bancas = Branches::whereIn('status', array(0, 1))->get();


        return Response::json([
            'bancas' => BranchesResource::collection($bancas),
            'usuarios' => Users::whereIn('status', array(0, 1))->get(),
            'loterias' => LotteriesResource::collection(Lotteries::whereStatus(1)->has('sorteos')->get()),
            'frecuencias' => Frecuency::all(),
            'dias' => Days::all()
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
            'datos.id' => 'required',
            'datos.descripcion' => 'required',
            'datos.ip' => 'required|min:1|max:15',
            'datos.codigo' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idUsuarioBanca' => 'required',
            'datos.dueno' => 'required',
            'datos.localidad' => 'required',
    
    
            'datos.balanceDesactivacion' => '',
            'datos.limiteVenta' => 'required',
            'datos.descontar' => 'required',
            'datos.deCada' => 'required',
            'datos.minutosCancelarTicket' => 'required',
            'datos.piepagina1' => '',
            'datos.piepagina2' => '',
            'datos.piepagina3' => '',
            'datos.piepagina4' => '',
            'datos.status' => 'required',
    
            'datos.lunes' => '',
            'datos.martes' => '',
            'datos.miercoles' => '',
            'datos.jueves' => '',
            'datos.viernes' => '',
            'datos.sabado' => '',
            'datos.domingo' => '',
    
            'datos.comisiones' => 'required',
            'datos.pagosCombinaciones' => 'required',
            'datos.loteriasSeleccionadas' => 'required',
            'datos.gastos' => '',
        ])['datos'];
    
    
        $errores = 0;
        $mensaje = '';
    
        
        $usuario = Users::whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso('Manejar bancas')){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }
    
       
        $banca = Branches::whereId($datos['id'])->get()->first();
        
    
        if($banca != null){
    
            if(Branches::where(['idUsuario'=> $datos['idUsuarioBanca'], 'status' => 1])->whereNotIn('id', [$banca->id])->first() != null){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Este usuario ya tiene una banca registrada y solo se permite un usaurio por banca'
                ], 201);
            }
            
            $banca['descripcion'] = $datos['descripcion'];
            $banca['ip'] = $datos['ip'];
            $banca['codigo'] = $datos['codigo'];
            $banca['idUsuario'] = $datos['idUsuarioBanca'];
            $banca['dueno'] = $datos['dueno'];
            $banca['localidad'] = $datos['localidad'];
            $banca['limiteVenta'] = $datos['limiteVenta'];
            $banca['balanceDesactivacion'] = $datos['balanceDesactivacion'];
            $banca['descontar'] = $datos['descontar'];
            $banca['deCada'] = $datos['deCada'];
            $banca['minutosCancelarTicket'] = $datos['minutosCancelarTicket'];
            $banca['piepagina1'] = $datos['piepagina1'];
            $banca['piepagina2'] = $datos['piepagina2'];
            $banca['piepagina3'] = $datos['piepagina3'];
            $banca['piepagina4'] = $datos['piepagina4'];
            $banca['status'] = $datos['status'];
            $banca->save();
    
        }else{
            if(Branches::where(['idUsuario'=> $datos['idUsuarioBanca'], 'status' => 1])->count() > 0)
            {
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Este usuario ya tiene una banca registrada y solo se permite un usaurio por banca'
                ], 201);
            }
            $banca = Branches::create([
                'descripcion' => $datos['descripcion'],
                'ip' => $datos['ip'],
                'codigo' => $datos['codigo'],
                'idUsuario' => $datos['idUsuarioBanca'],
                'dueno' => $datos['dueno'],
                'localidad' => $datos['localidad'],
                'limiteVenta' => $datos['limiteVenta'],
                'balanceDesactivacion' => $datos['balanceDesactivacion'],
                'descontar' => $datos['descontar'],
                'deCada' => $datos['deCada'],
                'minutosCancelarTicket' => $datos['minutosCancelarTicket'],
                'piepagina1' => $datos['piepagina1'],
                'piepagina2' => $datos['piepagina2'],
                'piepagina3' => $datos['piepagina3'],
                'piepagina4' => $datos['piepagina4'],
                'status' => $datos['status']
            ]);
    
    
           
        }
    
        /********************* LOTERIAS ************************/
            //Eliminamos los dias para luego agregarlos nuevamentes
            $banca->loterias()->detach();
            //$loterias = $datos['loteriasSeleccionadas']
            //Creamos una coleccion de loterias
            $loterias = collect($datos['loteriasSeleccionadas']);
            //Obtenemos las loterias seleccionadas (status == true)
            list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
                return $l['existe'] == true;
            });
            //Mapeamos la collecion para obtener los atributos idLoteria y idBanca
            $loterias_seleccionadas = collect($loterias_seleccionadas)->map(function($d) use($banca, $datos){
                return ['idLoteria' => $d['id'], 'idBanca' => $banca['id']];
            });
           
            //Guardamos loterias
            $banca->loterias()->attach($loterias_seleccionadas);
    
          /********************* DIAS ************************/
            //Eliminamos los dias para luego agregarlos nuevamentes
            $banca->dias()->detach();
            $dias = Days::all();
            $dias = collect($dias)->map(function($d) use($banca, $datos){
                switch ($d['descripcion']) {
                    case 'Lunes':
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["lunes"]["apertura"], 'horaCierre' => $datos["lunes"]["cierre"] ];
                        break;
                    case 'Martes':
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["martes"]["apertura"], 'horaCierre' => $datos["martes"]["cierre"] ];
                        break;
                    case 'Miercoles':
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["miercoles"]["apertura"], 'horaCierre' => $datos["miercoles"]["cierre"] ];
                        break;
                    case 'Jueves':
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["jueves"]["apertura"], 'horaCierre' => $datos["jueves"]["cierre"] ];
                        break;
                    case 'Viernes':
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["viernes"]["apertura"], 'horaCierre' => $datos["viernes"]["cierre"] ];
                        break;
                    case 'Sabado':
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["sabado"]["apertura"], 'horaCierre' => $datos["sabado"]["cierre"] ];
                        break;
                    
                    default:
                        return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["domingo"]["apertura"], 'horaCierre' => $datos["domingo"]["cierre"] ];
                        break;
                }
            });
           
            $banca->dias()->attach($dias);
    
    
            /********************* COMISIONES ************************/
            //Obtengo y guardo en un objeto los id de las loterias que han sido recibidas
            $idLoterias = collect($datos['loteriasSeleccionadas'])->map(function($id){
                return $id['id'];
            });
            //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
            // Commissions::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
            Commissions::where('idBanca', $banca['id'])->delete();
            foreach($datos['loteriasSeleccionadas'] as $l){
                if($banca->loterias()->wherePivot('idLoteria', $l['id'])->first() != null){
                    Commissions::create([
                        'idBanca' => $banca['id'],
                        'idLoteria' => $l['id'],
                        'directo' => $l['comisiones']['directo'],
                        'pale' => $l['comisiones']['pale'],
                        'tripleta' => $l['comisiones']['tripleta'],
                        'superPale' => $l['comisiones']['superPale'],
                        'pick3Straight' => $l['comisiones']['pick3Straight'],
                        'pick3Box' => $l['comisiones']['pick3Box'],
                        'pick4Straight' => $l['comisiones']['pick4Straight'],
                        'pick4Box' => $l['comisiones']['pick4Box'],
                    ]);
                }
            }

            Helper::cambiarComisionesATickets($banca['id']);
    
    
    
             /********************* PAGOS COMBINACIONES ************************/
            //Obtengo y guardo en un objeto los id de las loterias que han sido recibidas
            $idLoterias = collect($datos['loteriasSeleccionadas'])->map(function($id){
                return $id['id'];
            });
            // return Response::json([
            //     'errores' => 0,
            //     'mensaje' => 'Se ha guardado correctamente',
            //     'banca' => $datos['loteriasSeleccionadas']
            // ], 201);
            //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
            // Payscombinations::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
            
            Payscombinations::where('idBanca', $banca['id'])->delete();
            foreach($datos['loteriasSeleccionadas'] as $l){
                if($banca->loterias()->wherePivot('idLoteria', $l['id'])->first() != null){
                  
                    if((new Helper)->isNumber($l['pagosCombinaciones']['primera']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo primera no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['segunda']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo segunda no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['tercera']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo tercera no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['primeraSegunda']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo primeraSegunda no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['primeraTercera']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo primeraTercera no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['segundaTercera']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo segundaTercera no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['tresNumeros']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo tresNumeros no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['dosNumeros']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo dosNumeros no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['primerPago']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo primerPago no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick3TodosEnSecuencia']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick3 TodosEnSecuencia no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick33Way']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick3 3-way no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick36Way']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick3 6-way no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick4TodosEnSecuencia']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick4 TodosEnSecuencia no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick44Way']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick4 4-way no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick46Way']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick4 6-way no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick412Way']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick4 12-way no tiene formato correcto'], 201);
                    }
                    if((new Helper)->isNumber($l['pagosCombinaciones']['pick424Way']) == false){
                        return Response::json(['errores' => 1,'mensaje' => 'Campo pick4 24-way no tiene formato correcto'], 201);
                    }
                    Payscombinations::create([
                        'idBanca' => $banca['id'],
                        'idLoteria' => $l['id'],
                        'primera' => (int)$l['pagosCombinaciones']['primera'],
                        'segunda' => (int)$l['pagosCombinaciones']['segunda'],
                        'tercera' => (int)$l['pagosCombinaciones']['tercera'],
                        'primeraSegunda' => (int)$l['pagosCombinaciones']['primeraSegunda'],
                        'primeraTercera' => (int)$l['pagosCombinaciones']['primeraTercera'],
                        'segundaTercera' => (int)$l['pagosCombinaciones']['segundaTercera'],
                        'tresNumeros' => (int)$l['pagosCombinaciones']['tresNumeros'],
                        'dosNumeros' => (int)$l['pagosCombinaciones']['dosNumeros'],
                        'primerPago' => (int)$l['pagosCombinaciones']['primerPago'],
                        'pick3TodosEnSecuencia' => (int)$l['pagosCombinaciones']['pick3TodosEnSecuencia'],
                        'pick33Way' => (int)$l['pagosCombinaciones']['pick33Way'],
                        'pick36Way' => (int)$l['pagosCombinaciones']['pick36Way'],
                        'pick4TodosEnSecuencia' => (int)$l['pagosCombinaciones']['pick4TodosEnSecuencia'],
                        'pick44Way' => (int)$l['pagosCombinaciones']['pick44Way'],
                        'pick46Way' => (int)$l['pagosCombinaciones']['pick46Way'],
                        'pick412Way' => (int)$l['pagosCombinaciones']['pick412Way'],
                        'pick424Way' => (int)$l['pagosCombinaciones']['pick424Way'],
                    ]);
                }
            }
    

             /********************* GASTOS AUTOMATICOS ************************/
             $idGastos = collect($datos['gastos'])->map(function($d){
                return $d['id'];
            });
             Automaticexpenses::where('idBanca', $banca['id'])->whereNotIn('id', $idGastos)->delete();
             foreach($datos['gastos'] as $l){
                $gasto = Automaticexpenses::where(['idBanca' => $banca['id'], 'id' => $l['id']])->first();
                

                
                if($gasto != null){
                    
                    // if($l['fechaInicio'] != $gasto['fechaInicio']){
                    //     //Fecha actual
                    //     $first = Carbon::now();
                    //     //Fecha modificada
                    //     $second = new Carbon($l['fechaInicio']);
                    //     if($first->greaterThan($second)){
                    //         return Response::json([
                    //             'errores' => 1,
                    //             'mensaje' => 'La fecha modificada de un gasto debe ser mayor o igual a la fecha actual'
                    //         ], 201);
                    //     }
                    // }


                    $gasto['descripcion'] = $l['descripcion'];
                    $gasto['monto'] = $l['monto'];
                    $gasto['idFrecuencia'] = $l['frecuencia']['id'];
                    if(strtolower($l['frecuencia']['descripcion']) == strtolower("SEMANAL")){
                        $gasto['idDia'] = $l['idDia'];
                    }
                    // $gasto['fechaInicio'] = $l['fechaInicio'];
                    $gasto->save();
                }else{
                    $idDia = null;
                    if(strtolower($l['frecuencia']['descripcion']) == strtolower("SEMANAL")){
                        $idDia = $l['idDia'];
                    }
                    Automaticexpenses::create([
                        'idBanca' => $banca['id'],
                        'descripcion' => $l['descripcion'],
                        'monto' => $l['monto'],
                        'idFrecuencia' => $l['frecuencia']['id'],
                        'idDia' => $idDia,
                        // 'fechaInicio' => $l['fechaInicio'],
                    ]);
                }

               
                
             }
             
    
            
            
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'banca' => BranchesResource::collection(Branches::whereId($banca->id)->get()),
            'bancas' => BranchesResource::collection(Branches::whereIn('status', array(0, 1))->get()),
            'gastos' => $datos['gastos']
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branches  $branches
     * @return \Illuminate\Http\Response
     */
    public function show(Branches $branches)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Branches  $branches
     * @return \Illuminate\Http\Response
     */
    public function edit(Branches $branches)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branches  $branches
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branches $branches)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branches  $branches
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $datos = request()->validate([
            'datos.id' => 'required'
        ])['datos'];
    
        $errores = 0;
        $mensaje = 'Se ha guardado correctamente';
    
       
        $banca = Branches::whereId($datos['id'])->get()->first();
        
    
        if($banca != null){
            $banca['status'] = 2;
            $banca->save();
    
        }else{
            $errores = 1;
            $mensaje = 'La banca no existe';
        }
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }
}
