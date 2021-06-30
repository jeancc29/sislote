<?php
namespace App\Classes\v2;

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

class Branch{
    private $servidor;
    private $datos;
    function __construct($servidor, $datos) {
        $this->servidor = $servidor;
        $this->datos = $datos;
    }

    public static function save($data) : Branches
    {
       Branch::tienePermisoUsuario($data);
       Branch::usuarioYaTieneBancaAsignada($data);
        $banca = Branch::updateOrCreate($data);

        Branch::agregarLoterias($data, $banca);
        Branch::agregarDias($data, $banca);
        Branch::agregarComisiones($data, $banca);
        Branch::agregarPagosCombinaciones($data, $banca);
        Branch::agregarGastosAutomaticos($data, $banca);

        return $banca;
    }

    private static function tienePermisoUsuario($data)
    {
        $usuario = Users::on($data["servidor"])->whereId($data['usuarioData']["id"])->first();
        if(!$usuario->tienePermiso('Manejar bancas')){
            abort(403, 'No tiene permisos para realizar esta accion');
        }
    }

    private static function usuarioYaTieneBancaAsignada($data)
    {
        if(Branches::on($data["servidor"])->where(['idUsuario'=> $data['usuario']["id"], 'status' => 1])->whereNotIn('id', [$data["id"]])->first() != null){
            abort(403, 'Este usuario ya tiene una banca registrada y solo se permite un usuario por banca');
        }
        // if($banca == null){
        //     if(Branches::on($banca["servidor"])->where(['idUsuario'=> $this->datos['idUsuarioBanca'], 'status' => 1])->count() > 0)
        //     {
        //         abort(403, 'Este usuario ya tiene una banca registrada y solo se permite un usuario por banca');
        //     }
        // }else{
        //     if(Branches::on($banca["servidor"])->where(['idUsuario'=> $this->datos['idUsuarioBanca'], 'status' => 1])->whereNotIn('id', [$banca->id])->first() != null){
        //         abort(403, 'Este usuario ya tiene una banca registrada y solo se permite un usuario por banca');
        //     }
        // }
    }

    private static function updateOrCreate($data)
    {
        return Branches::on($data["servidor"])->updateOrCreate(
            ["id" => $data["id"]],
            [
            'descripcion' => $data['descripcion'],
            // 'ip' => $data['ip'],
            'ip' => isset($data['ip']) ? $data["ip"] : '',
            'codigo' => $data['codigo'],
            'idUsuario' => $data['usuario']["id"],
            'idMoneda' => $data['monedaObject']["id"],
            'dueno' => $data['dueno'],
            'localidad' => $data['localidad'],
            'limiteVenta' => $data['limiteVenta'],
            'balanceDesactivacion' => $data['balanceDesactivacion'],
            'descontar' => $data['descontar'],
            'deCada' => $data['deCada'],
            'minutosCancelarTicket' => $data['minutosCancelarTicket'],
            'piepagina1' => $data['piepagina1'],
            'piepagina2' => $data['piepagina2'],
            'piepagina3' => $data['piepagina3'],
            'piepagina4' => $data['piepagina4'],
            'status' => $data['status'],
            'imprimirCodigoQr' => $data['imprimirCodigoQr'],
            'idGrupo' => isset($data['grupo']) ? $data["grupo"]["id"] : null,
        ]);
    }

    private static function agregarLoterias($data, $banca)
    {
        //Eliminamos las loterias para luego agregarlos nuevamentes
        $banca->loterias()->detach();
        //$loterias = $datos['loteriasSeleccionadas']
        //Creamos una coleccion de loterias
        $loterias = collect($data['loterias']);
       
        //Mapeamos la collecion para obtener los atributos idLoteria y idBanca
        $loterias_seleccionadas = collect($loterias)->map(function($d) use($banca){
            return ['idLoteria' => $d['id'], 'idBanca' => $banca['id']];
        });
       
        //Guardamos loterias
        $banca->loterias()->attach($loterias_seleccionadas);
    }

    private static function agregarDias($data, Branches $banca)
    {
        //Eliminamos los dias para luego agregarlos nuevamentes
        $banca->dias()->detach();
        $dias = collect($data['dias']);
       
        //Mapeamos la collecion para obtener los atributos idLoteria y idBanca
        $diasToMap = collect($dias)->map(function($d) use($banca){
            return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $d['horaApertura'], 'horaCierre' => $d['horaCierre']];
        });
        
