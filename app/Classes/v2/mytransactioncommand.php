<?php
namespace App\Classes\v2;

class MyTransactionCommand{
    public static function update($fecha){
        $fechaString = $fecha->year.'-'.$fecha->month.'-'.$fecha->day;
        $servidores = \App\Server::on("mysql")->get();
        foreach ($servidores as $servi):
        $servidor = $servi->descripcion;
        

        $fechaDesde = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 00:00:00";
        $fechaHasta = $fecha->year.'-'.$fecha->month.'-'.$fecha->day. " 23:59:00";
        $usuario = Users::on($servidor)->whereNombres("Sistema")->first();
        $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Sorteo")->first();
        $idTipoEntidad1 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        $idTipoEntidad2 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Sistema'])->first();
        $entidad = Entity::on($servidor)->whereNombre("Sistema")->first();

        

        // $saldo = (new Helper)->_sendSms("+18294266800", "Hola jean como estas");
        // $this->info($prueba->hour);
        // return;

        if($fecha->hour != 23)
        return;
       
        if($usuario == null || $tipo == null)
            return;
            


        
        $bancas = BranchesResource::collection(Branches::on($servidor)->whereStatus(1)->get())->servidor($servidor);
        foreach($bancas as $b){
            
           
            $t = transactions::on($servidor)->where(['idTipoEntidad1' => $idTipoEntidad1->id, 'idEntidad1' => $b['id'], 'idTipo'=> $tipo->id, 'status' => 1])->whereBetween('created_at', array($fechaDesde, $fechaHasta))->first();
            if($t == null)
                 continue;
                
                //  $this->info('des:prem:comi '.$b['descuentosDelDia'].':'.$b['premiosDelDia'].':'.$b['comisionesDelDia']);
                $ventasDelDia = Helper::ventasPorBanca($servidor, $b['id'], $fechaString, $fechaString);
                $descuentosDelDia = Helper::descuentosPorBanca($servidor, $b['id'], $fechaString, $fechaString);
                $premiosDelDia = Helper::premiosPorBanca($servidor, $b['id'], $fechaString, $fechaString);
                $comisionesDelDia = Helper::comisionesPorBanca($servidor, $b['id'], $fechaString, $fechaString);
                //  $this->info('des:prem:comi '.$descuentosDelDia.';'.$premiosDelDia.';'.$comisionesDelDia);
                //  return $b;
                $saldoFinalEntidad1 = 0;
                $saldo = (new Helper)->saldoPorFecha($servidor, $b['id'], 1, $fecha);
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

                $t = transactions::on($servidor)->create([
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

    endforeach;
    }
}