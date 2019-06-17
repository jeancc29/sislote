<?php

use Illuminate\Http\Request;
use Faker\Generator as Faker;
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

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;

date_default_timezone_set("America/Santiago");



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//Route::get('/principal', 'PrincipalController@index');
//Route::get('/principal', 'PrincipalController@index');

Route::apiResource('principal', 'PrincipalController');

// Route::get('/principal', function(){
//     // return TaskResource::collection(Task::all());
    
//     $fecha = getdate();
//     $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
//             ->where('status', '!=', 0)->get();

//     $idVentas = collect($ventas)->map(function($id){
//         return $id->id;
//     });

//     return Response::json([
//         'loterias' => Lotteries::whereStatus(1)->get(),
//         'caracteristicasGenerales' =>  Generals::all(),
//         'total_ventas' => Sales::whereIn('id', $idVentas)->sum('total'),
//         'total_jugadas' => Salesdetails::whereIn('idVenta', $idVentas)->count('jugada'),
//         'ventas' => SalesResource::collection($ventas),
//         'Cerrado' => Lotteries::whereId(1)->first()->cerrada(),
//         // 'abierta' => Lotteries::whereId(1)->first()->abierta()
//     ], 201);
// });


Route::post('/principal/montodisponible', function(){
    // return TaskResource::collection(Task::all());

    
    
    $datos = request()->validate([
        'datos.jugada' => 'required|min:2|max:6',
        'datos.idLoteria' => 'required',
        'datos.idBanca' => 'required'
    ])['datos'];

    $fecha = getdate();
    $idSorteo = 0;
    $bloqueo = 0;

    $idDia = Days::whereWday($fecha['wday'])->first()->id;

    

   if(strlen($datos['jugada']) == 2){
        $idSorteo = 1;
   }
   else if(strlen($datos['jugada']) == 4){
        $idSorteo = 2;
   }
   else if(strlen($datos['jugada']) == 6){
        $idSorteo = 3;
   }

   $bloqueo = Stock::where([   
       'idLoteria' => $datos['idLoteria'], 
       'idBanca' => $datos['idBanca'], 
       'jugada' => $datos['jugada']
    ])
   ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->value('monto');
   
//Verificamos que la variable $stock no sea nula
if($bloqueo == null){
    $bloqueo = Blocksplays::where(
        [
            'idBanca' => $datos['idBanca'],
            'idLoteria' => $datos['idLoteria'], 
            'jugada' => $datos['jugada'],
            'status' => 1
        ])
        ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
        ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->value('monto');

    if($bloqueo == null){
        $bloqueo = Blockslotteries::where([
            'idBanca' => $datos['idBanca'], 
            'idLoteria' => $datos['idLoteria'], 
            'idDia' => $idDia,
            'idSorteo' => $idSorteo
        ])->value('monto');
    }
}

   

   if($bloqueo == null) $bloqueo = 0;

    return Response::json([
        'monto' => $bloqueo
    ], 201);
});



Route::post('/principal/guardar', function(Faker $faker){
    // return TaskResource::collection(Task::all());

    
    

    $datos = request()->validate([
        'datos.idUsuario' => 'required',
        'datos.idBanca' => 'required',
        'datos.descuentoMonto' => 'required',
        'datos.hayDescuento' => 'required',
        'datos.total' => 'required',
        'datos.subTotal' => 'required',

        'datos.loterias' => 'required',
        'datos.jugadas' => 'required',
    ])['datos'];

    $fecha = getdate();
    $idSorteo = 0;
    $idTicket = 0;
    $codigoBarraCorrecto = false;
    $errores = 0;
    $mensaje = '';
    $codigoBarra = '';
    $sale = null;
    $idDia = Days::whereWday($fecha['wday'])->first()->id;

    $usuario = Users::whereId($datos['idUsuario'])->first();
    if(!$usuario->tienePermiso('Vender tickets')){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'No tiene permisos para realizar esta accion'
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


    if(!Branches::whereId($datos['idBanca'])->first()->abierta()){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'La banca aun no ha abierto'
        ], 201);
    }

    if(Branches::whereId($datos['idBanca'])->first()->cerrada()){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'La banca ha cerrado'
        ], 201);
    }

    

   

    //Generamos y guardamos codigo de barra
    while($codigoBarraCorrecto != true){
        $codigoBarra = $faker->isbn10;
        //return 'codiog: ' . $codigoBarra . ' faker: ' . $faker->isbn10;
        //Verificamos de que el codigo de barra no exista
        if(Tickets::where('codigoBarra', $codigoBarra)->get()->first() == null){
            Tickets::create(['idBanca' => $datos['idBanca'], 'codigoBarra' => $codigoBarra]);
            $idTicket = Tickets::where('codigoBarra', $codigoBarra)->value('id');
            $codigoBarraCorrecto = true;
            break;
        }
    }

    //return 'codiog: ' . $codigoBarra . ' faker: ' . $faker->isbn10;
    
    /***************** Validamos la existencia de la jugada ***********************/
    //foreach($datos['loterias'] as $l){
        foreach($datos['jugadas'] as $d){
            
            
            if(strlen($d['jugada']) == 2){
                $idSorteo = 1;
           }
           else if(strlen($d['jugada']) == 4){
                $idSorteo = 2;
           }
           else if(strlen($d['jugada']) == 6){
                $idSorteo = 3;
           }

           $loteria = Lotteries::whereId($d['idLoteria'])->first();

           if(Branches::whereId($datos['idBanca'])->first()->loteriaExiste($loteria->id) == false){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'La loteria ' . $loteria->descripcion . ' no esta permitida en esta banca'
                ], 201);
            }

           if(!$loteria->sorteoExiste($d['jugada'])){
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
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'La loteria' . $loteria->descripcion . ' ha cerrado'
                ], 201);
            }
    

           //Obtenemos los datos correspondiente a la loteria y jugada dada que estan en la tabla inventario (stcoks)
           $stock = Stock::where([
                'idBanca' => $datos['idBanca'], 
                'idLoteria' => $d['idLoteria'], 
                'jugada' => $d['jugada']])
                ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->first();
               
               
           //Verificamos que la variable $stock no sea nula
           if($stock != null){
               //Si el monto en la variable $stock es menor que el monto que se jugara entonces no hay exitencia suficiente
                if($stock['monto'] < $d['monto']){
                    $errores = 1;
                    $mensaje = 'No hay existencia suficiente para la jugada ' . $d['jugada'] .' en la loteria ' . $d['descripcion'];
                    break;
                }
           }else{

                //Obtenemos el stock de la jugada en el bloqueo jugada
                $stock = Blocksplays::where(
                    [
                    'idBanca' => $datos['idBanca'],
                    'idLoteria' => $d['idLoteria'], 
                    'jugada' => $d['jugada'], 'status' => 1])
                    ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
                    ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->first();
            
                //Verificamos que no sea nulo
                if($stock != null){
                    //Si el monto en la variable $stock es mejor que el monto que se jugara entonces no hay exitencia suficiente
                    if($stock['monto'] < $d['monto']){
                        $errores = 1;
                        $mensaje = 'No hay existencia suficiente para la jugada ' . $d['jugada'] .' en la loteria ' . $d['descripcion'];
                        break;
                    }
                }else{
                    //Como la variable $stock esta nula porque no se encontro bloqueo de la jugada entonces vamos a obtener el bloqueo de la loteria para el sorteo
                    $stock = Blockslotteries::where([
                        'idBanca' => $datos['idBanca'],
                        'idLoteria' => $d['idLoteria'], 
                        'idDia' => $fecha['wday'],
                        'idSorteo' => $idSorteo
                    ])->first();


                    //Verificamos que la variable $stock no sea nula
                    if($stock != null){
                        //Si el monto en la variable $stock es mejor que el monto que se jugara entonces no hay exitencia suficiente
                        if($stock['monto'] < $d['monto']){
                            $errores = 1;
                            $mensaje = 'No hay existencia suficiente para la jugada ' . $d['jugada'] .' en la loteria ' . $d['descripcion'];
                            break;
                        }
                    }else{
                        //Como la variable $stock esta nula porque no se encontro bloqueo de la loteria entonces no hay existencia
                        $errores = 1;
                        $mensaje = 'No hay existencia suficiente para la jugada ' . $d['jugada'] .' en la loteria ' . $d['descripcion'];
                        break;
                    }
                }

           }
           
        }
    //}

