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
        $servidores = \App\Server::on("mysql")->get();
        foreach ($servidores as $ser):
        $servidor = $ser->descripcion;
        if(\App\Classes\Helper::dbExists($servidor) == false)
                continue;
        //$dueño = r::where('descripcion', 'Dueno')->get()->first()->value('id');
        $administrador = r::on($servidor)->where('descripcion', 'Administrador')->get()->first()->id;
        $supervisor = r::on($servidor)->where('descripcion', 'Supervisor')->get()->first()->id;
        $banquero = r::on($servidor)->where('descripcion', 'Banquero')->get()->first()->id;
        $programador = r::on($servidor)->where('descripcion', 'Programador')->get()->first()->id;


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


         // PROGRAMADOR
         $permisos = p::on($servidor)->get();
         foreach($permisos as $permiso){
            DB::connection($servidor)->table('permission_role')->insert(['idRole' => $programador,'idPermiso' => $permiso->id]);
         }
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 1]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 2]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 3]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 4]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 5]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 6]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 7]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 8]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 9]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 10]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 11]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 12]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 13]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 14]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 15]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 16]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 17]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 18]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 19]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 20]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 21]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 22]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 23]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 24]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 25]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 26]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 27]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 28]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 29]);

         // ADMINISTRADOR
         $permisos = p::on($servidor)->get();
         foreach($permisos as $permiso){
            DB::connection($servidor)->table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => $permiso->id]);
         }
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 1]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 2]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 3]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 4]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 5]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 6]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 7]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 8]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 9]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 10]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 11]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 12]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 13]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 14]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 15]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 16]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 17]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 18]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 19]);
        //  DB::table('permission_role')->insert(['idRole' => $administrador,'idPermiso' => 20]);


          // SUPERVISOR
          $permisos = p::on($servidor)->whereIn("descripcion", [
              "Manejar usuarios", "Ver inicios de sesion", "Marcar ticket como pagado", "Manejar bancas", "Vender tickets", "Acceso al sistema", "Manejar resultados"
            ])->get();
         foreach($permisos as $permiso){
            DB::connection($servidor)->table('permission_role')->insert(['idRole' => $supervisor,'idPermiso' => $permiso->id]);
         }
        //   DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 1]);
        //   DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 2]);
        //   DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 7]);
        //   DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 8]);
        //   DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 9]);
        //   DB::table('permission_role')->insert(['idRole' => $supervisor, 'idPermiso' => 3]);


          // BANQUERO
          $permisos = p::on($servidor)->whereIn("descripcion", [
             "Marcar ticket como pagado", "Vender tickets", "Acceso al sistema", "Monitorear ticket"
          ])->get();
       foreach($permisos as $permiso){
          DB::connection($servidor)->table('permission_role')->insert(['idRole' => $banquero,'idPermiso' => $permiso->id]);
       }
        //  DB::table('permission_role')->insert(['idRole' => $banquero,'idPermiso' => 12]);
        //  DB::table('permission_role')->insert(['idRole' => $banquero,'idPermiso' => 8]);

        endforeach;
    }
}
