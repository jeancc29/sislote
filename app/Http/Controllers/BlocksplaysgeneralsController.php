<?php

namespace App\Http\Controllers;

use App\Blocksplaysgenerals;
use Request;
use App\Http\Requests\BlocksplaysgeneralsRequest;
use Illuminate\Support\Facades\Response;
use App\Classes\Helper;


class BlocksplaysgeneralsController extends Controller
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
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return \Illuminate\Http\Response
     */
    public function show(Blocksplaysgenerals $blocksplaysgenerals)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return \Illuminate\Http\Response
     */
    public function edit(Blocksplaysgenerals $blocksplaysgenerals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blocksplaysgenerals $blocksplaysgenerals)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blocksplaysgenerals $blocksplaysgenerals)
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

        $bloqueo = Blocksplaysgenerals::on($datos["servidor"])->whereId($datos["idBloqueo"])->get()->first();
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

    public function eliminar(BlocksplaysgeneralsRequest $request)
    {
        
        $validated = $request->validated();
        Blocksplaysgenerals::on($validated["datos"]["servidor"])->whereId($validated["datos"]["idBloqueo"])->first()->delete();

    
        return Response::json([
            'mensaje' => "Se ha eliminado correctamente"
        ], 201);
    }
}