if($errores == 0){

    /***************** Insertar la venta y obtener el idVenta ***********************/
    
   $sale = Sales::create([
       'idUsuario' => $datos['idUsuario'],
       'idBanca' => $datos['idBanca'],
       'total' => $datos['total'],
       'subTotal' => $datos['subTotal'],
       'descuentoMonto' => $datos['descuentoMonto'],
       'hayDescuento' => $datos['hayDescuento'],
       'idTicket' => $idTicket
   ]);


   /***************** Insertar el datelle ventas ***********************/

   foreach($datos['loterias'] as $l){

    $coleccionJugadas = collect($datos['jugadas']);
    list($jugadasLoterias, $no) = $coleccionJugadas->partition(function($j) use($l){
        return $j['idLoteria'] == $l['id'];
    });

    foreach($jugadasLoterias as $d){
        
        if(strlen($d['jugada']) == 2){
            $idSorteo = 1;
       }
       else if(strlen($d['jugada']) == 4){
            $idSorteo = 2;
       }
       else if(strlen($d['jugada']) == 6){
            $idSorteo = 3;
       }

       $stock = Stock::where(['idLoteria' => $d['idLoteria'],'idBanca' => $datos['idBanca'], 'jugada' => $d['jugada']])
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
                    'jugada' => $d['jugada'], 'status' => 1])
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
                        'monto' => $stock['monto'] - $d['monto']
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
     ]);

    }
}

    if($errores == 0){
        $mensaje = 'Se ha guardado correctamente';
    }

} //END if validacion si hay errores

    return Response::json([
        'errores' => $errores,
        'mensaje' => $mensaje,
        'venta' => ($sale != null) ? new SalesResource($sale) : null
    ], 201);
});



Route::post('/reportes/monitoreo/', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.fecha' => 'required',
        'datos.idUsuario' => 'required'
    ])['datos'];

    $usuario = Users::whereId($datos['idUsuario'])->first();
    if(!$usuario->tienePermiso("Monitorear ticket")){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'No tiene permisos para realizar esta accion'
        ], 201);
    }
    

    $fecha = getdate(strtotime($datos['fecha']));

    // $monitoreo = Sales::join('tickets', 'sales.idTicket', '=', 'tickets.id')
    //             ->join('branches', 'sales.idBanca', '=', 'branches.id')
    //             ->join('users', 'sales.idUsuario', '=', 'users.id')
    //             //->join('salesdetails', 'sales.id', '=', 'salesdetails.idVenta')
    //             ->leftJoin('cancellations', 'sales.idTicket', '=', 'cancellations.idTicket')
                
    //             ->selectRaw('
    //                 sales.*, tickets.codigoBarra, branches.codigo, 
    //                 users.usuario, 
    //                 (select sum(premio) from salesdetails where idVenta = sales.id) as premio,
    //                 cancellations.razon,
    //                 cancellations.created_at as fechaCancelacion
    //                 ')
    //             // ->groupBy('sales.id')
    //             //->sum('sales.id')
    //             ->whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
    //             ->get();


    $monitoreo = Sales::whereBetween('sales.created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->get();

   // return $ventas;
    

    return Response::json([
        'monitoreo' => SalesResource::collection($monitoreo),
        'loterias' => Lotteries::whereStatus(1)->get(),
        'caracteristicasGenerales' =>  Generals::all(),
        'total_ventas' => Sales::sum('total'),
        'total_jugadas' => Salesdetails::count('jugada')
    ], 201);
});







Route::post('/reportes/ventas/', function(){
    // return TaskResource::collection(Task::all());

    $fecha = request()->validate([
        'datos.fecha' => 'required'
    ])['datos'];

    

    $fecha = getdate(strtotime($fecha['fecha']));

    $pendientes = Sales::
                whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereStatus(1)
                ->count();

    $ganadores = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereStatus('2')
                ->count();
    $perdedores = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereStatus('3')
                ->count();

    $total = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereIn('status', array(1,2,3))
                ->count();

                $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->where('status', '!=', '0')
                ->sum('total');

                //AQUI COMIENSA LAS COMISIONES

                //AQUI TERMINAN LAS COMISIONES

                $descuentos = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->where('status', '!=', 0)
                ->sum('descuentoMonto');

                $premios = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->whereIn('status', array(1,2))
                ->sum('premios');

                //Obtener loterias con el monto total jugado y con los premios totales

                

                $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
                $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
                $loterias = Lotteries::
                        selectRaw('
                            id, 
                            descripcion, 
                            (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.created_at between ? and ?) as ventas,
                            (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.created_at between ? and ?) as premios,
                            (select substring(numeroGanador, 1, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as primera,
                            (select substring(numeroGanador, 3, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as segunda,
                            (select substring(numeroGanador, 5, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as tercera
                            ', [$fechaInicial, $fechaFinal, //Parametros para ventas
                                $fechaInicial, $fechaFinal, //Parametros para premios
                                $fechaInicial, $fechaFinal, //Parametros primera
                                $fechaInicial, $fechaFinal, //Parametros segunda
                                $fechaInicial, $fechaFinal //Parametros tercera
                                ])
                        ->where('lotteries.status', '=', '1')
                        ->get();

  
    $ticketsGanadores = Sales::
        whereStatus(2)
        ->wherePagado(0)
        ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
        ->get();

    return Response::json([
        'pendientes' => $pendientes,
        'perdedores' => $perdedores,
        'ganadores' => $ganadores,
        'total' => $total,
        'ventas' => $ventas,
        'descuentos' => $descuentos,
        'premios' => $premios,
        'neto_final' => ($ventas - $premios - $descuentos),
        'loterias' => $loterias,
        'ticketsGanadores' => SalesResource::collection($ticketsGanadores)
    ], 201);
});






Route::post('/principal/duplicar/', function(){
    // return TaskResource::collection(Task::all());

    $codigoBarra = request()->validate([
        'datos.codigoBarra' => 'required'
    ])['datos'];

    $errores = 0;
    $mensaje = '';
    $loterias = null;
    $jugadas = null;

    $idTicket = Tickets::where('codigoBarra', $codigoBarra['codigoBarra'])->value('id');
    $idVenta = Sales::where('idTicket', $idTicket)->where('status', '!=', 0)->value('id');
    
    if(strlen($codigoBarra['codigoBarra']) == 10 && is_numeric($codigoBarra['codigoBarra']) == true){
        if($idVenta != null){
            $idLoterias = Salesdetails::distinct()->select('idLoteria')->where('idVenta', $idVenta)->get();
            $idLoterias = collect($idLoterias)->map(function($id){
                return $id->idLoteria;
            });

            $loterias = Lotteries::whereIn('id', $idLoterias)->whereStatus(1)->get();
            $jugadas = Salesdetails::where('idVenta', $idVenta)->get();
        }else{
            $errores = 1;
            $mensaje = "El ticket no existe";
        }
    }else{
            $errores = 1;
            $mensaje = "El numero de ticket no es correcto";
    }
    //$fecha = getdate(strtotime($fecha['fecha']));

    

    
  
    

    return Response::json([
        'loterias' => $loterias,
        'jugadas' => $jugadas,
        'errores' => $errores,
        'mensaje' => $mensaje
    ], 201);
});



Route::post('/reportes/jugadas/', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.idLoteria' => 'required',
        'datos.fecha' => 'required'
    ])['datos'];

    $fecha = getdate(strtotime($datos['fecha']));

    $errores = 0;
    $mensaje = '';
    $loterias = null;
    $jugadas = null;


    $idVentas = Sales::select('id')
            ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
            ->where('status', '!=', 0)
            ->get();

    $idVentas = collect($idVentas)->map(function($id){
        return $id->id;
    });


    $jugadas = Salesdetails::
                where('idLoteria', $datos['idLoteria'])
                ->whereIn('idVenta', $idVentas)
                ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                ->get();

    

    
  
    

    return Response::json([
        'jugadas' => $jugadas,
        'errores' => $errores,
        'mensaje' => $mensaje
    ], 201);
});



Route::post('/principal/pagar/', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.codigoBarra' => 'required',
        'datos.idUsuario' => 'required'
    ])['datos'];

    $usuario = Users::whereId($datos['idUsuario'])->first();
    if(!$usuario->tienePermiso("Marcar ticket como pagado")){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'No tiene permisos para realizar esta accion'
        ], 201);
    }

    $fecha = getdate();

    $errores = 0;
    $mensaje = '';
    $loterias = null;
    $jugadas = null;

    


    if(strlen($datos['codigoBarra']) == 10 && is_numeric($datos['codigoBarra'])){
        $idTicket = Tickets::where('codigoBarra', $datos['codigoBarra'])->value('id');
        $venta = Sales::where('idTicket', $idTicket)->whereStatus(2)->wherePagado(0)->wherePagado(0)->get()->first();

        if($venta != null){
            $venta['pagado'] = 1;
            $venta->save();

            $mensaje = "El ticket se ha pagado correctamente";

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
        'mensaje' => $mensaje
    ], 201);
});