        $banca->dias()->attach($diasToMap);
    }
    
    private static function agregarComisiones($data, Branches $banca)
    {
        //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
        // Commissions::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
        Commissions::on($data["servidor"])->where('idBanca', $banca['id'])->delete();
        foreach($data['comisiones'] as $l){
            if($banca->loterias()->wherePivot('idLoteria', $l['idLoteria'])->first() != null){
                Commissions::on($data["servidor"])->create([
                    'idBanca' => $banca['id'],
                    'idLoteria' => $l['idLoteria'],
                    'directo' => $l['directo'],
                    'pale' => $l['pale'],
                    'tripleta' => $l['tripleta'],
                    'superPale' => $l['superPale'],
                    'pick3Straight' => $l['pick3Straight'],
                    'pick3Box' => $l['pick3Box'],
                    'pick4Straight' => $l['pick4Straight'],
                    'pick4Box' => $l['pick4Box'],
                ]);
            }
        }

        Helper::cambiarComisionesATickets($data["servidor"], $banca['id']);
    }

    private static function agregarPagosCombinaciones($data, Branches $banca)
    {
        Payscombinations::on($data["servidor"])->where('idBanca', $banca['id'])->delete();
        foreach($data['pagosCombinaciones'] as $l){
            if($banca->loterias()->wherePivot('idLoteria', $l['idLoteria'])->first() != null){
              
                if((new Helper)->isNumber($l['primera']) == false){
                    return abort(404, 'Campo primera no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['segunda']) == false){
                    return abort(404, 'Campo segunda no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['tercera']) == false){
                    return abort(404, 'Campo tercera no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['primeraSegunda']) == false){
                    return abort(404, 'Campo primeraSegunda no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['primeraTercera']) == false){
                    return abort(404, 'Campo primeraTercera no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['segundaTercera']) == false){
                    return abort(404, 'Campo segundaTercera no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['tresNumeros']) == false){
                    return abort(404, 'Campo tresNumeros no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['dosNumeros']) == false){
                    return abort(404, 'Campo dosNumeros no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['primerPago']) == false){
                    return abort(404, 'Campo primerPago no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick3TodosEnSecuencia']) == false){
                    return abort(404, 'Campo pick3 TodosEnSecuencia no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick33Way']) == false){
                    return abort(404, 'Campo pick3 3-way no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick36Way']) == false){
                    return abort(404, 'Campo pick3 6-way no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick4TodosEnSecuencia']) == false){
                    return abort(404, 'Campo pick4 TodosEnSecuencia no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick44Way']) == false){
                    return abort(404, 'Campo pick4 4-way no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick46Way']) == false){
                    return abort(404, 'Campo pick4 6-way no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick412Way']) == false){
                    return abort(404, 'Campo pick4 12-way no tiene formato correcto');
                }
                if((new Helper)->isNumber($l['pick424Way']) == false){
                    return abort(404, 'Campo pick4 24-way no tiene formato correcto');
                }
                Payscombinations::on($data["servidor"])->create([
                    'idBanca' => $banca['id'],
                    'idLoteria' => $l['idLoteria'],
                    'primera' => (int)$l['primera'],
                    'segunda' => (int)$l['segunda'],
                    'tercera' => (int)$l['tercera'],
                    'primeraSegunda' => (int)$l['primeraSegunda'],
                    'primeraTercera' => (int)$l['primeraTercera'],
                    'segundaTercera' => (int)$l['segundaTercera'],
                    'tresNumeros' => (int)$l['tresNumeros'],
                    'dosNumeros' => (int)$l['dosNumeros'],
                    'primerPago' => (int)$l['primerPago'],
                    'pick3TodosEnSecuencia' => (int)$l['pick3TodosEnSecuencia'],
                    'pick33Way' => (int)$l['pick33Way'],
                    'pick36Way' => (int)$l['pick36Way'],
                    'pick4TodosEnSecuencia' => (int)$l['pick4TodosEnSecuencia'],
                    'pick44Way' => (int)$l['pick44Way'],
                    'pick46Way' => (int)$l['pick46Way'],
                    'pick412Way' => (int)$l['pick412Way'],
                    'pick424Way' => (int)$l['pick424Way'],
                ]);
            }
        }
    }

    private static function agregarGastosAutomaticos($data, Branches $banca)
    {
        $idGastos = collect($data['gastos'])->map(function($d){
            return $d['id'];
        });
         Automaticexpenses::on($data["servidor"])->where('idBanca', $banca['id'])->whereNotIn('id', $idGastos)->delete();
         foreach($data['gastos'] as $l){
            $gasto = Automaticexpenses::on($data["servidor"])->where(['idBanca' => $banca['id'], 'id' => $l['id']])->first();
            if($gasto != null){

                $gasto['descripcion'] = $l['descripcion'];
                $gasto['monto'] = $l['monto'];
                $gasto['idFrecuencia'] = $l['frecuencia']['id'];
                if(strtolower($l['frecuencia']['descripcion']) == strtolower("SEMANAL")){
                    $gasto['idDia'] = $l['dia']['id'];
                }
                // $gasto['fechaInicio'] = $l['fechaInicio'];
                $gasto->save();
            }else{
                $idDia = null;
                if(strtolower($l['frecuencia']['descripcion']) == strtolower("SEMANAL")){
                    $idDia = $l['dia']['id'];
                }
                Automaticexpenses::on($data["servidor"])->create([
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