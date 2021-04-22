<?php
namespace App\Classes;

use App\Users;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;

use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksplays;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Branches;
use App\Roles;
use App\Commissions;
use App\Permissions;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;
use App\Classes\Helper;

use App\Events\UsersEvent;

class UsersClass{
    private $servidor;
    private $datos;

    function __construct($servidor, $datos) {
        $this->servidor = $servidor;
        $this->datos = $datos;
    }

    public function save()
    {
        $usuario = Users::on($this->servidor)->whereId($this->datos['id'])->get()->first();
        if($usuario != null)
            $usuarioDBPrincipal = Users::on("mysql")->whereUsuario($usuario->usuario)->get()->first();
        else
            $usuarioDBPrincipal = null;

        if($usuario != null){
            $this->validarCorreo($this->servidor, $usuario);
            $this->validarUsuario($this->servidor, $usuario);
            $this->validarCorreo("mysql", $usuarioDBPrincipal);
            $this->validarUsuario("mysql", $usuarioDBPrincipal);
            
            $usuario = $this->update($usuario);

            if($usuarioDBPrincipal != null)
                $this->update($usuarioDBPrincipal);
            else{
                $this->datos['password'] = Crypt::decryptString($usuario->password);
                $this->datos['confirmar'] = Crypt::decryptString($usuario->password);
                $this->create("mysql");
            }

        }else{
            $this->validarCorreo($this->servidor);
            $this->validarUsuario($this->servidor);
            $this->validarCorreo("mysql");
            $this->validarUsuario("mysql");
            $usuario = $this->create($this->servidor);
            $this->create("mysql");
        }

        $this->agregarPermisos($usuario);
        return $usuario;
    }

    private function validarCorreo($servidor, Users $usuario = null)
    {
        $id = Users::on($servidor)->whereEmail($this->datos['email'])->first();
        if($id != null){
            if($usuario != null)
            {
                if($usuario->id != $id->id){
                    abort(403, 'El correo ya existe, elija uno diferente');
                }
                if($servidor == "mysql"){
                    //Si el usuario existe con un servidor diferente eso quiere decir que no se puede usar ese usuario
                    //Si el usuario existe en la tabla Users de la base de datos principal 'mysql' y el valor del campo 'servidor' es diferente
                    //al valor del campo 'servidor' de la base de datos del se esta intentando guardar el usuario, eso quiere decir que el usuario existe
                    if($this->servidor != $usuario->servidor){
                        abort(403, "El usuario ya existe, elija uno diferente {$this->servidor} : {$usuario->servidor}");
                    }
                }
            }else{
                abort(403, 'El correo ya existe, elija uno diferente');
            }
        }
    }

    private function validarUsuario($servidor, Users $usuario = null)
    {
        $id = Users::on($servidor)->whereUsuario($this->datos['usuario'])->first();
        if($id != null){
            if($usuario != null)
            {
                if($usuario->id != $id->id){
                    abort(403, 'El usuario ya existe, elija uno diferente');
                }
                if($servidor == "mysql"){
                    //Si el usuario existe con un servidor diferente eso quiere decir que no se puede usar ese usuario
                    if($this->servidor != $usuario->servidor){
                        abort(403, 'El usuario ya existe, elija uno diferente');
                    }
                }
            }else{
                abort(403, 'El usuario ya existe, elija uno diferente');
            }
        }
    }

    private function validarContrasena()
    {
        if(empty($this->datos['password']) && empty($this->datos['confirmar'])){
            abort(403, 'Contraseña no valida');
        }

        if($this->datos['password'] != $this->datos['confirmar']){
            abort(403, 'Contraseña no valida');
        }
    }

    private function update(Users $usuario)
    {
        $usuario['nombres'] = $this->datos['nombres'];
        $usuario['email'] = $this->datos['email'];
        $usuario['usuario'] = $this->datos['usuario'];
        $usuario['idRole'] = $this->datos['idTipoUsuario'];
        $usuario['status'] = $this->datos['status'];
        $usuario['servidor'] = $this->servidor;

        if(!empty($this->datos['password']) && !empty($this->datos['confirmar'])){
            if($this->datos['password'] == $this->datos['confirmar']){
                $usuario['password'] = Crypt::encryptString($this->datos['password']);
            }
        }

        $usuario->save();
        return $usuario;
    }

    private function create($servidor)
    {
        $this->validarContrasena();
        $usuario = Users::on($servidor)->create([
            'nombres' => $this->datos['nombres'],
            'email' => $this->datos['email'],
            'usuario' => $this->datos['usuario'],
            'password' => Crypt::encryptString($this->datos['password']),
            'idRole' => $this->datos['idTipoUsuario'],
            'status' => $this->datos['status'],
            'servidor' => $this->datos['servidor']
        ]);
        return $usuario;
    }

    private function agregarPermisos($usuario)
    {
        //Eliminamos los PERMISOS para luego agregarlos nuevamentes
        $usuario->permisos()->detach();
        $permisos = collect($this->datos['permisos'])->map(function($d) use($usuario){
            return ['idPermiso' => $d['id'], 'idUsuario' => $usuario['id']];
        });
       
        $usuario->permisos()->attach($permisos);
    }
}