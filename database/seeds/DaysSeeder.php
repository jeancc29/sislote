<?php

use Illuminate\Database\Seeder;
use App\Days as d;
class DaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        d::create([
            'descripcion' => 'Lunes',
            'wday' => 1
        ]);
        d::create([
            'descripcion' => 'Martes',
            'wday' => 2
        ]);
        d::create([
            'descripcion' => 'Miercoles',
            'wday' => 3
        ]);
        d::create([
            'descripcion' => 'Jueves',
            'wday' => 4
        ]);
        d::create([
            'descripcion' => 'Viernes',
            'wday' => 5
        ]);
        d::create([
            'descripcion' => 'Sabado',
            'wday' => 6
        ]);
        d::create([
            'descripcion' => 'Domingo',
            'wday' => 0
        ]);
    }
}
