<?php

namespace App\Http\Controllers;

use App\Group;
use Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route; 


class GroupController extends Controller
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
            if(!\App\Classes\Helper::existe_sesion()){
                return redirect()->route('login');
            }

            $u = \App\Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Manejar loterias") == true){
                return redirect()->route('sinpermiso');
            }

            return view('grupos.index', compact('controlador'));
        }
    }

    public function get()
    {
        $datos = request()->validate([
            // 'fecha' => 'required',
            // 'idUsuario' => 'required',
            // 'idMoneda' => 'required',
            // 'servidor' => 'required',
            'token' => ''
        ]);

        try {
            // $datos = JWT::decode($datos['token'], \config('data.apiKey'), array('HS256'));
            // $datos = json_decode(json_encode($datos), true);
            $datos = \Helper::jwtDecode($datos["token"]);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }

        return Response::json([
            "grupos" => Group::on($datos["servidor"])->get()
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
        $datos = request()['datos'];
        try {
            // $datos = JWT::decode($datos['token'], \config('data.apiKey'), array('HS256'));
            // $datos = json_decode(json_encode($datos), true);
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                "datos" => $datos
            ], 201);
        }

        $grupo = Group::on($datos["servidor"])->whereDescripcion($datos["grupo"]["descripcion"])->first();
        if($grupo != null){
            if($grupo->id != $datos["id"])
                return Response::json([
                    "mensaje" => "El grupo ya existe, debe crear un grupo diferente"
                ], 402);
        }

        // return Response::json([
        //     "mensaje" => "El grupo ya existe, debe crear un grupo diferente",
        //     "data" => $datos
        // ], 402);

        $grupo = Group::on($datos["servidor"])->updateOrCreate(
            [
                "id" => $datos["grupo"]["id"]
            ],
            [
                "descripcion" => $datos["grupo"]["descripcion"],
                "codigo" => $datos["grupo"]["codigo"],
                "status" => $datos["grupo"]["status"],
            ]
        );

        return Response::json([
            "grupo" => $grupo,
            "mensaje" => "Se ha guardado correctamente"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        try {
            // $datos = JWT::decode($datos['token'], \config('data.apiKey'), array('HS256'));
            // $datos = json_decode(json_encode($datos), true);
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                "datos" => $datos
            ], 201);
        }

        $grupo = Group::on($datos["servidor"])->whereId($datos["grupo"]["id"])->first();
        if($grupo != null){
            $grupo->delete();
            return Response::json([
                "mensaje" => "Se ha eliminado correctamente"
            ], 201);
        }

        return Response::json([
            "mensaje" => "El grupo no existe",
            "errores" => 1
        ], 404);
    }
}
