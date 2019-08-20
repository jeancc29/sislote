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
use App\Classes\Helper;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class LotteriesClass{
    public static function cerrada($idLoteria){
        $cerrado = false;
        $fecha = getdate();

        if($this->abreHoy()){
            $horaCierre = DB::table('days')
                ->join('day_lottery', 'days.id', '=', 'day_lottery.idDia')
                ->whereWday($fecha['wday'])
                ->where('day_lottery.idLoteria', $idLoteria)
                ->first()->pivot->horaCierre
            $hora = explode(':', $horaCierre);
            if((int)$fecha['hours'] > (int)$hora[0])
                $cerrado = true;
            else if((int)$hora[0] == (int)$fecha['hours']){
                //Validamos si los minutos actuales son mayores que los minutos horaCierre  
                if((int)$fecha['minutes'] >= (int)$hora[1])
                    $cerrado = true;
            }
        }else{
            $cerrado = true;
        }

        

        return $cerrado;
    }
}