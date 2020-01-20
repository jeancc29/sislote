<?php
namespace App\Classes;
use App\Sales;




use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\TicketClass;
use Illuminate\Support\Facades\DB;


// use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
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

class DashboardClass{
    public $idUsuario;
    public $fecha;
    public $fechaActual;
    public $fecha6DiasAtras;

    function __construct($fecha = null, $idUsuario){
        if($fecha == null){
            $this->fecha = Carbon::now();
            $this->fechaActual = Carbon::now();
        }else{
            $this->fecha = new Carbon($fecha);
            $this->fechaActual = new Carbon($fecha);
        }
        
        $this->fecha6DiasAtras = $this->fechaActual->copy()->subDays(6);
        $this->fechaActual = $this->fechaActual->toDateString() . ' 23:00:00';
        $this->fecha6DiasAtras = $this->fecha6DiasAtras->toDateString() . ' 00:00:00';
        $this->idUsuario = $idUsuario;
    }
    

    function ventasGrafica()
    {
        $daysSpanish = [
            1 => 'lun',
            2 => 'mar',
            3 => 'mié',
            4 => 'jue',
            5 => 'vie',
            6 => 'sáb',
            0 => 'dom',
        ];

        //VENTAS AGRUPADAS POR DIA PARA LA GRAFICA
        $ventasGrafica = Sales::select(DB::raw('DATE(created_at) as date, sum(subTotal) subTotal, sum(total) total, sum(premios) premios, sum(descuentoMonto)  as descuentoMonto'))
            ->whereBetween('created_at', array($this->fecha6DiasAtras, $this->fechaActual))
            ->whereNotIn('status', [0,5])
            ->groupBy('date')
            //->orderBy('created_at', 'asc')
            ->get();
    
        
        $ventasGrafica = collect($ventasGrafica)->map(function($d) use($daysSpanish){
            $fecha = new Carbon($d['date']);
            $dia = $daysSpanish[$fecha->dayOfWeek] . ' ' . $fecha->day;

            return ["total" => $d['total'], "neto" => $d['total'] - ($d['premios'] + $d['descuentoMonto']), "dia" => $dia];
        });

        //VERIFICAMOS SI LA FECHA ES IGUAL A LA FECHA DE HOY, SI ES ASI, ENTONCES 
        //VERIFICAMOS SI ESTA INCLUIDA, YA QUE, SI NO HAY VENTAS EN LA FECHA DE HOY 
        //ENTONCES HOY NO SE INCLUYE EN LA BUSQUEDA SQL QUE SE HIZO ARRIBA, 
        //POR ESO SI NO ESTA ENTONCES LA INCLUIMOS EN EL CODIGO DEBAJO
        // $incluyeVentasDeHoy = false;
        // $fechaHoy = Carbon::now();
        // if($this->fecha->toDateString() == $fechaHoy->toDateString())
        // {
        //     $dia = $daysSpanish[$fechaHoy->dayOfWeek] . ' ' . $fechaHoy->day;
        //     foreach($ventasGrafica as $v)
        //     {
        //         if($v["dia"] == $dia){
        //             $incluyeVentasDeHoy = true;
        //         }
        //     }

            
        //     if($incluyeVentasDeHoy == false){
                
        //         $ventasGrafica->push([
        //             "total" => 0, 
        //             "neto" => 0,
        //             "dia" => $dia
        //         ]);
        //     }
        // }
        

        

        return $ventasGrafica;
    }

    function ventasPremiosPorLoteria()
    {
        $fechaInicial = $this->fecha;
        $fechaFinal = $fechaInicial->toDateString() . ' 23:00:00';
        $fechaInicial = $fechaInicial->toDateString() . ' 00:00:00';
        $loterias = Lotteries::
            selectRaw('
                id, 
                descripcion, 
                (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status not in(0,5) and sd.idLoteria = lotteries.id and s.created_at between ? and ?) as ventas,
                (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status not in(0,5) and sd.idLoteria = lotteries.id and s.created_at between ? and ?) as premios
                ', [$fechaInicial, $fechaFinal, //Parametros para ventas
                    $fechaInicial, $fechaFinal //Parametros para premios
                    ])
            ->where('lotteries.status', '=', '1')
            ->get();

