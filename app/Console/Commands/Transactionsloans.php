<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use App\Loans;
use App\Branches;
use App\transactions;
use App\Types;
use App\Users;
use App\Entity;
use App\Amortization;
use App\Classes\Helper;

class Transactionsloans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transacciones:prestamos';

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
        $horaParaRealizarGasto = 0;
        

        $fechaDesde = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 00:00:00";
        $fechaHasta = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 23:59:00";
        $usuario = Users::whereNombres("Sistema")->first();
        // $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Consumo automatico de banca")->first();
        $tipo = Types::where(['renglon' => 'transaccion', 'descripcion' => 'Cobro prestamo'])->first();

        $idTipoEntidadBanca = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Sistema'])->first();
        $entidad = Entity::whereNombre("Sistema")->first();

        

        // $saldo = (new Helper)->_sendSms("+18294266800", "Hola jean como estas");
        // $this->info($fecha->hour);
        // return;
       
        if($usuario == null || $tipo == null)
            return;
            


        
        $bancas = Branches::whereStatus(1)->get();
        foreach($bancas as $b){
            $prestamos = DB::table('loans')
            ->selectRaw('loans.id, loans.montoPrestado,
                    loans.numeroCuotas, 
                    loans.montoCuotas, 
                    loans.montoCapital, 
                    loans.tasaInteres, 
                    loans.created_at, 
                    loans.status, 
                    loans.idFrecuencia, 
                    loans.idTipoEntidadPrestamo, 
                    loans.idTipoEntidadFondo, 
                    loans.idEntidadPrestamo, 
                    loans.idEntidadFondo, 
                    loans.fechaInicio, 
                    branches.descripcion AS banca, 
                    frecuencies.descripcion AS frecuencia,
                    types.descripcion AS tipoAmortizacion,
                    (select loans.montoPrestado - (sum(montoCapital) - sum(montoPagadoCapital)) from amortizations where idPrestamo = loans.id) as totalSaldado,
                    (select sum(montoCapital) - sum(montoPagadoCapital) from amortizations where idPrestamo = loans.id) as balancePendiente,
                    (select fecha from amortizations where ((montoCapital + montoInteres) - (montoPagadoInteres + montoPagadoCapital)) > 0 and fecha >= date(NOW()) and idPrestamo = loans.id order by fecha asc limit 1) as fechaPagoProxima
                    ')
                ->join('branches', 'branches.id', '=', 'loans.idEntidadPrestamo')
                ->join('frecuencies', 'loans.idFrecuencia', '=', 'frecuencies.id')
                ->join('types', 'loans.idTipoAmortizacion', '=', 'types.id')
                ->where('loans.status', 1)
                ->where(['idTipoEntidadPrestamo' => $idTipoEntidadBanca->id, 'idEntidadPrestamo' => $b->id])
                ->get();


                

            foreach($prestamos as $p){
                $this->info('See hizo la transaccion diaria: ' . $p->fechaPagoProxima . ' ' . $fecha->toDateString());
                
                if($p->fechaPagoProxima != $fecha->toDateString())
                    continue;

                $this->info('Primera condicion: ' . $p->fechaPagoProxima != $fecha->toDateString() . ' fechaProxi:' .$p->fechaPagoProxima . ' today:'.$fecha->toDateString());


                $amortizacion = Amortization::where(['idPrestamo' => $p->id, 'fecha' => $fecha->toDateString()])->first();
                if($amortizacion == null)
                    continue;
                    $this->info('Segunda condicion no null: ' . $amortizacion);

                    
                $montoAPagar = ($amortizacion->montoCapital + $amortizacion->montoInteres) - ($amortizacion->montoPagadoCapital + $amortizacion->montoPagadoInteres);
                $this->info('Antes de tecera condicion no null: ' . $montoAPagar);
                if($montoAPagar <= 0)
                    continue;

                $this->info('Despues de tecera condicion no null: ' . $montoAPagar);


                $montoInteresAPagar = 0;
                $montoCapitalAPagar = 0;
                //Si el monto pagado del interes y capital es igual a cero entonces el $montoInteresAPagar sera igual 
                //al montoInteres y el $montoCapitalAPagar sera igual a montoCapital
                if($amortizacion->montoPagadoCapital == 0 && $amortizacion->montoPagadoInteres == 0){
                    $montoInteresAPagar = $amortizacion->montoInteres;
                    $montoCapitalAPagar =$amortizacion->montoCapital;
                    $amortizacion->montoPagadoCapital = $amortizacion->montoCapital;
                    $amortizacion->montoPagadoInteres = $amortizacion->montoInteres;
                }else{
                    $montoInteresAPagar = ($amortizacion->montoInteres - $amortizacion->montoPagadoInteres);
                    $montoCapitalAPagar = ($amortizacion->montoCapital - $amortizacion->montoPagadoCapital);
                    if($montoInteresAPagar > 0){
                        $amortizacion->montoPagadoInteres += $montoInteresAPagar;
                    }
                    if($montoCapitalAPagar > 0){
                        $amortizacion->montoPagadoCapital += $montoCapitalAPagar;                        
                    }
                }


                // $t = transactions::where(['idTipo' => $tipo->id, 'idTipoEntidad1' => $idTipoEntidadBanca->id, 'idEntidad1' => $p['idEntidadPrestamo'], 'idPrestamo' => $p->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();


                $saldo = (new Helper)->saldo($p->idEntidadPrestamo, 1);

                $t = transactions::create([
                    'idUsuario' => $usuario->id,
                    'idTipo' => $tipo->id,
                    'idTipoEntidad1' => $idTipoEntidadBanca->id,
                    'idTipoEntidad2' => $idTipoEntidad2->id,
                    'idEntidad1' => $b['id'],
                    'idEntidad2' => $entidad->id,
                    'entidad1_saldo_inicial' => $saldo,
                    'entidad2_saldo_inicial' => 0,
                    'debito' => ($montoCapitalAPagar + $montoInteresAPagar),
                    'credito' => 0,
                    'idPrestamo' => $p->id,
                    'entidad1_saldo_final' => $saldo - ($montoCapitalAPagar + $montoInteresAPagar),
                    'entidad2_saldo_final' => 0,
                    'nota' => "Pago automatico de prestamo"
                ]);


                $amortizacion->save();
                $this->info('See hizo la transaccion diaria: '.$p->id);


            }
        }

    }//End handle
}
