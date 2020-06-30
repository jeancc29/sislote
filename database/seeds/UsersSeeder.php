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
        

        $servidores = \App\Server::on("mysql")->get();
        //Primero creamos o actualizamos, los usuarios jean y sistema en la DB principal
        $this->createOrUpdateJean("mysql");
        $this->createOrUpdateSistema("mysql");

        //creamos o actualizamos, los usuarios jean y sistema en las DB correspondientes a cada cliente
        foreach ($servidores as $ser):
            $servidor = $ser->descripcion;

            $this->createOrUpdateJean($servidor);
            $this->createOrUpdateSistema($servidor);

        endforeach;
    }

    public function createOrUpdateJean($servidor)
    {
        $usuario = u::on($servidor)->whereUsuario("jean")->get()->first();
        if($usuario == null){
            $usuario = u::on($servidor)->create([
                'nombres' => 'Jean carlos',
                'apellidos' => 'Contreras',
                'sexo' => 'Masculino',
                'email' => 'jean29@outlook.com',
                'celular' => '8094266800',
                'idRole' => \App\Roles::on($servidor)->whereDescripcion("Programador")->first()->id,
                'usuario' => 'jean',
                'password' => Crypt::encryptString('111729'),
            ]);
        }else{
            $usuario->servidor = $servidor;
            $usuario->password = Crypt::encryptString('111729');
            $usuario->idRole = \App\Roles::on($servidor)->whereDescripcion("Programador")->first()->id;
            $usuario->save();
        }
        
        
        $usuario->permisos()->detach();
        $permisos = p::on($servidor)->get();
        $permisos = collect($permisos)->map(function($d) use($usuario){
            return ['idPermiso' => $d['id'], 'idUsuario' => $usuario['id']];
        });
       
        $usuario->permisos()->attach($permisos);
    }

    public function createOrUpdateSistema($servidor){
        $usuario = u::on($servidor)->whereUsuario("sistema")->get()->first();
        if($usuario == null){
            $usuario = u::on($servidor)->create([
                'nombres' => 'Sistema',
                'apellidos' => 'Sistema',
                'sexo' => 'Masculino',
                'email' => 'jeancon29@gmail.com',
                'celular' => '8294266800',
                'idRole' => 1,
                'usuario' => 'sistema',
                'password' => Crypt::encryptString('111729'),
                'status' => 3,
                'servidor' => $serv
            ]);
        }else{
            $usuario->servidor = $servidor;
            $usuario->save();
        }
        
    }
}
