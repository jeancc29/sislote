<?php

use Illuminate\Database\Seeder;
use App\Settings as s;
use App\Coins as c;
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $idMoneda = c::whereDescripcion("Dolar")->first();
        s::create([
            'idMoneda' => $idMoneda->id
        ]);
    }
}
