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
use App\Transactionscheduled;
use App\Classes\Helper;
use App\Http\Resources\AutomaticexpensesResource;
use App\Http\Resources\BranchesResource;

class Transactionsdiasnolaborados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transacciones:nolaborados';

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

        if($fecha->hour != 0)
        return;
       
        if($usuario == null || $tipo == null)
            return;
            


        
        $transaccionesProgramadas = Transactionscheduled::whereStatus(1)->whereFecha($fecha->toDateString())->get();
        foreach($transaccionesProgramadas as $t){
            
           
         
                
               
                $idTipoEntidad1 = Types::whereId($t->idTipoEntidad1)->first();
                $idTipoEntidad2 = Types::whereId($t->idTipoEntidad2)->first();
                $saldoInicialEntidad1 = 0;
                $saldoInicialEntidad2 = 0;
                if($idTipoEntidad1 == null || $idTipoEntidad2 == null)
                    continue;

                if($idTipoEntidad1->descripcion == "Banca"){
                    $saldoInicialEntidad1 = (new Helper)->saldo($t->idEntidad1, 1);
                }
                else if($idTipoEntidad1->descripcion == "Banco"){
                    $saldoInicialEntidad1 = (new Helper)->saldo($t->idEntidad1, 2);
                }

                if($idTipoEntidad2->descripcion == "Banca"){
                    $saldoInicialEntidad2 = (new Helper)->saldo($t->idEntidad2, 1);
                }
                else if($idTipoEntidad2->descripcion == "Banco"){
                    $saldoInicialEntidad2 = (new Helper)->saldo($t->idEntidad2, 2);
                }

                
                
                
                $saldoFinalEntidad1 = ($t->credito == 0 || $t->credito == null) ? $saldoInicialEntidad1 + $t->debito : $saldoInicialEntidad1 - $t->credito; 
                $saldoFinalEntidad2 = ($t->credito == 0 || $t->credito == null) ? $saldoInicialEntidad2 - $t->debito : $saldoInicialEntidad2 + $t->credito; 

                $t = transactions::create([
                    'idUsuario' => $usuario->id,
                    'idTipo' => $t->idTipo,
                    'idTipoEntidad1' => $idTipoEntidad1->id,
                    'idTipoEntidad2' => $idTipoEntidad2->id,
                    'idEntidad1' => $t->idEntidad1,
                    'idEntidad2' => $t->idEntidad2,
                    'entidad1_saldo_inicial' => $saldoInicialEntidad1,
                    'entidad2_saldo_inicial' => $saldoInicialEntidad2,
                    'debito' => $t->debito,
                    'credito' => $t->credito,
                    'idGasto' => null,
                    'entidad1_saldo_final' => $saldoFinalEntidad1,
                    'entidad2_saldo_final' => $saldoFinalEntidad2,
                    'nota' => "Transaccion programada"
                ]);
                
        }
    }
    
}
