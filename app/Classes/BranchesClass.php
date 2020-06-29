<?php
namespace App\Classes;

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
use App\Coins;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\BranchesResourceSmall;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class BranchesClass{
    private $servidor;
    private $datos;
    function __construct($servidor, $datos) {
        $this->servidor = $servidor;
        $this->datos = $datos;
    }

    public function save() : Branches
    {
        $this->tienePermisoUsuario();
        $banca = Branches::on($this->datos["servidor"])->whereId($this->datos['id'])->get()->first();
        if($banca != null){
            $banca = $this->update($banca);
        }else{
            $banca = $this->create();
        }

        $this->agregarLoterias($banca);
        $this->agregarDias($banca);
        $this->agregarComisiones($banca);
        $this->agregarPagosCombinaciones($banca);
        $this->agregarGastosAutomaticos($banca);

        return $banca;
    }

    private function tienePermisoUsuario()
    {
        $usuario = Users::on($this->datos["servidor"])->whereId($this->datos['idUsuario'])->first();
        if(!$usuario->tienePermiso('Manejar bancas')){
            abort(403, 'No tiene permisos para realizar esta accion');
        }
    }

    private function usuarioYaTieneBancaAsignada(Branches $banca = null)
    {
        if($banca == null){
            if(Branches::on($this->datos["servidor"])->where(['idUsuario'=> $this->datos['idUsuarioBanca'], 'status' => 1])->count() > 0)
            {
                abort(403, 'Este usuario ya tiene una banca registrada y solo se permite un usuario por banca');
            }
        }else{
            if(Branches::on($this->datos["servidor"])->where(['idUsuario'=> $this->datos['idUsuarioBanca'], 'status' => 1])->whereNotIn('id', [$banca->id])->first() != null){
                abort(403, 'Este usuario ya tiene una banca registrada y solo se permite un usuario por banca');
            }
        }
    }

    private function create()
    {
        $this->usuarioYaTieneBancaAsignada();

        return Branches::on($this->datos["servidor"])->create([
            'descripcion' => $this->datos['descripcion'],
            'ip' => $this->datos['ip'],
            'codigo' => $this->datos['codigo'],
            'idUsuario' => $this->datos['idUsuarioBanca'],
            'idMoneda' => $this->datos['idMoneda'],
            'dueno' => $this->datos['dueno'],
            'localidad' => $this->datos['localidad'],
            'limiteVenta' => $this->datos['limiteVenta'],
            'balanceDesactivacion' => $this->datos['balanceDesactivacion'],
            'descontar' => $this->datos['descontar'],
            'deCada' => $this->datos['deCada'],
            'minutosCancelarTicket' => $this->datos['minutosCancelarTicket'],
            'piepagina1' => $this->datos['piepagina1'],
            'piepagina2' => $this->datos['piepagina2'],
            'piepagina3' => $this->datos['piepagina3'],
            'piepagina4' => $this->datos['piepagina4'],
            'status' => $this->datos['status'],
            'imprimirCodigoQr' => $this->datos['imprimirCodigoQr']
        ]);
    }

    private function update(Branches $banca)
    {
        $this->usuarioYaTieneBancaAsignada($banca);

        $banca['descripcion'] = $this->datos['descripcion'];
        $banca['ip'] = $this->datos['ip'];
        $banca['codigo'] = $this->datos['codigo'];
        $banca['idUsuario'] = $this->datos['idUsuarioBanca'];
        $banca['idMoneda'] = $this->datos['idMoneda'];
        $banca['dueno'] = $this->datos['dueno'];
        $banca['localidad'] = $this->datos['localidad'];
        $banca['limiteVenta'] = $this->datos['limiteVenta'];
        $banca['balanceDesactivacion'] = $this->datos['balanceDesactivacion'];
        $banca['descontar'] = $this->datos['descontar'];
        $banca['deCada'] = $this->datos['deCada'];
        $banca['minutosCancelarTicket'] = $this->datos['minutosCancelarTicket'];
        $banca['piepagina1'] = $this->datos['piepagina1'];
        $banca['piepagina2'] = $this->datos['piepagina2'];
        $banca['piepagina3'] = $this->datos['piepagina3'];
        $banca['piepagina4'] = $this->datos['piepagina4'];
        $banca['status'] = $this->datos['status'];
        $banca['imprimirCodigoQr'] = $this->datos['imprimirCodigoQr'];
        $banca->save();
        return $banca;
    }

    private function agregarLoterias(Branches $banca)
    {
        //Eliminamos las loterias para luego agregarlos nuevamentes
        $banca->loterias()->detach();
        //$loterias = $datos['loteriasSeleccionadas']
        //Creamos una coleccion de loterias
        $loterias = collect($this->datos['loteriasSeleccionadas']);
        //Obtenemos las loterias seleccionadas (status == true)
        list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
            return $l['existe'] == true;
        });
        //Mapeamos la collecion para obtener los atributos idLoteria y idBanca
        $loterias_seleccionadas = collect($loterias_seleccionadas)->map(function($d) use($banca){
            return ['idLoteria' => $d['id'], 'idBanca' => $banca['id']];
        });
       
        //Guardamos loterias
        $banca->loterias()->attach($loterias_seleccionadas);
    }

    private function agregarDias(Branches $banca)
    {
        //Eliminamos los dias para luego agregarlos nuevamentes
        $banca->dias()->detach();
        $dias = Days::on($this->datos["servidor"])->get();
        $dias = collect($dias)->map(function($d) use($banca){
            switch ($d['descripcion']) {
                case 'Lunes':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["lunes"]["aperturaGuardar"], 'horaCierre' => $this->datos["lunes"]["cierreGuardar"] ];
                    break;
                case 'Martes':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["martes"]["aperturaGuardar"], 'horaCierre' => $this->datos["martes"]["cierreGuardar"] ];
                    break;
                case 'Miercoles':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["miercoles"]["aperturaGuardar"], 'horaCierre' => $this->datos["miercoles"]["cierreGuardar"] ];
                    break;
                case 'Jueves':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["jueves"]["aperturaGuardar"], 'horaCierre' => $this->datos["jueves"]["cierreGuardar"] ];
                    break;
                case 'Viernes':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["viernes"]["aperturaGuardar"], 'horaCierre' => $this->datos["viernes"]["cierreGuardar"] ];
                    break;
                case 'Sabado':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["sabado"]["aperturaGuardar"], 'horaCierre' => $this->datos["sabado"]["cierreGuardar"] ];
                    break;
                
                default:
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $this->datos["domingo"]["aperturaGuardar"], 'horaCierre' => $this->datos["domingo"]["cierreGuardar"] ];
                    break;
            }
        });
        
        $banca->dias()->attach($dias);
    }
    
    private function agregarComisiones(Branches $banca)
    {
        //Obtengo y guardo en un objeto los id de las loterias que han sido recibidas
        $idLoterias = collect($this->datos['loteriasSeleccionadas'])->map(function($id){
            return $id['id'];
        });
        //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
        // Commissions::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
        Commissions::on($this->datos["servidor"])->where('idBanca', $banca['id'])->delete();
        foreach($this->datos['loteriasSeleccionadas'] as $l){
            if($banca->loterias()->wherePivot('idLoteria', $l['id'])->first() != null){
                Commissions::on($this->datos["servidor"])->create([
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

        Helper::cambiarComisionesATickets($this->datos["servidor"], $banca['id']);
    }

    private function agregarPagosCombinaciones(Branches $banca)
    {
        //Obtengo y guardo en un objeto los id de las loterias que han sido recibidas
        $idLoterias = collect($this->datos['loteriasSeleccionadas'])->map(function($id){
            return $id['id'];
        });
        // return Response::json([
        //     'errores' => 0,
        //     'mensaje' => 'Se ha guardado correctamente',
        //     'banca' => $this->datos['loteriasSeleccionadas']
        // ], 201);
        //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
        // Payscombinations::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
        
        Payscombinations::on($this->datos["servidor"])->where('idBanca', $banca['id'])->delete();
        foreach($this->datos['loteriasSeleccionadas'] as $l){
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
                Payscombinations::on($this->datos["servidor"])->create([
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
    }

    private function agregarGastosAutomaticos(Branches $banca)
    {
        $idGastos = collect($this->datos['gastos'])->map(function($d){
            return $d['id'];
        });
         Automaticexpenses::on($this->datos["servidor"])->where('idBanca', $banca['id'])->whereNotIn('id', $idGastos)->delete();
         foreach($this->datos['gastos'] as $l){
            $gasto = Automaticexpenses::on($this->datos["servidor"])->where(['idBanca' => $banca['id'], 'id' => $l['id']])->first();
            

            
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
                Automaticexpenses::on($this->datos["servidor"])->create([
                    'idBanca' => $banca['id'],
                    'descripcion' => $l['descripcion'],
                    'monto' => $l['monto'],
                    'idFrecuencia' => $l['frecuencia']['id'],
                    'idDia' => $idDia,
                    // 'fechaInicio' => $l['fechaInicio'],
                ]);
            }

           
            
         }
    }
}