Route::post('/principal/cancelar/', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.codigoBarra' => 'required',
        'datos.razon' => 'required',
        'datos.idUsuario' => 'required'
    ])['datos'];

    $usuario = Users::whereId($datos['idUsuario'])->first();
    if(!$usuario->tienePermiso("Cancelar tickets en cualquier momento")){
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
        $idTicket = Tickets::where('codigoBarra', $datos['codigoBarra'])->value('id');
        $venta = Sales::where('idTicket', $idTicket)->where('status', '!=', 2)->wherePagado(0)->get()->first();

        
        
        if($venta != null){
            $general = Generals::all()->first();
            $minutoTicketJugado =  getdate(strtotime($venta['created_at']));
            $minutoActual = $fecha['minutes'];

            if($minutoTicketJugado['year'] != $fecha['year']){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => "Han pasado los " . $general['minutosParaCancelar'] ." minutos de plazo para cancelar",
                    'ticket' => $minutoTicketJugado
                ], 201);
            }
            if($minutoTicketJugado['mon'] != $fecha['mon']){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => "Han pasado los " . $general['minutosParaCancelar'] ." minutos de plazo para cancelar"
                ], 201);
            }
            if($minutoTicketJugado['mday'] != $fecha['mday']){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => "Han pasado los " . $general['minutosParaCancelar'] ." minutos de plazo para cancelar"
                ], 201);
            }
            if($minutoTicketJugado['hours'] != $fecha['hours']){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => "Han pasado los " . $general['minutosParaCancelar'] ." minutos de plazo para cancelar"
                ], 201);
            }

           // return ($minutoActual - $minutoTicketJugado) . " - " .$general['minutosParaCancelar'];

            if(($minutoActual - $minutoTicketJugado['minutes']) < $general['minutosParaCancelar']){
                $venta['status'] = 0;
                $venta->save();

                Cancellations::create([
                    'idTicket' => $venta['idTicket'],
                    'idUsuario' => $datos['idUsuario'],
                    'razon' => $datos['razon']
                ]);

                $mensaje = "El ticket se ha cancelado correctamente";
            }else{
                $errores = 1;
                $mensaje = "Han pasado los " . $general['minutosParaCancelar'] ." minutos de plazo para cancelar";
                //$mensaje = "minutos actual: $minutoActual, minutosticket: $minutoTicketJugado";
            }
            

        }else{
            $errores = 1;
            $mensaje = "El ticket no existe o ya han los 7 minutos de plazo para cancelar";
        }
    }else{
            $errores = 1;
            $mensaje = "El numero de ticket no es correcto";
    }


    return Response::json([
        'errores' => $errores,
        'mensaje' => $mensaje,
        'resta' => ($minutoActual - $minutoTicketJugado['minutes']),
        'minutoActual' => $minutoActual,
        'minutoTicketJugado' => $minutoTicketJugado
    ], 201);
});



Route::get('/premios', function(){
    // return TaskResource::collection(Task::all());

    // $datos = request()->validate([
    //     'datos.codigoBarra' => 'required',
    //     'datos.razon' => 'required',
    //     'datos.idUsuario' => 'required'
    // ])['datos'];


        $fecha = getdate();
        $fechaDesde = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
    

    
        $loterias = Lotteries::whereStatus(1)->get();

        $loterias = collect($loterias)->map(function($l) use($fechaDesde, $fechaHasta){
            $primera = null;
            $segunda = null;
            $tercera = null;
            $premios = Awards::whereBetween('created_at', array($fechaDesde , $fechaHasta))
                            ->where('idLoteria', $l['id'])
                            ->first();

            if($premios != null){
                $primera = $premios->primera;
                $segunda = $premios->segunda;
                $tercera = $premios->tercera;
            }
            return [
                    'id' => $l['id'],
                    'descripcion' => $l['descripcion'],
                    'abreviatura' => $l['abreviatura'],
                    'primera' => $primera,
                    'segunda' => $segunda,
                    'tercera' => $tercera
                ];
        });

    return Response::json([
        'loterias' => $loterias
    ], 201);
});


Route::post('/premios/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        //'datos.idLoteria' => 'required',
        //'datos.numerosGanadores' => 'required|min:2|max:6',
        'datos.idUsuario' => 'required',
        'datos.loterias' => 'required',
        'datos.idBanca' => 'required',
    ])['datos'];

    $fecha = getdate();

    $errores = 0;
    $mensaje = '';

    $idBanca = Branches::whereId($datos['idBanca'])->whereStatus(1)->first();
    if($idBanca == null){
        $idBanca = Branches::
            where(['status' => 1, 'idUsuario' => $datos['idUsuario']])
            ->first()->id;
            
    }else{
        $idBanca = $idBanca->id;
    }

foreach($datos['loterias'] as $l):

$numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];

    //Si uno de estos campos es nulo entonces eso quiere decir que esta loteria no se insertara, asi que pasaremos a la siguiente loteria
    if($l['primera'] == null || $l['segunda'] == null || $l['tercera'] == null)
        continue;

    if(!is_numeric($numerosGanadores)){
        return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
    }

    /************* VALIDAMOS DIAS DE LA LOTERIA ***************/
    $loteria = Lotteries::whereId($l['id'])->get()->first();

    $loteriaWday = $loteria->dias()->whereWday(getdate()['wday'])->get()->first();
    if($loteriaWday == null){
        return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $loteria['descripcion'] .' no abre este dia '], 201);
    }
    /************* END VALIDAMOS DIAS DE LA LOTERIA ***************/


    //if(is_numeric($datos['numerosGanadores'])){
        //Obtenemos la fecha actual y la hora de cierre de la loteria
        $fechaActual = getdate();
        // $horaCierre = $loteria->dias()->whereWday();
    
        // //Convertimos la hora de cierre en una fecha completa
        // $horaCierreFecha = getdate(strtotime($horaCierre));
    
    
        // //Convertimos la hora y minutos en segundo y despues lo sumamos
        // $sumHoraMinutosActual = ($fechaActual['hours'] *  3600) + ($fechaActual['minutes'] *  60);
        // $sumHoraMinutosCierre = ($horaCierreFecha['hours'] *  3600) + ($horaCierreFecha['minutes'] *  60);

        // if($sumHoraMinutosActual <  $sumHoraMinutosCierre){
        //     return Response::json(['errores' => 1,'mensaje' => 'La loteria aun no ha cerrado'], 201);
        // }

        //Si la variable $sumHoraMinutosActual es mayor que la variable $sumHoraMinutosCierre entonces la loteria ya ha cerrado por lo tanto se pueden guardar los premios
       // if($sumHoraMinutosActual >  $sumHoraMinutosCierre){
             $numeroGanador = Awards::where('idLoteria', $l['id'])
                ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->get()->first();
    
            //Si es diferente de nulo entonces existe, asi que debo actualizar los numeros ganadores
            if($numeroGanador != null){
                $numeroGanador['numeroGanador'] = $numerosGanadores;
                $numeroGanador['primera'] =  $l['primera'];
                $numeroGanador['segunda'] = $l['segunda'];
                $numeroGanador['tercera'] =  $l['tercera'];
                $numeroGanador->save();

                
                $mensaje = "Los numeros ganadores se han guardado correctamente";
            }else{
                Awards::create([
                    'idUsuario' => $datos['idUsuario'],
                    'idLoteria' => $l['id'],
                    'numeroGanador' => $numerosGanadores,
                    'primera' => $l['primera'],
                    'segunda' => $l['segunda'],
                    'tercera' => $l['tercera']
                ]);

                $mensaje = "Los numeros ganadores se han guardado correctamente";
            }
        // }else{
        //     $errores = 1;
        //     $mensaje = "La loteria aun no ha cerrado";
        // }
        // }else{
        //     $errores = 1;
        //     $mensaje = "Los numeros ganadores no son correctos";
        // }

        //Obtenemos todas las jugadas pertenecientes a dicha loteria y utilizamos un Foreach para actualizar premios de la tabla SalesDetails
        $jugadas = Salesdetails::where(['idLoteria' => $l['id']])
                    ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
                    ->orderBy('jugada', 'asc')
                    ->get();
        
                    // return Response::json(['errores' => 1,'mensaje' => $jugadas], 201);
        $c = 0;
        $colleccion = null;
        foreach($jugadas as $j){

            $j['premio'] = 0;
            $contador = 0;
            $busqueda1 = false;
            $busqueda2 = false;
            $busqueda3 = false;

            // return Response::json(['errores' => 1,'mensaje' => strlen($j['jugada'])], 201);
            if(strlen($j['jugada']) == 2){
                $busqueda = strpos($numerosGanadores, $j['jugada']);
                
                
                if(gettype($busqueda) == "integer"){
                    if($busqueda == 0) $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primera');
                    else if($busqueda == 2) $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('segunda');
                    else if($busqueda == 4) $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('tercera');
                }
                else
                    $j['premio'] = 0;

               

                //return Response::json(['busqueda' => $busqueda,'jugada' => $j['jugada'], 'premio' => $j['premio'], 'monto' => $j['monto'], 'segunda' => Payscombinations::where('idLoteria',$datos['idLoteria'])->value('segunda')], 201);
                
            }
            else if(strlen($j['jugada']) == 4){
               // return Response::json(['numGanador' => $numeroGanador['numeroGanador'],'juada' => substr('jean', 0, 2)], 201);
                $busqueda1 = strpos($numerosGanadores, substr($j['jugada'], 0, 2));
                $busqueda2 = strpos($numerosGanadores, substr($j['jugada'], 2, 2));

               

                //Primera y segunda
                if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer"){
                    if($busqueda1 == 0 && $busqueda2 == 2 || $busqueda2 == 0 && $busqueda1 == 2){
                        $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primeraSegunda');
                    }
                    //Primera y tercera
                    else if($busqueda1 == 0 && $busqueda2 == 4 || $busqueda2 == 0 && $busqueda1 == 4){
                        $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('primeraTercera');
                    }
                    //Segunda y tercera
                    else if($busqueda1 == 2 && $busqueda2 == 4 || $busqueda2 == 2 && $busqueda1 == 4){
                        $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('segundaTercera');
                    }
                }
                else $j['premio'] = 0;

                if($j['premio'] > 0)
                 {
                    $j['status'] = 1;
                    $j->save();
                 }
            }
            else if(strlen($j['jugada']) == 6){
                $contador = 0;
                $busqueda1 = strpos($numerosGanadores, substr($j['jugada'], 0, 2));
                $busqueda2 = strpos($numerosGanadores, substr($j['jugada'], 2, 2));
                $busqueda3 = strpos($numerosGanadores, substr($j['jugada'], 4, 2));

                if(gettype($busqueda1) == "integer" && gettype($busqueda2) == "integer" && gettype($busqueda3) == "integer"){
                    $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('tresNumeros');
                }
                else{
                    if(gettype($busqueda1) == "integer")
                        $contador++;
                    if(gettype($busqueda2) == "integer")
                        $contador++;
                    if(gettype($busqueda3) == "integer")
                        $contador++;
                    
                    //Si el contador es = 2 entonces hay premio
                    if($contador == 2)
                        $j['premio'] = $j['monto'] * Payscombinations::where(['idLoteria' => $l['id'], 'idBanca' => $idBanca])->value('dosNumeros');
                    else
                        $j['premio'] = 0;
                }
                
            }


            $j['status'] = 1;
            $j->save();


            if($c == 0){
                $colleccion = collect([
                    'busqueda_false' => gettype($busqueda),
                    'busqueda' => $busqueda,
                    'jugada' => $j['jugada'], 
                    'premio' => $j['premio'], 
                    'monto' => $j['monto'],
                    'contador' => $contador,
                    'busqueda1' => $busqueda1,
                    'busqueda2' => $busqueda2,
                    'busqueda3' => $busqueda3
                ]);
            }else{
                $colleccion->push([
                    'busqueda_false' => gettype($busqueda),
                    'busqueda' => $busqueda,
                    'jugada' => $j['jugada'], 
                    'premio' => $j['premio'], 
                    'monto' => $j['monto'],
                    'contador' => $contador,
                    'busqueda1' => $busqueda1,
                    'busqueda2' => $busqueda2,
                    'busqueda3' => $busqueda3
                ]);
            }

            $c++;
        }

    endforeach;



        $ventas = Sales::whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
        ->get();

        foreach($ventas as $v){
            $todas_las_jugadas = Salesdetails::where(['idVenta' => $v['id']])->count();
            $todas_las_jugadas_salientes = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->count();
            $cantidad_premios = Salesdetails::where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();

            if($todas_las_jugadas == $todas_las_jugadas_salientes)
            {
                if($cantidad_premios > 0)
                    $v['status'] = 2;
                else
                    $v['status'] = 3;

                $v->save();
            }
        }



    return Response::json([
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente',
        'colleccon' => $colleccion
    ], 201);
});

Route::get('/prueba', function(){
    // return TaskResource::collection(Task::all());
    

    //$fechaActual = date("d-m-Y H:i:00",time());
    // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
    
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
    

    $timezone = date_default_timezone_get();

    return Response::json([
        'ventas' => SalesResource::collection(Sales::whereId(12)->get()),
        'coleccion' => $loterias_seleccionadas,
        'bancas' => BranchesResource::collection($bancas),
        'busqueda' => strpos($cadena, $buscar),
        'fechaActual' => $fechaActual,
        'timezone' => $timezone
    ], 201);
});



