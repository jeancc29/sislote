<?php

namespace App\Http\Controllers;

use App\Blocksdirtygenerals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

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

        $loterias_seleccionadas = collect($datos['loterias']);

        $sorteos = collect($datos['sorteos']);
        list($sorteos_seleccionadas, $no) = $sorteos->partition(function($l){
            if(isset($l['cantidad'])){
                //Cuando la cantidad no se pueda convertir a entero con este 
                //metodo (int)$l["cantidad"]; pues este retornara un cero
                $cantidadConvertidaAEntero = (int)$l["cantidad"];
                return $cantidadConvertidaAEntero > 0;
            }else
                return false;
        });

        $fecha = getdate();
        $fechaActualCarbon = Carbon::now();
    
        foreach($loterias_seleccionadas as $l):
            foreach($sorteos_seleccionadas as $s):
                if(\App\Lotteries::on($datos["servidor"])->whereId($l['id'])->first()->sorteos()->wherePivot('idSorteo', $s['id'])->first() == null)
                    continue;
                
                $bloqueo = Blocksdirtygenerals::on($datos["servidor"])->updateOrCreate(
                    [
                        'idLoteria' => $l['id'], 
                        'idSorteo' => $s['id'],
                        'idMoneda' => $datos['idMoneda']
                    ],
                    [
                        'idLoteria' => $l['id'], 
                        'idSorteo' => $s['id'],
                        'idMoneda' => $datos['idMoneda'],
                        'cantidad' => $s['cantidad'],
                    ],
                );

                event(new \App\Events\BlocksdirtygeneralsEvent($bloqueo));

            endforeach;//End foreach sorteos
        endforeach; //End foreahc loterias
    
        // $loterias = App\Lotteries::on($datos["servidor"])->select('id', 'descripcion', 'abreviatura')->whereStatus(1)->get();
        // $bancas = App\Branches::on($datos["servidor"])->select('id', 'descripcion')->whereStatus(1)->get();


        return Response::json([
            "message" => "Se ha guardado correctamente",
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
