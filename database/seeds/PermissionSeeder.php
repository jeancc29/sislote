<?php

use Illuminate\Database\Seeder;
use App\Permissions as p;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // p::create([
        //     'descripcion' => 'Principal',
        //     'status' => 1
        // ]);


        // p::create([
        //     'descripcion' => 'Loterias',
        //     'status' => 1
        // ]);

        // p::create([
        //     'descripcion' => 'Bloqueos',
        //     'status' => 1
        // ]);

        // p::create([
        //     'descripcion' => 'Premios',
        //     'status' => 1
        // ]);

        // p::create([
        //     'descripcion' => 'Bancas',
        //     'status' => 1
        // ]);

        // p::create([
        //     'descripcion' => 'Reportes',
        //     'status' => 1
        // ]);







        //Usuarios idTipo == 1
        p::create([
            'descripcion' => 'Manejar usuarios',
            'status' => 1,
            'idTipo' => 1
        ]);
        p::create([
            'descripcion' => 'Ver inicios de sesion',
            'status' => 1,
            'idTipo' => 1
        ]);

        
        //Tickets idTipo == 2
        //3
        p::create([
            'descripcion' => 'Cancelar tickets en cualquier momento',
            'status' => 1,
            'idTipo' => 2
        ]);
        p::create([
            'descripcion' => 'Marcar ticket como pagado',
            'status' => 1,
            'idTipo' => 2
        ]);
        p::create([
            'descripcion' => 'Monitorear ticket',
            'status' => 1,
            'idTipo' => 2
        ]);
        p::create([
            'descripcion' => 'Eliminar ticket',
            'status' => 1,
            'idTipo' => 2
        ]);


        //Bancas idTipo == 3
        //7
        p::create([
            'descripcion' => 'Manejar bancas',
            'status' => 1,
            'idTipo' => 3
        ]);
        p::create([
            'descripcion' => 'Vender tickets',
            'status' => 1,
            'idTipo' => 3
        ]);

        //Jugar idTipo == 4
        p::create([
            'descripcion' => 'Jugar como cualquier banca',
            'status' => 1,
            'idTipo' => 4
        ]);
        p::create([
            'descripcion' => 'Jugar fuera de horario',
            'status' => 1,
            'idTipo' => 4
        ]);
        p::create([
            'descripcion' => 'Jugar sin disponibilidad',
            'status' => 1,
            'idTipo' => 4
        ]);





         //Ventas idTipo == 5
         //12
         p::create([
            'descripcion' => 'Procesar ventas',
            'status' => 1,
            'idTipo' => 5
        ]);
         p::create([
            'descripcion' => 'Ver ventas',
            'status' => 1,
            'idTipo' => 5
        ]);



        //Acceso al sistema idTipo == 6
        //14
        p::create([
            'descripcion' => 'Ver menus',
            'status' => 1,
            'idTipo' => 6
        ]);

    }
}
