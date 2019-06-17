<?php

use Illuminate\Database\Seeder;
use App\Generals as g;

class GeneralsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        g::create([
            'minutosParaCancelar' => 4
        ]);
    }
}
