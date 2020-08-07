<?php
namespace App\Classes;
use App\Realtime;
use App\Users;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

use App\Userssesions;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksgenerals;
use App\Blocksplays;
use App\Blocksplaysgenerals;
use App\Branches;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Frecuency;
use App\Automaticexpenses;
use App\Androidversions;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;
use App\Classes\Helper;

class RealtimeClass{
    public static function todos($servidor){
        $datos = array();
        $fecha = getdate();

    
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        

        $maxId = Realtime::on($servidor)->max('id');
        $datos['stocks'] = Stock::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get();
        $datos['blockslotteries'] = Blockslotteries::on($servidor)->get();
        $datos['blocksgenerals'] = Blocksgenerals::on($servidor)->get();
        $datos['blocksplays'] = Blocksplays::on($servidor)->whereStatus(1)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->get();
        $datos['blocksplaysgenerals'] = Blocksplaysgenerals::on($servidor)->whereStatus(1)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->get();
        $datos['draws'] = Draws::on($servidor)->whereStatus(1)->get();

        return $datos;
    }
}