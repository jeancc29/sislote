<?php
namespace App\Classes;

use App\Awards;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;


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
use App\Classes\Helper;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;


class AwardsClass{
    public $servidor;
    public $fecha;
    public $loteria;
    public $primera;
    public $segunda;
    public $tercera;
    public $pick3;
    public $pick4;
    public $numerosGanadores;
    public $idUsuario;

    function __construct($servidor, $idLteria) {
        $this->servidor = $servidor;
        $this->loteria = Lotteries::on($this->servidor)->whereId($idLteria)->first();
    }

    public function getLoteriaDescripcion(){
        return $this->loteria['descripcion'];
    }

    public function datosValidos(){
        $sorteos = $this->loteria->sorteos;

        $validos = true;

        foreach($sorteos as $sorteo):

        if($sorteo == null)
            return false;

        if($sorteo['descripcion'] == "Directo" || $sorteo['descripcion'] == "Pale" || $sorteo['descripcion'] == "Tripleta" || $sorteo['descripcion'] == "Super pale"){
            if(!Helper::isNumber($this->primera)){
                $validos = false;
            }
            else if(strlen($this->primera) != 2){
                $validos = false;
            }
            if(!Helper::isNumber($this->segunda)){
                $validos = false;
            }
            else if(strlen($this->segunda) != 2){
                $validos = false;
            }
            if($sorteo['descripcion'] != "Super pale"){
                if(!Helper::isNumber($this->tercera)){
                    $validos = false;
                }
                else if(strlen($this->tercera) != 2){
                    $validos = false;
                }
            } 
        }
        else if($sorteo['descripcion'] == "Pick 3 Straight" || $sorteo['descripcion'] == "Pick 3 Box"){
            return $sorteo;
            if(!Helper::isNumber($this->pick3)){
                $validos = false;
            }
            else if(strlen($this->pick3) != 3){
                $validos = false;
            }
        }
        else if($sorteo['descripcion'] == "Pick 4 Straight" || $sorteo['descripcion'] == "Pick 4 Box"){
            return $sorteo;
            if(!Helper::isNumber($this->pick4)){
                $validos = false;
            }
            else if(strlen($this->pick4) != 4){
                $validos = false;
            }
        }

    endforeach;

        return $validos;
    }

    public function combinacionesNula(){
        $es_superpale = $this->loteria->sorteos()->whereDescripcion('Super pale')->first();
        if($es_superpale == null){
            if($this->loteria->sorteos()->whereDescripcion('Directo')->first() != null 
            || $this->loteria->sorteos()->whereDescripcion('Pale')->first()
            || $this->loteria->sorteos()->whereDescripcion('Tripleta')->first())
            {
                //Si uno de estos campos es nulo entonces eso quiere decir que esta loteria no se insertara, asi que pasaremos a la siguiente loteria
                if(!is_numeric($this->primera) || !is_numeric($this->segunda) ||  !is_numeric($this->tercera))
                    return true;
            }

            if($this->loteria->sorteos()->whereDescripcion('Pick 3 Straight')->first() != null 
            || $this->loteria->sorteos()->whereDescripcion('Pick 3 Box')->first()
            )
            {
                //Si uno de estos campos es nulo entonces eso quiere decir que esta loteria no se insertara, asi que pasaremos a la siguiente loteria
                if(!is_numeric($this->pick3))
                    return true;
            }
            if($this->loteria->sorteos()->whereDescripcion('Pick 4 Straight')->first() != null 
            || $this->loteria->sorteos()->whereDescripcion('Pick 4 Box')->first()
            )
            {
                //Si uno de estos campos es nulo entonces eso quiere decir que esta loteria no se insertara, asi que pasaremos a la siguiente loteria
                if(!is_numeric($this->pick4))
                    return true;
            }
        }else{
            if(!is_numeric($this->primera) || !is_numeric($this->segunda))
            return true;
        }

        return false;
    }

    public function loteriaAbreDiaActual(){
        $loteriaWday = $this->loteria->dias()->whereWday($this->fecha['wday'])->get()->first();
        if($loteriaWday == null){
            return false;
        }

        return true;
    }

