<?php
namespace App\Classes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\transactions;
use App\Types;
use App\Days;
use App\Lotteries;
use App\Stock;
use App\Blocksplays;
use App\Blockslotteries;
use App\Draws;
use App\Awards;
use App\Sales;
use App\Salesdetails;
use App\Commissions;
use App\Settings;
use App\Coins;
use App\Logs;
use App\Branches;
use App\Idventatemporal;
use App\Users;
use App\Tickets;
use App\Frecuency;
use App\Amortization;
use Illuminate\Support\Facades\Hash;


use Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class Helper{
    static function saldo($id, $entidad = 1){
        $datos = Array("id" => $id, "entidad" => $entidad);

        $saldo_inicial = 0;

        if($datos["entidad"] == 1){
            $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])
                ->sum('debito');
            $credito =  transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])                
                ->sum('credito');
            //El debito desembolso es un fondo emitido para un prestamo desde esta entidad, osea, que es un dinero que sale
            // por lo tanto este se le suma al credito
            $debitoDesembolso =  transactions::where(
                    [
                        'idEntidad1'=> $datos["id"], 
                        'idTipoEntidad2' => $idTipoEntidad1->id, 
                        'status' => 1
                    ])
                    ->where('idTipo', Types::whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id)                
                    ->sum('debito');
            $saldo_inicial = $debito - ($credito + $debitoDesembolso);
        }else if($datos["entidad"] == 2){
            $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
            $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)                
                ->sum('debito');
            $credito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)                
                ->sum('credito');
            $saldo_inicial = $credito - $debito;
        }
        else if($datos["entidad"] == 3){
            $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1,
                    'idTipo' => $tipo->id
                ])
                ->where('idTipo', '=', $tipo->id)
                ->sum('debito');
            $credito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1,
                    'idTipo' => $tipo->id
                ])
                ->where('idTipo', '=', $tipo->id)
                ->sum('credito');
                $saldo_inicial = $debito - $credito;
        }

       return round($saldo_inicial, 2);
    }



    static function saldoPorFecha($id, $entidad = 1, $fechaHasta = null){
        $datos = Array("id" => $id, "entidad" => $entidad);
        $saldo_inicial = 0;
        $fecha = getdate();
   
        if($fechaHasta != null){
            $fecha = getdate(strtotime($fechaHasta));
            $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }else{
            $fechaHasta = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
            

            





        if($datos["entidad"] == 1){
            $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])                
                ->where('created_at', '<=', $fechaHasta)
                ->sum('debito');
            $credito =  transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])                
                ->where('created_at', '<=', $fechaHasta)
                ->sum('credito');
            //El debito desembolso es un fondo emitido para un prestamo desde esta entidad, osea, que es un dinero que sale
            // por lo tanto este se le suma al credito
            $debitoDesembolso =  transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad2' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->where('idTipo', Types::whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id)   
                ->where('created_at', '<=', $fechaHasta)             
                ->sum('debito');
            $saldo_inicial = $debito - ($credito + $debitoDesembolso);
        }else if($datos["entidad"] == 2){
            $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
            $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)
                ->where('created_at', '<=', $fechaHasta)
                ->sum('debito');
            $credito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)
                ->where('created_at', '<=', $fechaHasta)
                ->sum('credito');
            $saldo_inicial = $credito - $debito;
        }
        else if($datos["entidad"] == 3){
            $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1,
                    'idTipo' => $tipo->id
                ])
                ->where('idTipo', '=', $tipo->id)
                ->where('created_at', '<=', $fechaHasta)
                ->sum('debito');
            $credito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1,
                    'idTipo' => $tipo->id
                ])
                ->where('idTipo', '=', $tipo->id)
                ->where('created_at', '<=', $fechaHasta)
                ->sum('credito');
                $saldo_inicial = $debito - $credito;
        }

       return round($saldo_inicial, 2);
    }

    public function _sendSms($to, $codigoBarra, $sms = true)
    {
        $accountSid = config('twilio.TWILIO_SID');
        // $accountSid ='AC2380875a2809c90354752c05ab783704';
        $authToken = config('twilio.TWILIO_TOKEN');
        // $authToken = '6f48c6fcd85eac850dd032d2515ba79b';
        if($sms){
            $twilioNumber = config('twilio.TWILIO_SMS_NUMBER');
            $to ='+'. $to;
        }
        else{
            $twilioNumber ="whatsapp:" . config('twilio.TWILIO_WHATSAPP_NUMBER');
            $to ="whatsapp:" . '+'. $to;
        }

        $client = new Client($accountSid, $authToken);
        try {
            $client->messages->create(
                $to,
                [
                    "body" => "",
                    "from" => $twilioNumber,
                    'MediaUrl' => url('public/assets/ticket') . "/" . $codigoBarra . ".png"
                    //   On US phone numbers, you could send an image as well!
                    //  'mediaUrl' => $imageUrl
                ]
            );
            return array('errores' => 0, 'mensaje' => 'Message sent to ' . $twilioNumber);
            //Log::info('Message sent to ' . $twilioNumber);
        } catch (TwilioException $e) {
            // return 'Error ' . $e;
            return 'mensaje' . $e;
            Log::error(
                'Could not send SMS notification.' .
                ' Twilio replied with: ' . $e
            );
        }
    }


    static function existe_sesion()
    {
        if(!session()->has('idUsuario'))
        {
            return false;
        }

        return true;
    }

    function cerrar_session()
    {
        if(session()->has('idUsuario')){
            session()->forget('idUsuario');
            session()->forget('idBanca');
            session()->forget('permisos');
            
            redirect()->route('login');
        }
    }

    public static function determinarSorteo($jugada, $loteria){
        
        $idSorteo = 0;
  

    if(strlen($jugada) == 2){
        $idSorteo = 1;
    }
   else if(strlen($jugada) == 3){
        $idSorteo = DB::table('draws')->whereDescripcion("Pick 3 Straight")->first();
        if($idSorteo != null){
            $idSorteo = $idSorteo->id;
        }
   }
   else if(strlen($jugada) == 4){
        if(gettype(strpos($jugada, '+')) == "integer"){
            $idSorteo = DB::table('draws')->whereDescripcion("Pick 3 Box")->first();
            if($idSorteo != null){
                $idSorteo = $idSorteo->id;
            }
        }
        else{
            $sorteo = DB::table('draws')
                ->select('draws.id')
                ->join('draw_lottery', 'draws.id', '=', 'draw_lottery.idSorteo')
                ->where(['draw_lottery.idLoteria' => $loteria->id, 'draws.descripcion' => 'Super pale'])->first();
            $drawRelations = DB::table('drawsrelations')->where('idLoteriaPertenece', $loteria->id)->count();
            if($sorteo == null || $drawRelations <= 1)
                $idSorteo = 2;
            else if($sorteo != null || $drawRelations >= 2)
                $idSorteo = 4;
        }
   }
    else if(strlen($jugada) == 5){
            if(gettype(strpos($jugada, '+')) == "integer"){
                $idSorteo = DB::table('draws')->whereDescripcion("Pick 4 Box")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
            else if(gettype(strpos($jugada, '-')) == "integer"){
                $idSorteo = DB::table('draws')->whereDescripcion("Pick 4 Straight")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
    }
    else if(strlen($jugada) == 6){
            $idSorteo = 3;
    }


       return $idSorteo;
    }


    public function determinarSorteoViejo($jugada, $idLoteria){
        $loteria = Lotteries::whereId($idLoteria)->first();
        $idSorteo = 0;
  

    if(strlen($jugada) == 2){
        $idSorteo = 1;
    }
   else if(strlen($jugada) == 3){
        $idSorteo = Draws::whereDescripcion("Pick 3 Straight")->first();
        if($idSorteo != null){
            $idSorteo = $idSorteo->id;
        }
   }
   else if(strlen($jugada) == 4){
        if(gettype(strpos($jugada, '+')) == "integer"){
            $idSorteo = Draws::whereDescripcion("Pick 3 Box")->first();
            if($idSorteo != null){
                $idSorteo = $idSorteo->id;
            }
        }
        else if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
            $idSorteo = 2;
        else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
            $idSorteo = 4;
   }
    else if(strlen($jugada) == 5){
            if(gettype(strpos($jugada, '+')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 4 Box")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
            else if(gettype(strpos($jugada, '-')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 4 Straight")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
    }
    else if(strlen($jugada) == 6){
            $idSorteo = 3;
    }


       return $idSorteo;
    }

    public static function determinarSorteoDescripcion($jugada, $idLoteria){
        $loteria = Lotteries::whereId($idLoteria)->first();
        $idSorteo = "";
  

    if(strlen($jugada) == 2){
        $idSorteo = "Directo";
    }
   else if(strlen($jugada) == 3){
        $idSorteo = Draws::whereDescripcion("Pick 3 Straight")->first();
        if($idSorteo != null){
            $idSorteo = $idSorteo->descripcion;
        }
   }
   else if(strlen($jugada) == 4){
        if(gettype(strpos($jugada, '+')) == "integer"){
            $idSorteo = Draws::whereDescripcion("Pick 3 Box")->first();
            if($idSorteo != null){
                $idSorteo = $idSorteo->descripcion;
            }
        }
        else if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
            $idSorteo = "Pale";
        else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
            $idSorteo = "Super pale";
   }
    else if(strlen($jugada) == 5){
            if(gettype(strpos($jugada, '+')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 4 Box")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->descripcion;
                }
            }
            else if(gettype(strpos($jugada, '-')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 4 Straight")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->descripcion;
                }
            }
    }
    else if(strlen($jugada) == 6){
            $idSorteo = "Tripleta";
    }


       return $idSorteo;
    }


    static function agregarGuion($jugada, $idSorteo){
        $sorteo = Draws::whereId($idSorteo)->first();
        if($sorteo == null)
            return $jugada;
        
        if($sorteo->descripcion == 'Pick 3 Straight' || $sorteo->descripcion == 'Pick 4 Straight')
            $jugada .= 'S';
        else if($sorteo->descripcion == 'Pick 3 Box' || $sorteo->descripcion == 'Pick 4 Box')
            $jugada .= 'B';
        else if($sorteo->descripcion == 'Pale' || $sorteo->descripcion == 'Super pale')
            $jugada = substr($jugada, 0, 2) . '-' . substr($jugada, 2, 2);
        else if($sorteo->descripcion == 'Tripleta')
            $jugada = substr($jugada, 0, 2) . '-' . substr($jugada, 2, 2) . '-' . substr($jugada, 4, 2);

        return $jugada;
    }

    static function quitarUltimoCaracter($cadena, $idSorteo){
        $sorteo = DB::table('draws')->whereId($idSorteo)->first();
        if($sorteo == null){
            return $cadena;
        }
        else if($sorteo->descripcion == 'Pick 3 Box' || $sorteo->descripcion == 'Pick 4 Straight' || $sorteo->descripcion == 'Pick 4 Box'){
            if(strlen($cadena) == 0)
            return "";
            else{
                return substr($cadena, 0, strlen($cadena) - 1);
            }
        }

        return $cadena;
    }

    static function existenNumerosIdenticos($numeros){
        $contador = 0;
        if(strlen($numeros) > 0){
            for ($c1=0; $c1 < strlen($numeros); $c1++) { 
                for ($c2=0; $c2 < strlen($numeros); $c2++) { 
                    //Si $numeros[$c1] y $numeros[$c2] son iguales y los contador $c1 y $c2 son distintos
                    //esto quiere decir que existen numeros identicos en diferentes posiciones 
                    if($numeros[$c1] == $numeros[$c2] && $c1 != $c2)
                        $contador++;
                }
            }
        }
        return ($contador > 0) ? true : false;
    }

    static function contarNumerosIdenticos($numeros){
        $contador = 0;
        $contadorTotal = 0;
        $vecesEntro = 0;
        
  
        for ($c1=0; $c1 < strlen($numeros); $c1++) { 
            session()->forget('_'.$numeros[$c1]);
        }
        if(strlen($numeros) > 0){
            for ($c1=0; $c1 < strlen($numeros); $c1++) { 
                $contador = 0;
                for ($c2=0; $c2 < strlen($numeros); $c2++) { 
                    //Si $numeros[$c1] y $numeros[$c2] son iguales y los contador $c1 y $c2 son distintos
                    //esto quiere decir que existen numeros identicos en diferentes posiciones 
                    if($numeros[$c1] == $numeros[$c2])
                        $contador++;
                }

                if(session()->has('_'.$numeros[$c1]) == false && $contador > 1){
                    //El nombre de la sesion sera _1
                        session(['_'.$numeros[$c1] => $contador]);
                    
                }

                if($c1 + 1 == strlen($numeros)){
                    for ($c3=0; $c3 < strlen($numeros); $c3++) { 
                        if(session()->has('_'.$numeros[$c3]) == true){
                            //El nombre de la sesion sera _1
                            $contadorTotal += session('_'.$numeros[$c3]);

                            
                        }

                        session()->forget('_'.$numeros[$c3]);
                    }
                }
                
                
                
            }
        }
        return $contadorTotal;
        // return session()->has('_'."1") == false;
        // return   session('_'."1") . ':' . session('_'."2"). ':' . session('_'."3") . ':'. session('_'."4");
    }

    function montodisponible($jugada, $loteria, $idBanca){ 
        $fecha = getdate();
        $idSorteo = 0;
        $bloqueo = 0;
    
        $idDia = DB::table('days')->whereWday($fecha['wday'])->first()->id;
    
        
        $idSorteo = Helper::determinarSorteo($jugada, $loteria);

       $jugada = Helper::quitarUltimoCaracter($jugada, $idSorteo);
    
       $bloqueo = DB::table('stocks')->where([   
           'idLoteria' => $loteria->id, 
           'idBanca' => $idBanca, 
           'jugada' => $jugada,
           'idSorteo' => $idSorteo
        ])
       ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->value('monto');
       
    //Verificamos que la variable $stock no sea nula
    if($bloqueo == null){
        $bloqueo = DB::table('blocksplays')->where(
            [
                'idBanca' => $idBanca,
                'idLoteria' => $loteria->id, 
                'jugada' => $jugada,
                'idSorteo' => $idSorteo,
                'status' => 1
            ])
            ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
            ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->value('monto');
    
        if($bloqueo == null){
            $bloqueo = DB::table('blockslotteries')->where([
                'idBanca' => $idBanca, 
                'idLoteria' => $loteria->id, 
                'idDia' => $idDia,
                'idSorteo' => $idSorteo
            ])->value('monto');
        }
    }
    
       
    
       if($bloqueo == null) $bloqueo = 0;
    
        return $bloqueo;
    }

    function montodisponibleViejo($jugada, $idLoteria, $idBanca){ 
        $fecha = getdate();
        $idSorteo = 0;
        $bloqueo = 0;
    
        $idDia = Days::whereWday($fecha['wday'])->first()->id;
    
        $loteria = Lotteries::whereId($idLoteria)->first();
    
       if(strlen($jugada) == 2){
            $idSorteo = 1;
       }
       else if(strlen($jugada) == 3){
            $idSorteo = Draws::whereDescripcion("Pick 3 Straight")->first();
            if($idSorteo != null){
                $idSorteo = $idSorteo->id;
            }
       }
       else if(strlen($jugada) == 4){
            if(gettype(strpos($jugada, '+')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 3 Box")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
            else if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
                $idSorteo = 2;
            else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
                $idSorteo = 4;
       }
       else if(strlen($jugada) == 5){
            if(gettype(strpos($jugada, '+')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 4 Box")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
            else if(gettype(strpos($jugada, '-')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Pick 4 Straight")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
       }
       else if(strlen($jugada) == 6){
            $idSorteo = 3;
       }

       $jugada = Helper::quitarUltimoCaracter($jugada, $idSorteo);
    
       $bloqueo = Stock::where([   
           'idLoteria' => $idLoteria, 
           'idBanca' => $idBanca, 
           'jugada' => $jugada,
           'idSorteo' => $idSorteo
        ])
       ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->value('monto');
       
    //Verificamos que la variable $stock no sea nula
    if($bloqueo == null){
        $bloqueo = Blocksplays::where(
            [
                'idBanca' => $idBanca,
                'idLoteria' => $idLoteria, 
                'jugada' => $jugada,
                'idSorteo' => $idSorteo,
                'status' => 1
            ])
            ->where('fechaDesde', '<=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00')
            ->where('fechaHasta', '>=', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00')->value('monto');
    
        if($bloqueo == null){
            $bloqueo = Blockslotteries::where([
                'idBanca' => $idBanca, 
                'idLoteria' => $idLoteria, 
                'idDia' => $idDia,
                'idSorteo' => $idSorteo
            ])->value('monto');
        }
    }
    
       
    
       if($bloqueo == null) $bloqueo = 0;
    
        return $bloqueo;
    }


    function toSecuencia($idTicket){
        $str = $idTicket;
        $pad = "000000000";
        $ans = substr($pad, 0, strlen($pad) - strlen($str)) . $str;
        return $ans;
    }

    static function isNumber($number){
        $c = true;
        try {
            //code...
            number_format($number);
        } catch (\Throwable $th) {
            //throw $th;
            $c = false;
        }

        return $c;
    }

    static function loteriaTienePremiosRegistradosHoy($idLoteria){
            $fechaActual = getdate();
            $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
            $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
            
            $premios = Awards::where('idLoteria', $idLoteria)
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get()->first();

            return ($premios != null) ? true : false;
    }


    static function ventasPorBanca($idBanca, $fechaInicial = null, $fechaFinal = null){
        $fecha = getdate();
   
        if($fechaInicial != null && $fechaFinal != null){
            $fecha = getdate(strtotime($fechaInicial));
            $fechaF = getdate(strtotime($fechaFinal));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
            

            
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        return round(Sales::whereIn('id', $idVentas)->sum('total'), 2);
    }

    static function descuentosPorBanca($idBanca, $fechaInicial = null, $fechaFinal = null){
        $fecha = getdate();
   
        if($fechaInicial != null && $fechaFinal != null){
            $fecha = getdate(strtotime($fechaInicial));
            $fechaF = getdate(strtotime($fechaFinal));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
            

            
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        return round(Sales::whereIn('id', $idVentas)->sum('descuentoMonto'), 2);
    }

    static function premiosPorBanca($idBanca, $fechaInicial = null, $fechaFinal = null){
        $fecha = getdate();
   
        if($fechaInicial != null && $fechaFinal != null){
            $fecha = getdate(strtotime($fechaInicial));
            $fechaF = getdate(strtotime($fechaFinal));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
            

            
        if($idBanca == 0){
            $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        return round(Salesdetails::whereIn('idVenta', $idVentas)->sum('premio'), 2);
    }

    static function ticketsPorBanca($idBanca, $fechaInicial = null, $fechaFinal = null){
        $fecha = getdate();
   
        if($fechaInicial != null && $fechaFinal != null){
            $fecha = getdate(strtotime($fechaInicial));
            $fechaF = getdate(strtotime($fechaFinal));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
     
        $tickets = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
        ->whereNotIn('status', [0,5])
        ->where('idBanca', $idBanca)
        ->count();
    
    
        

        return $tickets;
    }

    static function ticketsPendientesPorBanca($idBanca, $fechaInicial = null, $fechaFinal = null){
        $fecha = getdate();
   
        if($fechaInicial != null && $fechaFinal != null){
            $fecha = getdate(strtotime($fechaInicial));
            $fechaF = getdate(strtotime($fechaFinal));
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fechaF['year'].'-'.$fechaF['mon'].'-'.$fechaF['mday'] . ' 23:50:00';
        }else{
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
     
        $tickets = Sales::whereBetween('created_at', array($fechaInicial, $fechaFinal))
        ->whereNotIn('status', [0,5])
        ->where(['idBanca' => $idBanca, 'status' => 1])
        ->count();
    
    
        

        return $tickets;
    }

    static function comisionesPorBanca($idBanca, $fechaInicial = null, $fechaFinal = null){
        if($fechaInicial == null and $fechaFinal == null){
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
        
        $comisionesMonto = 0;
            $datosComisiones = Commissions::where('idBanca', $idBanca)->get();
            $idVentasDeEstaBanca = Sales::select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $idBanca)->whereNotIn('status', [0,5])->get();
            $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
                return $id->id;
            });
            foreach($datosComisiones as $d){
                $loteria = Lotteries::whereId($d['idLoteria'])->first();
                if($loteria == null)
                    continue;

                if($d['directo'] > 0){
                    $sorteo = Draws::whereDescripcion('Directo')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pale'] > 0){
                    $sorteo = Draws::whereDescripcion('Pale')->first();
                    if($sorteo != null && $loteria->sorteoExiste($sorteo->id) == true){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pale'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['tripleta'] > 0){
                    $sorteo = Draws::whereDescripcion('Tripleta')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['tripleta'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['superPale'] > 0){
                    $sorteo = Draws::whereDescripcion('Super pale')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['superPale'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick3Straight'] > 0){
                    $sorteo = Draws::whereDescripcion('Pick 3 Straight')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick3Straight'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick3Box'] > 0){
                    $sorteo = Draws::whereDescripcion('Pick 3 Box')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick3Box'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick4Straight'] > 0){
                    $sorteo = Draws::whereDescripcion('Pick 4 Straight')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick4Straight'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick4Box'] > 0){
                    $sorteo = Draws::whereDescripcion('Pick 4 Box')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick4Box'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

            }

        return round($comisionesMonto, 2);
    }


    static function cambiarComisionesATickets($idBanca, $fechaInicial = null, $fechaFinal = null){
        if($fechaInicial == null && $fechaFinal == null){
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }

        
        $idVentasDeEstaBanca = Sales::select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $idBanca)->whereNotIn('status', [0,5])->get();
        $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
            return $id->id;
        });
            $datosComisiones = Commissions::where('idBanca', $idBanca)->get();
            
            foreach($datosComisiones as $d){
                $loteria = Lotteries::whereId($d['idLoteria'])->first();
                if($loteria == null)
                    continue;

                

                if($d['directo'] >= 0){
                    $sorteo = Draws::whereDescripcion('Directo')->first();
                    if($sorteo != null){

                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['directo'] / 100) * $s['monto'];
                            $s->save();
                        }
                      
                    }
                }

                if($d['pale'] >= 0){
                    $sorteo = Draws::whereDescripcion('Pale')->first();
                    if($sorteo != null && $loteria->sorteoExiste($sorteo->id) == true){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pale'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['tripleta'] > 0){
                    $sorteo = Draws::whereDescripcion('Tripleta')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['tripleta'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['superPale'] >= 0){
                    $sorteo = Draws::whereDescripcion('Super pale')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['superPale'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick3Straight'] >= 0){
                    $sorteo = Draws::whereDescripcion('Pick 3 Straight')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick3Straight'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick3Box'] > 0){
                    $sorteo = Draws::whereDescripcion('Pick 3 Box')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick3Box'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick4Straight'] >= 0){
                    $sorteo = Draws::whereDescripcion('Pick 4 Straight')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick4Straight'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick4Box'] > 0){
                    $sorteo = Draws::whereDescripcion('Pick 4 Box')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick4Box'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

            }
    }

    static function comisionesPorLoteria($idBanca, $fechaInicial = null, $fechaFinal = null){
      
        if($fechaInicial == null and $fechaFinal == null){
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }

        $loterias = Lotteries::
                            selectRaw('
                                id, 
                                descripcion, 
                                (select sum(sd.monto) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and  s.idBanca = ? and s.created_at between ? and ?) as ventas,
                                (select sum(sd.premio) from salesdetails as sd inner join sales as s on s.id = sd.idVenta where s.status != 0 and sd.idLoteria = lotteries.id and s.idBanca = ? and s.created_at between ? and ?) as premios,
                                (select substring(numeroGanador, 1, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as primera,
                                (select substring(numeroGanador, 3, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as segunda,
                                (select substring(numeroGanador, 5, 2) from awards where idLoteria = lotteries.id and created_at between ? and ?) as tercera
                                ', [$idBanca, $fechaInicial, $fechaFinal, //Parametros para ventas
                                    $idBanca, $fechaInicial, $fechaFinal, //Parametros para premios
                                    $fechaInicial, $fechaFinal, //Parametros primera
                                    $fechaInicial, $fechaFinal, //Parametros segunda
                                    $fechaInicial, $fechaFinal //Parametros tercera
                                    ])
                            ->where('lotteries.status', '=', '1')
                            ->get();

                    $loterias = collect($loterias)->map(function($d) use($idBanca, $fechaInicial, $fechaFinal){
                        $datosComisiones = Commissions::where(['idBanca' => $idBanca, 'idLoteria' => $d['id']])->first();
                        $comisionesMonto = 0;
                        $idVentasDeEstaBanca = Sales::select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $idBanca)->whereNotIn('status', [0,5])->get();
                        $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
                            return $id->id;
                        });
                        if($datosComisiones['directo'] > 0){
                            $sorteo = Draws::whereDescripcion('Directo')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['directo'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
        
                        if($datosComisiones['pale'] > 0){
                            $sorteo = Draws::whereDescripcion('Pale')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['pale'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
        
                        if($datosComisiones['tripleta'] > 0){
                            $sorteo = Draws::whereDescripcion('Tripleta')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['tripleta'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
        
                        if($datosComisiones['superPale'] > 0){
                            $sorteo = Draws::whereDescripcion('Super pale')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['superPale'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }

                        if($datosComisiones['pick3Straight'] > 0){
                            $sorteo = Draws::whereDescripcion('Pick 3 Straight')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['pick3Straight'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }

                        if($datosComisiones['pick3Box'] > 0){
                            $sorteo = Draws::whereDescripcion('Pick 3 Box')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['pick3Box'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }

                        if($datosComisiones['pick4Straight'] > 0){
                            $sorteo = Draws::whereDescripcion('Pick 4 Straight')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['pick4Straight'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }

                        
                        if($datosComisiones['pick4Box'] > 0){
                            $sorteo = Draws::whereDescripcion('Pick 4 Box')->first();
                            if($sorteo != null){
                                //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                                $comisionesMonto += ($datosComisiones['pick4Box'] / 100) * Salesdetails::whereBetween('created_at', array($fechaInicial, $fechaFinal))
                                    ->whereIn('idVenta', $idVentasDeEstaBanca)
                                    ->where(['idLoteria' => $datosComisiones['idLoteria'], 'idSorteo' => $sorteo->id])
                                    ->sum('monto');
                            }
                        }
                        $comisionesMonto = $comisionesMonto;
                        if($d->ventas == null)
                            $d->ventas = 0;
                        if($d->premios == null)
                            $d->premios = 0;
                        if($d->primera == null)
                            $d->primera = "";
                        if($d->segunda == null)
                            $d->segunda = "";
                        if($d->tercera == null)
                            $d->tercera = "";
                        return ['id' => $d->id, 'descripcion' => $d->descripcion, 'comisiones' => round($comisionesMonto, 2), 'ventas' => $d->ventas, 'premios' => $d->premios, 'primera' => $d->primera, 'segunda' => $d->segunda, 'tercera' => $d->tercera, 'neto' => ($d->ventas) - ((int)$d->premios + $comisionesMonto)];
                    });
            return $loterias;
    }



    static function comision($idBanca, $idLoteria, $idSorteo, $monto){
      
        $comision = 0;
       
        $datosComisiones = Commissions::where(['idBanca' => $idBanca, 'idLoteria' => $idLoteria])->first();
        $sorteo = Draws::whereId($idSorteo)->first();
        if($sorteo == null)
            return 0;

        if($sorteo->descripcion == "Directo"){
            if($datosComisiones['directo'] > 0){
                $comision = ($datosComisiones['directo'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Pale"){
            if($datosComisiones['pale'] > 0){
                $comision = ($datosComisiones['pale'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Tripleta"){
            if($datosComisiones['tripleta'] > 0){
                $comision = ($datosComisiones['tripleta'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Super pale"){
            if($datosComisiones['superPale'] > 0){
                $comision = ($datosComisiones['superPale'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Pick 3 Straight"){
            if($datosComisiones['pick3Straight'] > 0){
                $comision = ($datosComisiones['pick3Straight'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Pick 3 Box"){
            if($datosComisiones['pick3Box'] > 0){
                $comision = ($datosComisiones['pick3Box'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Pick 4 Straight"){
            if($datosComisiones['pick4Straight'] > 0){
                $comision = ($datosComisiones['pick4Straight'] / 100) * $monto;
            }
        }
        else if($sorteo->descripcion == "Pick 4 Box"){
            if($datosComisiones['pick4Box'] > 0){
                $comision = ($datosComisiones['pick4Box'] / 100) * $monto;
            }
        }
        
        return $comision;
    }


    static function decimalesDelMontoJugadoSonValidos($monto, $loteria, $sorteo){
        $monto = strval($monto);

        
        $montoValido = false;
        //Validamos de que el monto sea decimal para ello verificamos si existe un punto en el monto
        if(gettype(strpos($monto, '.')) == "integer"){
            //Validamos de que la moneda seleccionada permita decimales
            $settings = Settings::find(1);
            $moneda = Coins::whereId($settings->idMoneda)->first();
            if($moneda->permiteDecimales != true)
                return false;

            if($loteria != null){
                if($sorteo->descripcion == "Pick 3 Box" || $sorteo->descripcion == "Pick 3 Straight" || $sorteo->descripcion == "Pick 4 Straight" || $sorteo->descripcion == "Pick 4 Box"){
                    if($monto == "0.50"){
                        $montoValido = true;
                    }
                }
            }
            
        }else{
            $montoValido = true;
        }
        
        return $montoValido;
    }

    static function loteriasOrdenadasPorHoraCierre($usuario, $retornarSoloAbiertas = false){
        $loterias = Lotteries::whereStatus(1)->get();
        $loterias = collect($loterias);
        $idDia = Days::whereWday(getdate()['wday'])->first()->id;
        list($loterias, $no) = $loterias->partition(function($l) use($usuario, $retornarSoloAbiertas){
            $loteria = Lotteries::whereId($l['id'])->first();
            
            //Si puede jugar fuera de horario entonces solo nos retornamos las loterias que no tengan premios registrados
            if($usuario->tienePermiso('Jugar fuera de horario') && $retornarSoloAbiertas == false)
                return Helper::loteriaTienePremiosRegistradosHoy($loteria->id) != true;
            else
                return $loteria->cerrada() != true &&  Helper::loteriaTienePremiosRegistradosHoy($loteria->id) != true;
        });

        $idLoteriasAbiertas = collect($loterias)->map(function($l){
            return $l['id'];
        });

         $loterias = 
            Lotteries::
            join('day_lottery', 'day_lottery.idLoteria', '=', 'lotteries.id')
            ->whereIn('lotteries.id', $idLoteriasAbiertas)
            ->where('day_lottery.idDia', $idDia)
            ->orderBy('day_lottery.horaCierre', 'asc')
            ->get();

            $loterias = collect($loterias)->map(function($l){
                return ['id' => $l['idLoteria'], 'descripcion' => $l['descripcion'], 'abreviatura' => $l['abreviatura'], 'horaCierre' => $l['horaCierre']];
            });

        return $loterias;
    }


    //Debemos verificar si todas las jugadas han sido pagadas
    static function verificarTicketHaSidoPagado($idVenta){
        $jugadasQueAunEstanPendiente = Salesdetails::where('idVenta', $idVenta)->whereStatus(0)->count();
        if($jugadasQueAunEstanPendiente > 0){
            return false;
        }
        $jugadas = Salesdetails::where('idVenta', $idVenta)->get();

        $montoPremios = 0;
        $montoPagado = 0;
        foreach($jugadas as $j){
            $montoPremios += $j['premio'];
            if($j['pagado'] == true){
                $montoPagado += $j['premio'];
            }
            
        }

        return ($montoPremios > $montoPagado) ? false : true;
    }


    static function pagar($idVenta, $idUsuario, $idBanca = null){
        
        $venta = Sales::whereId($idVenta)->whereNotIn('status', [0,5])->first();
        if($venta == null){
            return false;
        }
        $jugadas = Salesdetails::where(['idVenta' => $idVenta, 'pagado' => 0])->where('premio', '>', 0)->get();
        $jugadasQueAunEstanPendiente = Salesdetails::where('idVenta', $idVenta)->whereStatus(0)->count();
        $seMarcaronJugadasComoPagadas = false;
        $idTicket = $venta->idTicket;

        $montoPremios = 0;
        $montoPagado = 0;
        if($jugadas != null && count($jugadas) > 0){
            foreach($jugadas as $j){
                $j['pagado'] = 1;
                $j->save();
                $seMarcaronJugadasComoPagadas = true;
                
                Logs::create([
                    'idBanca' => Helper::getIdBanca($idUsuario, $idBanca),
                    'idUsuario' => $idUsuario,
                    'tabla' => 'salesdetails',
                    'idRegistroTablaAccion' => $j['id'],
                    'accion' => 'update',
                    'campo' => 'pagado',
                    'valor_viejo' => '0',
                    'valor_nuevo' => '1'
                ]);
            }

            if($jugadasQueAunEstanPendiente > 0){
                if($seMarcaronJugadasComoPagadas){
                     //Generamos y guardamos codigo de barra
                     $codigoBarraCorrecto = false;
                    while($codigoBarraCorrecto != true){
                        // $codigoBarra = $faker->isbn10;
                        $codigoBarra = rand(1111111111, getrandmax());
                        //return 'codiog: ' . $codigoBarra . ' faker: ' . $faker->isbn10;
                        //Verificamos de que el codigo de barra no exista
                        if(Tickets::where('codigoBarra', $codigoBarra)->get()->first() == null){
                            if(is_numeric($codigoBarra)){
                                $ticket = Tickets::whereId($idTicket)->first();
                                $ticket['codigoBarraAntiguo'] = $ticket['codigoBarra'];
                                $ticket['codigoBarra'] = $codigoBarra;
                                $ticket->save();
                                $codigoBarraCorrecto = true;
                                break;
                            }
                        }
                    }
                }
            }
        }else{
            return false;
        }
        

        return $jugadas;
    }


    public static function getIdBanca($idUsuario, $idBanca = null){
        if($idBanca != null){
            $idBanca = Branches::where(['id' => $idBanca, 'status' => 1])->first();
            if($idBanca != null)
                $idBanca = $idBanca->id;
        }else{
            
            $idBanca = Branches::where(['idUsuario' => $idUsuario, 'status' => 1])->first();
            if($idBanca != null)
                $idBanca = $idBanca->id;
            else{
                $u = Users::whereId($idUsuario)->first();
                //Si el usuario no tiene banca entonces verificamos si tiene permiso para jugar como cualquier banca para retornar el id de la primera banca activa
                if($u->tienePermiso("Jugar como cualquier banca") == true){
                    $idBanca = Branches::where(['status' => 1])->first();
                    if($idBanca != null){
                        $idBanca = $idBanca->id;
                    }
                }
                
            }
        }

        return $idBanca;
    }


    //Esta funcion apartara el siguiente idVenta
    public static function createIdVentaTemporal($idBanca){
        $siguienteIdVenta = Sales::max('id');
        if($siguienteIdVenta == null)
            $siguienteIdVenta = 0;
        $siguienteIdVenta++;

        //Hasing laravel https://laravel.com/docs/5.8/hashing
        $id = Idventatemporal::where(['idVenta' => $siguienteIdVenta])->first();
        if($id != null){
            if($id->idBanca == $idBanca)
                return $id->idVentaHash;
            else{
                $siguienteIdVenta = Idventatemporal::max('id');
                if($siguienteIdVenta == null)
                    $siguienteIdVenta = 0;
                $siguienteIdVenta++;
            }
        }

        $siguienteIdVentaHash = Hash::make($siguienteIdVenta);
        $id = Idventatemporal::create([
            'idBanca' => $idBanca,
            'idVenta' => $siguienteIdVenta,
            'idVentaHash' => $siguienteIdVentaHash
        ]);

        return $id->idVentaHash;
    }


    public static function getIdVentaTemporal($idVentaHash){
        $id = Idventatemporal::where('IdVentaHash', $idVentaHash)->first();
        if($id != null){
            return $id->idVenta;
        }

        return 0;
    }

    public static function borrarVentaErronea($venta){
        if($venta != null){
            $idTicket = $venta->idTicket;
            Salesdetails::where('idVenta', $venta->id)->delete();
            $venta->delete();
            Tickets::whereId($idTicket)->delete();
        }
    }
   

    public static function getVentasDeHoy($idBanca){
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

        return $ventas;
    }

    public static function getJugadasPertenecientesALoteria($idLoteria, $jugadas){
        $c = 0;
        $colleccion = null;
        foreach($jugadas as $j){
            if($idLoteria == $j['idLoteria']){
                 if($colleccion == null){
                    $colleccion = collect([
                        [
                            'idLoteria' => $idLoteria,
                            'jugada' => $j['jugada'],
                            'monto' => $j['monto'], 
                        ]
                    ]);
                }else{
                    $colleccion->push([
                        'idLoteria' => $idLoteria,
                        'jugada' => $j['jugada'],
                        'monto' => $j['monto'],
                    ]);
                }

                $c++;
            }
        }

        return $colleccion;
    }

    public static function getLoterias($jugadas){
        $c = 0;
        $colleccion = null;
        foreach($jugadas as $j){
            
                 if($c == 0){
                    $colleccion = collect([
                        ['id' => $j['idLoteria']]
                    ]);
                }else{
                    if(!Helper::existeLoteria($j['idLoteria'], $colleccion))
                        $colleccion->push([
                            'id' => $j['idLoteria']
                        ]);
                }

                $c++;
            
        }

        return $colleccion;
    }
    
    public static function existeLoteria($idLoteria, $collect){
        foreach($collect as $c){
            if($c['id'] == $idLoteria)
                return true;
        }

        return false;
    }

    public static function sumarTotalAmortizacion($collection, $cuotaFinal){
        $total = 0;
        foreach($collection as $c){
            $total += $c['amortizacion'];
        }

        $total += $cuotaFinal;

        return $total;
    }

    public static function amortizar($montoPrestado, $montoCuotas, $numeroCuotas, $tasaInteres, $idFrecuencia, $fechaInicio, $insertarColumnaCero = false, $incluirFechaInicio = false){
        //Aqui utilice el metodo americano con la unica exception de que el interes lo calculo en base al monto de la cuota y no en base al monto del prestamo saldado
        $frecuencia = Frecuency::whereId($idFrecuencia)->first();
        $colleccion = null;
        $idTipoAmortizacion = 0;
        $montoCuotaPrimeraCuota = 0;
        if($montoCuotas == 0 || $montoCuotas == null){
            $montoCuotas = $montoPrestado / $numeroCuotas;
            $idTipoAmortizacion = Types::whereDescripcion('Campo numeroCuotas, ya sea con tasaInteres o no')->first()->id;
        }
        else if($montoCuotas > 0 && $numeroCuotas > 0 && ($tasaInteres == 0 || $tasaInteres == null)){
            $idTipoAmortizacion = Types::whereDescripcion('Campo montoCuotas y numeroCuotas, se calcula la tasaInteres automatico')->first()->id;            
            $valorPresente = $montoPrestado;
            $valorFuturo = $montoCuotas * $numeroCuotas;
            if($valorFuturo > $valorPresente){
                $tasaInteres = ($valorFuturo - $valorPresente) / $valorPresente;
                $tasaInteres = round($tasaInteres, 2);
                //Aqui le restamos el interes a la cuota porque mas adelante se le suma nuevamente el interes
                // $montoCuotas = $montoCuotas - ($montoCuotas * $tasaInteres);
                $tasaInteres = $tasaInteres * 100;
                //Como ya obtuvimos el interes entonces ahora vamos a dividir el monto prestado sobre las cuotas y asi obtendremos el nuevo $montoCuotas
                $montoCuotas = round($montoPrestado / $numeroCuotas, 2);
            }else{
                $tasaInteres = 0;
            }
                
        }
        else if($montoCuotas > 0 && $montoPrestado > 0 && ($numeroCuotas == 0 || $numeroCuotas == null)){
            $idTipoAmortizacion = Types::whereDescripcion('Campo montoCuotas, ya sea con tasaInteres o no')->first()->id;
            $numeroCuotas = $montoPrestado / $montoCuotas;
            $numeroCuotasEntero = (int)$numeroCuotas;
            if($numeroCuotas > $numeroCuotasEntero){
                $numeroCuotas = $numeroCuotasEntero + 1;
            }
        }//Endif

        $fechaInicioCarbon = new Carbon($fechaInicio);
            $montoPrestadoDeducible = $montoPrestado;
            for($c = 1; $c <= $numeroCuotas; $c++){
                
                if($c == 1 && $insertarColumnaCero == true){
                    $colleccion = collect([
                        [
                            'numeroCuota' => 0,
                            'montoCuota' => 0,
                            'fecha' => $fechaInicioCarbon->toDateString(),
                            'montoInteres' => 0,
                            'amortizacion' => 0,
                            'montoCapital' => 0,
                            'saldoPendiente' => $montoPrestadoDeducible,
                            'tasaInteres' => $tasaInteres,
                            'idTipoAmortizacion' => $idTipoAmortizacion
                        ]
                    ]);
                }
                if($c == 1){
                    $montoInteres =  $montoCuotas * ($tasaInteres / 100);
                    $montoCuotaPrimeraCuota = round($montoInteres + $montoCuotas, 2);
                }
                else
                    $montoInteres =  $montoCuotas * ($tasaInteres / 100);

                    $montoCuotas = round($montoCuotas, 2);
                if($montoCuotas > $montoPrestadoDeducible){
                    $montoCuotas = $montoPrestadoDeducible;
                    $montoInteres =  $montoCuotas * ($tasaInteres / 100);
                }
                else if($montoPrestadoDeducible > $montoCuotas && $c == $numeroCuotas){
                    //En caso de sea la ultima cuota y el montoPrestadoDeducible sea mayor que el montoCuotas entonces el montoCuotas sera igual al montoPrestadoDeducible
                    $montoCuotas = $montoPrestadoDeducible;
                    $calculoParaRestarAlMontoInteres = 0;

                    
                    if(count($colleccion) > 1){
                        //Si la suma total de la amortizacion o capital es mayor que el monto prestado entonces el montoCuota sera igual al montoCuota anterior
                        // return 'dentro:' . Helper::sumarTotalAmortizacion($colleccion, $montoPrestadoDeducible) . ' monto:' .$montoPrestado . ' dedu:'.$montoPrestadoDeducible;
                        // if(Helper::sumarTotalAmortizacion($colleccion, $montoPrestadoDeducible) > $montoPrestado)
                        // {
                        //     //Aqui le reducimos dos al contador porque este contador empieza desde el numero 1 y el collection empieza desde el numero cero
                        //     //asi que la posicion anterior seria $c - 2
                        //     if($colleccion[$c - 2]['amortizacion'] < $montoCuotas){
                        //         $montoCuotas = $colleccion[$c - 2]['amortizacion'];
                        //         $montoPrestadoDeducible = $colleccion[$c - 2]['amortizacion'];
                        //     }
                        // }


                        if($colleccion[$c - 2]['amortizacion'] < $montoCuotas && $tasaInteres > 0){
                            $montoInteres =  $montoCuotas * ($tasaInteres / 100);
                            $calculoMontoCuota = $montoCuotas + $montoInteres;
                            if($colleccion[$c - 2]['montoCuota'] < $calculoMontoCuota){
                                $calculoParaRestarAlMontoInteres = round($calculoMontoCuota - $colleccion[$c - 2]['montoCuota'], 2);
                                $montoInteres -= $calculoParaRestarAlMontoInteres;
                                // return 'dentro:' . $calculoParaRestarAlMontoInteres . ' monto:' .($montoInteres + $montoCuotas) . ' dedu:'.$montoPrestadoDeducible;

                            }
                            // $montoCuotas = $colleccion[$c - 2]['amortizacion'];
                            // $montoPrestadoDeducible = $colleccion[$c - 2]['amortizacion'];
                        }
                        else if($colleccion[$c - 2]['amortizacion'] < $montoCuotas && $tasaInteres <= 0 && (Helper::sumarTotalAmortizacion($colleccion, $montoCuotas) != $montoPrestado)){
                            $montoCuotas = $colleccion[$c - 2]['amortizacion'];
                            $montoPrestadoDeducible = $colleccion[$c - 2]['amortizacion'];
                        }
                    }
                    if($calculoParaRestarAlMontoInteres == 0)
                        $montoInteres =  $montoCuotas * ($tasaInteres / 100);
                    // $montoInteres = $montoInteres - $calculoParaRestarAlMontoInteres;
                    
                }

                if($c== $numeroCuotas && $montoInteres > 0){
                    if(($montoCuotas + $montoInteres) < $montoCuotaPrimeraCuota){
                        $calculo = round($montoCuotaPrimeraCuota - ($montoCuotas + $montoInteres), 2);
                        $montoInteres = $montoInteres + $calculo;
                    }
                }
                // else if($c== $numeroCuotas && ($montoInteres == 0 || $montoInteres == null)){
                //     return Helper::sumarTotalAmortizacion($colleccion, $montoCuotas);
                // }
                // if($c== $numeroCuotas)
                // return  ' monto:' .($montoInteres + $montoCuotas) . ' dedu:'.$montoPrestadoDeducible . " cal:".Helper::sumarTotalAmortizacion($colleccion, $montoPrestadoDeducible) . " montoPrimera:" . $montoCuotaPrimeraCuota;


                    $montoInteres = round($montoInteres, 2);
                    $montoPrestadoDeducible = $montoPrestadoDeducible - ($montoCuotas);
                   
                
                if($frecuencia->descripcion == "Diario"){
                    if($incluirFechaInicio == true && $c == 1){
                        
                    }else{
                        $fechaInicioCarbon->addDay();
                    }
                        

                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }
                }
                else if($frecuencia->descripcion == "Semanal"){
                    if($incluirFechaInicio == true && $c == 1){
                        
                    }else{
                        $fechaInicioCarbon->addWeek();
                    }
                    
                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }
                }
                else if($frecuencia->descripcion == "Quincenal"){
                    if($incluirFechaInicio == true && $c == 1){
                        
                    }else{
                        $fechaInicioCarbon->addDays(15);
                    }

                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }
                }
                else if($frecuencia->descripcion == "Mensual"){
                    if($incluirFechaInicio == true && $c == 1){
                        
                    }else{
                        $fechaInicioCarbon->addMonthNoOverflow();
                    }
                    
                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }
                }
                else if($frecuencia->descripcion == "Anual"){
                    if($incluirFechaInicio == true && $c == 1){
                        
                    }else{
                        $fechaInicioCarbon->addYear();
                    }
                    
                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }
                }

            } //ENdFOR

        return $colleccion;
    }

    public static function getAmortizacionesNoPagadas($prestamo){
        $fecha = Carbon::now();
        $amortizaciones = Amortization::where('idPrestamo', $prestamo->id)->where('fecha', '>', $fecha->toDateString())->get();
        if($amortizaciones == null){
            return false;
        }

        list($amortizacionesNoPagadas, $no) = $amortizaciones->partition(function($a){
            //Vamos a retornar las cuotas que no se han pagado
            $totalPagado = $a->montoPagadoCapital + $a->montoPagadoInteres;
            $totalAPagar = $a->montoCuota;
            return $totalPagado < $totalAPagar;
        });


        return $amortizacionesNoPagadas;
    }


    public static function indexPost($idUsuario, $idBanca){
        return DB::select('call indexPost(?, ?)', array($idUsuario, $idBanca));

        
    }

    public static function guardarVenta($idUsuario, $idBanca, $idVentaHash, $compartido, $descuentoMonto, $hayDescuento, $total, $jugadas){
        return DB::select('call guardarVenta(?, ?, ?, ?, ?, ?, ?, ?)', array($idUsuario, $idBanca, $idVentaHash, $compartido, $descuentoMonto, $hayDescuento, $total, $jugadas));
    }

    public static function montoDisponibleFuncion($jugada, $idLoteria, $idBanca){
        return DB::select('select montoDisponible(?, ?, ?) as monto', array($jugada, $idLoteria, $idBanca));    
    }

}