Route::get('/loterias', function(){
    // return TaskResource::collection(Task::all());

    $fechaActual = strtotime(date("d-m-Y H:i:00",time()));
    // $fechaActual = strtotime($fechaActual['mday'] . ' ' . $fechaActual['month'].' '.$fechaActual['year'] . ' ' . time() );
    

    $cadena = "060829";
    $buscar = "99";

    

    return Response::json([
        'loterias' => LotteriesResource::collection( Lotteries::whereIn('status', [1,0])->get()),
        'dias' => Days::all(),
        'sorteos' => Draws::all(),
    ], 201);
});



Route::post('/loterias/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.id' => 'required',
        'datos.descripcion' => 'required',
        'datos.abreviatura' => 'required|min:1|max:4',
        'datos.status' => 'required',
        'datos.horaCierre' => 'required',
        'datos.sorteos' => 'required',

        // 'datos.primera' => 'required',
        // 'datos.segunda' => 'required',
        // 'datos.tercera' => 'required',
        // 'datos.primeraSegunda' => 'required',
        // 'datos.primeraTercera' => 'required',
        // 'datos.segundaTercera' => 'required',
        // 'datos.tresNumeros' => 'required',
        // 'datos.dosNumeros' => 'required',

        //'datos.dias' => 'required',
    ])['datos'];

    $errores = 0;
    $mensaje = '';

   
    $loteria = Lotteries::whereId($datos['id'])->get()->first();

    /********************* PAGOS COMBINACIONES ************************/
    //$combinaciones = Payscombinations::where('idLoteria', $datos['id'])->get()->first();
    /********************* END PAGOS COMBINACIONES ************************/

    if($loteria != null){
        $loteria['descripcion'] = $datos['descripcion'];
        $loteria['abreviatura'] = $datos['abreviatura'];
        $loteria['status'] = $datos['status'];
        $loteria['horaCierre'] = $datos['horaCierre'];
        $loteria->save();

       /********************* PAGOS COMBINACIONES ************************/
        // if($combinaciones != null){
        //     $combinaciones['primera'] = $datos['primera'];
        //     $combinaciones['segunda'] = $datos['segunda'];
        //     $combinaciones['tercera'] = $datos['tercera'];
        //     $combinaciones['primeraSegunda'] = $datos['primeraSegunda'];
        //     $combinaciones['primeraTercera'] = $datos['primeraTercera'];
        //     $combinaciones['segundaTercera'] = $datos['segundaTercera'];
        //     $combinaciones['tresNumeros'] = $datos['tresNumeros'];
        //     $combinaciones['dosNumeros'] = $datos['dosNumeros'];
        //     $combinaciones->save();
        // }else{
        //     Payscombinations::create([
        //         'idLoteria' => $loteria['id'],
        //         'primera' => $datos['primera'],
        //         'segunda' => $datos['segunda'],
        //         'tercera' => $datos['tercera'],
        //         'primeraSegunda' => $datos['primeraSegunda'],
        //         'primeraTercera' => $datos['primeraTercera'],
        //         'segundaTercera' => $datos['segundaTercera'],
        //         'tresNumeros' => $datos['tresNumeros'],
        //         'dosNumeros' => $datos['dosNumeros']
        //     ]);
        // }

        /********************* END PAGOS COMBINACIONES ************************/
        

        /********************* DIAS ************************/
        //Eliminamos los dias para luego agregarlos nuevamentes
        // $loteria->dias()->detach();
        // $dias = collect($datos['dias'])->map(function($d) use($loteria){
            
        //     return ['idDia' => $d['id'], 'idLoteria' => $loteria['id'] ];
        // });
        // $loteria->dias()->attach($dias);
        /********************* END DIAS ************************/

        $loteria->sorteos()->detach();
        $sorteos = collect($datos['sorteos'])->map(function($s) use($loteria){
            
            return ['idSorteo' => $s['id'], 'idLoteria' => $loteria['id'] ];
        });
        $loteria->sorteos()->attach($sorteos);

    }else{
        $loteria = Lotteries::create([
            'descripcion' => $datos['descripcion'],
            'abreviatura' => $datos['abreviatura'],
            'horaCierre' => $datos['horaCierre'],
            'status' => $datos['status'],
        ]);

        /********************* PAGOS COMBINACIONES ************************/
        // Payscombinations::create([
        //     'idLoteria' => $loteria['id'],
        //     'primera' => $datos['primera'],
        //     'segunda' => $datos['segunda'],
        //     'tercera' => $datos['tercera'],
        //     'primeraSegunda' => $datos['primeraSegunda'],
        //     'primeraTercera' => $datos['primeraTercera'],
        //     'segundaTercera' => $datos['segundaTercera'],
        //     'tresNumeros' => $datos['tresNumeros'],
        //     'dosNumeros' => $datos['dosNumeros']
        // ]);
        /********************* END PAGOS COMBINACIONES ************************/

        /********************* DIAS ************************/
        //Eliminamos los dias para luego agregarlos nuevamentes
        // $loteria->dias()->detach();
        // $dias = collect($datos['dias'])->map(function($d) use($loteria){
        //     return ['idDia' => $d['id'], 'idLoteria' => $loteria['id'] ];
        // });
        // $loteria->dias()->attach($dias);
        /********************* END DIAS ************************/

        $loteria->sorteos()->detach();
        $sorteos = collect($datos['sorteos'])->map(function($s) use($loteria){
            return ['idSorteo' => $s['id'], 'idLoteria' => $loteria['id'] ];
        });
        $loteria->sorteos()->attach($sorteos);
    }

    return Response::json([
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente'
    ], 201);
});



Route::get('/bloqueos', function(){
    // return TaskResource::collection(Task::all());

    // $datos = request()->validate([
    //     'datos.codigoBarra' => 'required',
    //     'datos.razon' => 'required',
    //     'datos.idUsuario' => 'required'
    // ])['datos'];


        $loterias = Lotteries::whereStatus(1)->get();
        $bancas = Branches::whereStatus(1)->get();


    return Response::json([
        'loterias' => LotteriesResource::collection($loterias),
        'bancas' => BranchesResource::collection($bancas),
        'sorteos' => Draws::all(),
        'dias' => Days::all()
    ], 201);
});


Route::post('/bloqueos/loteriasnuevo/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.loterias' => 'required',
        'datos.sorteos' => 'required',
        'datos.idUsuario' => 'required',
        'datos.bancas' => 'required',
        'datos.ckbDias' => 'required'
    ])['datos'];


        $loterias = collect($datos['loterias']);
        list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
            return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
        });

        $sorteos = collect($datos['sorteos']);
        list($sorteos_seleccionadas, $no) = $sorteos->partition(function($l){
            return $l['monto'] != null && $l['monto'] >= 0 && isset($l['monto']);
        });

        $dias = collect($datos['ckbDias']);
        list($dias_seleccionadas, $no) = $dias->partition(function($l){
            return $l['existe'] == true;
        });
       
    


foreach($datos['bancas'] as $banca):
    foreach($loterias_seleccionadas as $l):
       
        if(Branches::whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                continue;

               
       
        foreach($sorteos_seleccionadas as $s):
            if(Lotteries::whereId($l['id'])->first()->sorteos()->wherePivot('idSorteo', $s['id'])->first() == null)
                continue;

               

            foreach($dias_seleccionadas as $d):
                
                $bloqueo = Blockslotteries::where(
                        [
                            'idBanca' => $banca['id'], 
                            'idLoteria' => $l['id'], 
                            'idSorteo' => $s['id'],
                            'idDia' => $d['id']
                        ])->get()->first();

                if($bloqueo != null){
                    $bloqueo['monto'] = $s['monto'];
                    $bloqueo->save();
                }else{
                    Blockslotteries::create([
                        'idBanca' => $banca['id'],
                        'idLoteria' => $l['id'],
                        'idSorteo' => $s['id'],
                        'idDia' => $d['id'],
                        'monto' => $s['monto']
                    ]);
                }
            endforeach; //End foreach dias
        endforeach;//End foreach sorteos
        endforeach; //End foreahc loterias
    endforeach; //End foreach banca

    $loterias = Lotteries::whereStatus(1)->get();
        $bancas = Branches::whereStatus(1)->get();


    return Response::json([
        'loterias' => LotteriesResource::collection($loterias),
        'bancas' => BranchesResource::collection($bancas),
        'sorteos' => Draws::all(),
        'dias' => Days::all(),
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente'
    ], 201);
});


