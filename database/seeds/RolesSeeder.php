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

        $servidores = \App\Server::on("mysql")->get();
        
        //Primero se crean los roles en la DB principal
        $this->crearRoles($servidor);
        
        //Se crean los roles en la DB de cada cliente
        foreach ($servidores as $ser):
        $servidor = $ser->descripcion;

            $this->crearRoles($servidor);

        endforeach;
    }

    public function crearRoles($servidor)
    {
        r::on($servidor)->create([
            'descripcion' => 'Administrador',
            'status' => 1
        ]);

        r::on($servidor)->create([
            'descripcion' => 'Supervisor',
            'status' => 1
        ]);

        r::on($servidor)->create([
            'descripcion' => 'Banquero',
            'status' => 1
        ]);

        r::on($servidor)->create([
            'descripcion' => 'Programador',
            'status' => 1
        ]);
    }
}
