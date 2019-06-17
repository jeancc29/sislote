<?php

use Illuminate\Database\Seeder;
use App\Frecuency as f;

class FrecuencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        f::create([
                'descripcion' => 'Anual',
                'observacion' => 'Será aplicado a las 12:00 AM del 1ro de enero cada año'
            ]);
        f::create([
                'descripcion' => 'Mensual',
                'observacion' => 'Será aplicado a las 12:00 AM del último día del mes'
            ]);
        f::create([
                'descripcion' => 'Quincenal',
                'observacion' => 'Será aplicado a las 12:00 AM los 15 y el último día de cada mes'
            ]);
        f::create([
                'descripcion' => 'Semanal',
                'observacion' => 'Será aplicado a las 12:00 AM del día especificado'
            ]);
        f::create([
                'descripcion' => 'Diario',
                'observacion' => 'Será aplicado a las 12:00 AM de cada día'
            ]);
    }
}
