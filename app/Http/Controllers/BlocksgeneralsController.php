<?php

namespace App\Http\Controllers;

use App\Blocksgenerals;
use Request;

use App\Http\Requests\BlocksGeneralsRequest;

use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Classes\Helper;

class BlocksgeneralsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return \Illuminate\Http\Response
     */
    public function show(Blocksgenerals $blocksgenerals)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return \Illuminate\Http\Response
     */
    public function edit(Blocksgenerals $blocksgenerals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blocksgenerals $blocksgenerals)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blocksgenerals $blocksgenerals)
    {
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }

        $bloqueo = Blocksgenerals::on($datos["servidor"])->whereId($datos["idBloqueo"])->get()->first();
        if($bloqueo != null){
            $bloqueo->delete();
            return Response::json([
                "mensaje" => "Se ha eliminado correctamente",
                "errores" => 0
            ], 201);
        }

        return Response::json([
            "mensaje" => "El bloqueo no existe",
            "errores" => 1
        ], 201);
    }

    public function eliminar(BlocksGeneralsRequest $request)
    {
        
        // $validated = $request->validated();
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }
        Blocksgenerals::on($datos["servidor"])->whereId($datos["idBloqueo"])->first()->delete();

    
        return Response::json([
            'mensaje' => "Se ha eliminado correctamente"
        ], 201);
    }
}
