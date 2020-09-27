<?php

namespace App\Http\Controllers;

use App\Blocksdirtygenerals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use App\Classes\Helper;

class BlocksdirtygeneralsController extends Controller
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
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto'
            ], 201);
        }


        return Response::json([
            "message" => "Se ha guardado correctamente",
            "data" => $datos
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blocksdirtygenerals  $blocksdirtygenerals
     * @return \Illuminate\Http\Response
     */
    public function show(Blocksdirtygenerals $blocksdirtygenerals)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Blocksdirtygenerals  $blocksdirtygenerals
     * @return \Illuminate\Http\Response
     */
    public function edit(Blocksdirtygenerals $blocksdirtygenerals)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Blocksdirtygenerals  $blocksdirtygenerals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blocksdirtygenerals $blocksdirtygenerals)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blocksdirtygenerals  $blocksdirtygenerals
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blocksdirtygenerals $blocksdirtygenerals)
    {
        //
    }
}
