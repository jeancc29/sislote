<?php

namespace App\Http\Controllers;

use App\Androidversions;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\TicketPrintClass;
use App\Users;

class AndroidversionsController extends Controller
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
            $u = Users::whereId(session("idUsuario"))->first();
            if($u->usuario != "jean"){
                return redirect()->route('principal');
            }

            

            return view('versiones.index', compact('controlador'));
        }

        // $datos = request()->validate([
        //     'datos.idUsuario' => ''
        // ])['datos'];


        //Hay 3 status
        //Intalacion == 2
        //Activo == 1
        //Elimnado == 0
        $versiones = Androidversions::where('status', '!=', 2)->get();


        return Response::json([
            'errores' => 0,
            'mensaje' => '',
            'versiones' => $versiones
        ], 201);
    }

    public function publicar(Request $request)
    {
        $datos = request()->validate([
            'datos.id' => 'required'
        ])['datos'];


        //Hay 3 status
        //Intalacion == 2
        //Activo == 1
        //Elimnado == 0
        $versiones = Androidversions::where('status', '!=', 2)->get();
        foreach($versiones as $v){
            $v->status = 1;
            $v->save();
        }

        $version = Androidversions::whereId($datos['id'])->first();
        if($version != null){
            $version->status = 3;
            $version->save();
        }


        $versiones = Androidversions::where('status', '!=', 2)->get();
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'version' => $version,
            'versiones' => $versiones
        ], 201);
    }

    public function publicada(Request $request)
    {
        $datos = request()->validate([
            'datos.idUsuario' => ''
        ])['datos'];


       

        $version = Androidversions::whereStatus(3)->first();
       


        $versiones = Androidversions::where('status', '!=', 2)->get();
        return Response::json([
            'errores' => 0,
            'version' => $version
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
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.id' => '',
            'datos.version' => 'required',
            'datos.enlace' => 'required',
            'datos.status' => 'required',
        ])['datos'];


        //Hay 3 status
        //Intalacion == 2
        //Activo == 1
        //Elimnado == 0
        $version = Androidversions::whereId($datos['id'])->first();
        if($version != null){
            $version->status = $datos['status'];
            $version->version = $datos['version'];
            $version->enlace = $datos['enlace'];
            $version->save();
        }else{
            $version = Androidversions::create([
                'version' => $datos['version'],
                'enlace' => $datos['enlace'],
                'status' => $datos['status']
            ]);

        }


        $versiones = Androidversions::where('status', '!=', 2)->get();
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'version' => $version,
            'versiones' => $versiones
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Androidversions  $androidversions
     * @return \Illuminate\Http\Response
     */
    public function show(Androidversions $androidversions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Androidversions  $androidversions
     * @return \Illuminate\Http\Response
     */
    public function edit(Androidversions $androidversions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Androidversions  $androidversions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Androidversions $androidversions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Androidversions  $androidversions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Androidversions $androidversions)
    {
        //
    }
}