    public function insertarPremio(){
        $guardadoCorrectamente = true;
        try{
            $fechaActual = $this->fecha;
            $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
            $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
            
            $numeroGanador = Awards::on($this->servidor)->where('idLoteria', $this->loteria['id'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get()->first();

                //Si es diferente de nulo entonces existe, asi que debo actualizar los numeros ganadores
                if($numeroGanador != null){
                    $numeroGanador['numeroGanador'] = $this->numerosGanadores;
                    $numeroGanador['primera'] =  $this->primera;
                    $numeroGanador['segunda'] = $this->segunda;
                    $numeroGanador['tercera'] =  $this->tercera;
                    $numeroGanador['pick3'] =  $this->pick3;
                    $numeroGanador['pick4'] =  $this->pick4;
                    $numeroGanador->save();
                }else{
                    $fechaString = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' ' .$fechaActual['hours'] . ':' . $fechaActual['minutes'];
                    $fecha = new Carbon($fechaString);
                    // abort(403, "No tiene permisos para realizar esta accion: $this->servidor");
                    // $f->toDatetimeString
                    $premio = new  Awards;
                    $premio->setConnection($this->servidor);
                    $premio->idUsuario = $this->idUsuario;
                    $premio->idLoteria = $this->loteria->id;
                    $premio->numeroGanador = $this->numerosGanadores;
                    $premio->primera = $this->primera;
                    $premio->segunda = $this->segunda;
                    $premio->tercera = $this->tercera;
                    $premio->pick3 = $this->pick3;
                    $premio->pick4 = $this->pick4;
                    $premio->created_at = $fecha;
                    $premio->save();

                    // Awards::on($this->servidor)->create([
                    //     'idUsuario' => $this->idUsuario,
                    //     'idLoteria' => $this->loteria->id,
                    //     'numeroGanador' => $this->numerosGanadores,
                    //     'primera' =>  $this->primera,
                    //     'segunda' => $this->segunda,
                    //     'tercera' => $this->tercera,
                    //     'pick3' => $this->pick3,
                    //     'pick4' => $this->pick4,
                    //     'created_at' => $fecha
                    // ]);
                }
        }catch (Exception $e) {
            $guardadoCorrectamente = false;
        }

        return $guardadoCorrectamente;
    }

    public function eliminarPremio(){
        $eliminadoCorrectamente = true;
        try{
            $fechaActual = $this->fecha;
            $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
            $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
            
            $numeroGanador = Awards::on($this->servidor)->where('idLoteria', $this->loteria['id'])
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get()->first();

            //Si es diferente de nulo entonces existe, asi que debo eliminar
            if($numeroGanador != null){
                Awards::on($this->servidor)->where('idLoteria', $this->loteria['id'])
                ->whereBetween('created_at', array($fechaInicial, $fechaFinal))->delete();
            }
        }catch (Exception $e) {
            $eliminadoCorrectamente = false;
        }

        return $eliminadoCorrectamente;
    }

    public function getJugadasDeFechaDada($idLoteria){
        $fechaActual = $this->fecha;
        $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
        $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:59:00';
        $idSuperpale = Draws::on($this->servidor)->whereDescripcion("Super pale")->first()->id;

        
        $idVentas = Sales::on($this->servidor)->select('sales.id')
        ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
        ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
        ->where('salesdetails.idLoteria', $idLoteria)
        ->whereNotIn('sales.status', [0,5])
        ->where('salesdetails.idSorteo', '!=', $idSuperpale)
        ->get();

        // abort(404, $fechaInicial . " " . $fechaFinal);

        $jugadas = Salesdetails::on($this->servidor)->whereIn('idVenta', $idVentas)->where('idLoteria', $idLoteria)
            ->orderBy('jugada', 'asc')
            ->where('salesdetails.idSorteo', '!=', $idSuperpale)
            ->get();

        return $jugadas;
    }

    public static function getVentasDeFechaDada($servidor, $idLoteria, $fecha){
        $fechaActual = $fecha;
        $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
        $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
        $idSuperpale = Draws::on($servidor)->whereDescripcion("Super pale")->first()->id;

        $idVentas = Sales::on($servidor)->select('sales.id')
        ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
        ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
        ->where('salesdetails.idLoteria', $idLoteria)
        ->whereNotIn('sales.status', [0,5])
        ->get();

        $ventas = Sales::on($servidor)->whereIn('id', $idVentas)
                ->orderBy('id', 'asc')
                ->get();

        return $ventas;
    }

    public function getJugadasSuperpaleDeFechaDada($idLoteria){
        $fechaActual = $this->fecha;
        $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
        $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:59:00';
        $idSuperpale = Draws::on($this->servidor)->whereDescripcion("Super pale")->first()->id;

        $idVentas = Sales::on($this->servidor)->select('sales.id')
        ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
        ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
        // ->where('salesdetails.idLoteria', $idLoteria)
        ->whereNotIn('sales.status', [0,5])
        ->where('salesdetails.idSorteo', '=', $idSuperpale)
        ->where(function($query) use($idLoteria){
            $query->where('salesdetails.idLoteria', $idLoteria)
              ->orWhere('salesdetails.idLoteriaSuperpale', $idLoteria);
        })
        ->get();

        // return $idVentas;

        $jugadas = Salesdetails::on($this->servidor)->whereIn('idVenta', $idVentas)
            ->orderBy('jugada', 'asc')
            ->where('salesdetails.idSorteo', '=', $idSuperpale)
            ->get();

        return $jugadas;
    }

    public function existenTicketsMarcadoComoPagado($idLoteria){
        $fechaActual = $this->fecha;
        $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
        $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:59:00';

        $cantidadTicksMarcadoComoPagado = Sales::on($this->servidor)->select('sales.id')
        ->join('salesdetails', 'salesdetails.idVenta', '=', 'sales.id')
        ->whereBetween('sales.created_at', array($fechaInicial, $fechaFinal))
        ->where(['salesdetails.idLoteria' => $idLoteria, 'salesdetails.pagado' => 1])->whereNotIn('sales.status', [0,5])->count();

    

        return ($cantidadTicksMarcadoComoPagado > 0) ? true : false;
    }

    public function directoBuscarPremio($idVenta, $idLoteria, $jugada, $monto){
        $premio = 0;
        $busqueda = strpos($this->numerosGanadores, $jugada);

        $venta = Sales::on($this->servidor)->whereId($idVenta)->first();
        $idBanca = Branches::on($this->servidor)->whereId($venta->idBanca)->first()->id;  
        
        
        if($this->primera == $jugada){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primera');
        }

        if($this->segunda == $jugada){
            if($premio == null) $premio = 0;
            $premio += $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('segunda');
        }

        

        if($this->tercera == $jugada){
            if($premio == null) $premio = 0;
            $premio += $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('tercera');
        }

        // if(gettype($busqueda) == "integer"){
        //     $venta = Sales::whereId($idVenta)->first();
        //     $idBanca = Branches::whereId($venta->idBanca)->first()->id;
        //     if($busqueda == 0) $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primera');
        //     else if($busqueda == 2) $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('segunda');
        //     else if($busqueda == 4) $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('tercera');
        // }
        // else
        //     $premio = 0;

        return $premio;
    }

    public function paleBuscarPremio($idVenta, $idLoteria, $jugada, $monto, $idSorteo){
        // return Response::json(['numGanador' => $numeroGanador['numeroGanador'],'juada' => substr('jean', 0, 2)], 201);
        $premio = 0;
        $contador = 0;
        $busqueda1 = strpos($this->numerosGanadores, substr($jugada, 0, 2));
        $busqueda2 = strpos($this->numerosGanadores, substr($jugada, 2, 2));

        $venta = Sales::on($this->servidor)->whereId($idVenta)->first();
        $idBanca = Branches::on($this->servidor)->whereId($venta->idBanca)->first()->id;
        $sorteo = Draws::on($this->servidor)->whereId($idSorteo)->first();

        $primerParDeNumeros = substr($jugada, 0, 2);
        $segundoParDeNumeros = substr($jugada, 2, 2);
        $hayPremiadoEnPrimera = false;
        $hayPremiadoEnSegunda = false;
        $hayPremiadoEnTercera = false;

     //Si el sorteo es diferente de super pale entonces es un pale normal
     if($sorteo['descripcion'] != "Super pale"){

         switch ($primerParDeNumeros) {
            case $this->primera:
                $hayPremiadoEnPrimera = true;
                $contador++;
                break;
            case $this->segunda:
                $hayPremiadoEnSegunda = true;
                $contador++;
                break;
            case $this->tercera:
                $hayPremiadoEnTercera = true;
                $contador++;
                break;
        }

        

        switch ($segundoParDeNumeros) {
            case $this->primera:
                if($hayPremiadoEnPrimera == false){
                    $hayPremiadoEnPrimera = true;
                    $contador++;
                }
                break;
            case $this->segunda:
                if($hayPremiadoEnSegunda == false){
                    $hayPremiadoEnSegunda = true;
                    $contador++;
                }
                break;
            case $this->tercera:
                if($hayPremiadoEnTercera == false){
                    $hayPremiadoEnTercera = true;
                    $contador++;
                }
                break;
        }

        if($hayPremiadoEnPrimera == true && $hayPremiadoEnSegunda == true){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primeraSegunda');
        }
        else if($hayPremiadoEnPrimera == true && $hayPremiadoEnTercera == true){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primeraTercera');
        }
        else if($hayPremiadoEnSegunda == true && $hayPremiadoEnTercera){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('segundaTercera');
        }

        
    }else{
        
        switch ($primerParDeNumeros) {
            case $this->primera:
                $hayPremiadoEnPrimera = true;
                $contador++;
                break;
            case $this->segunda:
                $hayPremiadoEnSegunda = true;
                $contador++;
                break;
        }

        

        switch ($segundoParDeNumeros) {
            case $this->primera:
                if($hayPremiadoEnPrimera == false){
                    $hayPremiadoEnPrimera = true;
                    $contador++;
                }
                break;
            case $this->segunda:
                if($hayPremiadoEnSegunda == false){
                    $hayPremiadoEnSegunda = true;
                    $contador++;
                }
                break;
        }

        if($hayPremiadoEnPrimera == true && $hayPremiadoEnSegunda == true){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primerPago');
        }

       

    }

        return $premio;
    }

    public function tripletaBuscarPremio($idVenta, $idLoteria, $jugada, $monto){
        $premio = 0;
        $contador = 0;
        $primerParDeNumeros = substr($jugada, 0, 2);
        $segundoParDeNumeros = substr($jugada, 2, 2);
        $tercerParDeNumeros = substr($jugada, 4, 2);
        $hayPremiadoEnPrimera = false;
        $hayPremiadoEnSegunda = false;
        $hayPremiadoEnTercera = false;
        // $busqueda1 = strpos($this->numerosGanadores, substr($jugada, 0, 2));
        // $busqueda2 = strpos($this->numerosGanadores, substr($jugada, 2, 2));
        // $busqueda3 = strpos($this->numerosGanadores, substr($jugada, 4, 2));

        $venta = Sales::on($this->servidor)->whereId($idVenta)->first();
        $idBanca = Branches::on($this->servidor)->whereId($venta->idBanca)->first()->id;

        
        // if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer" && gettype($busqueda3) == "integer"){
        //     $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('tresNumeros');
        // }
        // else{
        //     if(gettype($busqueda1) == "integer")
        //         $contador++;
        //     if(gettype($busqueda2) == "integer")
        //         $contador++;
        //     if(gettype($busqueda3) == "integer")
        //         $contador++;
            
        //     //Si el contador es = 2 entonces hay premio
        //     if($contador == 2)
        //         $premio = $monto * Payscombinations::where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('dosNumeros');
        //     else
        //         $premio = 0;
        // }

        switch ($primerParDeNumeros) {
            case $this->primera:
                $hayPremiadoEnPrimera = true;
                $contador++;
                break;
            case $this->segunda:
                $hayPremiadoEnSegunda = true;
                $contador++;
                break;
            case $this->tercera:
                $hayPremiadoEnTercera = true;
                $contador++;
                break;
        }

        

        switch ($segundoParDeNumeros) {
            case $this->primera:
                if($hayPremiadoEnPrimera == false){
                    $hayPremiadoEnPrimera = true;
                    $contador++;
                }
                break;
            case $this->segunda:
                if($hayPremiadoEnSegunda == false){
                    $hayPremiadoEnSegunda = true;
                    $contador++;
                }
                break;
            case $this->tercera:
                if($hayPremiadoEnTercera == false){
                    $hayPremiadoEnTercera = true;
                    $contador++;
                }
                break;
        }

        

        switch ($tercerParDeNumeros) {
            case $this->primera:
                if($hayPremiadoEnPrimera == false){
                    $hayPremiadoEnPrimera = true;
                    $contador++;
                }
                break;
            case $this->segunda:
                if($hayPremiadoEnSegunda == false){
                    $hayPremiadoEnSegunda = true;
                    $contador++;
                }
                break;
            case $this->tercera:
                if($hayPremiadoEnTercera == false){
                    $hayPremiadoEnTercera = true;
                    $contador++;
                }
                break;
        }

        if($contador == 3){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('tresNumeros');
        }
        else if($contador == 2){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('dosNumeros');
        }

        return $premio;
        // return $premio . ":" . $contador . "1ra:".$hayPremiadoEnPrimera." 2da:" .$hayPremiadoEnSegunda ." 3ra:".$hayPremiadoEnTercera;
    }

    //Si el premio superpale es igual a -1 entonces eso quiere decir que la otra loteria no ha salido, 
    //por lo tanto el status de la jugada seguira siendo igual a cero, indicando que todavia la jugada estara pendiente
    public function superPaleBuscarPremio($idVenta, $idLoteria, $jugada){
        // return Response::json(['numGanador' => $numeroGanador['numeroGanador'],'juada' => substr('jean', 0, 2)], 201);
        $premio = 0;
        $contador = 0;
        
        //Buscamos el primer premio de la otra loteria
        $idOtraLoteria = 0;
        if($idLoteria == $jugada["idLoteria"])
            $idOtraLoteria = $jugada["idLoteriaSuperpale"];
        else
            $idOtraLoteria = $jugada["idLoteria"];

        
        
        $fechaActual = $this->fecha;
        $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
        $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:59:00';
        $premiosDeLaOtraLoteria = Awards::on($this->servidor)
        ->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        ->where('idLoteria', $idOtraLoteria)
        // ->where('idSorteo', $jugada["idSorteo"])
        ->first();

        

        if($premiosDeLaOtraLoteria == null){
            // $l = \App\Lotteries::on($this->servidor)->whereId($idOtraLoteria)->first();
            // $d = ($l != null) ? $l->descripcion : null;
            // abort(404, "premiosDeLaOtraLoteria estan nulos {$fechaInicial} - {$fechaFinal} - {$idOtraLoteria}");
            return -1;
        }
        // abort(404, "premiosDeLaOtraLoteria primera estan nulos");
        
        if($premiosDeLaOtraLoteria->primera == null){
            // abort(404, "premiosDeLaOtraLoteria primera estan nulos");
            return -1;
        }

        $venta = Sales::on($this->servidor)->whereId($idVenta)->first();
        $idBanca = Branches::on($this->servidor)->whereId($venta->idBanca)->first()->id;
        $sorteo = Draws::on($this->servidor)->whereId($jugada["idSorteo"])->first();

        $primerParDeNumeros = substr($jugada["jugada"], 0, 2);
        $segundoParDeNumeros = substr($jugada["jugada"], 2, 2);
        $hayPremiadoEnPrimera = false;
        $hayPremiadoEnPrimeraDeLaOtraLoteria = false;
        $hayPremiadoEnTercera = false;

     
        
        switch ($primerParDeNumeros) {
            case $this->primera:
                $hayPremiadoEnPrimera = true;
                $contador++;
                break;
            case $premiosDeLaOtraLoteria->primera:
                $hayPremiadoEnPrimeraDeLaOtraLoteria = true;
                $contador++;
                break;
        }

        // abort(404, "hayPremiadoEnPrimera: {$hayPremiadoEnPrimera} hayPremiadoEnPrimeraDeLaOtraLoteria: {$hayPremiadoEnPrimeraDeLaOtraLoteria}");

        switch ($segundoParDeNumeros) {
            case $this->primera:
                if($hayPremiadoEnPrimera == false){
                    $hayPremiadoEnPrimera = true;
                    $contador++;
                }
                break;
            case $premiosDeLaOtraLoteria->primera:
                if($hayPremiadoEnPrimeraDeLaOtraLoteria == false){
                    $hayPremiadoEnPrimeraDeLaOtraLoteria = true;
                    $contador++;
                }
                break;
        }

        // return $this->numerosGanadores. " " .$jugada["jugada"] . " " . $hayPremiadoEnPrimera.":".$hayPremiadoEnPrimeraDeLaOtraLoteria;
        if($hayPremiadoEnPrimera == true && $hayPremiadoEnPrimeraDeLaOtraLoteria == true){
            $premio = $jugada["monto"] * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('primerPago');
        }


        return $premio;
    }

    public function pick3BuscarPremio($idVenta, $idLoteria, $jugada, $monto, $esStraight = true){
        $premio = 0;
        $contador = 0;
        $primerNumero = substr($jugada, 0, 1);
        $segundoNumero = substr($jugada, 1, 1);
        $tercerNumero = substr($jugada, 2, 1);
        $existenNumerosIdenticos = false;
        

        // return strpos($this->pick3, $segundoNumero) + 1;
        // $busqueda1 = strpos($this->numerosGanadores, substr($jugada, 0, 2));
        // $busqueda2 = strpos($this->numerosGanadores, substr($jugada, 2, 2));
        // $busqueda3 = strpos($this->numerosGanadores, substr($jugada, 4, 2));

        $venta = Sales::on($this->servidor)->whereId($idVenta)->first();
        $idBanca = Branches::on($this->servidor)->whereId($venta['idBanca'])->first()->id;

        if($esStraight == true){
            if($jugada == $this->pick3){
                $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick3TodosEnSecuencia');
            }else{
                $premio = 0;
            }
        }
        else{
            if(gettype(strpos($this->pick3, $primerNumero)) == "integer")
                $contador++;
            if(gettype(strpos($this->pick3, $segundoNumero)) == "integer"){
                //Si el segundoNumero y el primero son iguales entonces
                //Nos aseguramos de que la posicion de la coincidencia encontrada del segundoNumero por la funcion strpos,
                //sea diferente de la posicion 
                //encontrada del primerNumero, para eso le indicamos a la funcion strpos desde que posicion comenzar a buscar y 
                //La posicion sera, la posicion encontrada del primerNumero mas uno => strpos($this->pick3, $primerNumero) + 1
                
                if($segundoNumero == $primerNumero){
                    $posicionEncontradaPrimerNumeroMasUno = strpos($this->pick3, $primerNumero) + 1;
                    if(gettype(strpos($this->pick3, $segundoNumero, $posicionEncontradaPrimerNumeroMasUno)) == "integer"){
                        $contador++;
                    }
                }else{
                    $contador++;
                }
            }
            if(gettype(strpos($this->pick3, $tercerNumero)) == "integer"){
                //Si el segundoNumero y el primero son iguales entonces
                //Nos aseguramos de que la posicion de la coincidencia encontrada del segundoNumero por la funcion strpos,
                //sea diferente de la posicion 
                //encontrada del primerNumero, para eso le indicamos a la funcion strpos desde que posicion comenzar a buscar y 
                //La posicion sera, la posicion encontrada del primerNumero mas uno => strpos($this->pick3, $primerNumero) + 1
                
                if($primerNumero == $tercerNumero){
                    
                    $posicionEncontradaPrimerNumeroMasUno = strpos($this->pick3, $primerNumero) + 1;
                    if(gettype(strpos($this->pick3, $tercerNumero, $posicionEncontradaPrimerNumeroMasUno)) == "integer"){
                        $contador++;
                    }
                }
                else if($segundoNumero == $tercerNumero){
                    $posicionEncontradaSegundoNumeroMasUno = strpos($this->pick3, $segundoNumero) + 1;
                    if(gettype(strpos($this->pick3, $tercerNumero, $posicionEncontradaSegundoNumeroMasUno)) == "integer"){
                        $contador++;
                    }
                }else{
                    $contador++;
                }
            }
        }  

        if($contador == 3 && Helper::existenNumerosIdenticos($jugada) == true){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick33Way');
        }
        else if($contador == 3){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick36Way');
        }
      

        return $premio;
    }

    //pick4
    public function pick4BuscarPremio($idVenta, $idLoteria, $jugada, $monto, $esStraight = true){
        $premio = 0;
        $contador = 0;
        $primerNumero = substr($jugada, 0, 1);
        $segundoNumero = substr($jugada, 1, 1);
        $tercerNumero = substr($jugada, 2, 1);
        $cuartoNumero = substr($jugada, 3, 1);
        $contadorNumerosIdenticos = 0;

        // return strpos($this->pick3, $segundoNumero) + 1;
        // $busqueda1 = strpos($this->numerosGanadores, substr($jugada, 0, 2));
        // $busqueda2 = strpos($this->numerosGanadores, substr($jugada, 2, 2));
        // $busqueda3 = strpos($this->numerosGanadores, substr($jugada, 4, 2));

        $venta = Sales::on($this->servidor)->whereId($idVenta)->first();
        $idBanca = Branches::on($this->servidor)->whereId($venta['idBanca'])->first()->id;

        if($esStraight == true){
            if($jugada == $this->pick4){
                $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick4TodosEnSecuencia');
            }else{
                $premio = 0;
            }
        }
        else{
            if(gettype(strpos($this->pick4, $primerNumero)) == "integer"){
                $contador++;
                $posicionEncontradaPrimerNumeroMasUno = strpos($this->pick4, $primerNumero) + 1;
            }
            if(gettype(strpos($this->pick4, $segundoNumero)) == "integer"){
                //Si el segundoNumero y el primero son iguales entonces
                //Nos aseguramos de que la posicion de la coincidencia encontrada del segundoNumero por la funcion strpos,
                //sea diferente de la posicion 
                //encontrada del primerNumero, para eso le indicamos a la funcion strpos desde que posicion comenzar a buscar y 
                //La posicion sera, la posicion encontrada del primerNumero mas uno => strpos($this->pick4, $primerNumero) + 1
                $posicionEncontradaSegundoNumeroMasUno = strpos($this->pick4, $segundoNumero) + 1;
                
                
                if($segundoNumero == $primerNumero){
                    if(gettype(strpos($this->pick4, $segundoNumero, $posicionEncontradaPrimerNumeroMasUno)) == "integer"){
                        $contador++;
                        $contadorNumerosIdenticos++;
                        $posicionEncontradaSegundoNumeroMasUno = strpos($this->pick4, $segundoNumero, $posicionEncontradaPrimerNumeroMasUno) + 1;
                    }
                }else{
                    $contador++;
                }
            }
            if(gettype(strpos($this->pick4, $tercerNumero)) == "integer"){
                //Si el segundoNumero y el primero son iguales entonces
                //Nos aseguramos de que la posicion de la coincidencia encontrada del segundoNumero por la funcion strpos,
                //sea diferente de la posicion 
                //encontrada del primerNumero, para eso le indicamos a la funcion strpos desde que posicion comenzar a buscar y 
                //La posicion sera, la posicion encontrada del primerNumero mas uno => strpos($this->pick4, $primerNumero) + 1

                $posicionEncontradaTercerNumeroMasUno = strpos($this->pick4, $tercerNumero) + 1;
                
                if($primerNumero == $tercerNumero && $segundoNumero != $tercerNumero){
                    
                   
                    if(gettype(strpos($this->pick4, $tercerNumero, $posicionEncontradaPrimerNumeroMasUno)) == "integer"){
                        $contador++;
                        $contadorNumerosIdenticos++;
                        $posicionEncontradaTercerNumeroMasUno = strpos($this->pick4, $tercerNumero, $posicionEncontradaPrimerNumeroMasUno) + 1;
                    }
                }
                else if($segundoNumero == $tercerNumero){
                    
                    if(gettype(strpos($this->pick4, $tercerNumero, $posicionEncontradaSegundoNumeroMasUno)) == "integer"){
                        $contador++;
                        $contadorNumerosIdenticos++;
                        $posicionEncontradaTercerNumeroMasUno = strpos($this->pick4, $tercerNumero, $posicionEncontradaSegundoNumeroMasUno) + 1;
                    }
                }else{
                    $contador++;
                }
            }
            if(gettype(strpos($this->pick4, $cuartoNumero)) == "integer"){
                //Si el segundoNumero y el primero son iguales entonces
                //Nos aseguramos de que la posicion de la coincidencia encontrada del segundoNumero por la funcion strpos,
                //sea diferente de la posicion 
                //encontrada del primerNumero, para eso le indicamos a la funcion strpos desde que posicion comenzar a buscar y 
                //La posicion sera, la posicion encontrada del primerNumero mas uno => strpos($this->pick4, $primerNumero) + 1
                $posicionEncontradaCuartoNumeroMasUno = strpos($this->pick4, $tercerNumero) + 1;
                
                if($primerNumero == $cuartoNumero && $segundoNumero != $cuartoNumero && $tercerNumero != $cuartoNumero){
                    
                    if(gettype(strpos($this->pick4, $cuartoNumero, $posicionEncontradaPrimerNumeroMasUno)) == "integer"){
                        $contador++;
                        $contadorNumerosIdenticos++;
                        $posicionEncontradaCuartoNumeroMasUno = strpos($this->pick4, $cuartoNumero, $posicionEncontradaPrimerNumeroMasUno) + 1;
                    }
                }
                else if($segundoNumero == $cuartoNumero && $tercerNumero != $cuartoNumero){
                    if(gettype(strpos($this->pick4, $cuartoNumero, $posicionEncontradaSegundoNumeroMasUno)) == "integer"){
                        $contador++;
                        $contadorNumerosIdenticos++;
                        $posicionEncontradaCuartoNumeroMasUno = strpos($this->pick4, $cuartoNumero, $posicionEncontradaSegundoNumeroMasUno) + 1;
                    }
                }else if($tercerNumero == $cuartoNumero){
                    if(gettype(strpos($this->pick4, $cuartoNumero, $posicionEncontradaTercerNumeroMasUno)) == "integer"){
                        $contador++;
                        $contadorNumerosIdenticos++;
                        $posicionEncontradaCuartoNumeroMasUno = strpos($this->pick4, $cuartoNumero, $posicionEncontradaTercerNumeroMasUno) + 1;                        
                    }
                }else{
                    $contador++;
                }
            }
        }  
        //return $posicionEncontradaPrimerNumeroMasUno.':'.$posicionEncontradaSegundoNumeroMasUno.':'.$posicionEncontradaTercerNumeroMasUno.':'.$posicionEncontradaCuartoNumeroMasUno . ' identicos:' . Helper::contarNumerosIdenticos($jugada) . ' contador:' . $contador;


        if($contador == 4 && Helper::contarNumerosIdenticos($jugada) == 3 ){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick44Way');
        }
        else if($contador == 4 && Helper::contarNumerosIdenticos($jugada) == 4){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick46Way');
        }
        else if($contador == 4 && Helper::contarNumerosIdenticos($jugada) == 2){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick412Way');
        }
        else if($contador == 4){
            $premio = $monto * Payscombinations::on($this->servidor)->where(['idLoteria' => $idLoteria, 'idBanca' => $idBanca])->value('pick424Way');
        }
      

        return $premio;
    }

    public static function getLoterias($servidor, $layout = null){
        $fecha = getdate();
        $fechaDesde = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:59:00';


        $loterias = Lotteries::on($servidor)->whereStatus(1)->has('sorteos')->get();
        // == "vistaPremiosModal"
        if($layout != null){
            

            // if($layout != "vistaPremiosModal")
                
            $loterias = collect($loterias)->map(function($l) use($servidor, $fechaDesde, $fechaHasta){
                $primera = null;
                $segunda = null;
                $tercera = null;
                $pick3 = null;
                $pick4 = null;
                $premios = Awards::on($servidor)->whereBetween('created_at', array($fechaDesde , $fechaHasta))
                                ->where('idLoteria', $l['id'])
                                ->first();
    
                if($premios != null){
                    $primera = $premios->primera;
                    $segunda = $premios->segunda;
                    $tercera = $premios->tercera;
                    $pick3 = $premios->pick3;
                    $pick4 = $premios->pick4;
                }
                return [
                        'id' => $l['id'],
                        'descripcion' => $l['descripcion'],
                        'abreviatura' => $l['abreviatura'],
                        'primera' => $primera,
                        'segunda' => $segunda,
                        'tercera' => $tercera,
                        'pick3' => $pick3,
                        'pick4' => $pick4,
                        'sorteos' => $l->sorteos
                    ];
            });
    
            // $loterias = collect($loteri']);
            list($loterias, $no) = $loterias->partition(function($l) use($servidor){
                return Helper::loteriaTienePremiosRegistradosHoy($servidor, $l['id']) != true;
            });
        }else{
            $loterias = collect($loterias)->map(function($l) use($servidor, $fechaDesde, $fechaHasta){
                $primera = null;
                $segunda = null;
                $tercera = null;
                $pick3 = null;
                $pick4 = null;
                $premios = Awards::on($servidor)->whereBetween('created_at', array($fechaDesde , $fechaHasta))
                                ->where('idLoteria', $l['id'])
                                ->first();
    
                if($premios != null){
                    $primera = $premios->primera;
                    $segunda = $premios->segunda;
                    $tercera = $premios->tercera;
                    $pick3 = $premios->pick3;
                    $pick4 = $premios->pick4;
                }
                return [
                        'id' => $l['id'],
                        'descripcion' => $l['descripcion'],
                        'abreviatura' => $l['abreviatura'],
                        'primera' => $primera,
                        'segunda' => $segunda,
                        'tercera' => $tercera,
                        'pick3' => $pick3,
                        'pick4' => $pick4,
                        'sorteos' => $l->sorteos
                    ];
            });
        }

        //La funcion partition retorna los objetos que cumplan la condicion pero esta tambien retornara su mismo index, en algunos
        //casos no se retorno el index cero porque el elemento en esta posicion no ha sido incluido, entonces lo que hace la funcion values()
        //es empezar la collection desde su indice cero
        return $loterias = $loterias->values();
    }
}