Route::post('/bloqueos/jugadasnuevo/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.loterias' => 'required',
        'datos.idUsuario' => 'required',
        'datos.bancas' => 'required',
        'datos.jugada' => 'required',
        'datos.monto' => 'required',
        'datos.fechaDesde' => 'required',
        'datos.fechaHasta' => 'required',
    ])['datos'];


        $loterias = collect($datos['loterias']);
        list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
            return $l['seleccionado'] == true && Lotteries::where(['id' => $l['id'], 'status' => 1])->first() != null;
        });

        $fechaDesde = getdate(strtotime($datos['fechaDesde']));
        $fechaHasta = getdate(strtotime($datos['fechaHasta']));
        $fecha = getdate();

        $loterias = Lotteries::whereStatus(1)->get();
        $bancas = Branches::whereStatus(1)->get();

foreach($datos['bancas'] as $banca):
    foreach($loterias_seleccionadas as $l):
       
        if(Branches::whereId($banca['id'])->first()->loterias()->wherePivot('idLoteria', $l['id'])->first() == null)
                continue;

               

                $bloqueo = Blocksplays::where(
                    [
                        'idBanca' => $banca['id'], 
                        'idLoteria' => $l['id'], 
                        'jugada' => $datos['jugada'],
                        'status' => 1
                    ])
                    ->where('fechaDesde', '<=', $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00')
                    ->where('fechaHasta', '>=', $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00')->first();


                if($bloqueo != null){
                    $bloqueo['monto'] = $datos['monto'];
                    $bloqueo->save();
                }else{
                    Blocksplays::create([
                        'idBanca' => $banca['id'],
                        'idLoteria' => $l['id'],
                        'idSorteo' => 1,
                        'jugada' => $datos['jugada'],
                        'montoInicial' => $datos['monto'],
                        'monto' => $datos['monto'],
                        'fechaDesde' => $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00',
                        'fechaHasta' => $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00',
                        'idUsuario' => $datos['idUsuario'],
                        'status' => 1
                    ]);
                }
               
      
        endforeach; //End foreahc loterias
    endforeach; //End foreach banca

    $loterias = Lotteries::whereStatus(1)->get();
        $bancas = Branches::whereStatus(1)->get();


    return Response::json([
        'loterias' => LotteriesResource::collection($loterias),
        'bancas' => BranchesResource::collection($bancas),
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente'
    ], 201);
});



Route::post('/bloqueos/loterias/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.idLoteria' => 'required',
        'datos.quiniela' => 'required',
        'datos.pale' => 'required',
        'datos.tripleta' => 'required'
    ])['datos'];


        $bloqueo = Blockslotteries::where(['idLoteria' => $datos['idLoteria'], 'idSorteo' => 1])->get()->first();

        if($bloqueo != null){
            $bloqueo['monto'] = $datos['quiniela'];
            $bloqueo->save();

            $bloqueo = Blockslotteries::where(['idLoteria' => $datos['idLoteria'], 'idSorteo' => 2])->get()->first();
            $bloqueo['monto'] = $datos['pale'];
            $bloqueo->save();

            $bloqueo = Blockslotteries::where(['idLoteria' => $datos['idLoteria'], 'idSorteo' => 3])->get()->first();
            $bloqueo['monto'] = $datos['tripleta'];
            $bloqueo->save();
        }else{
            Blockslotteries::create([
                'idLoteria' => $datos['idLoteria'],
                'idSorteo' => 1,
                'monto' => $datos['quiniela']
            ]);

            Blockslotteries::create([
                'idLoteria' => $datos['idLoteria'],
                'idSorteo' => 2,
                'monto' => $datos['pale']
            ]);
            Blockslotteries::create([
                'idLoteria' => $datos['idLoteria'],
                'idSorteo' => 3,
                'monto' => $datos['tripleta']
            ]);
        }

        $loterias = Lotteries::whereStatus(1)->get();
        $bancas = Branches::whereStatus(1)->get();


    return Response::json([
        'loterias' => LotteriesResource::collection($loterias),
        'bancas' => BranchesResource::collection($bancas),
        'sorteos' => Draws::all(),
        'dias' => Days::all(),
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente'
    ], 201);
});



Route::post('/bloqueos/jugadas/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.idLoteria' => 'required',
        'datos.jugada' => 'required',
        'datos.monto' => 'required',
        'datos.fechaDesde' => 'required',
        'datos.fechaHasta' => 'required',
        'datos.idUsuario' => 'required'
    ])['datos'];

    $fechaDesde = getdate(strtotime($datos['fechaDesde']));
    $fechaHasta = getdate(strtotime($datos['fechaHasta']));
    $fecha = getdate();

    $bloqueo = Blocksplays::where(
        [
            'idLoteria' => $datos['idLoteria'], 
            'jugada' => $datos['jugada'],
            'status' => 1
        ])
        ->where('fechaDesde', '<=', $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00')
        ->where('fechaHasta', '>=', $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00')->value('monto');


    if($bloqueo != null){
        $bloqueo['monto'] = $datos['quiniela'];
        $bloqueo->save();

        // if

        // $bloqueo = Stock::where(['idLoteria' => $datos['idLoteria'], 'jugada' => $datos['jugada']])
        //     ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
        //     ->get()->first();

        // if($bloqueo != null){
        //     $bloqueo['monto'] = 
        // }
   

       
    }else{
        Blocksplays::create([
            'idLoteria' => $datos['idLoteria'],
            'idSorteo' => 1,
            'jugada' => $datos['jugada'],
            'montoInicial' => $datos['monto'],
            'monto' => $datos['monto'],
            'fechaDesde' => $fechaDesde['year'].'-'.$fechaDesde['mon'].'-'.$fechaDesde['mday'] . ' 00:00:00',
            'fechaHasta' => $fechaHasta['year'].'-'.$fechaHasta['mon'].'-'.$fechaHasta['mday'] . ' 23:50:00',
            'idUsuario' => $datos['idUsuario'],
            'status' => 1
        ]);
    }


    return Response::json([
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente'
    ], 201);
});






Route::post('/bloqueos/jugadas/eliminar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.id' => 'required'
    ])['datos'];


    $bloqueo = Blocksplays::where(['id' => $datos['id'], 'status' => 1])
        ->get()->first();


    if($bloqueo != null){
        $bloqueo['status'] = 0;
        $bloqueo->save();

        

        return Response::json([
            'errores' => 0,
            'mensaje' => 'Se ha guardado correctamente'
        ], 201);
    }


    return Response::json([
        'errores' => 1,
        'mensaje' => 'El bloqueo jugada no existe'
    ], 201);
});





Route::get('/bancas', function(){
    // return TaskResource::collection(Task::all());

    // $datos = request()->validate([
    //     'datos.codigoBarra' => 'required',
    //     'datos.razon' => 'required',
    //     'datos.idUsuario' => 'required'
    // ])['datos'];


        $bancas = Branches::whereIn('status', array(0, 1))->get();


    return Response::json([
        'bancas' => BranchesResource::collection($bancas),
        'usuarios' => Users::whereIn('status', array(0, 1))->get(),
        'loterias' => LotteriesResource::collection(Lotteries::whereStatus(1)->has('sorteos')->get())
    ], 201);
});








