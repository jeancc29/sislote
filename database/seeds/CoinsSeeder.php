<?php

use Illuminate\Database\Seeder;
use App\Coins as c;
class CoinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        c::create([
            'descripcion' => 'Dolar',
            'permiteDecimales' => 1
        ]);

        c::create([
            'descripcion' => 'Peso Dominicano',
            'permiteDecimales' => 0
        ]);
    }
}
