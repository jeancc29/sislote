<?php

use Illuminate\Database\Seeder;
use App\Types as t;
class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $servidores = \App\Server::on("mysql")->get();
        //Primero creamos o actualizamos, los usuarios jean y sistema en la DB principal

        //creamos o actualizamos, los usuarios jean y sistema en las DB correspondientes a cada cliente
        foreach ($servidores as $ser):
            $servidor = $ser->descripcion;

            if(\App\Classes\Helper::dbExists($servidor) == false)
                continue;

        //Entidades
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Agente externo',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Banca',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Banco',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Caida Acumulada',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Empleado',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Grupo',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Otros',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Sistema',
            'status' => 1,
            'renglon' => 'entidad'
        ]);



        //Transaccion
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Ajuste',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Cobro',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Pago',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Consumo automatico de banca',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Caida Acumulada',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Sorteo',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Cobro prestamo',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Desembolso de prestamo',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Descuento dias no laborados',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Pago cuota',
            'status' => 1,
            'renglon' => 'pago'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Abono a capital',
            'status' => 1,
            'renglon' => 'pago'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Campo montoCuotas, ya sea con tasaInteres o no',
            'status' => 1,
            'renglon' => 'amortizacion'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Campo numeroCuotas, ya sea con tasaInteres o no',
            'status' => 1,
            'renglon' => 'amortizacion'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Campo montoCuotas y numeroCuotas, se calcula la tasaInteres automatico',
            'status' => 1,
            'renglon' => 'amortizacion'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Formato de ticket 1',
            'status' => 1,
            'renglon' => 'ticket'
        ]);

        t::on($servidor)->updateOrCreate([
            'descripcion' => 'Formato de ticket 2',
            'status' => 1,
            'renglon' => 'ticket'
        ]);
      
        endforeach;
    }
}
