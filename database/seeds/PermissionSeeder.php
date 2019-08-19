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




        //Estos idTipo que esta aqui no estan registrado en la table tipos, solo son para manejarlos de manera superficial


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
            'descripcion' => 'Acceso al sistema',
            'status' => 1,
            'idTipo' => 6
        ]);
        
        p::create([
            'descripcion' => 'Ver menus',
            'status' => 1,
            'idTipo' => 6
        ]);


        //Transacciones idTipo == 7
        //16
        p::create([
            'descripcion' => 'Crear ajustes',
            'status' => 1,
            'idTipo' => 7
        ]);

        p::create([
            'descripcion' => 'Crear cobros',
            'status' => 1,
            'idTipo' => 7
        ]);

        p::create([
            'descripcion' => 'Crear pagos',
            'status' => 1,
            'idTipo' => 7
        ]);

        p::create([
            'descripcion' => 'Manejar transacciones',
            'status' => 1,
            'idTipo' => 7
        ]);





         //Balances idTipo == 8
        //20
        p::create([
            'descripcion' => 'Ver lista de balances de bancas',
            'status' => 1,
            'idTipo' => 8
        ]);

        p::create([
            'descripcion' => 'Ver lista de balances de bancos',
            'status' => 1,
            'idTipo' => 8
        ]);


        //Otros idTipo == 9
        //22
       

        p::create([
            'descripcion' => 'Manejar resultados',
            'status' => 1,
            'idTipo' => 9
        ]);
        p::create([
            'descripcion' => 'Manejar agentes externos',
            'status' => 1,
            'idTipo' => 9
        ]);
        p::create([
            'descripcion' => 'Manejar entidades contables',
            'status' => 1,
            'idTipo' => 9
        ]);
        p::create([
            'descripcion' => 'Manejar horarios de loterias',
            'status' => 1,
            'idTipo' => 9
        ]);
        p::create([
            'descripcion' => 'Manejar loterias',
            'status' => 1,
            'idTipo' => 9
        ]);
        p::create([
            'descripcion' => 'Manejar reglas',
            'status' => 1,
            'idTipo' => 9
        ]);
        p::create([
            'descripcion' => 'Manejar prestamos',
            'status' => 1,
            'idTipo' => 9
        ]);
        // p::create([
        //     'descripcion' => 'Manejar correos',
        //     'status' => 1,
        //     'idTipo' => 9
        // ]);
         // p::create([
        //     'descripcion' => 'Cierre programado global de loteria',
        //     'status' => 1,
        //     'idTipo' => 9
        // ]);
        // p::create([
        //     'descripcion' => 'Editar caida acumulada',
        //     'status' => 1,
        //     'idTipo' => 9
        // ]);

      

    }
}
