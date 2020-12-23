<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AwardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $datos;
    protected $fecha;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datos, $fecha)
    {
        $this->datos = $datos;
        $this->fecha = $fecha;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $servers = \App\Server::all();
        foreach ($servers as $server) {
            $this->datos["servidor"] = $server->descripcion;
            $this->save($this->datos, $this->fecha);
        }
    }

    public function save($datos, $fecha){
        foreach($datos['loterias'] as $l):
            $loteria = \App\Lotteries::on($datos["servidor"])->whereDescripcion($l["descripcion"])->first();
            $l['id'] = $loteria->id;
            print "Loteria: " . $loteria->descripcion . "\n";
            $awardsClass = new \App\Classes\AwardsClass($datos["servidor"], $l['id']);
            $awardsClass->fecha = $fecha;
            $awardsClass->idUsuario = $datos['idUsuario'];
            $awardsClass->primera = $l['primera'];
            $awardsClass->segunda = $l['segunda'];
            $awardsClass->tercera = $l['tercera'];
            $awardsClass->pick3 = $l['pick3'];
            $awardsClass->pick4 = $l['pick4'];
            $awardsClass->numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];
    
            
            if($awardsClass->combinacionesNula() == true){
                continue;
            }
           
            if(!$awardsClass->loteriaAbreDiaActual()){
                // return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $awardsClass->getLoteriaDescripcion() .' no abre este dia '], 201);
            }  
            if($awardsClass->insertarPremio() == false){
                // return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
            }
    
               
                $c = 0;
                $colleccion = null;
                
                foreach($awardsClass->getJugadasDeFechaDada($l['id']) as $j){
        
                    $j['premio'] = 0;
                    $contador = 0;
                    $busqueda1 = false;
                    $busqueda2 = false;
                    $busqueda3 = false;
    
                    // abort(404, $j["jugada"]);
    
                    $sorteo = \App\Draws::on($datos["servidor"])->whereId($j['idSorteo'])->first();
    
                // return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);
                    
        
                    
                    if($sorteo->descripcion == "Directo"){
                        if(!is_numeric($awardsClass->numerosGanadores)){
                            abort(404, 'Los numeros ganadores no son correctos');
                        }
                        $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                    }
                    else if($sorteo->descripcion == "Pale"){
                        if(!is_numeric($awardsClass->numerosGanadores)){
                            abort(404, 'Los numeros ganadores no son correctos');
                        }
                        $j['premio'] = $awardsClass->paleBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], $j['idSorteo']);
                    }
                    // else if($sorteo->descripcion == "Super pale"){
                    //     if(!is_numeric($awardsClass->numerosGanadores)){
                    //         return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
                    //     }
                    //     $j['premio'] = $awardsClass->superPaleBuscarPremio($j['idVenta'], $l['id'], $j['idLoteriaSuperpale'], $j['jugada'], $j['monto'], $j['idSorteo']);
                    // }
                    else if($sorteo->descripcion == "Tripleta"){
                        if(!is_numeric($awardsClass->numerosGanadores)){
                            abort(404, 'Los numeros ganadores no son correctos');
                        }
                        $j['premio'] = $awardsClass->tripletaBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                    }
                    else if($sorteo->descripcion == "Pick 3 Straight"){
                        $j['premio'] = $awardsClass->pick3BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                    }
                    else if($sorteo->descripcion == "Pick 3 Box"){
                        $j['premio'] = $awardsClass->pick3BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], false);
                    }
                    else if($sorteo->descripcion == "Pick 4 Straight"){
                        $j['premio'] = $awardsClass->pick4BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
                    }
                    else if($sorteo->descripcion == "Pick 4 Box"){
                        $j['premio'] = $awardsClass->pick4BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], false);
                    }
        
        
                    $j['status'] = 1;
                    $j->save();
        
                    $c++;
                }
    
    
                //Buscar jugadas super pale de esa loteria, ya sea esta la loteria primaria o loteria superpale de la tabla salesdetails
                // return Response::json(['errores' => 1,'mensaje' => $awardsClass->getJugadasSuperpaleDeFechaDada($l['id'])], 201);
                foreach($awardsClass->getJugadasSuperpaleDeFechaDada($l['id']) as $j){
                    
        
                    $j['premio'] = 0;
                    $contador = 0;
                    $busqueda1 = false;
                    $busqueda2 = false;
                    $busqueda3 = false;
    
                    if(!is_numeric($awardsClass->numerosGanadores)){
                        abort(404,'Los numeros ganadores no son correctos');
                    }
    
                    //Si el premio superpale es igual a -1 entonces eso quiere decir que la otra loteria no ha salido, 
                    //por lo tanto el status de la jugada seguira siendo igual a cero, indicando que todavia la jugada estara pendiente
                    $premioSuperpale = $awardsClass->superPaleBuscarPremio($j['idVenta'], $l['id'], $j);
                    // return Response::json(['errores' => 1,'mensaje' => "Dentro jugadas super pale premio: {$premioSuperpale}"], 201);
                    if($premioSuperpale != -1){
                        $j['premio'] = $premioSuperpale;
                        $j['status'] = 1;
                        $j->save();
                    }else{
                        $j['premio'] = 0;
                        $j['status'] = 0;
                        $j->save();
                    }
                    
        
                    $c++;
                }
        
            endforeach;
    
    
            foreach($datos['loterias'] as $l):
               
                /************** MANERA NUEVA DE CAMBIAR STATUS DE LOS TICKETS ***********/
                \App\Classes\AwardsClass::actualizarStatusDelTicket($datos["servidor"], $l["id"], $fecha);
    
    
            endforeach;
    }
}