        return $loterias;
    }


    function todasLasJugadasPorLoteria()
    {
        $fechaActual = Carbon::now();
        if($fechaActual->toDateString() != $this->fecha->toDateString()){
            return null;
        }
        $fecha = getdate();
        $usuario = Users::whereId($this->idUsuario)->first();
        $loteriasOrdenadasPorHoraCierre = helper::loteriasOrdenadasPorHoraCierre($usuario, false);
        $sorteos = Draws::all();
        $totalVentasLoterias = 0;
        $totalPremiosLoterias = 0;
        $loteriasJugadasDashboard = null;
        
        // return $loteriasOrdenadasPorHoraCierre;
        if($loteriasOrdenadasPorHoraCierre != null && count($loteriasOrdenadasPorHoraCierre) > 0){
            list($loteriasValidadas, $no) = $loteriasOrdenadasPorHoraCierre->partition(function($l) use($loteriasOrdenadasPorHoraCierre){
                $fechaActual = Carbon::now();
                
                $horaCierreCarbonPrimeraLoteria = new Carbon($fechaActual->toDateString() . " " . $loteriasOrdenadasPorHoraCierre[0]['horaCierre']);
                $horaCierreCarbonOtraLoteria = new Carbon($fechaActual->toDateString() . " " . $l['horaCierre']);

                if($l['horaCierre'] == $loteriasOrdenadasPorHoraCierre[0]['horaCierre'])
                    return true;

                //Si la primera loteria no ha cerrado entonces le sumamos 20 minutos a la horaCierreCarbonPrimeraLoteria 
                //para tambien incluir y retornar las loterias que tengan la misma hora pero que sean menores o iguales a los 20 minutos extra
                // pero si la loteria ha cerrado entonces le sumamos los 20 minutos a la horaCierreCarbonSegundaLoteria que es la proxima loteria que toca
                if($fechaActual->gt($horaCierreCarbonPrimeraLoteria) == false){
                    $horaCierreCarbonPrimeraLoteria->addMinutes(20);
                    if($horaCierreCarbonPrimeraLoteria->gt($horaCierreCarbonOtraLoteria) == true)
                        return true;
                }else{
                    // $fechaActual->addMinutes(20);
                    if(count($loteriasOrdenadasPorHoraCierre) > 1)
                        $horaCierreCarbonSegundaLoteria = new Carbon($fechaActual->toDateString() . " " . $loteriasOrdenadasPorHoraCierre[1]['horaCierre']);

                        $horaCierreCarbonSegundaLoteria->addMinutes(20);
                        if($horaCierreCarbonSegundaLoteria->gt($horaCierreCarbonOtraLoteria) == true)
                        return true;
                }

                
            });

            

            $loteriasJugadasDashboard = collect($loteriasValidadas)->map(function($l) use($fecha, $sorteos){
                $ventas = Salesdetails::selectRaw('salesdetails.jugada, sum(salesdetails.monto) as monto, salesdetails.idSorteo, salesdetails.
                idLoteria')->join('sales', 'sales.id', 'salesdetails.idVenta')
                ->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereNotIn('sales.status', [0,5])
                ->where('salesdetails.idLoteria', $l['id'])
                ->groupBy('salesdetails.jugada', 'salesdetails.idSorteo', 'salesdetails.idLoteria')
                ->orderBy('monto', 'desc')
                ->get();

                $sorteos = collect($sorteos)->map(function($d) use($ventas){
                    list($jugadas, $no) = $ventas->partition(function($v) use($d){
                    return $v['idSorteo'] == $d['id'];
                    });
        
                    $jugadas = collect($jugadas)->map(function($j){
                        $loteria = Lotteries::whereId($j['idLoteria'])->first();
                        return ['descripcion' => $loteria['descripcion'], 'abreviatura' => $loteria['abreviatura'], 'jugada' => Helper::agregarGuion($j['jugada'], $j['idSorteo']), 'monto' => $j['monto']];
                    });
                    return ['descripcion' => $d['descripcion'], 'jugadas' => $jugadas];
                });

                return ['id' => $l['id'], 'descripcion' => $l['descripcion'], 'abreviatura' => $l['abreviatura'], 'sorteos' => $sorteos];
            });

        }

        return $loteriasJugadasDashboard;
    }

    function bancaConYSinVentas()
    {
        $fecha = getdate(\strtotime($this->fecha->toDateString()));
        $idBancas = Sales::whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
        ->whereNotIn('sales.status', [0,5])
        ->get();
        
        $idBancas = collect($idBancas)->map(function($id){
            return $id['idBanca'];
        });
        
        $bancasConVentas = Branches::whereIn('id', $idBancas)->whereStatus(1)->count();
        $bancasSinVentas = Branches::whereNotIn('id', $idBancas)->whereStatus(1)->count();
        return array('bancasConVentas' => $bancasConVentas, 'bancasSinVentas' => $bancasSinVentas);
    }

    function totalVentasYPremiosLoterias($loterias)
    {
        $totalVentasLoterias = 0;
        $totalPremiosLoterias = 0;
        foreach($loterias as $l){
            $totalVentasLoterias += $l['ventas'];
            $totalPremiosLoterias += $l['premios'];
        }

        return array('totalVentasLoterias' => $totalVentasLoterias, 'totalPremiosLoterias' => $totalPremiosLoterias);
    }
}