<?php
namespace App\Classes;

use App\Awards;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;


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


class AwardsClass{
    public $loteria;
    public $primera;
    public $segunda;
    public $tercera;
    public $numerosGanadores;
    public $idUsuario;

    function __construct($idLteria) {
        $this->loteria = Lotteries::whereId($idLteria)->first();
    }

    public function getLoteriaDescripcion(){
        return $this->loteria['descripcion'];
    }

    public function combinacionesNula(){
        $es_superpale = $this->loteria->sorteos()->whereDescripcion('Super pale')->first();
        if($es_superpale == null){
            //Si uno de estos campos es nulo entonces eso quiere decir que esta loteria no se insertara, asi que pasaremos a la siguiente loteria
            if(!is_numeric($this->primera) || !is_numeric($this->segunda) ||  !is_numeric($this->tercera))
                return true;
        }else{
            if(!is_numeric($this->primera) || !is_numeric($this->segunda))
            return true;
        }

        return false;
    }

    public function loteriaAbreDiaActual(){
        $loteriaWday = $this->loteria->dias()->whereWday(getdate()['wday'])->get()->first();
        if($loteriaWday == null){
            return false;
        }

        return true;
    }

    public function insertarPremio(){
        $guardadoCorrectamente = true;
        try{
            $fechaActual = getdate();
            $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
            $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
            
            $numeroGanador = Awards::where('idLoteria', $this->loteria['id'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get()->first();

                //Si es diferente de nulo entonces existe, asi que debo actualizar los numeros ganadores
                if($numeroGanador != null){
                    $numeroGanador['numeroGanador'] = $this->numerosGanadores;
                    $numeroGanador['primera'] =  $this->primera;
                    $numeroGanador['segunda'] = $this->segunda;
                    $numeroGanador['tercera'] =  $this->tercera;
                    $numeroGanador->save();
                }else{
                    Awards::create([
                        'idUsuario' => $this->idUsuario,
                        'idLoteria' => $this->loteria->id,
                        'numeroGanador' => $this->numerosGanadores,
                        'primera' =>  $this->primera,
                        'segunda' => $this->segunda,
                        'tercera' => $this->tercera
                    ]);
                }
        }catch (Exception $e) {
            $guardadoCorrectamente = false;
        }

        return $guardadoCorrectamente;
    }

    public function getJugadasDeHoy($idLoteria){
        $fechaActual = getdate();
        $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
        $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';

        $idVentas = Sales::select('sales.id')
        ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
        ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
        ->where('salesdetails.idLoteria', $idLoteria)->where('sales.status', '!=', 0)->get();

        $jugadas = Salesdetails::whereIn('idVenta', $idVentas)
                ->orderBy('jugada', 'asc')
                ->get();

        return $jugadas;
    }

    public function directoBuscarPremio($idVenta, $idLoteria, $jugada, $monto){
        $premio = 0;
        $busqueda = strpos($this->numerosGanadores, $jugada);
                    
                    
        if(gettype($busqueda) == "integer"){
            $venta = Sales::whereId($idVenta)->first();
            $idBanca = Branches::whereId($venta->idBanca)->first()->id;
            if($busqueda == 0) $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primera');
            else if($busqueda == 2) $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('segunda');
            else if($busqueda == 4) $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('tercera');
        }
        else
            $premio = 0;

        return $premio;
    }

    public function paleBuscarPremio($idVenta, $idLoteria, $jugada, $monto, $idSorteo){
        // return Response::json(['numGanador' => $numeroGanador['numeroGanador'],'juada' => substr('jean', 0, 2)], 201);
        $premio = 0;
        $busqueda1 = strpos($numerosGanadores, substr($jugada, 0, 2));
        $busqueda2 = strpos($numerosGanadores, substr($jugada, 2, 2));

        $venta = Sales::whereId($idVenta)->first();
        $idBanca = Branches::whereId($venta->idBanca)->first()->id;
        $sorteo = Draws::whereId($idSorteo)->first();

       //Si el sorteo es diferente de super pale entonces es un pale normal
        if($sorteo['descripcion'] != "Super pale"){
            //Verificamos que los tipos de datos de las busquedas sean enteros
            if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer"){
                //Primera y segunda
                if($busqueda1 == 0 && $busqueda2 == 2 || $busqueda2 == 0 && $busqueda1 == 2){
                    $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primeraSegunda');
                }
                //Primera y tercera
                else if($busqueda1 == 0 && $busqueda2 == 4 || $busqueda2 == 0 && $busqueda1 == 4){
                    $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primeraTercera');
                }
                //Segunda y tercera
                else if($busqueda1 == 2 && $busqueda2 == 4 || $busqueda2 == 2 && $busqueda1 == 4){
                    $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('segundaTercera');
                }
            }else $premio = 0;
        }else{
            //Verificamos que los tipos de datos de las busquedas sean enteros
            if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer"){
                if($busqueda1 == 0 && $busqueda2 == 2 || $busqueda2 == 0 && $busqueda1 == 2){
                    $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primerPago');
                }
            }else $premio = 0;
        }

        return $premio;
    }

    public function tripletaBuscarPremio($idVenta, $idLoteria, $jugada, $monto){
        $premio = 0;
        $contador = 0;
        $busqueda1 = strpos($numerosGanadores, substr($jugada, 0, 2));
        $busqueda2 = strpos($numerosGanadores, substr($jugada, 2, 2));
        $busqueda3 = strpos($numerosGanadores, substr($jugada, 4, 2));

        $venta = Sales::whereId($idVenta)->first();
        $idBanca = Branches::whereId($venta->idBanca)->first()->id;

        if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer" && gettype($busqueda3) == "integer"){
            $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('tresNumeros');
        }
        else{
            if(gettype($busqueda1) == "integer")
                $contador++;
            if(gettype($busqueda2) == "integer")
                $contador++;
            if(gettype($busqueda3) == "integer")
                $contador++;
            
            //Si el contador es = 2 entonces hay premio
            if($contador == 2)
                $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('dosNumeros');
            else
                $premio = 0;
        }

        return $premio;
    }
}