<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;


use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response; 
use Carbon\Carbon;
use App\Classes\Helper;
use App\Classes\TicketPrintClass; 
use App\Classes\TicketToHtmlClass; 


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
use App\Realtime;
use App\Events\RealtimeStockEvent;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

class PrincipalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $controlador = Route::getCurrentRoute()->getName();
        // $usuario = Users::on(session("servidor"))->whereId(session('idUsuario'))->first();
        if(!strpos(Request::url(), '/api/')){
            if(!Helper::existe_sesion()){
                return redirect()->route('login');
            }
            $u = Users::on(session("servidor"))->whereId(session("idUsuario"))->first();
            if(!$u->tienePermiso("Vender tickets") == true){
                return redirect()->route('dashboard');
            }
            $usuario = Users::on(session("servidor"))->whereId(session('idUsuario'))->first();
            return view('principal.index', compact('controlador', 'usuario'));
        }

        $idBanca = 0;
        

        $datos = request()->validate([
            'datos.idUsuario' => ''
        ])['datos'];

        if(isset($datos['idUsuario'])){
            $idBanca = Branches::where(['id' => $datos['idUsuario'], 'status' => 1])->first();
            if($idBanca != null)
                $idBanca = $idBanca->id;
        }
       
        $usuario = Users::where(['id' => $datos['idUsuario'], 'status' => 1])->first();
        $fecha = getdate();
   
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
    
    
    
        return Response::json([
            'loterias' =>($usuario != null) ? Helper::loteriasOrdenadasPorHoraCierre($usuario) : [],
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas),
            'bancas' => Branches::whereStatus(1)->get()
        ], 201);


        
        
    }

    public function indexPostAntiguo()
    {
        $idBanca = 0;
        

        $datos = request()->validate([
            'datos.idUsuario' => 'required'
        ])['datos'];

        if(isset($datos['idUsuario'])){
            $idBanca = Helper::getIdBanca($datos['idUsuario']);
            // $idBanca = Branches::where(['idUsuario' => $datos['idUsuario'], 'status' => 1])->first();
            // if($idBanca != null)
            //     $idBanca = $idBanca->id;
        }
        

        if($idBanca == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No hay bancas registradas'
            ], 201);
        }
       
        $fecha = getdate();
   
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
    
    
        $usuario = Users::where(['id' => $datos['idUsuario'], 'status' => 1])->first();
    
        return Response::json([
            'idVenta' => Helper::createIdVentaTemporal($idBanca),
            'loterias' => ($usuario != null) ? Helper::loteriasOrdenadasPorHoraCierre($usuario) : [],
            'caracteristicasGenerales' =>  Generals::all(),
            'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas),
            'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            'idUsuario' => $datos['idUsuario'],
            'idBanca' => $idBanca
        ], 201);









        // $idBanca = 0;
        

        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required'
        // ])['datos'];

        // $data = Helper::indexPost();
        

        //  return Response::json([
        //     'idVenta' => $data[0]->idVentaHash,
        //     'loterias' => ($data[0]->loterias != null) ? json_decode($data[0]->loterias) : [],
        //     'caracteristicasGenerales' =>  ($data[0]->caracteristicasGenerales != null) ? json_decode($data[0]->caracteristicasGenerales) : [],
        //     'total_ventas' => $data[0]->total_ventas,
        //     'total_jugadas' => $data[0]->total_jugadas,
        //     'ventas' => ($data[0]->ventas != null) ? json_decode($data[0]->ventas) : [],
        //     'bancas' => ($data[0]->bancas != null) ? json_decode($data[0]->bancas) : [],
        //     'idUsuario' => $datos['idUsuario'],
        //     'idBanca' => $data[0]->idBanca
        // ], 201);
    }

    public function indexPost()
    {
        $idBanca = 0;
        

        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => ''
        // ])['datos'];
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
            //  return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'Token incorrecto',
            //     'token' => $datos
            // ], 201);
            
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        $idBanca = 0;
        if(isset($datos['idBanca'])){
            $idBanca = $datos['idBanca'];
        }

        // return Response::json([
        //     'errores' => 0,
        //     'mensaje' => 'Token incorrecto',
        //     'token' => $datos["idUsuario"],
        //     'idBanca' => $idBanca,
        // ], 201);

        $data = Helper::indexPost($datos["servidor"], $datos['idUsuario'], $idBanca);
        

         return Response::json([
            'idVenta' => $data[0]->idVentaHash,
            'loterias' => ($data[0]->loterias != null) ? json_decode($data[0]->loterias) : [],
            'caracteristicasGenerales' =>  ($data[0]->caracteristicasGenerales != null) ? json_decode($data[0]->caracteristicasGenerales) : [],
            'total_ventas' => $data[0]->total_ventas,
            'total_jugadas' => $data[0]->total_jugadas,
            'ventas' => ($data[0]->ventas != null) ? json_decode($data[0]->ventas) : [],
            'bancas' => ($data[0]->bancas != null) ? json_decode($data[0]->bancas) : [],
            'idUsuario' => $datos['idUsuario'],
            'idBanca' => $data[0]->idBanca,
            'culo' => $idBanca,
            'loteriasTodas' => Lotteries::on($datos["servidor"])->select("id", "descripcion")->whereStatus(1)->get(),
            "ajustes" => \App\Settings::customFirst($datos["servidor"])
        ], 201);
    }

    public function ticket()
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        
        return view('principal.ticket', compact('controlador'));
    }

    public function pruebahttp(Request $request)
    {
        $controlador = Route::getCurrentRoute()->getName(); 
        if(!strpos(Request::url(), '/api/')){
            // return view('principal.pruebahttp', compact('controlador'));
            return view('principal.p', compact('controlador'));
        }

        
       
        
        $saldo = (new Helper)->_sendSms("+18294266800", "Hola jean como estas");

        $fechaActual = getdate();

        $cadena = "100121";
        $buscar = "10";

        $bancas = Branches::whereIn('status', array(0, 1))->get();

        $coleccion = collect([
            ["nombre" => "jean", "edad" => 24, "status" => true],
            ["nombre" => "pedro", "edad" => 24, "status" => true],
            ["nombre" => "juana", "edad" => 24, "status" => false],
        ]);

        list($loterias_seleccionadas, $no) = $coleccion->partition(function($l){
            return $l['status'] == true;
        });
        

  

       
        $fechaCarbon = Carbon::now();
        /***  ADD PARA AGREGAR Y SUB PARA QUITAR  */
        // $fechaCarbon->addSecond()
        // $fechaCarbon->addSeconds(10)
        // $fechaCarbon->addMinute()
        // $fechaCarbon->addMinutes(5)
        // $fechaCarbon->addHour()
        // $fechaCarbon->addHours(5)
        // $fechaCarbon->addDays(5)
        // $fechaCarbon->addWeek()
        // $fechaCarbon->addWeeks(5)
        // $fechaCarbon->addYear()
        // $fechaCarbon->addYears(3)
        // $fechaCarbon->subYears(3)
        $fechaCarbon2 = Carbon::now()->addDay();
        $a = new Carbon('2019-01-30 5:23');

        return Response::json([
            'ventas' => SalesResource::collection(Sales::whereId(2)->get()),
            'coleccion' => $loterias_seleccionadas,
            'bancas' => BranchesResource::collection($bancas),
            'busqueda' => strpos($cadena, $buscar),
            'fechaActual' => $fechaActual,
            // 'timezone' => $timezone,
            'a' => $fechaCarbon,
            'a1' => $fechaCarbon2 ,
            'a2' => $a->addMonthNoOverflow(),
            'a3' => $a->copy()->addMonthNoOverflow(),
            'a4' => $saldo
        ], 201);
    }

    public function duplicar()
    {
        // $codigoBarra = request()->validate([
        //     'datos.codigoBarra' => '',
        //     'datos.codigoQr' => ''
        // ])['datos'];

        $codigoBarra = request()['datos'];
        try {
            $codigoBarra = \Helper::jwtDecode($codigoBarra);
            if(isset($codigoBarra["datosMovil"]))
               $codigoBarra = $codigoBarra["datosMovil"];

            //    return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'Token incorrecto',
            //     'datos' =>  $codigoBarra,
            // ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }

        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;

        $esCodigoBarra = true;
        if(isset($codigoBarra['codigoBarra']) && !isset($codigoBarra['codigoQr'])){
            if(!(new Helper)->isNumber($codigoBarra['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Numero de ticket incorrecto"], 201);
            }
            // if(strlen($codigoBarra['codigoBarra']) != 9){
            //     return Response::json(['errores' => 1, 'mensaje' => "Numero de ticket incorrecto"], 201);
            // }

            //Quitamos todos los ceros de la izquierda
            $idTicket = ltrim($codigoBarra['codigoBarra'], "0");
            $codigoBarra['codigoBarra'] = $idTicket;
            $esCodigoBarra = false;
        }
        else if(isset($codigoBarra['codigoQr'])){
            $codigoBarra['codigoBarra'] = base64_decode($codigoBarra['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        
    
        if($esCodigoBarra){
            $idTicket = Tickets::on($codigoBarra["servidor"])->where('codigoBarra', $codigoBarra['codigoBarra'])->value('id');
            if((strlen($codigoBarra['codigoBarra']) != 10 && strlen($codigoBarra['codigoBarra']) != 14)|| is_numeric($codigoBarra['codigoBarra']) != true){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => "El numero de ticket no es correcto"
                ], 201);
            }
        }else{
            $idTicket = $codigoBarra['codigoBarra'];
        }
        
        $idVenta = Sales::on($codigoBarra["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [5])->value('id');
        
        // if(strlen($codigoBarra['codigoBarra']) == 10 && is_numeric($codigoBarra['codigoBarra']) == true){
            if($idVenta != null){
                $idLoterias = Salesdetails::on($codigoBarra["servidor"])->distinct()->select('idLoteria')->where('idVenta', $idVenta)->get();
                $idLoterias = collect($idLoterias)->map(function($id){
                    return $id->idLoteria;
                });
                
    
                $loterias = Lotteries::on($codigoBarra["servidor"])->whereIn('id', $idLoterias)->whereStatus(1)->get();
                $loterias = collect($loterias)->map(function($e) use($idVenta, $codigoBarra){
                    $idLoteriaSuperpale = Salesdetails::on($codigoBarra["servidor"])->select("idLoteriaSuperpale")->where(['idVenta' => $idVenta, 'idLoteria' => $e["id"]])->whereNotNull("idLoteriaSuperpale")->get();
                    $loteriaSuperpale = Lotteries::on($codigoBarra["servidor"])->whereIn("id", $idLoteriaSuperpale)->get();
                    return ["id" => $e["id"], "descripcion" => $e["descripcion"], "abreviatura" => $e["abreviatura"], "loteriaSuperpale" => $loteriaSuperpale];
                });
                // $jugadas = Salesdetails::where('idVenta', $idVenta)->get();
                
                $jugadas = collect(Salesdetails::on($codigoBarra["servidor"])->where('idVenta', $idVenta)->get())->map(function($d) use($codigoBarra){
                    $sorteo = Draws::on($codigoBarra["servidor"])->whereId($d['idSorteo'])->first()->descripcion;
                    $loteriaSuperpale = Lotteries::on($codigoBarra["servidor"])->whereId($d["idLoteriaSuperpale"])->first();
                    return ['id' => $d['id'], 'idVenta' => $d['idVenta'], 'jugada' => $d['jugada'], 'idLoteria' => $d['idLoteria'], 'idLoteriaSuperpale' => $d['idLoteriaSuperpale'], 'loteriaSuperpale' => $loteriaSuperpale, 'idSorteo' => $d['idSorteo'], 'monto' => $d['monto'], 'premio' => $d['premio'], 'status' => $d['status'], 'sorteo' => $sorteo];
                });
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe";
            }
        // }else{
        //         $errores = 1;
        //         $mensaje = "El numero de ticket no es correcto";
        // }
        //$fecha = getdate(strtotime($fecha['fecha']));
    
        
    
        // $jugadas = collect($jugadas)->map(function($d){
        //     $loteria = Lotteries::whereId($d['idLoteria'])->first();
        //     return ['id' => $d['id'], 'idVenta' => $d['idVenta'], 
        //             'idLoteria' => $d['idLoteria'], 'idSorteo' => $d['idSorteo'], 
        //             'jugada' => $d['jugada'], 'monto' => $d['monto'],
        //             'premio' => $d['premio'],
        //             'status' => $d['status'],
        //             ]
        // });
      
        
    
        return Response::json([
            'loterias' => $loterias,
            'jugadas' => $jugadas,
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }

    public function pagar()
    {
        // $datos = request()->validate([
        //     'datos.codigoBarra' => '',
        //     'datos.codigoQr' => '',
        //     'datos.idUsuario' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
               $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }
    
        $usuario = Users::on($datos['servidor'])->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Marcar ticket como pagado")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }



        if(isset($datos['codigoBarra'])){
            if(!(new Helper)->isNumber($datos['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
            if(strlen($datos['codigoBarra']) != 10 && strlen($datos['codigoBarra']) != 14){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
        }
        else if(isset($datos['codigoQr']) && !isset($datos['codigoBarra'])){
            $datos['codigoBarra'] = base64_decode($datos['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = 'Se ha pagado correctamente';
        $loterias = null;
        $jugadas = null;
        $venta = null;
        
    
    
        if((strlen($datos['codigoBarra']) == 10 || strlen($datos['codigoBarra']) == 14) && is_numeric($datos['codigoBarra'])){
            $idTicket = Tickets::on($datos['servidor'])->where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::on($datos['servidor'])->where('idTicket', $idTicket)->whereIn('status', [1,2])->wherePagado(0)->wherePagado(0)->get()->first();
    
            if($venta != null){
                // $venta['pagado'] = 1;
                // $venta->save();
                if(\App\Settings::puedePagarTicketEnCualquierBanca($datos["servidor"]) == false){
                    $usuario = \App\Users::on($datos["servidor"])->whereId($datos["idUsuario"])->first();
                    if($usuario->esBancaAsignada($venta->idBanca) == false){

                        if($usuario->esAdministradorOProgramador() == false){
                            $errores = 1;
                            $mensaje = "El ticket no pertenece a esta banca";

                            return Response::json([
                                'errores' => $errores,
                                'mensaje' => $mensaje,
                                'venta' => null,
                            ], 201);
                        }
                    }
                }
    
                if(Helper::pagar($datos['servidor'], $venta->id, $datos['idUsuario'])){
                    $venta = Sales::on($datos['servidor'])->whereId($venta->id)->first();
                    $mensaje = "El ticket se ha pagado correctamente";
                }
                else{
                    $errores = 1;
                    $mensaje = "El ticket no existe, no esta premiado o ya ha sido pagado";
                }
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe, no esta premiado o ya ha sido pagado";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje,
            'venta' => ($venta != null) ? (new SalesResource($venta))->servidor($datos['servidor']) : null,
        ], 201);
    }


    public function buscarTicket()
    {
        // $datos = request()->validate([
        //     'datos.codigoBarra' => '',
        //     'datos.codigoQr' => '',
        //     'datos.idUsuario' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
               $datos = $datos["datosMovil"];

            //    return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'Token incorrecto',
            //     'datos' =>  $codigoBarra,
            // ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        

        if(isset($datos['codigoBarra'])){
            if(!(new Helper)->isNumber($datos['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
            // if(strlen($datos['codigoBarra']) != 10 && strlen($datos['codigoBarra']) != 14){
            //     return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            // }
        }
        else if(isset($datos['codigoQr']) && !isset($datos['codigoBarra'])){
            $datos['codigoBarra'] = base64_decode($datos['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
    
        // if((strlen($datos['codigoBarra']) == 10 || strlen($datos['codigoBarra']) == 14) && is_numeric($datos['codigoBarra'])){
        if(is_numeric($datos['codigoBarra'])){
            $idTicket = Tickets::on($datos["servidor"])->where('codigoBarra', $datos['codigoBarra'])->value('id');
            //->wherePagado(0)
            // $venta = Sales::where('idTicket', $idTicket)->whereStatus(2)->get()->first();
            $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [5])->get()->first();
            
            if($venta != null){
                
                    return Response::json([
                        'errores' => 0,
                        'mensaje' => '',
                        'venta' =>  (new SalesResource($venta))->servidor($datos["servidor"])
                    ], 201);
    
            }else{
                
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El ticket no existe'
                ], 201);
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }

    public function buscarTicketAPagar()
    {
        // $datos = request()->validate([
        //     'datos.codigoBarra' => '',
        //     'datos.codigoQr' => '',
        //     'datos.idUsuario' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
               $datos = $datos["datosMovil"];

            //    return Response::json([
            //     'errores' => 1,
            //     'mensaje' => 'Token incorrecto',
            //     'datos' =>  $codigoBarra,
            // ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Marcar ticket como pagado")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }

        if(isset($datos['codigoBarra'])){
            if(!(new Helper)->isNumber($datos['codigoBarra'])){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
            if(strlen($datos['codigoBarra']) != 10 && strlen($datos['codigoBarra']) != 14){
                return Response::json(['errores' => 1, 'mensaje' => "Codigo de barra incorrecto"], 201);
            }
        }
        else if(isset($datos['codigoQr']) && !isset($datos['codigoBarra'])){
            $datos['codigoBarra'] = base64_decode($datos['codigoQr']);
        }else{
            return Response::json(['errores' => 1, 'mensaje' => "Codigos no existen"], 201);
        }
    
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
    
        if((strlen($datos['codigoBarra']) == 10 || strlen($datos['codigoBarra']) == 14) && is_numeric($datos['codigoBarra'])){
            $idTicket = Tickets::on($datos["servidor"])->where('codigoBarra', $datos['codigoBarra'])->value('id');
            //->wherePagado(0)
            // $venta = Sales::where('idTicket', $idTicket)->whereStatus(2)->get()->first();
            // $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereIn('status', [1,2])->wherePagado(0)->wherePagado(0)->get()->first();
            // $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [0,5])->get()->first();
            $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [0,5])->get()->first();
            if($venta != null){
                
                // if(Helper::verificarTicketHaSidoPagado($datos["servidor"], $venta->id)){
                //     return Response::json([
                //         'errores' => 1,
                //         'mensaje' => 'El ticket ya ha sido pagado'
                //     ], 201);
                // }else{
                //     return Response::json([
                //         'errores' => 0,
                //         'mensaje' => '',
                //         'venta' =>  (new SalesResource($venta))->servidor($datos["servidor"])
                //     ], 201);
                // }

                return Response::json([
                    'errores' => 0,
                    'mensaje' => '',
                    'venta' =>  (new SalesResource($venta))->servidor($datos["servidor"])
                ], 201);
    
            }else{
                
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El ticket no existe, no esta premiado'
                ], 201);
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }


    public function cancelar()
    {
        // $datos = request()->validate([
        //     'datos.codigoBarra' => 'required',
        //     'datos.razon' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        // if(!$usuario->tienePermiso("Eliminar ticket")){
        //     return Response::json([
        //         'errores' => 1,
        //         'mensaje' => 'No tiene permisos para realizar esta accion'
        //     ], 201);
        // }
    
       //return $datos;
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
        
        // if((strlen($datos['codigoBarra']) == 10 || strlen($datos['codigoBarra']) == 14) && is_numeric($datos['codigoBarra'])){
        if(is_numeric($datos['codigoBarra'])){
            //Obtenemos el ticket
            $idTicket = Tickets::on($datos["servidor"])->where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [0, 5])->get()->first();
    
            
            
            if($venta != null){
                if(($usuario->roles->descripcion != "Administrador" && $usuario->roles->descripcion != "Programador") && $venta->compartido == 1){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => "Los tickets compartidos solo pueden cancelarse por el administrador"
                    ], 201);
                }
                $banca = Branches::on($datos["servidor"])->whereId($datos['idBanca'])->first();
                $minutoTicketJugado =  getdate(strtotime($venta['created_at']));
                $minutoActual = $fecha['minutes'];
    
                
                if(!$usuario->tienePermiso("Cancelar tickets en cualquier momento")){
                    if($minutoTicketJugado['year'] != $fecha['year']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar",
                            'ticket' => $minutoTicketJugado
                        ], 201);
                    }
                    if($minutoTicketJugado['mon'] != $fecha['mon']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                    if($minutoTicketJugado['mday'] != $fecha['mday']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                    if($minutoTicketJugado['hours'] != $fecha['hours']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                }
               // return ($minutoActual - $minutoTicketJugado) . " - " .$banca[' minutosCancelarTicket'];
    
                if(($minutoActual - $minutoTicketJugado['minutes']) < $banca['minutosCancelarTicket'] || $usuario->tienePermiso("Cancelar tickets en cualquier momento")){
                    $venta['pagado'] = 0;
                    $venta['status'] = 0;
                    $venta->save();

                    $ventasDetalles = Salesdetails::on($datos["servidor"])->where('idVenta', $venta['id'])->get();
                    foreach($ventasDetalles as $v){
                        $v['premio'] = 0;
                        $v['status'] = 0;
                        $stock = Stock::on($datos["servidor"])->whereId($v["idStock"])->first();
                        if($stock != null){
                            $stock->monto = $stock->monto + $v["monto"];
                            $stock->save();
                        }
                        $v->save();

                    }
    
                    Cancellations::on($datos["servidor"])->create([
                        'idTicket' => $venta['idTicket'],
                        'idUsuario' => $datos['idUsuario'],
                        'razon' => $datos['razon']
                    ]);
                    event(new \App\Events\BranchesEvent(\App\Branches::on($datos["servidor"])->whereId($venta["idBanca"])->first()));

    
                    $mensaje = "El ticket se ha cancelado correctamente";
                }else{
                    $errores = 1;
                    $mensaje = "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar";
                    //$mensaje = "minutos actual: $minutoActual, minutosticket: $minutoTicketJugado";
                }
                
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe o ya ha sido cancelado";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }

        $fecha = getdate();
        $ventas = Sales::on($datos["servidor"])->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereNotIn('status', [0,5])->get();
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });
    
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje,

            'loterias' => Lotteries::on($datos["servidor"])->whereStatus(1)->get(),
            'caracteristicasGenerales' =>  Generals::on($datos["servidor"])->get(),
            'total_ventas' => Sales::on($datos["servidor"])->whereIn('id', $idVentas)->sum('total'),
            'total_jugadas' => Salesdetails::on($datos["servidor"])->whereIn('idVenta', $idVentas)->count('jugada'),
            'ventas' => SalesResource::collection($ventas)->servidor($datos["servidor"]),
            'bancas' => Branches::on($datos["servidor"])->whereStatus(1)->get()
        ], 201);
    }


    public function cancelarMovil()
    {
        // $datos = request()->validate([
        //     'datos.codigoBarra' => 'required',
        //     'datos.razon' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
       try {
           $datos = \Helper::jwtDecode($datos);
           if(isset($datos["datosMovil"]))
               $datos = $datos["datosMovil"];
       } catch (\Throwable $th) {
           //throw $th;
           return Response::json([
               'errores' => 1,
               'mensaje' => 'Token incorrecto',
               'token' => $datos
           ], 201);
       }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        // if(!$usuario->tienePermiso("Eliminar ticket")){
        //     return Response::json([
        //         'errores' => 1,
        //         'mensaje' => 'No tiene permisos para realizar esta accion'
        //     ], 201);
        // }
    
       //return $datos;
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
        
        if((strlen($datos['codigoBarra']) == 10 || strlen($datos['codigoBarra']) == 14) && is_numeric($datos['codigoBarra'])){
            //Obtenemos el ticket
            $idTicket = Tickets::on($datos["servidor"])->where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [0, 5])->get()->first();
    
            
            
            if($venta != null){
                if(\App\Settings::puedeCancelarTicketsPorWhatsapp($datos["servidor"]) == false){
                    if(($usuario->roles->descripcion != "Administrador" && $usuario->roles->descripcion != "Programador") && $venta->compartido == 1){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Los tickets compartidos solo pueden cancelarse por el administrador"
                        ], 201);
                    }
                }

                $banca = Branches::on($datos["servidor"])->whereId($datos['idBanca'])->first();
                $minutoTicketJugado =  getdate(strtotime($venta['created_at']));
                $minutoActual = $fecha['minutes'];
    
                
                if(!$usuario->tienePermiso("Cancelar tickets en cualquier momento")){
                    if($minutoTicketJugado['year'] != $fecha['year']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar",
                            'ticket' => $minutoTicketJugado
                        ], 201);
                    }
                    if($minutoTicketJugado['mon'] != $fecha['mon']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                    if($minutoTicketJugado['mday'] != $fecha['mday']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                    if($minutoTicketJugado['hours'] != $fecha['hours']){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar"
                        ], 201);
                    }
                }
               // return ($minutoActual - $minutoTicketJugado) . " - " .$banca[' minutosCancelarTicket'];
    
                if(($minutoActual - $minutoTicketJugado['minutes']) < $banca['minutosCancelarTicket'] || $usuario->tienePermiso("Cancelar tickets en cualquier momento")){
                    $venta['pagado'] = 0;
                    $venta['status'] = 0;
                    $venta->save();

                    $ventasDetalles = Salesdetails::on($datos["servidor"])->where('idVenta', $venta['id'])->get();
                    foreach($ventasDetalles as $v){
                        $v['premio'] = 0;
                        $v['status'] = 0;
                        $stock = Stock::on($datos["servidor"])->whereId($v["idStock"])->first();
                        if($stock != null){
                            $stock->monto = $stock->monto + $v["monto"];
                            $stock->save();
                        }
                        $v->save();
                    }
    
                    Cancellations::on($datos["servidor"])->create([
                        'idTicket' => $venta['idTicket'],
                        'idUsuario' => $datos['idUsuario'],
                        'razon' => $datos['razon']
                    ]);
                    event(new \App\Events\BranchesEvent(\App\Branches::on($datos["servidor"])->whereId($venta["idBanca"])->first()));
    
                    $mensaje = "El ticket se ha cancelado correctamente";
                }else{
                    $errores = 1;
                    $mensaje = "Han pasado los " . $banca[' minutosCancelarTicket'] ." minutos de plazo para cancelar";
                    //$mensaje = "minutos actual: $minutoActual, minutosticket: $minutoTicketJugado";
                }
                
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe o ya ha sido cancelado";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }

        $fecha = getdate();
        $ventas = Sales::on($datos["servidor"])->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereNotIn('status', [0,5])->get();
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        $sale = 
        ($errores == 1) ?
        null
        :
        \DB::connection($datos["servidor"])->select("select 
            s.id, s.total, s.pagado, s.status, s.idTicket, s.created_at, 
            t.codigoBarra, s.idUsuario, u.usuario, b.codigo, sum(sd.premio) as premio, 
            sum(IF(sd.pagado = 0, sd.premio, 0)) as montoAPagar, 
            sum(IF(sd.pagado = 1, sd.premio, 0)) as montoPagado, 
            (select cancellations.razon from cancellations where cancellations.idTicket = s.idTicket) as razon, 
            (select users.usuario from users where users.id = (select cancellations.idUsuario from cancellations where cancellations.idTicket = s.idTicket)) as usuarioCancelacion, 
            (select cancellations.created_at from cancellations where cancellations.idTicket = s.idTicket) as fechaCancelacion 
            from sales s  inner join salesdetails sd on s.id = sd.idVenta 
            inner join users u on u.id = s.idUsuario 
            inner join tickets t on t.id = s.idTicket 
            inner join branches b on b.id = s.idBanca 
            where s.id = {$venta->id}
            group by s.id, s.total, s.pagado, s.status, s.idTicket, t.id, t.codigoBarra, s.idUsuario, u.usuario, b.codigo, razon, fechaCancelacion, usuarioCancelacion 
            order by s.created_at desc");
    
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje,
            'ticket' => $sale != null ? count($sale) > 0 ? $sale[0] : null : null
        ], 201);
    }



    public function eliminar()
    {
        // $datos = request()->validate([
        //     'datos.codigoBarra' => 'required',
        //     'datos.razon' => 'required',
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => 'required'
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
            ], 201);
        }
    
        $usuario = Users::on($datos["servidor"])->whereId($datos['idUsuario'])->first();
        if(!$usuario->tienePermiso("Eliminar ticket")){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion'
            ], 201);
        }

    
    
       //return $datos;
        $fecha = getdate();
    
        $errores = 0;
        $mensaje = '';
        $loterias = null;
        $jugadas = null;
    
        
    
    
        if(strlen($datos['codigoBarra']) == 10 && is_numeric($datos['codigoBarra'])){
            //Obtenemos el ticket
            $idTicket = Tickets::on($datos["servidor"])->where('codigoBarra', $datos['codigoBarra'])->value('id');
            $venta = Sales::on($datos["servidor"])->where('idTicket', $idTicket)->whereNotIn('status', [5])->get()->first();
    
            
            
            if($venta != null){
                if($venta["status"] != 0){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'Primero debe cancelar el ticket'
                    ], 201);
                }
                $banca = Branches::on($datos["servidor"])->whereId($datos['idBanca'])->first();
            
    
               
               // return ($minutoActual - $minutoTicketJugado) . " - " .$banca[' minutosCancelarTicket'];
    
                    $venta['status'] = 5;
                    $venta['pagado'] = 0;
                    $venta->save();

                    $ventasDetalles = Salesdetails::on($datos["servidor"])->where('idVenta', $venta['id'])->get();
                    foreach($ventasDetalles as $v){
                        $v['premio'] = 0;
                        $v['status'] = 0;
                        $v->save();
                    }
    
                    Cancellations::on($datos["servidor"])->create([
                        'idTicket' => $venta['idTicket'],
                        'idUsuario' => $datos['idUsuario'],
                        'razon' => $datos['razon']
                    ]);
    
                    $mensaje = "El ticket se ha eliminado correctamente";
               
                
    
            }else{
                $errores = 1;
                $mensaje = "El ticket no existe para eliminar";
            }
        }else{
                $errores = 1;
                $mensaje = "El numero de ticket no es correcto";
        }

       
    
    
        return Response::json([
            'errores' => $errores,
            'mensaje' => $mensaje
        ], 201);
    }




    public function copiarTicket()
    {
        $idBanca = 0;
        

        $datos = request()->validate([
            'datos.idTicket' => 'required'
        ])['datos'];

        $idVenta = Sales::where('idTicket', $datos['idTicket'])->first();
        if($idVenta == null){
            $idVenta = $idVenta->id;
        }else{
            return Response::json([
                'errores' => 1,
                'mensaje' => 'El ticket no existe',
                'img' => null
            ], 201);
        }

       
    
        $img = new TicketClass($sale->id);
        $img = $img->generate();
    
    
        return Response::json([
            'errores' => 0,
            'mensaje' => '',
            'img' => $img
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
        // $datos = request()->validate([
        //     'datos.idUsuario' => 'required',
        //     'datos.idBanca' => 'required',
        //     'datos.idVenta' => 'required',
        //     'datos.compartido' => 'required',
        //     'datos.descuentoMonto' => 'required',
        //     'datos.hayDescuento' => 'required',
        //     'datos.total' => 'required',
        //     'datos.subTotal' => 'required',
    
        //     'datos.loterias' => '',
        //     'datos.jugadas' => 'required',
        // ])['datos'];

        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }
        $data = Helper::guardarVenta($datos['servidor'], $datos['idUsuario'], $datos['idBanca'], $datos['idVenta'], $datos['compartido'], $datos['descuentoMonto'], $datos['hayDescuento'], $datos['total'], json_encode($datos['jugadas']));
        
        // return Response::json([
        //     'jugadas' => $data
        // ], 201);

        if($data[0]->errores == 1){
            return Response::json([
                'errores' => 1,
                'mensaje' => $data[0]->mensaje,
                'data' => $data,
            ], 201);
        }
        // return Response::json([
        //     'errores' => 1,
        //     'mensaje' => $data[0]
        // ], 201);

        $img = new TicketToHtmlClass($datos["servidor"], $data);
        event(new RealtimeStockEvent($datos["servidor"], true));
        event(new \App\Events\BranchesEvent(\App\Branches::on($datos["servidor"])->whereId($datos['idBanca'])->first()));

        
         return Response::json([
            'errores' => 0,
            'mensaje' => $data[0]->mensaje,
            'idVenta' => $data[0]->idVentaHash,
            'loterias' => ($data[0]->loterias != null) ? json_decode($data[0]->loterias) : [],
            'caracteristicasGenerales' =>  ($data[0]->caracteristicasGenerales != null) ? json_decode($data[0]->caracteristicasGenerales) : [],
            'total_ventas' => $data[0]->total_ventas,
            'total_jugadas' => $data[0]->total_jugadas,
            'ventas' => ($data[0]->ventas != null) ? json_decode($data[0]->ventas) : [],
            'bancas' => ($data[0]->bancas != null) ? json_decode($data[0]->bancas) : [],
            'idUsuario' => $datos['idUsuario'],
            'idBanca' => $data[0]->idBanca,
            'img' => $img->generate(),
            'venta' => ($data[0]->venta != null) ? json_decode($data[0]->venta)[0] : []
        ], 201);
    }

    public function storeMobileV2(Request $request)
    {
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }

        try {
            $venta = \App\Sales::on($datos["servidor"])->where(["idTicket" => $datos["sale"]["idTicket"]])->first();
        if($venta != null){
            if($venta->idBanca == $datos["sale"]["idBanca"])
                return Response::json([
                    "idTicket" => $venta->idTicket,
                ]);
            else
                abort(404, "Este ticket no pertenece a esta banca");
        }
        
        \DB::connection($datos["servidor"])->beginTransaction();
        $idVenta = \App\Sales::on($datos["servidor"])->max("id");
        if($idVenta == null)
            $idVenta = 0;
        $idVenta++;
        $venta = \App\Sales::on($datos["servidor"])->create([
            "id" => $idVenta,
            "compartido" => $datos["sale"]["compartido"],
            "idUsuario" => $datos["sale"]["idUsuario"],
            "idBanca" => $datos["sale"]["idBanca"],
            "total" => $datos["sale"]["total"],
            "subTotal" => $datos["sale"]["subTotal"],
            "descuentoMonto" => $datos["sale"]["descuentoMonto"],
            "hayDescuento" => $datos["sale"]["hayDescuento"],
            "idTicket" => $datos["sale"]["idTicket"],
            "created_at" => $datos["sale"]["created_at"],
            "updated_at" => $datos["sale"]["updated_at"],
            "status" => 1
        ]);

        \App\Tickets::on($datos["servidor"])->updateOrCreate(
            ["id" => $venta->idTicket],
            ["codigoBarra" => $datos["sale"]["ticket"]["codigoBarra"]]
        );

        foreach ($datos["salesdetails"] as $detail) {
            // $montoDisponible = \DB::connection($datos["servidor"])->select("select montoDisponible({$detail['jugada']}, {$detail['idLoteria']}, {$venta->idBanca}, {$detail['idLoteriaSuperpale']}) as montoDisponible")[0]->montoDisponible;
            $jugada = $detail["jugada"];

            //Si la jugada es de tipo Pick 3, Pick 4 o Super pale, le quitamos el ultimo caracter
            // ya que este es un caracter especial
            $ultimoCaracterDeLaJugada = substr($jugada, -1, 1); 
            if($ultimoCaracterDeLaJugada == '-' || $ultimoCaracterDeLaJugada == '+' || $ultimoCaracterDeLaJugada == 'S' || $ultimoCaracterDeLaJugada == 's')
                $jugada = substr($jugada, 0, strlen($jugada) - 1);
            
            $comision = 0;
            $idStock = \DB::connection($datos["servidor"])->select("select insertarBloqueo('$jugada', {$detail['idLoteria']}, {$detail['idSorteo']}, '{$detail['sorteoDescripcion']}', {$venta->idBanca}, {$detail['idLoteriaSuperpale']}, {$detail['monto']}) as idStock")[0]->idStock;
            
            if($idStock == null)
                abort(404, "Error idStock $idStock");

            $datosComisiones = \App\Commissions::on($datos["servidor"])->where(["idBanca" => $venta->idBanca, "idLoteria" => $detail["idLoteria"]])->orderBy("id", "desc")->first();
            if($detail["sorteoDescripcion"] == 'Directo')
                $comision = ($datosComisiones->directo / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Pale')
                $comision = ($datosComisiones->pale / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Tripleta')
                $comision = ($datosComisiones->tripleta / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Super pale')
                $comision = ($datosComisiones->superPale / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Pick 3 Straight')
                $comision = ($datosComisiones->pick3Straight / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Pick 3 Box')
                $comision = ($datosComisiones->pick3Box / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Pick 4 Straight')
                $comision = ($datosComisiones->pick4Straight / 100) * $detail["monto"];
            else if($detail["sorteoDescripcion"] == 'Pick 4 Box')
                $comision = ($datosComisiones->pick4Box / 100) * $detail["monto"];
            
            if($comision == null)
                $comision = 0;

            \App\Realtime::on($datos["servidor"])->create(["idAfectado" => $idStock, 'tabla' => 'stocks']);
            \App\Salesdetails::on($datos["servidor"])->create([
                "idVenta" => $idVenta,
                "idLoteria" => $detail["idLoteria"],
                "idSorteo" => $detail["idSorteo"],
                "jugada" => $jugada,
                "monto" => $detail["monto"],
                "premio" => $detail["premio"],
                "comision" => $detail["comision"],
                "idStock" => $idStock,
                "idLoteriaSuperpale" => $detail["idLoteriaSuperpale"],
                "created_at" => $detail["created_at"],
                "updated_at" => $detail["updated_at"],
            ]);
        }
        \DB::connection($datos["servidor"])->commit();
        event(new RealtimeStockEvent($datos["servidor"], true));
        event(new \App\Events\BranchesEvent(\App\Branches::on($datos["servidor"])->whereId($datos["sale"]["idBanca"])->first()));

        return Response::json([
            'idTicket' => isset($venta) ? $venta->idTicket : null,
            "idVenta" => $idVenta,
        ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::connection($datos["servidor"])->rollback();
            abort(404, $th->getMessage());
        }
        
    }


    public function storeMovil(Request $request)
    {
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.idBanca' => 'required',
            'datos.idVenta' => 'required',
            'datos.compartido' => 'required',
            'datos.descuentoMonto' => 'required',
            'datos.hayDescuento' => 'required',
            'datos.total' => 'required',
            'datos.subTotal' => 'required',
    
            'datos.loterias' => '',
            'datos.jugadas' => 'required',
        ])['datos'];
        $datos["jugadas"] = json_decode($datos["jugadas"]);

        $data = Helper::guardarVenta($datos['idUsuario'], $datos['idBanca'], $datos['idVenta'], $datos['compartido'], $datos['descuentoMonto'], $datos['hayDescuento'], $datos['total'], json_encode($datos['jugadas']));
        
        // return Response::json([
        //     'jugadas' => $data
        // ], 201);

        if($data[0]->errores == 1){
            return Response::json([
                'errores' => 1,
                'mensaje' => $data[0]->mensaje
            ], 201);
        }

        $img = new TicketToHtmlClass($data);

        
         return Response::json([
            'errores' => 0,
            'mensaje' => $data[0]->mensaje,
            'idVenta' => $data[0]->idVentaHash,
            'loterias' => ($data[0]->loterias != null) ? json_decode($data[0]->loterias) : [],
            'caracteristicasGenerales' =>  ($data[0]->caracteristicasGenerales != null) ? json_decode($data[0]->caracteristicasGenerales) : [],
            'total_ventas' => $data[0]->total_ventas,
            'total_jugadas' => $data[0]->total_jugadas,
            'ventas' => ($data[0]->ventas != null) ? json_decode($data[0]->ventas) : [],
            'bancas' => ($data[0]->bancas != null) ? json_decode($data[0]->bancas) : [],
            'idUsuario' => $datos['idUsuario'],
            'idBanca' => $data[0]->idBanca,
            'img' => $img->generate(),
            'venta' => ($data[0]->venta != null) ? json_decode($data[0]->venta)[0] : []
        ], 201);
    }


    public function storeViejo(Request $request)
    {
        $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.idBanca' => 'required',
            'datos.idVenta' => 'required',
            'datos.compartido' => 'required',
            'datos.descuentoMonto' => 'required',
            'datos.hayDescuento' => 'required',
            'datos.total' => 'required',
            'datos.subTotal' => 'required',
    
            'datos.loterias' => 'required',
            'datos.jugadas' => 'required',
        ])['datos'];

        // return Response::json([
        //     'errores' => 1,
        //     'mensaje' => 'Error de seguridad: Idventa incorrecto',
        //     'datos' => $datos['jugadas']
        // ], 201);

        $datos['idVenta'] = Helper::getIdVentaTemporal($datos['idVenta']);
        if($datos['idVenta'] == 0){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Error de seguridad: Idventa incorrecto'
            ], 201);
        }

        $sale = Sales::where(['id' => $datos['idVenta'], 'idBanca' => $datos['idBanca']])->whereNotIn('status', [0, 5])->first();
        if($sale != null){
            //En caso de que la venta ya exista eso quiere decir que 
            //la venta se guardo pero hubo un error antes de terminar de guardar todo por lo tanto es mejor borrarla y volver a crearla
            Helper::borrarVentaErronea($sale);
            // $img = new TicketPrintClass($sale->id);
            // $img = $img->generate();
            // return Response::json([
            //     'idVenta' => Helper::createIdVentaTemporal($datos['idBanca']),
            //     'errores' => 0,
            //     'mensaje' => 'Se ha guardado Correctamente',
            //     'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            //     'loterias' => Helper::loteriasOrdenadasPorHoraCierre(),
            //     'venta' => ($sale != null) ? new SalesResource($sale) : null,
            //     'img' => $img
            // ], 201);
        }

   
        
    
        $fecha = getdate();
        $idSorteo = 0;
        $idTicket = 0;
        $codigoBarraCorrecto = false;
        $errores = 0;
        $mensaje = '';
        $codigoBarra = '';
        $sale = null;
        $idDia = Days::whereWday($fecha['wday'])->first()->id;
        $banca = Branches::whereId($datos['idBanca'])->first();
    
        $usuario = Users::on(session("servidor"))->whereId($datos['idUsuario'])->whereStatus(1)->first();
        if($usuario == null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Usuario no existe'
            ], 201);
        }
        if(!$usuario->tienePermiso('Vender tickets')){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion vender'
            ], 201);
        }
        if(!$usuario->tienePermiso('Acceso al sistema')){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'No tiene permisos para realizar esta accion acceso'
            ], 201);
        }
    
        if(!$usuario->esBancaAsignada($datos['idBanca'])){
            if(!$usuario->tienePermiso('Jugar como cualquier banca')){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'No tiene permisos para realizar esta accion'
                ], 201);
            }
        }
    
    
        if(!$banca->abierta()){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'La banca aun no ha abierto'
            ], 201);
        }

    
        if($banca->cerrada()){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'La banca ha cerrado'
            ], 201);
        }

        if($banca->limiteVenta($datos['total'])){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'A excedido el limite de ventas de la banca'
            ], 201);
        }
    
        
    
       //Aqui quede
    
        //Generamos y guardamos codigo de barra
        while($codigoBarraCorrecto != true){
            // $codigoBarra = $faker->isbn10;
            $codigoBarra = rand(1111111111, getrandmax());
            //return 'codiog: ' . $codigoBarra . ' faker: ' . $faker->isbn10;
            //Verificamos de que el codigo de barra no exista
            if(Tickets::where('codigoBarra', $codigoBarra)->get()->first() == null){
                if(is_numeric($codigoBarra)){
                    Tickets::create(['idBanca' => $datos['idBanca'], 'codigoBarra' => $codigoBarra]);
                    $idTicket = Tickets::where('codigoBarra', $codigoBarra)->value('id');
                    $codigoBarraCorrecto = true;
                    break;
                }
            }
        }
    
        
        /***************** Validamos la existencia de la jugada ***********************/
        $loterias = Helper::getLoterias($datos['jugadas']);
        // return Response::json([
        //     'errores' => 1,
        //     'mensaje' => 'Erro',
        //     'a' => $datos['jugadas']
        // ], 201);
        
        foreach($loterias as $l){
            $loteria = Lotteries::whereId($l['id'])->first();
            $jugadas = Helper::getJugadasPertenecientesALoteria($l['id'], $datos['jugadas']);

            foreach($jugadas as $d){

               
                
                
                 //Confirmamos de que la loteria no tenga premios registrados en el dia de hoy
               //si es asi entonces no puede realizar la jugada y en caso de querer hacer jugadas
               //enonces debe borrar los premios de dicha loteria
               if(Helper::loteriaTienePremiosRegistradosHoy($d['idLoteria']) == true){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'Error: La loteria ' . $loteria->descripcion . ' ya tiene numeros ganadores registrados'
                    ], 201);
                }

                

               $idSorteo = (new Helper)->determinarSorteo($d['jugada'], $loteria);
               $sorteo = Draws::whereId($idSorteo)->first();
              
               if(Helper::decimalesDelMontoJugadoSonValidos($d['monto'], $loteria, $sorteo) == false){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'Error: El monto de la jugada ' . $d['jugada'] . ' es incorrecto'
                ], 201);
            }
               

            $banca = Branches::whereId($datos['idBanca'])->first();
            
                if(!$banca->loteriaExisteYTienePagoCombinaciones($d['idLoteria'], $idSorteo)){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria ' . $loteria->descripcion . ' no esta permitida en esta banca, o no tiene combinaciones para el sorteo ' . $sorteo->descripcion
                    ], 201);
                }
    
               if(!$loteria->sorteoExiste($idSorteo)){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'El sorteo no existe para la loteria' . $loteria->descripcion
                    ], 201);
                }
    
               if(!$loteria->abreHoy()){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria' . $loteria->descripcion . ' no habre hoy'
                    ], 201);
                }
    
               if(!$loteria->abierta()){
                    return Response::json([
                        'errores' => 1,
                        'mensaje' => 'La loteria' . $loteria->descripcion . ' aun no ha abierto'
                    ], 201);
                }
            
                if($loteria->cerrada()){
                    if(!$usuario->tienePermiso('Jugar fuera de horario')){
                        return Response::json([
                            'errores' => 1,
                            'mensaje' => 'La loteria' . $loteria->descripcion . ' ha cerrado'
                        ], 201);
                    }
                }
        
    
               //Obtenemos los datos correspondiente a la loteria y jugada dada que estan en la tabla inventario (stcoks)
               $stock = Stock::where([
                    'idBanca' => $datos['idBanca'], 
                    'idLoteria' => $d['idLoteria'], 
                    'jugada' => $d['jugada']])
                    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
                   
                if((new Helper)->montodisponible($d['jugada'], $loteria, $datos['idBanca']) < $d['monto']){
                        $errores = 1;
                        $mensaje = 'No hay existencia suficiente para la jugada ' . $d['jugada'] .' en la loteria ' . $d['descripcion'];
                        break;
                }
            
               
            }
        }
    
    if($errores == 0){
    
        /***************** Insertar la venta y obtener el idVenta ***********************/
        
       $sale = Sales::create([
           'id' => $datos['idVenta'],
           'compartido' => $datos['compartido'],
           'idUsuario' => $datos['idUsuario'],
           'idBanca' => $datos['idBanca'],
           'total' => $datos['total'],
           'subTotal' => $datos['subTotal'],
           'descuentoMonto' => $datos['descuentoMonto'],
           'hayDescuento' => $datos['hayDescuento'],
           'idTicket' => $idTicket
       ]);

       //Hago esto porque al momento de guardar me retorna el id 0 entonces no es correcto
       $sale = Sales::whereId($datos['idVenta'])->first();
    
    
       /***************** Insertar el datelle ventas ***********************/
    
   
    // $coleccionJugadas = collect($datos['jugadas']);
        // list($jugadasLoterias, $no) = $coleccionJugadas->partition(function($j) use($l){
        //     return $j['idLoteria'] == $l['id'];
        // });

    foreach($loterias as $l){
        $loteria = Lotteries::whereId($l['id'])->first();
        $jugadas = Helper::getJugadasPertenecientesALoteria($l['id'], $datos['jugadas']);
        foreach($jugadas as $d){

            // $loteria = Lotteries::whereId($d['idLoteria'])->first();
            
           $idSorteo = (new Helper)->determinarSorteo($d['jugada'], $loteria);
           $d['jugada'] = Helper::quitarUltimoCaracter($d['jugada'], $idSorteo);
    
           $stock = Stock::where(['idLoteria' => $d['idLoteria'],'idBanca' => $datos['idBanca'], 'jugada' => $d['jugada'], 'idSorteo' => $idSorteo])
                    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
                    
               //Verificamos que la variable $stock no sea nula
               if($stock != null){
                   //Restamos el monto jugado a la tabla stock y guardamos
                   $stock['monto'] = $stock['monto'] - $d['monto'];
                   $stock->save();
               }else{
                   //Obtenemos el stock de la tabla bloqueo jugadas
                    $stock = Blocksplays::where(
                        ['idBanca' => $datos['idBanca'], 
                        'idLoteria' => $d['idLoteria'],
                        'idSorteo' => $idSorteo,
                        'jugada' => $d['jugada'], 
                        'status' => 1])
                        ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
                        ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->first();
                
                    //Verificamos que no sea nulo
                    if($stock != null){
                        //Si el monto en la variable $stock es mejor que el monto que se jugara entonces no hay exitencia suficiente
                        Stock::create([
                            'idBanca' => $datos['idBanca'],
                            'idLoteria' => $d['idLoteria'],
                            'idSorteo' => $idSorteo,
                            'jugada' => $d['jugada'],
                            'montoInicial' => $stock['monto'],
                            'monto' => $stock['monto'] - $d['monto'],
                            'esBloqueoJugada' => 1
                        ]);
                    }else{
                        $stock = Blockslotteries::where([
                            'idBanca' => $datos['idBanca'], 
                            'idLoteria' => $d['idLoteria'], 
                            'idSorteo' => $idSorteo
                        ])->first();
    
                        Stock::create([
                            'idBanca' => $datos['idBanca'],
                            'idLoteria' => $d['idLoteria'],
                            'idSorteo' => $idSorteo,
                            'jugada' => $d['jugada'],
                            'montoInicial' => $stock['monto'],
                            'monto' => $stock['monto'] - $d['monto']
                        ]);
                    }
               }
    
         Salesdetails::create([
             'idVenta' => Sales::where('idTicket', $idTicket)->value('id'),
             'idLoteria' => $d['idLoteria'],
             'idSorteo' => $idSorteo,
             'jugada' => $d['jugada'],
             'monto' => $d['monto'],
             'premio' => 0,
             'monto' => $d['monto'],
             'comision' => Helper::comision($datos['idBanca'], $d['idLoteria'], $idSorteo, $d['monto'])
         ]);

         
    
        }
    }

        if($errores == 0)
            $mensaje = "Se ha guardado correctamente";

    
    } //END if validacion si hay errores

        $img = null;
        if($sale != null){
            // $img = new TicketClass($sale->id);\
            
            $img = new TicketPrintClass($sale->id);
            $img = $img->generate();
        }
    
        return Response::json([
            'idVenta' => Helper::createIdVentaTemporal($datos['idBanca']),
            'errores' => $errores,
            'mensaje' => $mensaje,
            'bancas' => BranchesResource::collection(Branches::whereStatus(1)->get()),
            'loterias' => Helper::loteriasOrdenadasPorHoraCierre($usuario),
            'venta' => ($sale != null) ? new SalesResource($sale) : null,
            'img' => $img,
            'ventas' => SalesResource::collection(Helper::getVentasDeHoy($datos['idBanca'])),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function montodisponible(Request $request)
    {
        // $datos = request()->validate([
        //     'datos.jugada' => 'required|min:2|max:6',
        //     'datos.idLoteria' => 'required',
        //     'datos.idBanca' => 'required'
        // ])['datos'];
    
    
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }
       

    $bloqueo = (new Helper)->montoDisponibleFuncion($datos['servidor'], $datos['jugada'], $datos['idLoteria'], $datos['idBanca'], $datos['idLoteriaSuperpale']);
    
       
    
       if($bloqueo == null) $bloqueo = 0;
    
        return Response::json([
            'monto' => $bloqueo[0]->monto
        ], 201);
    }

    public function montodisponibleViejo(Request $request)
    {
        $datos = request()->validate([
            'datos.jugada' => 'required|min:2|max:6',
            'datos.idLoteria' => 'required',
            'datos.idBanca' => 'required'
        ])['datos'];
    
        $fecha = getdate();
        $idSorteo = 0;
        $bloqueo = 0;
    
        // $idDia = Days::whereWday($fecha['wday'])->first()->id;
    
        $loteria = DB::table('lotteries')->whereId($datos['idLoteria'])->first();
        
    
    //    if(strlen($datos['jugada']) == 2){
    //         $idSorteo = 1;
    //    }
    //    else if(strlen($datos['jugada']) == 4){
    //         if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
    //             $idSorteo = 2;
    //         else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
    //             $idSorteo = 4;
    //    }
    //    else if(strlen($datos['jugada']) == 6){
    //         $idSorteo = 3;
    //    }

    
  
        // $idSorteo = (new Helper)->determinarSorteo($datos['jugada'], $loteria);
    
    //    $bloqueo = Stock::where([   
    //        'idLoteria' => $datos['idLoteria'], 
    //        'idBanca' => $datos['idBanca'], 
    //        'jugada' => $datos['jugada'],
    //        'idSorteo' => $idSorteo,
    //     ])
    //    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->value('monto');
       
    //Verificamos que la variable $stock no sea nula
    // if($bloqueo == null){
    //     $bloqueo = Blocksplays::where(
    //         [
    //             'idBanca' => $datos['idBanca'],
    //             'idLoteria' => $datos['idLoteria'], 
    //             'jugada' => $datos['jugada'],
    //             'idSorteo' => $idSorteo,
    //             'status' => 1
    //         ])
    //         ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
    //         ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->value('monto');
    
    //     if($bloqueo == null){
    //         $bloqueo = Blockslotteries::where([
    //             'idBanca' => $datos['idBanca'], 
    //             'idLoteria' => $datos['idLoteria'], 
    //             'idDia' => $idDia,
    //             'idSorteo' => $idSorteo
    //         ])->value('monto');
    //     }
    // }

    $bloqueo = (new Helper)->montodisponible($datos['jugada'], $loteria, $datos['idBanca']);
    
       
    
       if($bloqueo == null) $bloqueo = 0;
    
        return Response::json([
            'monto' => $bloqueo
        ], 201);
    }


    public function imagen(Request $request)
    {
            $datos = request()->validate([
            'datos.imagen' => 'required',
            'lastModifiedDate' => '',
            'datos.nombre' => '',
            'name' => '',
            'size' => '',
            'type' => ''
        ])['datos'];

        

        // $lastModifiedDate = $datos['imagen']['lastModifiedDate'];
        // $name = request()->datos['name'];
        // $type = request()->datos['type'];
        // $size = request()->datos['size'];

        // $img = collect($datos['imagen'])->map(function($s) use($lastModifiedDate, $name, $type, $size){
                
        //     return ['name' => $name, 'lastModifiedDate' => $lastModifiedDate, 'type' => $type, 'size' => $size ];
        // });

        // $img = str_replace('data:image/png;base64,', '', $datos['imagen']);
        // $img = str_replace(' ', '+', $img);
        // $fileData  = base64_decode($img);
        // $mensaje = 'Funciona conversion';
        // if($fileData == false){
        //     $mensaje = "Error base64 conversion";
        // }





    // $image = file_put_contents(public_path().'/img/'.'test2.png',$img);
    // $output_file = "fotoo.png";
    // $output_file = public_path().'/ticket/'. $datos['nombre'] . ".png";
    
    $output_file = public_path() . "\\assets\\ticket\\" . $datos['nombre'] . ".png";
    $file = fopen($output_file, "wb");
    $imagenBase64SinComaillas = explode(',', $datos['imagen']);
    fwrite($file, base64_decode($imagenBase64SinComaillas[1]));
    fclose($file);

    $ticket = Tickets::where('codigoBarra', $datos['nombre'])->first();
    if($ticket != null){
        $ticket->imageBase64 = $imagenBase64SinComaillas[1];
        $ticket->save();
    }

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'imagenBase64' => $imagenBase64SinComaillas[1],
            'nombre' => $datos['nombre'],
            'blob' => $output_file,
            'request' => request()->all(),
            'base_decode' => $output_file
        ], 201);

        $image = file_put_contents(public_path().'/img/'.'test.png',$datos['imagen']);
        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'temp' => $image,
            'blob' => $datos['imagen']
        ], 201);

        // ruta de las imagenes guardadas
    $ruta = public_path().'/img/';

    // recogida del form
    $imagenOriginal = $datos['imagen'];

    // crear instancia de imagen
    $imagen = Image::make($imagenOriginal);

    // generar un nombre aleatorio para la imagen
    $temp_name = $this->random_string() . '.' . $imagenOriginal->getClientOriginalExtension();

    $imagen->resize(300,300);

    // guardar imagen
    // save( [ruta], [calidad])
    $imagen->save($ruta . $temp_name, 100);






        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            'temp' => $temp_name
        ], 201);
    }



    public function sms(Request $request)
    {
            $datos = request()->validate([
            'datos.idUsuario' => 'required',
            'datos.codigoBarra' => 'required',
            'datos.sms' => 'required',
            'datos.whatsapp' => 'required',
            'datos.numSms' => '',
            'datos.numWhatsapp' => '',
        ])['datos'];

        $colleccion = [];


        if($datos["sms"] == 1){
            $arreglo = (new Helper)->_sendSms($datos["numSms"], $datos["codigoBarra"]);
            $colleccion = collect([$arreglo ]);
        }
        if($datos["whatsapp"] == 1){
            $arreglo = (new Helper)->_sendSms($datos["numSms"], $datos["codigoBarra"], false);
            if($colleccion == null){
                $colleccion = collect([$arreglo ]);
            }else{
                $colleccion->push($arreglo);
            }
        }



        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente',
            "errrores" => $colleccion
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sales $sales)
    {
        //
    }

    public function createIdTicket(Request $request)
    {
        
        $datos = request()['datos'];
        try {
            $datos = \Helper::jwtDecode($datos);
            if(isset($datos["datosMovil"]))
                $datos = $datos["datosMovil"];
        } catch (\Throwable $th) {
            //throw $th;
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Token incorrecto',
                'token' => $datos
            ], 201);
        }
       
        if($datos["idBanca"] == null)
            abort(404, "El id de la banca no puede ser nulo");

        $ticket = \App\Classes\Helper::createIdTicket($datos["servidor"], $datos["idBanca"], $datos["uuid"], $datos["createNew"]);
    
        return Response::json([
            'ticket' => $ticket
        ], 201);
    }
    
}
