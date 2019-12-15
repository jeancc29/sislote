<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use \App\Roles as r;
use App\Permissions as p;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$dueño = r::where('descripcion', 'Dueno')->get()->first()->value('id');
        $administrador = r::where('descripcion', 'Administrador')->get()->first()->id;
        $supervisor = r::where('descripcion', 'Supervisor')->get()->first()->id;
        $banquero = r::where('descripcion', 'Banquero')->get()->first()->id;


        // $principal = p::where('descripcion', 'Principal')->get()->first()->value('id');
        // $loterias = p::where('descripcion', 'Loterias')->get()->first()->value('id');
        // $bloqueos = p::where('descripcion', 'Bloqueos')->get()->first()->value('id');
        // $premios = p::where('descripcion', 'Premios')->get()->first()->value('id');
        // $bancas = p::where('descripcion', 'Bancas')->get()->first()->value('id');
        // $reportes = p::where('descripcion', 'Reportes')->get()->first()->value('id');


        // DUEÑO
        // DB::table('permission_role')->insert(['idPermiso' => $dueño,'idRole' => $principal]);
        // DB::table('permission_role')->insert(['idPermiso' => $dueño,'idRole' => $loterias]);
        // DB::table('permission_role')->insert(['idPermiso' => $dueño,'idRole' => $bloqueos]);
        // DB::table('permission_role')->insert(['idPermiso' => $dueño,'idRole' => $premios]);
        // DB::table('permission_role')->insert(['idPermiso' => $dueño,'idRole' => $bancas]);
        // DB::table('permission_role')->insert(['idPermiso' => $dueño,'idRole' => $reportes]);


         // ADMINISTRADOR
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 1]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 2]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 3]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 4]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 5]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 6]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 7]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 8]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 9]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 10]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 11]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 12]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 13]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 14]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 15]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 16]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 17]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 18]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 19]);
         DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 20]);


          // SUPERVISOR
          DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 1]);
          DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 2]);
          DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 7]);
          DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 8]);
          DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 9]);
          DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 3]);


          // BANQUERO
         DB::table('permission_role')->insert(['idRole' => $banquero,'idPermiso' => 12]);
         DB::table('permission_role')->insert(['idRole' => $banquero,'idPermiso' => 8]);

          
    }
}
