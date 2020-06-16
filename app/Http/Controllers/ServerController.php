<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
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

    public function servidorExiste(Request $request)
    {
     
        $datos = request()->validate([
            'token' => 'required'
        ]);

        try {
            $datos = \Helper::jwtDecode($datos["token"]);
            $servidor = \App\Server::on("mysql")->whereDescripcion($datos["data"]["servidor"])->first();
            if($servidor == null){
                (new Helper)->cerrar_session();
                return redirect()->route('login');
            }
            

            
        } catch (\Throwable $th) {
            //throw $th;
            // return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'Token incorrecto',
            //     'token' => $datos
            // ], 201);
            abort(403, "Token incorrecto" . $th);
        }
       
        
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
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function edit(Server $server)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Server $server)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        //
    }
}
