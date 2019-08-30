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
        //Entidades
        t::create([
            'descripcion' => 'Agente externo',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Banca',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Banco',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Caida Acumulada',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Empleado',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Grupo',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Otros',
            'status' => 1,
            'renglon' => 'entidad'
        ]);
        t::create([
            'descripcion' => 'Sistema',
            'status' => 1,
            'renglon' => 'entidad'
        ]);



        //Transaccion
        t::create([
            'descripcion' => 'Ajuste',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Cobro',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Pago',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Consumo automatico de banca',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Caida Acumulada',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Sorteo',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Cobro prestamo',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);
        t::create([
            'descripcion' => 'Desembolso de prestamo',
            'status' => 1,
            'renglon' => 'transaccion'
        ]);

        t::create([
            'descripcion' => 'Pago cuota',
            'status' => 1,
            'renglon' => 'pago'
        ]);

        t::create([
            'descripcion' => 'Abono a capital',
            'status' => 1,
            'renglon' => 'pago'
        ]);

    }
}
