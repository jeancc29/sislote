<?php

use Illuminate\Database\Seeder;
use App\Blockslotteries as b;
use App\Blocksplays as bp;

class BlockslotteriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // b::create([
        //     'idBanca' => 1,
        //     'idLoteria' => 1,
        //     'idSorteo' => 1,
        //     'monto' => '250',
        //     'idDia' => '250'
        // ]);

        // b::create([
        //     'idBanca' => 1,
        //     'idLoteria' => 1,
        //     'idSorteo' => 2,
        //     'monto' => '70',
        //     'idDia' => '70'
        // ]);
        // b::create([
        //     'idBanca' => 1,
        //     'idLoteria' => 1,
        //     'idSorteo' => 3,
        //     'monto' => '25',
        //     'idDia' => '25'
        // ]);

        // b::create([
        //     'idBanca' => 1,
        //     'idLoteria' => 2,
        //     'idSorteo' => 1,
        //     'monto' => '250'
        // ]);

        // b::create([
        //     'idBanca' => 1,
        //     'idLoteria' => 2,
        //     'idSorteo' => 2,
        //     'monto' => '70'
        // ]);

        // b::create([
        //     'idBanca' => 1,
        //     'idLoteria' => 2,
        //     'idSorteo' => 3,
        //     'monto' => '25'
        // ]);

        // bp::create([
        //     'idLoteria' => 1,
        //     'idSorteo' => 1,
        //     'jugada' => "0929",
        //     'montoInicial' => '20',
        //     'monto' => '20',
        //     'fechaDesde' => '2019-01-19',
        //     'fechaHasta' => '2019-01-19 23:59:00',
        //     'idUsuario' => 1,
        //     'status' => 1
        // ]);


        // bp::create([
        //     'idLoteria' => 1,
        //     'idSorteo' => 1,
        //     'jugada' => "0929",
        //     'montoInicial' => '20',
        //     'monto' => '20',
        //     'fechaDesde' => '2019-01-18',
        //     'fechaHasta' => '2019-01-19 00:00:00',
        //     'idUsuario' => 1,
        //     'status' => 1
        // ]);
    }
}
