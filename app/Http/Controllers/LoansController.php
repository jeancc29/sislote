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
            return view('prestamos.index', compact('controlador', 'bancas', 'bancos', 'frecuencias', 'dias'));
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
            'idPrestamo' => '',
            'idUsuario' => 'required',
            'datos.idEntidadPrestamo' => 'required',
            'datos.idEntidadFondo' => 'required',
            'datos.montoPrestado' => 'required',
            'datos.montoCuotas' => 'required',
            'datos.numeroCuotas' => 'required',
            'datos.tasaInteres' => 'required',
            'datos.status' => 'required',
            'datos.detalles' => 'required',
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

            $amortizacion = Helper::amortizar($prestamo->montoPrestado, $prestamo->montoCuotas, $prestamo->numeroCuotas, $prestamo->tasaInteres, $prestamo->idFrecuencia, false);
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

            $t = transactions::create([
                'idUsuario' => $datos['idUsuario'],
                'idTipo' => $tipo->id,
                'idTipoEntidad1' => $idTipoEntidad1->id,
                'idTipoEntidad2' => $idTipoEntidad2->id,
                'idEntidad1' => $b['id'],
                'idEntidad2' => $entidad->id,
                'entidad1_saldo_inicial' => $saldo,
                'entidad2_saldo_inicial' => 0,
                'debito' => $debito,
                'credito' => $credito,
                'idGasto' => null,
                'entidad1_saldo_final' => $saldoFinalEntidad1,
                'entidad2_saldo_final' => 0,
                'nota' => "Proceso diario automático de ventas"
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
