<?php

namespace App\Http\Controllers;

use App\Users;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Crypt; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;


use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksplays;
use App\Branches;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Frecuency;
use App\Automaticexpenses;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;
use App\Classes\Helper;


class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        // $route = Route();
        //echo $controlador;
        
        return view('login.index', compact('controlador'));
    }

    public function acceder(Request $request)
    {
        $data = request()->validate([
            'usuario' => 'required',
            'password' => 'required'
        ], [
            'usuario.required' => 'El campo usuario es obligatorio',
            'password.required' => 'El campo password es obligatorio'
        ]);

       // dd($data);

        

        $u = Users::where(['usuario' => $data['usuario']])->get()->first();
        $idBanca = Branches::where('idUsuario', $u->id)->first();
        if($idBanca != null){
            $idBanca = $idBanca->id;
        }
        
   

        if($u == null){
            return redirect('login')->withErrors([
                'usuario' => 'Usuario o contraseña incorrectos'
            ]);
        }

        if(Crypt::decryptString($u->password) != $data['password']){
            return redirect('login')->withErrors([
                'password' => 'Contraseña incorrecta'
            ]);
        }

       
        if(!$u->tienePermiso("Acceso al sistema") == true){
            return redirect('login')->withErrors([
                'acceso' => 'Usuario no tiene acceso al sistema'
            ]);
        }

        
        
        //Session::put('idUsuario', $u->id);

       session(['idUsuario' => $u->id]);
       session(['idBanca' => $idBanca]);
       session(['permisos' => $u->permisos]);

      
       $role = Roles::whereId($u->idRole)->first();
       if($role->descripcion == "Administrador" || $role->descripcion == "Supervisor")
            return redirect()->route('dashboard');
        else
            return redirect()->route('principal');
    }

    public function accederApi(Request $request)
    {
     
        $datos = request()->validate([
            'datos.usuario' => 'required',
            'datos.password' => 'required'
        ])['datos'];
       // dd($data);

        

        $u = Users::where(['usuario' => $datos['usuario']])->get()->first();

    
        
   

        if($u == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Usuario o contraseña incorrectos'
            ], 201);
            // return redirect('login')->withErrors([
            //     'usuario' => 'Usuario o contraseña incorrectos'
            // ]);
        }

        if(Crypt::decryptString($u->password) != $datos['password']){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Contraseña incorrecta'
            ], 201);
            // return redirect('login')->withErrors([
            //     'password' => 'Contraseña incorrecta'
            // ]);
        }

        $banca = Branches::where(['idUsuario' => $u->id, 'status' => 1])->first();
        if($banca == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Este usuario no tiene banca asignada'
            ], 201);
        }

        if(!$u->tienePermiso("Acceso al sistema")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Este usuario no tiene acceso al sistema'
            ], 201);
        }
        
        //Session::put('idUsuario', $u->id);

    //    session(['idUsuario' => $u->id]);
    //    session(['permisos' => $u->permisos]);

      
       return Response::json([
        'errores' => 0,
        'mensaje' => '',
        'idUsuario' => $u->id,
        'permisos' => $u->permisos,
        'banca' => $banca->descripcion,
        'idBanca' => $banca->id
    ], 201);
    }

    public function cerrarSesion(Request $request)
    {
     
        $datos = request()->validate([
            'cerrar' => 'required'
        ]);
       
        (new Helper)->cerrar_session();
        return redirect()->route('login');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(Users $users)
    {
        
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
        //
    }
}
