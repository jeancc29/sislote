<?php

use Illuminate\Database\Seeder;
use App\Users as u;
use App\Permissions as p;
use Illuminate\Support\Facades\Crypt; 

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        



        $usuario = u::create([
            'nombres' => 'Sistema',
            'apellidos' => 'Sistema',
            'sexo' => 'Masculino',
            'email' => 'jeancon29@gmail.com',
            'celular' => '8294266800',
            'idRole' => 1,
            'usuario' => 'sistema',
            'password' => Crypt::encryptString('Jean06091929'),
            'status' => 3
        ]);
    }

    public function createOrUpdateJean($servidor)
    {
        $usuario = Users::on($servidor)->whereUsuario("jean")->get()->first();
        if($usuario == null){
            $usuario = u::on($servidor)->create([
                'nombres' => 'Jean carlos',
                'apellidos' => 'Contreras',
                'sexo' => 'Masculino',
                'email' => 'jean29@outlook.com',
                'celular' => '8094266800',
                'idRole' => 4,
                'usuario' => 'jean',
                'password' => Crypt::encryptString('Jean06091929')
            ]);
        }
        
        
        $usuario->permisos()->detach();
        $permisos = p::on($servidor)->all();
        $permisos = collect($permisos)->map(function($d) use($usuario){
            return ['idPermiso' => $d['id'], 'idUsuario' => $usuario['id']];
        });
       
        $usuario->permisos()->attach($permisos);
    }
}
