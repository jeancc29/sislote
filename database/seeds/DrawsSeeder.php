<?php

use Illuminate\Database\Seeder;
use App\Draws as d;
class DrawsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        d::create([
            'descripcion' => 'Directo',
            'bolos' => 3,
            'cantidadNumeros' => 2,
            'status' => 1
        ]);


        d::create([
            'descripcion' => 'Pale',
            'bolos' => 3,
            'cantidadNumeros' => 4,
            'status' => 1
        ]);

        d::create([
            'descripcion' => 'Tripleta',
            'bolos' => 3,
            'cantidadNumeros' => 6,
            'status' => 1
        ]);
        
        d::create([
            'descripcion' => 'Super pale',
            'bolos' => 3,
            'cantidadNumeros' => 4,
            'status' => 1
        ]);

        // d::create([
        //     'descripcion' => 'Super pale real',
        //     'bolos' => 3,
        //     'cantidadNumeros' => 4,
        //     'status' => 1
        // ]);
    }
}