Route::post('/bancas/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.id' => 'required',
        'datos.descripcion' => 'required',
        'datos.ip' => 'required|min:1|max:15',
        'datos.codigo' => 'required',
        'datos.idUsuario' => 'required',
        'datos.dueno' => 'required',
        'datos.localidad' => 'required',


        'datos.balanceDesactivacion' => '',
        'datos.limiteVenta' => 'required',
        'datos.descontar' => 'required',
        'datos.deCada' => 'required',
        'datos.minutosCancelarTicket' => 'required',
        'datos.piepagina1' => '',
        'datos.piepagina2' => '',
        'datos.piepagina3' => '',
        'datos.piepagina4' => '',
        'datos.status' => 'required',

        'datos.lunes' => '',
        'datos.martes' => '',
        'datos.miercoles' => '',
        'datos.jueves' => '',
        'datos.viernes' => '',
        'datos.sabado' => '',
        'datos.domingo' => '',

        'datos.comisiones' => 'required',
        'datos.pagosCombinaciones' => 'required',
        'datos.loteriasSeleccionadas' => 'required'
    ])['datos'];


    $errores = 0;
    $mensaje = '';

    
    $usuario = Users::whereId($datos['idUsuario'])->first();
    if(!$usuario->tienePermiso('Manejar bancas')){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'No tiene permisos para realizar esta accion'
        ], 201);
    }

   
    $banca = Branches::whereId($datos['id'])->get()->first();
    

    if($banca != null){

        if(Branches::where(['idUsuario'=> $datos['idUsuario'], 'status' => 1])->whereNotIn('id', [$banca->id])->first() != null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Este usuario ya tiene una banca registrada y solo se permite un usaurio por banca'
            ], 201);
        }
        
        $banca['descripcion'] = $datos['descripcion'];
        $banca['ip'] = $datos['ip'];
        $banca['codigo'] = $datos['codigo'];
        $banca['idUsuario'] = $datos['idUsuario'];
        $banca['dueno'] = $datos['dueno'];
        $banca['localidad'] = $datos['localidad'];
        $banca['limiteVenta'] = $datos['limiteVenta'];
        $banca['balanceDesactivacion'] = $datos['balanceDesactivacion'];
        $banca['descontar'] = $datos['descontar'];
        $banca['deCada'] = $datos['deCada'];
        $banca['minutosCancelarTicket'] = $datos['minutosCancelarTicket'];
        $banca['piepagina1'] = $datos['piepagina1'];
        $banca['piepagina2'] = $datos['piepagina2'];
        $banca['piepagina3'] = $datos['piepagina3'];
        $banca['piepagina4'] = $datos['piepagina4'];
        $banca['status'] = $datos['status'];
        $banca->save();

    }else{
        if(Branches::where(['idUsuario'=> $datos['idUsuario'], 'status' => 1])->count() > 0)
        {
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Este usuario ya tiene una banca registrada y solo se permite un usaurio por banca'
            ], 201);
        }
        $banca = Branches::create([
            'descripcion' => $datos['descripcion'],
            'ip' => $datos['ip'],
            'codigo' => $datos['codigo'],
            'idUsuario' => $datos['idUsuario'],
            'dueno' => $datos['dueno'],
            'localidad' => $datos['localidad'],
            'limiteVenta' => $datos['limiteVenta'],
            'balanceDesactivacion' => $datos['balanceDesactivacion'],
            'descontar' => $datos['descontar'],
            'deCada' => $datos['deCada'],
            'minutosCancelarTicket' => $datos['minutosCancelarTicket'],
            'piepagina1' => $datos['piepagina1'],
            'piepagina2' => $datos['piepagina2'],
            'piepagina3' => $datos['piepagina3'],
            'piepagina4' => $datos['piepagina4'],
            'status' => $datos['status']
        ]);


       
    }

    /********************* LOTERIAS ************************/
        //Eliminamos los dias para luego agregarlos nuevamentes
        $banca->loterias()->detach();
        //$loterias = $datos['loteriasSeleccionadas']
        //Creamos una coleccion de loterias
        $loterias = collect($datos['loteriasSeleccionadas']);
        //Obtenemos las loterias seleccionadas (status == true)
        list($loterias_seleccionadas, $no) = $loterias->partition(function($l){
            return $l['existe'] == true;
        });
        //Mapeamos la collecion para obtener los atributos idLoteria y idBanca
        $loterias_seleccionadas = collect($loterias_seleccionadas)->map(function($d) use($banca, $datos){
            return ['idLoteria' => $d['id'], 'idBanca' => $banca['id']];
        });
       
        //Guardamos loterias
        $banca->loterias()->attach($loterias_seleccionadas);

      /********************* DIAS ************************/
        //Eliminamos los dias para luego agregarlos nuevamentes
        $banca->dias()->detach();
        $dias = Days::all();
        $dias = collect($dias)->map(function($d) use($banca, $datos){
            switch ($d['descripcion']) {
                case 'Lunes':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["lunes"]["apertura"], 'horaCierre' => $datos["lunes"]["cierre"] ];
                    break;
                case 'Martes':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["martes"]["apertura"], 'horaCierre' => $datos["martes"]["cierre"] ];
                    break;
                case 'Miercoles':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["miercoles"]["apertura"], 'horaCierre' => $datos["miercoles"]["cierre"] ];
                    break;
                case 'Jueves':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["jueves"]["apertura"], 'horaCierre' => $datos["jueves"]["cierre"] ];
                    break;
                case 'Viernes':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["viernes"]["apertura"], 'horaCierre' => $datos["viernes"]["cierre"] ];
                    break;
                case 'Sabado':
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["sabado"]["apertura"], 'horaCierre' => $datos["sabado"]["cierre"] ];
                    break;
                
                default:
                    return ['idDia' => $d['id'], 'idBanca' => $banca['id'], 'horaApertura' => $datos["domingo"]["apertura"], 'horaCierre' => $datos["domingo"]["cierre"] ];
                    break;
            }
        });
       
        $banca->dias()->attach($dias);


        /********************* COMISIONES ************************/
        //Obtengo y guardo en un objeto los id de las loterias que han sido recibidas
        $idLoterias = collect($datos['comisiones']['loterias'])->map(function($id){
            return $id['id'];
        });
        //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
        // Commissions::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
        Commissions::where('idBanca', $banca['id'])->delete();
        foreach($datos['comisiones']['loterias'] as $l){
            if($banca->loterias()->wherePivot('idLoteria', $l['id'])->first() != null){
                Commissions::create([
                    'idBanca' => $banca['id'],
                    'idLoteria' => $l['id'],
                    'directo' => $l['comisiones']['directo'],
                    'pale' => $l['comisiones']['pale'],
                    'tripleta' => $l['comisiones']['tripleta'],
                ]);
            }
        }



         /********************* PAGOS COMBINACIONES ************************/
        //Obtengo y guardo en un objeto los id de las loterias que han sido recibidas
        $idLoterias = collect($datos['pagosCombinaciones']['loterias'])->map(function($id){
            return $id['id'];
        });
        //Eliminamos las loterias que no esten incluidas en las loterias que han sido recibidas
        // Payscombinations::where('idBanca', $banca['id'])->whereNotIn('idLoteria', $idLoterias)->delete();
        Payscombinations::where('idBanca', $banca['id'])->delete();
        foreach($datos['pagosCombinaciones']['loterias'] as $l){
            if($banca->loterias()->wherePivot('idLoteria', $l['id'])->first() != null){
                Payscombinations::create([
                    'idBanca' => $banca['id'],
                    'idLoteria' => $l['id'],
                    'primera' => $l['pagosCombinaciones']['primera'],
                    'segunda' => $l['pagosCombinaciones']['segunda'],
                    'tercera' => $l['pagosCombinaciones']['tercera'],
                    'primeraSegunda' => $l['pagosCombinaciones']['primeraSegunda'],
                    'primeraTercera' => $l['pagosCombinaciones']['primeraTercera'],
                    'segundaTercera' => $l['pagosCombinaciones']['segundaTercera'],
                    'tresNumeros' => $l['pagosCombinaciones']['tresNumeros'],
                    'dosNumeros' => $l['pagosCombinaciones']['dosNumeros']
                ]);
            }
        }


        
        

    return Response::json([
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente',
        'banca' => BranchesResource::collection(Branches::whereId($banca->id)->get()),
        'bancas' => BranchesResource::collection(Branches::whereIn('status', array(0, 1))->get())
    ], 201);
});



