<?php
namespace App\Classes;

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
                ->where('idTipo', '!=', $tipo->id)
                ->sum('debito');
            $credito =  transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)
                ->sum('credito');
            $saldo_inicial = $debito - $credito;
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

    public function determinarSorteo($jugada, $idLoteria){
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
        $sorteo = Draws::whereId($idSorteo)->first();
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

    function montodisponible($jugada, $idLoteria, $idBanca){
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

        return round(Sales::whereIn('id', $idVentas)->sum('premios'), 2);
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

}