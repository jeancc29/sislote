<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Draws;
use App\Branches;
use Carbon\Carbon;
use App\transactions;
use App\Types;
use App\Users;
use App\Entity;
use App\Days;
use App\Classes\Helper;
use App\Http\Resources\AutomaticexpensesResource;
use App\Http\Resources\BranchesResource;

class Transactionsdraws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transacciones:sorteos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $prueba = new Carbon("2019-01-20");

        $fecha = Carbon::now();
        $todayWday = getdate()['wday'];
        $ultimoDiaMes = new Carbon("last day of this month");
        $primerDiaMes = new Carbon("first day of this month");
        $horaParaRealizarGasto = 10;
        

        $fechaDesde = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 00:00:00";
        $fechaHasta = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 23:59:00";
        $usuario = Users::whereNombres("Sistema")->first();
        $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Sorteo")->first();
        $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Sistema'])->first();
        $entidad = Entity::whereNombre("Sistema")->first();

        

        // $saldo = (new Helper)->_sendSms("+18294266800", "Hola jean como estas");
        // $this->info($prueba->hour);
        // return;
       
        if($usuario == null || $tipo == null)
            return;
            


        
        $bancas = BranchesResource::collection(Branches::whereStatus(1)->get());
        foreach($bancas as $b){
            
           
            $t = transactions::where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idTipo'=> $tipo->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
            if($t != null)
                 continue;
                
                //  $this->info('des:prem:comi '.$b['descuentosDelDia'].':'.$b['premiosDelDia'].':'.$b['comisionesDelDia']);
                $ventasDelDia = Helper::ventasDelDia($b['id']);
                $descuentosDelDia = Helper::descuentosDelDia($b['id']);
                $premiosDelDia = Helper::premiosDelDia($b['id']);
                $comisionesDelDia = Helper::comisionesPorBanca($b['id']);
                //  $this->info('des:prem:comi '.$descuentosDelDia.';'.$premiosDelDia.';'.$comisionesDelDia);
                //  return $b;
                $saldoFinalEntidad1 = 0;
                $saldo = (new Helper)->saldo($b['id'], true);
                $totalNeto = $ventasDelDia - ($descuentosDelDia + $premiosDelDia + $comisionesDelDia);

                if($totalNeto >= 0){
                    $debito = $totalNeto;
                    $saldoFinalEntidad1 = $saldo + $debito;
                    $credito = 0;
                }else{
                    $credito = abs($totalNeto); //Esta funcion abs "valor absoluto" convierte numeros negativos a positivos
                    $saldoFinalEntidad1 = $saldo - $credito;
                    $debito = 0;
                }

                $t = transactions::create([
                    'idUsuario' => $usuario->id,
                    'idTipo' => $tipo->id,
                    'idTipoEntidad1' => $idTipoEntidad1->id,
                    'idTipoEntidad2' => $idTipoEntidad2->id,
                    'idEntidad1' => $b['id'],
                    'idEntidad2' => $entidad->id,
                    'entidad1_saldo_inicial' => $saldo,
                    'entidad2_saldo_inicial' => 0,
                    'debito' => $debito,
                    'credito' => $credito,
                    'idGasto' => null,
                    'entidad1_saldo_final' => $saldoFinalEntidad1,
                    'entidad2_saldo_final' => 0,
                    'nota' => "Proceso diario automático de ventas"
                ]);
                $this->info('See hizo la transaccion diaria: '.$b['id']);

                
                
               
        }
    }
}