Route::post('/bancas/eliminar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.id' => 'required'
    ])['datos'];

    $errores = 0;
    $mensaje = 'Se ha guardado correctamente';

   
    $banca = Branches::whereId($datos['id'])->get()->first();
    

    if($banca != null){
        $banca['status'] = 2;
        $banca->save();

    }else{
        $errores = 1;
        $mensaje = 'La banca no existe';
    }

    return Response::json([
        'errores' => $errores,
        'mensaje' => $mensaje
    ], 201);
});










Route::post('/usuarios/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.idUsuario' => 'required',
        'datos.id' => 'required',
        'datos.nombres' => 'required',
        'datos.email' => 'required|email',
        'datos.usuario' => 'required',
        'datos.password' => '',
        'datos.confirmar' => '',
        'datos.permisos' => 'required',
        'datos.status' => 'required',
        'datos.idTipoUsuario' => 'required'
    ])['datos'];


    $usuario = Users::whereId($datos['idUsuario'])->first();
    if(!$usuario->tienePermiso('Manejar usuarios')){
        return Response::json([
            'errores' => 1,
            'mensaje' => 'No tiene permisos para realizar esta accion'
        ], 201);
    }

    $errores = 0;
    $mensaje = '';

    
    //Verificar si el usuario tiene permisos
    //$permiso = Users

   
    $usuario = Users::whereId($datos['id'])->get()->first();
    

    if($usuario != null){
        $usuario['nombres'] = $datos['nombres'];
        $usuario['email'] = $datos['email'];
        $usuario['usuario'] = $datos['usuario'];
        $usuario['idRole'] = $datos['idTipoUsuario'];
        $usuario['status'] = $datos['status'];

        if(!empty($datos['password']) && !empty($datos['confirmar'])){
            if($datos['password'] == $datos['confirmar']){
                $usuario['password'] = Crypt::encryptString($datos['password']);
            }
        }

        $id = Users::whereEmail($datos['email'])->first();
        if($id != null){
            if($usuario->id != $id->id){
                return Response::json([
                    'errores' => 1,
                    'mensaje' => 'El correo ya existe, elija uno diferente'
                ], 201);
            }
        }

        $id = Users::whereEmail($datos['usuario'])->first();
       if($id != null){
        if($usuario->id != $id->id){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'El usuario ya existe, elija uno diferente'
            ], 201);
        }
       }


        $usuario->save();

    }else{

        if(empty($datos['password']) && empty($datos['confirmar'])){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Contraseña no valida'
            ], 201);
        }

        if($datos['password'] != $datos['confirmar']){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'Contraseña no valida'
            ], 201);
        }
        
        if(Users::whereEmail($datos['email'])->first() != null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'El correo ya existe, elija uno diferente'
            ], 201);
        }
        if(Users::whereEmail($datos['usuario'])->first() != null){
            return Response::json([
                'errores' => 1,
                'mensaje' => 'El usuario ya existe, elija uno diferente'
            ], 201);
        }

        $usuario = Users::create([
            'nombres' => $datos['nombres'],
            'email' => $datos['email'],
            'usuario' => $datos['usuario'],
            'password' => Crypt::encryptString($datos['password']),
            'idRole' => $datos['idTipoUsuario'],
            'status' => $datos['status']
        ]);
       
    }

      /********************* PERMISOS ************************/
        //Eliminamos los PERMISOS para luego agregarlos nuevamentes
        $usuario->permisos()->detach();
        $permisos = collect($datos['permisos'])->map(function($d) use($usuario, $datos){
            return ['idPermiso' => $d['id'], 'idUsuario' => $usuario['id']];
        });
       
        $usuario->permisos()->attach($permisos);


       
        

    return Response::json([
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente'
    ], 201);
});











Route::get('/usuarios', function(){
    // return TaskResource::collection(Task::all());

    // $datos = request()->validate([
    //     'datos.codigoBarra' => 'required',
    //     'datos.razon' => 'required',
    //     'datos.idUsuario' => 'required'
    // ])['datos'];


     
    return Response::json([
        'usuarios' => UsersResource::collection(Users::whereIn('status', array(0, 1))->get()),
        'usuariosTipos' => RolesResource::collection(Roles::all()),
        'permisos' => Permissions::all()
    ], 201);
});




Route::get('/horarios', function(){
    // return TaskResource::collection(Task::all());

    // $datos = request()->validate([
    //     'datos.codigoBarra' => 'required',
    //     'datos.razon' => 'required',
    //     'datos.idUsuario' => 'required'
    // ])['datos'];


     
     
    return Response::json([
        'loterias' => LotteriesResource::collection(Lotteries::whereStatus(1)->get()),
        'dias' => Days::all()
    ], 201);
});



Route::post('/horarios/normal/guardar', function(){
    // return TaskResource::collection(Task::all());

    $datos = request()->validate([
        'datos.loterias' => 'required',
        'datos.idUsuario' => 'required'
    ])['datos'];

    // return Response::json([
    //     'errores' => 0,
    //     'mensaje' => $datos['loterias']
    // ], 201);


    /********************* DIAS ************************/
        //Eliminamos los dias para luego agregarlos nuevamentes
       
        foreach($datos['loterias'] as $d){
            $loteria = Lotteries::whereId($d["id"])->first();
            $loteria->dias()->detach();
            $loteria->save();
        }

        foreach($datos['loterias'] as $d){
            $loteria = Lotteries::whereId($d["id"])->first();

            if($d["lunes"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Lunes")->first()->id, 'horaApertura' => $d["lunes"]["apertura"], 'horaCierre' => $d["lunes"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }
            
            if($d["martes"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Martes")->first()->id, 'horaApertura' => $d["martes"]["apertura"], 'horaCierre' => $d["martes"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }

            if($d["miercoles"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Miercoles")->first()->id, 'horaApertura' => $d["miercoles"]["apertura"], 'horaCierre' => $d["miercoles"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }

            if($d["jueves"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Jueves")->first()->id, 'horaApertura' => $d["jueves"]["apertura"], 'horaCierre' => $d["jueves"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }

            if($d["viernes"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Viernes")->first()->id, 'horaApertura' => $d["viernes"]["apertura"], 'horaCierre' => $d["viernes"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }

            if($d["sabado"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Sabado")->first()->id, 'horaApertura' => $d["sabado"]["apertura"], 'horaCierre' => $d["sabado"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }

            if($d["domingo"]["status"] == 1){
                $horario = ['idLoteria' => $d['id'], 'idDia' => Days::whereDescripcion("Domingo")->first()->id, 'horaApertura' => $d["domingo"]["apertura"], 'horaCierre' => $d["domingo"]["cierre"]];
                $loteria->dias()->attach([$horario]);
            }
        }

        


     
    return Response::json([
        'errores' => 0,
        'mensaje' => "Se ha guardado correctamente"
    ], 201);
});







Route::post('/imagen/guardar', function(){
    // return TaskResource::collection(Task::all());
    //header('content-type: image/jpeg');

    $datos = request()->validate([
        'datos.imagen' => 'required',
        'lastModifiedDate' => '',
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
   $output_file = "fotoo.png";
   $file = fopen($output_file, "wb");
   $data = explode(',', $datos['imagen']);
   fwrite($file, base64_decode($data[1]));
   fclose($file);

    return Response::json([
        'errores' => 0,
        'mensaje' => 'Se ha guardado correctamente',
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
});
