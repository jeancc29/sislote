<?php

use Illuminate\Database\Seeder;
use App\Roles as r;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // r::create([
        //     'descripcion' => 'Dueno',
        //     'status' => 1
        // ]);

        r::create([
            'descripcion' => 'Administrador',
            'status' => 1
        ]);

        r::create([
            'descripcion' => 'Supervisor',
            'status' => 1
        ]);

        r::create([
            'descripcion' => 'Banquero',
            'status' => 1
        ]);
    }
}
