<?php

namespace App\Http\Controllers;

use App\Loans;
use Illuminate\Support\Facades\DB;


use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\TicketPrintClass;
use App\transactions;


// use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksplays;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Awards;
use App\Draws;
use App\Branches;
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;
use App\Types;
use App\Entity;
use App\Frecuency;
use App\Amortization;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class LoansController extends Controller
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
            $u = Users::where(['id' => session("idUsuario"), 'status' => 1])->first();

            if($u == null){
                return redirect()->route('login');
            }
            if(!$u->tienePermiso("Manejar prestamos") == true){
                return redirect()->route('principal');
            }
        $idTipo = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first()->id;

            $bancas = Branches::whereStatus(1)->get();
            $bancos = Entity::where(['status' => 1, 'idTipo' => $idTipo])->get();
            $dias = Days::all();
            $frecuencias = Frecuency::orderBy('id', 'desc')->get();
            $tiposEntidades = Types::where(['renglon' => 'entidad'])->whereIn('descripcion', ['Banco', 'Banca'])->get();
            
            $prestamos = DB::table('loans')
                ->select('loans.id', 'loans.montoPrestado', 'loans.numeroCuotas', 'loans.montoCuotas', 'loans.tasaInteres', 'loans.created_at', 'loans.status', 'branches.descripcion AS banca', 'frecuencies.descripcion AS frecuencia')
                ->join('branches', 'branches.id', '=', 'loans.idEntidadPrestamo')
                ->join('frecuencies', 'loans.idFrecuencia', '=', 'frecuencies.id')
                ->where('loans.status', 1)
                ->get();
            return view('prestamos.index', compact('controlador', 'bancas', 'bancos', 'frecuencias', 'dias', 'prestamos', 'tiposEntidades'));
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
        $datos = request()->validate([
            'datos.id' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idEntidadPrestamo' => 'required',
            'datos.idTipoEntidadFondo' => 'required',
            'datos.idEntidadFondo' => 'required',
            'datos.montoPrestado' => 'required',
            'datos.montoCuotas' => '',
            'datos.numeroCuotas' => '',
            'datos.tasaInteres' => '',
            'datos.status' => 'required',
            'datos.detalles' => '',
            'datos.idFrecuencia' => 'required',
            'datos.fechaInicio' => 'required',
        ])['datos'];


        $idTipoBanca = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        $idTipoBanco = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();


        $prestamo = Loans::where(['id' => $datos['id']])->first();
        if($prestamo != null){
            $prestamo->montoPrestado = $datos['montoPrestado'];
            $prestamo->status = $datos['status'];
            $prestamo->numeroCuotas = $datos['numeroCuotas'];
            $prestamo->tasaInteres = $datos['tasaInteres'];
            $prestamo->detalles = $datos['detalles'];
            //Cuando se hace la transaccion para emitir los fondos se le restaran el monto pagado al monto del nuevo prestamo
        }else{
            

            // $amortizacion = Helper::amortizar($prestamo->montoPrestado, $prestamo->montoCuotas, $prestamo->numeroCuotas, $prestamo->tasaInteres, $prestamo->idFrecuencia, false);
            $amortizacion = Helper::amortizar($datos['montoPrestado'], $datos['montoCuotas'], $datos['numeroCuotas'], $datos['tasaInteres'], $datos['idFrecuencia'], false);
            $datos['montoCuotas'] = $amortizacion[0]['montoCuota'] + $amortizacion[0]['montoInteres'];
            $datos['numeroCuotas'] = count($amortizacion);
            $datos['tasaInteres'] = ($amortizacion[0]['tasaInteres'] == null) ? 0 : $amortizacion[0]['tasaInteres'];

            $fechaInicioCarbon = new Carbon($datos['fechaInicio']);
            $datos['fechaInicio'] = $fechaInicioCarbon->toDateString();
            $prestamo = Loans::create([
                'idUsuario' => $datos['idUsuario'],
                'idTipoEntidadPrestamo' => $idTipoBanca->id,
                'idTipoEntidadFondo' => $idTipoBanco->id,
                'idEntidadPrestamo' => $datos['idEntidadPrestamo'],
                'idEntidadFondo'=> $datos['idEntidadFondo'],
                'montoPrestado'=> $datos['montoPrestado'],
                'montoCuotas'=> $datos['montoCuotas'],
                'numeroCuotas'=> $datos['numeroCuotas'],
                'tasaInteres'=> $datos['tasaInteres'],
                'status'=> $datos['status'],
                'detalles' => $datos['detalles'],
                'idFrecuencia' => $datos['idFrecuencia'],
                'fechaInicio' => $datos['fechaInicio']
            ]);


            // $c[0]['a'];
            foreach($amortizacion as $a){
                Amortization::create([
                    'idPrestamo' => $prestamo->id,
                    'numeroCuota' => $a['numeroCuota'],
                    'montoCuota' => $a['montoCuota'],
                    'montoInteres' => $a['montoInteres'],
                    'amortizacion' => $a['amortizacion'],
                    'fecha' => $a['fecha'],
                ]);
            }

            $tipo = Types::where(['renglon' => 'transaccion', 'descripcion' => 'Desembolso de prestamo'])->first();
            $idTipoEntidadFondo = null;
            if($datos['idTipoEntidadFondo'] == $idTipoBanco->id){
                $idTipoEntidadFondo = $idTipoBanco->id;

                
                $saldo = (new Helper)->saldo($datos['idEntidadPrestamo'], 1);
                $saldoEntidad2 = (new Helper)->saldo($datos['idEntidadFondo'], 2);
                $t = transactions::create([
                    'idUsuario' => $datos['idUsuario'],
                    'idTipo' => $tipo->id,
                    'idTipoEntidad1' => $idTipoBanca->id,
                    'idTipoEntidad2' => $idTipoEntidadFondo,
                    'idEntidad1' => $prestamo->idEntidadPrestamo,
                    'idEntidad2' => $prestamo->idEntidadFondo,
                    'entidad1_saldo_inicial' => $saldo,
                    'entidad2_saldo_inicial' => $saldoEntidad2,
                    'debito' => $prestamo->montoPrestado,
                    'credito' => 0,
                    'idGasto' => null,
                    'entidad1_saldo_final' => 0,
                    'entidad2_saldo_final' => $saldoEntidad2 - $prestamo->montoPrestado,
                    'nota' => "Desembolso de prestamo"
                ]);
            }else{
                $idTipoEntidadFondo = $idTipoBanca->id;

                //Tambien debo ir al archivo helper y arreglar la funcio saldo y saldoPorFecha
                $saldo = (new Helper)->saldo($datos['idEntidadPrestamo'], 1);
                $saldoEntidad2 = (new Helper)->saldo($datos['idEntidadFondo'], 1);
                $t = transactions::create([
                    'idUsuario' => $datos['idUsuario'],
                    'idTipo' => $tipo->id,
                    'idTipoEntidad1' => $idTipoBanca->id,
                    'idTipoEntidad2' => $idTipoEntidadFondo,
                    'idEntidad1' => $prestamo->idEntidadPrestamo,
                    'idEntidad2' => $prestamo->idEntidadFondo,
                    'entidad1_saldo_inicial' => $saldo,
                    'entidad2_saldo_inicial' => $saldoEntidad2,
                    'debito' => $prestamo->montoPrestado,
                    'credito' => 0,
                    'idGasto' => null,
                    'entidad1_saldo_final' => 0,
                    'entidad2_saldo_final' => $saldoEntidad2 - $prestamo->montoPrestado,
                    'nota' => "Desembolso de prestamo"
                ]);
            }

            

        }
        
    }


    public function cobrar(Request $request)
    {
        $datos = request()->validate([
            'datos.idPrestamo' => 'required',
            'datos.idUsuario' => 'required',
            'datos.idTipoPago' => 'required', 
            'datos.montoPagado' => 'required', 
            'datos.cuotas' => 'required',
            'datos.idBanco'
        ])['datos'];


        $tipoPago = Types::whereId($datos['idTipoPago'])->first();
        $idTipoBanca = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
        $idTipoBanco = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();

        $prestamo = Loans::where(['id' => $datos['idPrestamo'], 'status' => 1])->first();
        if($prestamo == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'El prestamo no existe',
                //'colleccon' => $colleccion
            ], 201);
        }


        if($tipoPago->descripcion == "Pago cuota"){
            if(Helper::isNumber($datos['montoPagado'])){
                $datos['montoPagado'] = floatval($datos['montoPagado']);
                if($datos['montoPagado'] > 0){
                    //Ordenamos de menor a mayor
                    for($c = 0; $c < count($datos['cuotas']); $c++){
                        for($i = 0; $i < count($datos['cuotas']); $i++){
                            if($datos['cuotas'][$c]['id'] < $datos['cuotas'][$i]['id']){
                                if($c > $i){
                                    $idTemporal = $datos['cuotas'][$i]['id'];
                                    $datos['cuotas'][$i]['id'] = $datos['cuotas'][$c]['id'];
                                    $datos['cuotas'][$c]['id'] = $idTemporal;
                                }
                            }
                        }// end segundo for
                    }// end primer for

                    //Ahora vamos a crear las transacciones
                    for($c = 0; $c < count($datos['cuotas']); $c++){
                        $amortizacion = Amortization::whereId($datos['cuotas'][$c]['id'])->first();
                        if($amortizacion != null){
                            //Deducimos el interes pagado
                            if($datos['montoPagado'] > 0){
                                $interesPagado = 0;
                                $calculo = $amortizacion->montoInteres - $datos['montoPagado'];
                                if($calculo < 0){
                                    $datos['montoPagado'] = abs($interesPagado);
                                    $interesPagado = $amortizacion->montoInteres;
                                }else{
                                    $interesPagado = $amortizacion->montoInteres - $calculo;
                                    $datos['montoPagado'] = 0;
                                }
                                $amortizacion->montoPagadoInteres = $interesPagado;
                            }

                            //Deducimos el capital pagado
                            if($datos['montoPagado'] > 0){
                                $capitalPagado = 0;
                                $capital = ($amortizacion->montoInteres - $amortizacion->montoInteres);
                                $calculo = $capital - $datos['montoPagado'];

                                if($calculo < 0){
                                    $datos['montoPagado'] = abs($interesPagado);
                                    $capitalPagado = $capital;
                                }else{
                                    $capitalPagado = $capital - $calculo;
                                    $datos['montoPagado'] = 0;
                                }

                                $amortizacion->montoPagadoCapital = $capitalPagado;
                            }

                            $tipo = Types::where(['renglon' => 'transaccion', 'descripcion' => 'Cobro prestamo'])->first();

                            $saldo = (new Helper)->saldo($prestamo->idEntidadPrestamo, 1);
                            $saldoEntidad2 = (new Helper)->saldo($datos['idEntidadFondo'], 2);
                            $t = transactions::create([
                                'idUsuario' => $datos['idUsuario'],
                                'idTipo' => $tipo->id,
                                'idTipoEntidad1' => $prestamo->idEntidadPrestamo,
                                'idTipoEntidad2' => $idTipoBanco->id,
                                'idEntidad1' => $prestamo->idEntidadPrestamo,
                                'idEntidad2' => $datos['idBanco'],
                                'entidad1_saldo_inicial' => $saldo,
                                'entidad2_saldo_inicial' => $saldoEntidad2,
                                'debito' => $prestamo->montoPrestado,
                                'credito' => 0,
                                'idGasto' => null,
                                'entidad1_saldo_final' => $prestamo->montoPrestado + $saldo,
                                'entidad2_saldo_final' => round(($saldoEntidad2 - $prestamo->montoPrestado), 2),
                                'nota' => "Cobro manual de prestamo"
                            ]);

                        } //Endif amortization != null
                    }// end primer for
                }
            }
        }
        $prestamo = Loans::where(['id' => $datos['id']])->first();
        if($prestamo != null){
            $prestamo->montoPrestado = $datos['montoPrestado'];
            $prestamo->status = $datos['status'];
            $prestamo->numeroCuotas = $datos['numeroCuotas'];
            $prestamo->tasaInteres = $datos['tasaInteres'];
            $prestamo->detalles = $datos['detalles'];
            //Cuando se hace la transaccion para emitir los fondos se le restaran el monto pagado al monto del nuevo prestamo
        }else{
            

            // $amortizacion = Helper::amortizar($prestamo->montoPrestado, $prestamo->montoCuotas, $prestamo->numeroCuotas, $prestamo->tasaInteres, $prestamo->idFrecuencia, false);
            $amortizacion = Helper::amortizar($datos['montoPrestado'], $datos['montoCuotas'], $datos['numeroCuotas'], $datos['tasaInteres'], $datos['idFrecuencia'], false);
            $datos['montoCuotas'] = $amortizacion[0]['montoCuota'] + $amortizacion[0]['montoInteres'];
            $datos['numeroCuotas'] = count($amortizacion);
            $datos['tasaInteres'] = ($amortizacion[0]['tasaInteres'] == null) ? 0 : $amortizacion[0]['tasaInteres'];

            $fechaInicioCarbon = new Carbon($datos['fechaInicio']);
            $datos['fechaInicio'] = $fechaInicioCarbon->toDateString();
            $prestamo = Loans::create([
                'idUsuario' => $datos['idUsuario'],
                'idTipoEntidadPrestamo' => $idTipoBanca->id,
                'idTipoEntidadFondo' => $idTipoBanco->id,
                'idEntidadPrestamo' => $datos['idEntidadPrestamo'],
                'idEntidadFondo'=> $datos['idEntidadFondo'],
                'montoPrestado'=> $datos['montoPrestado'],
                'montoCuotas'=> $datos['montoCuotas'],
                'numeroCuotas'=> $datos['numeroCuotas'],
                'tasaInteres'=> $datos['tasaInteres'],
                'status'=> $datos['status'],
                'detalles' => $datos['detalles'],
                'idFrecuencia' => $datos['idFrecuencia'],
                'fechaInicio' => $datos['fechaInicio']
            ]);


            // $c[0]['a'];
            foreach($amortizacion as $a){
                Amortization::create([
                    'idPrestamo' => $prestamo->id,
                    'numeroCuota' => $a['numeroCuota'],
                    'montoCuota' => $a['montoCuota'],
                    'montoInteres' => $a['montoInteres'],
                    'amortizacion' => $a['amortizacion'],
                    'fecha' => $a['fecha'],
                ]);
            }

            $tipo = Types::where(['renglon' => 'transaccion', 'descripcion' => 'Desembolso de prestamo'])->first();

            $saldo = (new Helper)->saldo($datos['idEntidadPrestamo'], 1);
            $saldoEntidad2 = (new Helper)->saldo($datos['idEntidadFondo'], 2);
            $t = transactions::create([
                'idUsuario' => $datos['idUsuario'],
                'idTipo' => $tipo->id,
                'idTipoEntidad1' => $idTipoBanca->id,
                'idTipoEntidad2' => $idTipoBanco->id,
                'idEntidad1' => $prestamo->idEntidadPrestamo,
                'idEntidad2' => $prestamo->idEntidadFondo,
                'entidad1_saldo_inicial' => $saldo,
                'entidad2_saldo_inicial' => $saldoEntidad2,
                'debito' => $prestamo->montoPrestado,
                'credito' => 0,
                'idGasto' => null,
                'entidad1_saldo_final' => $prestamo->montoPrestado + $saldo,
                'entidad2_saldo_final' => round(($saldoEntidad2 - $prestamo->montoPrestado), 2),
                'nota' => "Desembolso de prestamo"
            ]);

        }
        
    }

    public function aplazarCuota(Loans $loans)
    {
        //Aplazar o perdonar cuota
        $datos = request()->validate([
            'idPrestamo' => '',
            'idUsuario' => 'required'
        ])['datos'];


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function show(Loans $loans)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function edit(Loans $loans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loans $loans)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loans  $loans
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loans $loans)
    {
        //
    }
}
