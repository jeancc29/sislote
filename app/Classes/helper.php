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

use Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class Helper{
    public function saldo($id, $es_banca = true){
        $datos = Array("id" => $id, "es_banca" => $es_banca);

        $saldo_inicial = 0;

        if($datos["es_banca"] == 1){
            $idTipoEntidad1 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $debito = transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])->sum('debito');
            $credito =  transactions::where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])->sum('credito');
            $saldo_inicial = $debito - $credito;
        }else{
            $idTipoEntidad2 = Types::where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
            $debito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])->sum('debito');
            $credito = transactions::where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])->sum('credito');
            $saldo_inicial = $credito - $debito;
        }

       return $saldo_inicial;
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


}