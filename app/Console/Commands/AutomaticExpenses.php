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



class AutomaticExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transacciones:gastos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se restaran los gastos automaticos';

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
        // $fichero = 'gente.txt';
        // // Abre el fichero para obtener el contenido existente
        // $actual = file_get_contents($fichero);
        // // Añade una nueva persona al fichero
        // $actual .= "John Smith\n";
        // // Escribe el contenido al fichero
        // file_put_contents($fichero, $actual);

        $servidores = \App\Server::on("mysql")->get();
        foreach ($servidores as $servi):
        $servidor = $servi->descripcion;
        
        $prueba = new Carbon("2019-01-20");

        $fecha = Carbon::now();
        $todayWday = getdate()['wday'];
        $ultimoDiaMes = new Carbon("last day of this month");
        $primerDiaMes = new Carbon("first day of this month");
        $horaParaRealizarGasto = 0;
        

        $fechaDesde = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 00:00:00";
        $fechaHasta = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 23:59:00";
        $usuario = Users::on($servidor)->whereNombres("Sistema")->first();
        $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Consumo automatico de banca")->first();
        $idTipoEntidad1 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        $idTipoEntidad2 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Sistema'])->first();
        $entidad = Entity::on($servidor)->whereNombre("Sistema")->first();

        

        // $saldo = (new Helper)->_sendSms("+18294266800", "Hola jean como estas");
        // $this->info($fecha->hour);
        // return;
       
        if($usuario == null || $tipo == null)
            return;
            


        
        $bancas = Branches::on($servidor)->whereStatus(1)->has('gastos')->get();
        foreach($bancas as $b){
            $gastos = AutomaticexpensesResource::collection($b->gastos);
            foreach($gastos as $g){
                
           

                //Verificamos si el gasto es diario
                if(strtolower($g->frecuencia->descripcion) == "diario"){
                    //$this->info('klk: '.$fecha->hour);
                    //Verificamos que la hora de la fecha actual sean las 11PM, osea las 24 que es igual a la hora cero 0
                    if($fecha->hour == $horaParaRealizarGasto){
                       //Verificamos que no haya transacciones realizadas en la fecha actual para la banca y el gasto especificado
                       $t = transactions::on($servidor)->where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idGasto' => $g->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
                       if($t != null)
                            continue;
                       
                        
                            $saldo = (new Helper)->saldo($servidor, $b['id'], 1);
                            $t = transactions::on($servidor)->create([
                                'idUsuario' => $usuario->id,
                                'idTipo' => $tipo->id,
                                'idTipoEntidad1' => $idTipoEntidad1->id,
                                'idTipoEntidad2' => $idTipoEntidad2->id,
                                'idEntidad1' => $b['id'],
                                'idEntidad2' => $entidad->id,
                                'entidad1_saldo_inicial' => $saldo,
                                'entidad2_saldo_inicial' => 0,
                                'debito' => 0,
                                'credito' => $g['monto'],
                                'idGasto' => $g['id'],
                                'entidad1_saldo_final' => $saldo - $g['monto'],
                                'entidad2_saldo_final' => 0
                            ]);
                            $this->info('See hizo la transaccion diaria: '.$g['id']);
                        
                    }
                        
                }
                //Verificamos si el gasto es semanal
                if(strtolower($g->frecuencia->descripcion) == "semanal"){
                    $gastoWday = Days::on($servidor)->whereId($g->idDia)->first()->wday;
                    $this->info('Semanal wday: '.$gastoWday . " - " . $todayWday);
                    if($todayWday != $gastoWday)
                        continue;
                    //$this->info('klk: '.$fecha->hour);
                    //Verificamos que la hora de la fecha actual sean las 12AM, osea las 24 que es igual a la hora cero 0
                    //Verificamos tambien si es el dia establecido cuando es semanal
                    if($fecha->hour == $horaParaRealizarGasto){
                       //Verificamos que no haya transacciones realizadas en la fecha actual para la banca y el gasto especificado
                       $t = transactions::on($servidor)->where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idGasto' => $g->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
                       if($t != null)
                            continue;
                       
                        
                            $saldo = (new Helper)->saldo($servidor, $b['id'], 1);
                            $t = transactions::on($servidor)->create([
                                'idUsuario' => $usuario->id,
                                'idTipo' => $tipo->id,
                                'idTipoEntidad1' => $idTipoEntidad1->id,
                                'idTipoEntidad2' => $idTipoEntidad2->id,
                                'idEntidad1' => $b['id'],
                                'idEntidad2' => $entidad->id,
                                'entidad1_saldo_inicial' => $saldo,
                                'entidad2_saldo_inicial' => 0,
                                'debito' => 0,
                                'credito' => $g['monto'],
                                'idGasto' => $g['id'],
                                'entidad1_saldo_final' => $saldo - $g['monto'],
                                'entidad2_saldo_final' => 0
                            ]);
                            $this->info('See hizo la transaccion semanal: '.$g['id']);
                        
                    }
                        
                }
                //Verificamos si el gasto es quincenal
                if(strtolower($g->frecuencia->descripcion) == "quincenal"){
                    
                    $this->info('quincenal today - lastday: '.$fecha->day . " - " . $ultimoDiaMes->day);
                    //Verificamos que la hora de la fecha actual sean las 12AM, osea las 24 que es igual a la hora cero 0
                    //Verificamos tambien si estamos a 15 o  al ultimo dia del mes para poder realizar la transaccion quincenal
                    if($fecha->hour == $horaParaRealizarGasto && ($fecha->day == 15 || $fecha->day == $ultimoDiaMes->day)){
                       //Verificamos que no haya transacciones realizadas en la fecha actual para la banca y el gasto especificado
                       $t = transactions::on($servidor)->where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idGasto' => $g->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
                       if($t != null)
                            continue;
                       
                        
                            $saldo = (new Helper)->saldo($servidor, $b['id'], 1);
                            $t = transactions::on($servidor)->create([
                                'idUsuario' => $usuario->id,
                                'idTipo' => $tipo->id,
                                'idTipoEntidad1' => $idTipoEntidad1->id,
                                'idTipoEntidad2' => $idTipoEntidad2->id,
                                'idEntidad1' => $b['id'],
                                'idEntidad2' => $entidad->id,
                                'entidad1_saldo_inicial' => $saldo,
                                'entidad2_saldo_inicial' => 0,
                                'debito' => 0,
                                'credito' => $g['monto'],
                                'idGasto' => $g['id'],
                                'entidad1_saldo_final' => $saldo - $g['monto'],
                                'entidad2_saldo_final' => 0
                            ]);
                            $this->info('See hizo la transaccion quincenal: '.$g['id']);
                        
                    }
                        
                } //END VERIFICACION DE FRECUENCIA
                //Verificamos si el gasto es quincenal
                if(strtolower($g->frecuencia->descripcion) == "mensual"){
                    
                    $this->info('mensual today - lastday: '.$fecha->day . " - " . $ultimoDiaMes->day);
                    //Verificamos que la hora de la fecha actual sean las 12AM, osea las 24 que es igual a la hora cero 0
                    //Verificamos tambien si hoy es el ultimo dia del mes para poder realizar la transaccion quincenal
                    if($fecha->hour == $horaParaRealizarGasto && $fecha->day == $ultimoDiaMes->day){
                       //Verificamos que no haya transacciones realizadas en la fecha actual para la banca y el gasto especificado
                       $t = transactions::on($servidor)->where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idGasto' => $g->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
                       if($t != null)
                            continue;
                       
                        
                            $saldo = (new Helper)->saldo($servidor, $b['id'], 1);
                            $t = transactions::on($servidor)->create([
                                'idUsuario' => $usuario->id,
                                'idTipo' => $tipo->id,
                                'idTipoEntidad1' => $idTipoEntidad1->id,
                                'idTipoEntidad2' => $idTipoEntidad2->id,
                                'idEntidad1' => $b['id'],
                                'idEntidad2' => $entidad->id,
                                'entidad1_saldo_inicial' => $saldo,
                                'entidad2_saldo_inicial' => 0,
                                'debito' => 0,
                                'credito' => $g['monto'],
                                'idGasto' => $g['id'],
                                'entidad1_saldo_final' => $saldo - $g['monto'],
                                'entidad2_saldo_final' => 0
                            ]);
                            $this->info('See hizo la transaccion quincenal: '.$g['id']);
                        
                    }
                        
                } //END VERIFICACION DE FRECUENCIA
                //Verificamos si el gasto es anual
                if(strtolower($g->frecuencia->descripcion) == "anual"){
                    
                    $this->info('mensual today - lastday: '.$fecha->day . " - " . $ultimoDiaMes->day);
                    //Verificamos que la hora de la fecha actual sean las 12AM, osea las 24 que es igual a la hora cero 0
                    //Verificamos tambien si hoy es el ultimo dia del mes para poder realizar la transaccion quincenal
                    if($fecha->hour == $horaParaRealizarGasto && $fecha->day == $primerDiaMes->day && strtolower($fecha->englishMonth) == "january"){
                       //Verificamos que no haya transacciones realizadas en la fecha actual para la banca y el gasto especificado
                       $t = transactions::on($servidor)->where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idGasto' => $g->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
                       if($t != null)
                            continue;
                       
                        
                            $saldo = (new Helper)->saldo($servidor, $b['id'], 1);
                            $t = transactions::on($servidor)->create([
                                'idUsuario' => $usuario->id,
                                'idTipo' => $tipo->id,
                                'idTipoEntidad1' => $idTipoEntidad1->id,
                                'idTipoEntidad2' => $idTipoEntidad2->id,
                                'idEntidad1' => $b['id'],
                                'idEntidad2' => $entidad->id,
                                'entidad1_saldo_inicial' => $saldo,
                                'entidad2_saldo_inicial' => 0,
                                'debito' => 0,
                                'credito' => $g['monto'],
                                'idGasto' => $g['id'],
                                'entidad1_saldo_final' => $saldo - $g['monto'],
                                'entidad2_saldo_final' => 0
                            ]);
                            $this->info('See hizo la transaccion anual: '.$g['id']);
                        
                    }
                        
                } //END VERIFICACION DE FRECUENCIA
                
            }
        }

        
    endforeach;

        // Draws::create([
        //     'descripcion' => 'Sorteo cronjob',
        //     'bolos' => 3,
        //     'cantidadNumeros' => 4,
        //     'status' => 1
        // ]);
    }
}
