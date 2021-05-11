<?php

namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use App\Classes\Helper;
use Illuminate\Support\Facades\Response; 
use App\Events\SettingsEvent;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
            ], 404);
        }

        // $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        // if(!$usuario->tienePermiso("Ver configuracion") == true){
        //     return Response::json([
        //         'errores' => 1,
        //         'mensaje' => 'No tiene permisos para realizar esta accion'
        //     ], 201);
        // }
        
        // $data = \App\Settings::on($datos["servidor"])->first();


        return Response::json([
            "data" => \App\Settings::customFirst($datos["servidor"]),
            "tipos" => \App\Types::on($datos["servidor"])->whereRenglon("ticket")->get()
        ], 200);
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
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["data"]))
                $datos = $datos["data"];

            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            abort(404, "Token incorrecto");
        }

        $usuario = \App\Users::on($datos["servidor"])->whereId($datos['usuario']["id"])->first();
        if(!$usuario->tienePermiso("Ver ajustes") == true){
            abort(404, "No tiene permisos para realizar esta accion");
            // return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'No tiene permisos para realizar esta accion'
            // ], 404);
        }
        
        try {
            \DB::connection($datos["servidor"])->beginTransaction();
            $setting = \App\Settings::on($datos["servidor"])->first();
            if($setting != null)
                $datos["ajustes"]["id"] = $setting->id;

            $data = \App\Settings::on($datos["servidor"])->updateOrCreate(
                ["id" => $datos["ajustes"]["id"]],
                [
                    "consorcio" => $datos["ajustes"]["consorcio"],
                    "imprimirNombreConsorcio" => $datos["ajustes"]["imprimirNombreConsorcio"],
                    "cancelarTicketWhatsapp" => $datos["ajustes"]["cancelarTicketWhatsapp"],
                    "imprimirNombreBanca" => $datos["ajustes"]["imprimirNombreBanca"],
                    "pagarTicketEnCualquierBanca" => isset($datos["ajustes"]["pagarTicketEnCualquierBanca"]) ? $datos["ajustes"]["pagarTicketEnCualquierBanca"] : 0,
                    "idTipoFormatoTicket" => ($datos["ajustes"]["tipoFormatoTicket"] != null) ? $datos["ajustes"]["tipoFormatoTicket"]["id"] : null
                ]
            );
            \DB::connection($datos["servidor"])->commit();
            event(new SettingsEvent($data));
    
            return Response::json([
                "data" => \App\Settings::customFirst($datos["servidor"])
            ], 200);
        } catch (\Throwable $th) {
            \DB::connection($datos["servidor"])->rollback();
            abort(404, $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $settings)
    {
        //
    }
}
