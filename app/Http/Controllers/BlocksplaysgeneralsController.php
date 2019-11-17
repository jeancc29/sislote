<?php

namespace App\Http\Controllers;

use App\Blocksplaysgenerals;
use Request;
use App\Http\Requests\BlocksplaysgeneralsRequest;
use Illuminate\Support\Facades\Response;


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
        //
    }

    public function eliminar(BlocksplaysgeneralsRequest $request)
    {
        
        $validated = $request->validated();
        Blocksplaysgenerals::whereId($validated["datos"]["idBloqueo"])->delete();

    
        return Response::json([
            'mensaje' => "Se ha eliminado correctamente"
        ], 201);
    }
}
