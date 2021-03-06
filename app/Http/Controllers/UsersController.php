<?php

namespace App\Http\Controllers;

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

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        if(!strpos(Request::url(), '/api/')){
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar usuarios") == true){
                return redirect()->route('sinpermiso');
            }
            return view('usuarios.index', compact('controlador'));
        }


        $datos = request()->validate([
            'token' => '',
        ]);
        // $datos = \Helper::jwtDecode($datos["token"]);
        try {
            $datos = \Helper::jwtDecode($datos["token"]);

            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["data"]))
                $datos = $datos["data"];


            if(isset($datos["datosMovil"]))
               $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $roles = null;
        $usuarios = null;
        $usuario = Users::on($datos["servidor"])->whereId($datos["idUsuario"])->first();
        if($usuario != null){
            $role = $usuario->roles;
            if($role != null){
                if($role->descripcion == "Programador"){
                    $roles = Roles::on($datos["servidor"])->get();
                    $usuarios = Users::on($datos["servidor"])->whereIn('status', array(0, 1))->get();
                }
            }
        }

        if($roles == null)
            $roles = Roles::on($datos["servidor"])->where('descripcion', '!=', 'Programador')->get();
              
        if($usuarios == null)
            $usuarios = Users::on($datos["servidor"])->whereIn('status', array(0, 1))->where('idRole', '!=', Roles::on($datos["servidor"])->whereDescripcion("Programador")->first()->id)->get();

        return Response::json([
            'usuarios' => UsersResource::collection($usuarios),
            'usuariosTipos' => RolesResource::collection($roles),
            'permisos' => Permissions::on($datos["servidor"])->get(),
            "grupos" => \App\Group::on($datos["servidor"])->get(),
        ], 201);
    }

    public function search(Request $request)
    {
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["data"]))
                $datos = $datos["data"];
                
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];

        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'data' => \App\Users::search($datos["servidor"], $datos["search"])
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.id' => 'required',
        //     'datos.nombres' => 'required',
        //     'datos.email' => 'required|email',
        //     'datos.usuario' => 'required',
        //     'datos.password' => '',
        //     'datos.confirmar' => '',
        //     'datos.permisos' => 'required',
        //     'datos.status' => 'required',
        //     'datos.idTipoUsuario' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["data"]))
                $datos = $datos["data"];


            if(isset($datos["datosMovil"]))
               $datos = $datos["datosMovil"];

        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso('Manejar usuarios')){
           abort(403, "No tiene permisos para realizar esta accion");
        }
    
        $errores = 0;
        $mensaje = '';
    
        
        //Verificar si el usuario tiene permisos
        //$permiso = Users
    
        $usersClass = new \App\Classes\UsersClass($datos["servidor"], $datos);
        $usuario = $usersClass->save();
           
        $usuario = Users::on($datos["servidor"])->whereId($usuario->id)->first();
        $usuario = new UsersResource($usuario);
        event(new UsersEvent($usuario));
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'data' => $usuario
        ], 201);
    }

    public function storeViejo(Request $request)
    {
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.id' => 'required',
        //     'datos.nombres' => 'required',
        //     'datos.email' => 'required|email',
        //     'datos.usuario' => 'required',
        //     'datos.password' => '',
        //     'datos.confirmar' => '',
        //     'datos.permisos' => 'required',
        //     'datos.status' => 'required',
        //     'datos.idTipoUsuario' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso('Manejar usuarios')){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }
    
        $errores = 0;
        $mensaje = '';
    
        
        //Verificar si el usuario tiene permisos
        //$permiso = Users
    
       
        $usuario = Users::on("mysql")->whereId($datos['id'])->get()->first();
        
    
        if($usuario != null){
            $usuario['nombres'] = $datos['nombres'];
            $usuario['email'] = $datos['email'];
            $usuario['usuario'] = $datos['usuario'];
            $usuario['idRole'] = $datos['idTipoUsuario'];
            $usuario['status'] = $datos['status'];
    
            if(!empty($datos['password']) && !empty($datos['confirmar'])){
                if($datos['password'] == $datos['confirmar']){
                    $usuario['password'] = Crypt::encryptString($datos['password']);
                }
            }
    
            $id = Users::on($datos["mysql"])->whereEmail($datos['email'])->first();
            if($id != null){
                if($usuario->id != $id->id){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'El correo ya existe, elija uno diferente'
                    ], 201);
                }
            }
    
            $id = Users::on($datos["mysql"])->whereUsuario($datos['usuario'])->first();
           if($id != null){
            if($usuario->id != $id->id){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El usuario ya existe, elija uno diferente'
                ], 201);
            }
           }
    
    
            $usuario->save();

            $usuario = Users::on($datos["servidor"])->whereUsuario($datos["usuario"])->first();
            $usuario->nombres = $usuario->nombres;
            $usuario->email = $usuario->email;
            $usuario->usuario = $usuario->usuario;
            $usuario->idRole = $usuario->idRole;
            $usuario->password = $usuario->password;
            $usuario->save();

        }else{
    
            if(empty($datos['password']) && empty($datos['confirmar'])){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Contraseña no valida'
                ], 201);
            }
    
            if($datos['password'] != $datos['confirmar']){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Contraseña no valida'
                ], 201);
            }
            
            if(Users::on("mysql")->whereEmail($datos['email'])->first() != null){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El correo ya existe, elija uno diferente'
                ], 201);
            }
            if(Users::on("mysql")->whereUsuario($datos['usuario'])->first() != null){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El usuario ya existe, elija uno diferente'
                ], 201);
            }
    
            $usuario = Users::on($datos["servidor"])->create([
                'nombres' => $datos['nombres'],
                'email' => $datos['email'],
                'usuario' => $datos['usuario'],
                'password' => Crypt::encryptString($datos['password']),
                'idRole' => $datos['idTipoUsuario'],
                'status' => $datos['status']
            ]);
           

            $usuario = Users::on($datos["servidor"])->create([
                'nombres' => $datos['nombres'],
                'email' => $datos['email'],
                'usuario' => $datos['usuario'],
                'password' => Crypt::encryptString($datos['password']),
                'idRole' => $datos['idTipoUsuario'],
                'status' => $datos['status']
            ]);
           
        }
    
          /********************* PERMISOS ************************/
            //Eliminamos los PERMISOS para luego agregarlos nuevamentes
            $usuario->permisos()->detach();
            $permisos = collect($datos['permisos'])->map(function($d) use($usuario, $datos){
                return ['idPermiso' => $d['id'], 'idUsuario' => $usuario['id']];
            });
           
            $usuario->permisos()->attach($permisos);
    
    
           
            $usuario = Users::on($datos["servidor"])->whereId($usuario->id)->first();
            event(new UsersEvent(new UsersResource($usuario)));
    
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(Users $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Users $users)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(Users $users)
    {
        // $datos = request()->validate([
        //     'datos.id' => 'required',
        //     'datos.usuario' => 'required',
        //     'datos.nombres' => 'required',
        //     'datos.status' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["data"]))
            $datos = $datos["data"];

        if(isset($datos["data"]))
            $datos = $datos["data"];


        if(isset($datos["datosMovil"]))
           $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $usuario = Users::on($datos["servidor"])->whereId($datos['id'])->get()->first();
        if($usuario != null){
            $usuario->status = 2;
            $usuario->save();
            event(new UsersEvent($usuario));

            return Response::json([
                'data' => $usuario,
                'errores' => 0,
                'mensaje' => 'Se ha eliminado correctamente'
            ], 201);
        }

        return Response::json([
            'errores' => 1,
            'mensaje' => 'Error al eliminar usuario'
        ], 201);
    }
}
