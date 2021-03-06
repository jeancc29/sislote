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
    static function saldo($servidor, $id, $entidad = 1){
        $datos = Array("id" => $id, "entidad" => $entidad);

        $saldo_inicial = 0;

        if($datos["entidad"] == 1){
            $idTipoEntidad1 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])
                ->sum('debito');
            $credito =  transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])                
                ->sum('credito');
            //El debito desembolso es un fondo emitido para un prestamo desde esta entidad, osea, que es un dinero que sale
            // por lo tanto este se le suma al credito
            $debitoDesembolso =  transactions::on($servidor)->where(
                    [
                        'idEntidad1'=> $datos["id"], 
                        'idTipoEntidad2' => $idTipoEntidad1->id, 
                        'status' => 1
                    ])
                    ->where('idTipo', Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id)                
                    ->sum('debito');
            $saldo_inicial = $debito - ($credito + $debitoDesembolso);
        }else if($datos["entidad"] == 2){
            $idTipoEntidad2 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
            $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::on($servidor)->where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)                
                ->sum('debito');
            $credito = transactions::on($servidor)->where(
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
            $idTipoEntidad1 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1,
                    'idTipo' => $tipo->id
                ])
                ->where('idTipo', '=', $tipo->id)
                ->sum('debito');
            $credito = transactions::on($servidor)->where(
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



    static function saldoPorFecha($servidor, $id, $entidad = 1, $fechaHasta = null){
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
            $idTipoEntidad1 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])                
                ->where('created_at', '<=', $fechaHasta)
                ->sum('debito');
            $credito =  transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->whereNotIn('idTipo', [Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first()->id, Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id])                
                ->where('created_at', '<=', $fechaHasta)
                ->sum('credito');
            //El debito desembolso es un fondo emitido para un prestamo desde esta entidad, osea, que es un dinero que sale
            // por lo tanto este se le suma al credito
            $debitoDesembolso =  transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad2' => $idTipoEntidad1->id, 
                    'status' => 1
                ])
                ->where('idTipo', Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Desembolso de prestamo")->first()->id)   
                ->where('created_at', '<=', $fechaHasta)             
                ->sum('debito');
            $saldo_inicial = $debito - ($credito + $debitoDesembolso);
        }else if($datos["entidad"] == 2){
            $idTipoEntidad2 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banco'])->first();
            $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::on($servidor)->where(
                [
                    'idEntidad2'=> $datos["id"],
                    'idTipoEntidad2' => $idTipoEntidad2->id,  
                    'status' => 1
                ])
                ->where('idTipo', '!=', $tipo->id)
                ->where('created_at', '<=', $fechaHasta)
                ->sum('debito');
            $credito = transactions::on($servidor)->where(
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
            $idTipoEntidad1 = Types::on($servidor)->where(['renglon' => 'entidad', 'descripcion' => 'Banca'])->first();
            $tipo = Types::on($servidor)->whereRenglon('transaccion')->whereDescripcion("Caida Acumulada")->first();
            $debito = transactions::on($servidor)->where(
                [
                    'idEntidad1'=> $datos["id"], 
                    'idTipoEntidad1' => $idTipoEntidad1->id, 
                    'status' => 1,
                    'idTipo' => $tipo->id
                ])
                ->where('idTipo', '=', $tipo->id)
                ->where('created_at', '<=', $fechaHasta)
                ->sum('debito');
            $credito = transactions::on($servidor)->where(
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
            session()->forget('servidor');
            session()->forget('apiKey');
            session()->forget('servidores');
            session()->forget('usuario');
            session()->forget('tipoUsuario');
            session()->forget('socketKey');
            
            redirect()->route('login');
        }
    }

    public static function determinarSorteo($servidor, $jugada, $loteria){
        
        $idSorteo = 0;
  

    if(strlen($jugada) == 2){
        $idSorteo = 1;
    }
   else if(strlen($jugada) == 3){
        $idSorteo = DB::connection($servidor)->table('draws')->whereDescripcion("Pick 3 Straight")->first();
        if($idSorteo != null){
            $idSorteo = $idSorteo->id;
        }
   }
   else if(strlen($jugada) == 4){
        if(gettype(strpos($jugada, '+')) == "integer"){
            $idSorteo = DB::connection($servidor)->table('draws')->whereDescripcion("Pick 3 Box")->first();
            if($idSorteo != null){
                $idSorteo = $idSorteo->id;
            }
        }
        else{
            // $sorteo = DB::connection($servidor)->table('draws')
            //     ->select('draws.id')
            //     ->join('draw_lottery', 'draws.id', '=', 'draw_lottery.idSorteo')
            //     ->where(['draw_lottery.idLoteria' => $loteria->id, 'draws.descripcion' => 'Super pale'])->first();
            // $drawRelations = DB::connection($servidor)->table('drawsrelations')->where('idLoteriaPertenece', $loteria->id)->count();
            // if($sorteo == null || $drawRelations <= 1)
                $idSorteo = 2;
            // else if($sorteo != null || $drawRelations >= 2)
            //     $idSorteo = 4;
        }
   }
    else if(strlen($jugada) == 5){
            if(gettype(strpos($jugada, '+')) == "integer"){
                $idSorteo = DB::connection($servidor)->table('draws')->whereDescripcion("Pick 4 Box")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
            else if(gettype(strpos($jugada, '-')) == "integer"){
                $idSorteo = DB::connection($servidor)->table('draws')->whereDescripcion("Pick 4 Straight")->first();
                if($idSorteo != null){
                    $idSorteo = $idSorteo->id;
                }
            }
            else if(gettype(strpos($jugada, 'S')) == "integer" || gettype(strpos($jugada, 's')) == "integer"){
                $idSorteo = DB::connection($servidor)->table('draws')->whereDescripcion("Super pale")->first();
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
        // else if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
            $idSorteo = "Pale";
        // else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
            // $idSorteo = "Super pale";
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
            else if(gettype(strpos($jugada, 'S')) == "integer" || gettype(strpos($jugada, 's')) == "integer"){
                $idSorteo = Draws::whereDescripcion("Super pale")->first();
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


    static function agregarGuion($servidor, $jugada, $idSorteo){
        $sorteo = Draws::on($servidor)->whereId($idSorteo)->first();
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

    static function quitarUltimoCaracter($servidor, $cadena, $idSorteo){
        $sorteo = DB::connection($servidor)->table('draws')->whereId($idSorteo)->first();
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

    function to2Digitos($string){
        $str = $string;
        $pad = "00";
        $ans = substr($pad, 0, strlen($pad) - strlen($str)) . $str;
        return $ans;
    }

    static function toDosDigitos($string){
        $str = $string;
        $pad = "00";
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

    static function loteriaTienePremiosRegistradosHoy($servidor, $idLoteria){
            $fechaActual = getdate();
            $fechaInicial = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 00:00:00';
            $fechaFinal = $fechaActual['year'].'-'.$fechaActual['mon'].'-'.$fechaActual['mday'] . ' 23:50:00';
            
            $premios = Awards::on($servidor)->where('idLoteria', $idLoteria)
            ->whereBetween('created_at', array($fechaInicial, $fechaFinal))->get()->first();

            return ($premios != null) ? true : false;
    }


    static function ventasPorBanca($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
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
            $ventas = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        return round(Sales::on($servidor)->whereIn('id', $idVentas)->sum('total'), 2);
    }

    static function descuentosPorBanca($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
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
            $ventas = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        return round(Sales::on($servidor)->whereIn('id', $idVentas)->sum('descuentoMonto'), 2);
    }

    static function premiosPorBanca($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
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
            $ventas = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])->get();
        }else{
            $ventas = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->whereNotIn('status', [0,5])
            ->where('idBanca', $idBanca)
            ->get();
        }
    
        $idVentas = collect($ventas)->map(function($id){
            return $id->id;
        });

        return round(Salesdetails::on($servidor)->whereIn('idVenta', $idVentas)->sum('premio'), 2);
    }

    static function ticketsPorBanca($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
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
     
        $tickets = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        ->whereNotIn('status', [0,5])
        ->where('idBanca', $idBanca)
        ->count();
    
    
        

        return $tickets;
    }

    static function ticketsPendientesPorBanca($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
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
     
        $tickets = Sales::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
        ->whereNotIn('status', [0,5])
        ->where(['idBanca' => $idBanca, 'status' => 1])
        ->count();
    
    
        

        return $tickets;
    }

    static function comisionesPorBanca($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
        if($fechaInicial == null and $fechaFinal == null){
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }
        
        $comisionesMonto = 0;
            $datosComisiones = Commissions::on($servidor)->where('idBanca', $idBanca)->get();
            $idVentasDeEstaBanca = Sales::on($servidor)->select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $idBanca)->whereNotIn('status', [0,5])->get();
            $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
                return $id->id;
            });
            foreach($datosComisiones as $d){
                $loteria = Lotteries::on($servidor)->whereId($d['idLoteria'])->first();
                if($loteria == null)
                    continue;

                if($d['directo'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Directo')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['directo'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pale'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pale')->first();
                    if($sorteo != null && $loteria->sorteoExiste($sorteo->id) == true){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pale'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['tripleta'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Tripleta')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['tripleta'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['superPale'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Super pale')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['superPale'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick3Straight'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 3 Straight')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick3Straight'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick3Box'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 3 Box')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick3Box'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick4Straight'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 4 Straight')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick4Straight'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

                if($d['pick4Box'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 4 Box')->first();
                    if($sorteo != null){
                        //Obtenemos la sumatoria del campo monto de acuerdo a la loteria, banca, sorteo y rango de fecha
                        $comisionesMonto += ($d['pick4Box'] / 100) * Salesdetails::on($servidor)->whereBetween('created_at', array($fechaInicial, $fechaFinal))
                            ->whereIn('idVenta', $idVentasDeEstaBanca)
                            ->where(['idLoteria' => $d['idLoteria'], 'idSorteo' => $sorteo->id])
                            ->sum('monto');
                    }
                }

            }

        return round($comisionesMonto, 2);
    }


    static function cambiarComisionesATickets($servidor, $idBanca, $fechaInicial = null, $fechaFinal = null){
        if($fechaInicial == null && $fechaFinal == null){
            $fecha = getdate();
            $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
            $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        }

        
        $idVentasDeEstaBanca = Sales::on($servidor)->select('id')->whereBetween('created_at', array($fechaInicial, $fechaFinal))->where('idBanca', $idBanca)->whereNotIn('status', [0,5])->get();
        $idVentasDeEstaBanca = collect($idVentasDeEstaBanca)->map(function($id){
            return $id->id;
        });
            $datosComisiones = Commissions::on($servidor)->where('idBanca', $idBanca)->get();
            
            foreach($datosComisiones as $d){
                $loteria = Lotteries::on($servidor)->whereId($d['idLoteria'])->first();
                if($loteria == null)
                    continue;

                

                if($d['directo'] >= 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Directo')->first();
                    if($sorteo != null){

                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['directo'] / 100) * $s['monto'];
                            $s->save();
                        }
                      
                    }
                }

                if($d['pale'] >= 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pale')->first();
                    if($sorteo != null && $loteria->sorteoExiste($sorteo->id) == true){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pale'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['tripleta'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Tripleta')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['tripleta'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['superPale'] >= 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Super pale')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['superPale'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick3Straight'] >= 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 3 Straight')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick3Straight'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick3Box'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 3 Box')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick3Box'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick4Straight'] >= 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 4 Straight')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
                        foreach($Salesdetails as $s){
                            $s['comision'] = ($d['pick4Straight'] / 100) * $s['monto'];
                            $s->save();
                        }
                    }
                }

                if($d['pick4Box'] > 0){
                    $sorteo = Draws::on($servidor)->whereDescripcion('Pick 4 Box')->first();
                    if($sorteo != null){
                        $Salesdetails = Salesdetails::on($servidor)->whereIn('idVenta', $idVentasDeEstaBanca)->where(['idLoteria' => $loteria->id, 'idSorteo' => $sorteo->id])->get();
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

    static function loteriasOrdenadasPorHoraCierre($servidor, $usuario, $retornarSoloAbiertas = false){
        $loterias = Lotteries::on($servidor)->whereStatus(1)->get();
        $loterias = collect($loterias);
        $idDia = Days::on($servidor)->whereWday(getdate()['wday'])->first()->id;
        list($loterias, $no) = $loterias->partition(function($l) use($usuario, $retornarSoloAbiertas, $servidor){
            $loteria = Lotteries::on($servidor)->whereId($l['id'])->first();
            
            //Si puede jugar fuera de horario entonces solo nos retornamos las loterias que no tengan premios registrados
            if($usuario->tienePermiso('Jugar fuera de horario') && $retornarSoloAbiertas == false)
                return Helper::loteriaTienePremiosRegistradosHoy($servidor, $loteria->id) != true;
            if($usuario->tienePermiso('Jugar minutos extras'))
                return $loteria->cerrada(true) &&  Helper::loteriaTienePremiosRegistradosHoy($servidor, $loteria->id) != true;
            else
                return $loteria->cerrada() != true &&  Helper::loteriaTienePremiosRegistradosHoy($servidor, $loteria->id) != true;
        });

        $idLoteriasAbiertas = collect($loterias)->map(function($l){
            return $l['id'];
        });

         $loterias = 
            Lotteries::
            on($servidor)->
            join('day_lottery', 'day_lottery.idLoteria', '=', 'lotteries.id')
            ->whereIn('lotteries.id', $idLoteriasAbiertas)
            ->where('day_lottery.idDia', $idDia)
            ->orderBy('day_lottery.horaCierre', 'asc')
            ->get();

            $loterias = collect($loterias)->map(function($l) use($servidor){
                $loteria = Lotteries::on($servidor)->whereId($l["idLoteria"])->first();
                $fecha = getdate();
                $dia = $loteria->dias()->whereWday($fecha['wday'])->first();
                return ['id' => $l['idLoteria'], 'descripcion' => $l['descripcion'], 'abreviatura' => $l['abreviatura'], 'horaApertura' => $dia->pivot->horaApertura, 'horaCierre' => $dia->pivot->horaCierre, 'minutosExtras' => $dia->pivot->minutosExtras, 'sorteos' => $loteria->sorteos];
            });

        return $loterias;
    }


    //Debemos verificar si todas las jugadas han sido pagadas
    static function verificarTicketHaSidoPagado($servidor, $idVenta){
        $jugadasQueAunEstanPendiente = Salesdetails::on($servidor)->where('idVenta', $idVenta)->whereStatus(0)->count();
        if($jugadasQueAunEstanPendiente > 0){
            return false;
        }
        $jugadas = Salesdetails::on($servidor)->where('idVenta', $idVenta)->get();

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


    static function pagar($servidor, $idVenta, $idUsuario, $idBanca = null){
        
        $venta = Sales::on($servidor)->whereId($idVenta)->whereNotIn('status', [0,5])->first();
        if($venta == null){
            return false;
        }
        $jugadas = Salesdetails::on($servidor)->where(['idVenta' => $idVenta, 'pagado' => 0])->where('premio', '>', 0)->get();
        $jugadasQueAunEstanPendiente = Salesdetails::on($servidor)->where('idVenta', $idVenta)->whereStatus(0)->count();
        $seMarcaronJugadasComoPagadas = false;
        $idTicket = $venta->idTicket;

        $montoPremios = 0;
        $montoPagado = 0;
        if($jugadas != null && count($jugadas) > 0){
            foreach($jugadas as $j){
                $j['pagado'] = 1;
                $j->save();
                $seMarcaronJugadasComoPagadas = true;
                
                Logs::on($servidor)->create([
                    'idBanca' => Helper::getIdBanca($servidor, $idUsuario, $idBanca),
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
                        if(Tickets::on($servidor)->where('codigoBarra', $codigoBarra)->get()->first() == null){
                            if(is_numeric($codigoBarra)){
                                $ticket = Tickets::on($servidor)->whereId($idTicket)->first();
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


    public static function getIdBanca($servidor, $idUsuario, $idBanca = null){
        if($idBanca != null){
            $idBanca = Branches::on($servidor)->where(['id' => $idBanca, 'status' => 1])->first();
            if($idBanca != null)
                $idBanca = $idBanca->id;
        }else{
            
            $idBanca = Branches::on($servidor)->where(['idUsuario' => $idUsuario, 'status' => 1])->first();
            if($idBanca != null)
                $idBanca = $idBanca->id;
            else{
                $u = Users::on($servidor)->whereId($idUsuario)->first();
                //Si el usuario no tiene banca entonces verificamos si tiene permiso para jugar como cualquier banca para retornar el id de la primera banca activa
                if($u->tienePermiso("Jugar como cualquier banca") == true){
                    $idBanca = Branches::on($servidor)->where(['status' => 1])->first();
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

    //Estas dos variables  $insertarColumnaCero = false, $incluirFechaInicio = false no sirven para nada asi que puedo eliminarlas cuando quiera
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

                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }

                    $fechaInicioCarbon->addDay();
                }
                else if($frecuencia->descripcion == "Semanal"){
                    
                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }

                    $fechaInicioCarbon->addWeek();

                }
                else if($frecuencia->descripcion == "Quincenal"){

                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }

                    $fechaInicioCarbon->addDays(15);
                }
                else if($frecuencia->descripcion == "Mensual"){
                    
                    
                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }

                    $fechaInicioCarbon->addMonthNoOverflow();
                }
                else if($frecuencia->descripcion == "Anual"){
                    
                    if($colleccion==null){
                        $colleccion = collect([
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(), 'montoCuota' => $montoCuotas + $montoInteres, 'montoInteres' => $montoInteres, 'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        ]);
                    }else{
                        $colleccion->push(
                            ['numeroCuota' => $c, 'fecha' => $fechaInicioCarbon->toDateString(),'montoCuota' => $montoCuotas + $montoInteres,'montoInteres' => $montoInteres,'amortizacion' => $montoCuotas,'saldoPendiente' => $montoPrestadoDeducible, 'tasaInteres' => $tasaInteres, 'idTipoAmortizacion' => $idTipoAmortizacion, 'montoCapital' => $montoCuotas]
                        );
                    }

                    $fechaInicioCarbon->addYear();
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


    public static function indexPost($servidor, $idUsuario, $idBanca){
        return DB::connection($servidor)->select('call indexPost(?, ?)', array($idUsuario, $idBanca));
    }

    public static function guardarVenta($servidor, $idUsuario, $idBanca, $idVentaHash, $compartido, $descuentoMonto, $hayDescuento, $total, $jugadas){
        return DB::connection($servidor)->select('call guardarVenta(?, ?, ?, ?, ?, ?, ?, ?)', array($idUsuario, $idBanca, $idVentaHash, $compartido, $descuentoMonto, $hayDescuento, $total, $jugadas));
    }

    public static function montoDisponibleFuncion($servidor, $jugada, $idLoteria, $idBanca, $idLoteriaSuperpale){
        return DB::connection($servidor)->select('select montoDisponible(?, ?, ?, ?) as monto', array($jugada, $idLoteria, $idBanca, $idLoteriaSuperpale));    
    }

    public static function getEquivalenciaDeUnDolar($idBanca){
        $idMoneda = Branches::whereId($idBanca)->first();
        if($idMoneda == null){
            return 1; //porque cantidad / 1 == cantidad...
        }else{
            $moneda = Coins::whereId($idMoneda)->first();
            return ($moneda != null) ? $moneda->equivalenciaDeUnDolar : 1;
        }
    }

    public static function tienePermiso($idUsuario, $permiso)
    {
        $u = Users::on(session("servidor"))->where(["id" => $idUsuario, "status" => 1])->first();
        if($u != null)
            return $u->permisos->contains("descripcion", $permiso);
        else
            return false;
    }

    public static function jwtDecode($token)
    {
        $stdClass = \Firebase\JWT\JWT::decode($token, \config('data.apiKey'), array('HS256'));
        $datos = Helper::stdClassToArray($stdClass);
        return $datos;
    }

    public static function jwtEncodeServidor($servidor, $usuario)
    {
        $time = time();
        $key = \config('data.apiKey');

        $token = array(
            'iat' => $time, // Tiempo que inició el token
            'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
            'data' => [ // información del usuario
                'usuario' => $usuario,
                'servidor' => $servidor
            ]
        );

        return \Firebase\JWT\JWT::encode($token, $key);
    }

    public static function jwtEncoder($data = [])
    {
        $time = time();
        $key = \config('data.apiKey');

        $token = array(
            'iat' => $time, // Tiempo que inició el token
            'exp' => $time + (60*60), // Tiempo que expirará el token (+1 hora)
            'data' => $data
        );

        return \Firebase\JWT\JWT::encode($token, $key);
    }

    public static function stdClassToArray($stdClass)
    {
        return json_decode(json_encode($stdClass), true);
    }

    public static function combinarPremiosPaleYConvertirAExpresionRegular($primera, $segunda, $tercera){
        //Ejemplo expresion regular que retorna la function (^0102$|^0103$|^0203$)
        $unirYSepararPorComa = "{$primera},{$segunda},{$tercera}";
        $arreglo = explode(",", $unirYSepararPorComa);
        $arreglo = collect($arreglo);
        $arregloOrdenadoMenorAMayor = $arreglo->sort()->values()->all();
        $expresionRegular = "";
        // return $arregloOrdenadoMenorAMayor;
        //Vamos a recorrer dos ciclos
        //En el primer ciclo recorremos todos los numeros
        //En el segundo recorremos todos los numeros que sean mayores que el numero del primer ciclo
        for ($i=0; $i < count($arregloOrdenadoMenorAMayor); $i++) { 
            // return "{$arregloOrdenadoMenorAMayor[$i]} {$arreglo[0]}";
            if($i == 0)
                $expresionRegular .= "(";
            //Este segundo for empezara siempre con un directo mayor que el directo del primer ciclo por eso i2 = i + 1
            for ($i2=$i+1; $i2 < count($arregloOrdenadoMenorAMayor); $i2++) { 
                if($i == 0)
                    $expresionRegular .= "^";
                else if($i > 0)
                    $expresionRegular .= "|^";

                $expresionRegular .= $arregloOrdenadoMenorAMayor[$i] . $arregloOrdenadoMenorAMayor[$i2] ."$";
                
                if($i2 == 1)
                    $expresionRegular .= "|";
            }

            if($i + 1 == count($arregloOrdenadoMenorAMayor))
                $expresionRegular .=")";
        }

        return $expresionRegular;
    }

    public static function combinarPremiosTripletaYConvertirAExpresionRegular($primera, $segunda, $tercera){
        //Ejemplo expresion regular que retorna la function (^0102$|^0103$|^0203$)
        $unirYSepararPorComa = "{$primera},{$segunda},{$tercera}";
        $arreglo = explode(",", $unirYSepararPorComa);
        $arreglo = collect($arreglo);
        $arregloOrdenadoMenorAMayor = $arreglo->sort()->values()->all();
        $expresionRegular = "";
        // return $arregloOrdenadoMenorAMayor;
        //Vamos a recorrer dos ciclos
        //En el primer ciclo recorremos todos los numeros
        //En el segundo recorremos todos los numeros que sean mayores que el numero del primer ciclo
        for ($i=0; $i < count($arregloOrdenadoMenorAMayor); $i++) { 
            // return "{$arregloOrdenadoMenorAMayor[$i]} {$arreglo[0]}";
            if($i == 0)
                $expresionRegular .= "(";
            //Este segundo for empezara siempre con un directo mayor que el directo del primer ciclo por eso i2 = i + 1
            for ($i2=$i+1; $i2 < count($arregloOrdenadoMenorAMayor); $i2++) { 
                if($i == 0)
                    $expresionRegular .= "^";
                else if($i > 0)
                    $expresionRegular .= "|^";

                $expresionRegular .= $arregloOrdenadoMenorAMayor[$i] . $arregloOrdenadoMenorAMayor[$i2] ."$";
                
                if($i2 == 1)
                    $expresionRegular .= "|";
            }

            if($i + 1 == count($arregloOrdenadoMenorAMayor))
                $expresionRegular .=")";
        }

        return $expresionRegular;
    }

   

    public static function comb($alfabeto, $n, $resultados, $resultado) {
        $resultados = [];
        if(!$resultado) {
            $resultado = [];
        }
        for($i=0; $i<$alfabeto.length; ++$i) {
            $newResultado = $resultado.slice();
            $newAlfabeto = $alfabeto.slice();
            $newResultado.push($alfabeto[$i]);
            $newAlfabeto = array_splice($array, $i, 1);
            if(n>1) {
                comb($newAlfabeto, n-1, $resultados, $newResultado);
            } else {
                $resultados.push($newResultado);
            }
        }
    }

//     public static function pc_permute($items, $perms = array( ), $arrayToFill = array()) {
//         $string = "";
//         if (empty($items)) { 
//             // print join(' ', $perms) . "\n" . count($items) . "\n";
//                 //  print "hola: \n";
//             array_push($arrayToFill, join(' ', $perms));
//         }  else {
//             for ($i = count($items) - 1; $i >= 0; --$i) {
//                  $newitems = $items;
//                  $newperms = $perms;
//                  list($foo) = array_splice($newitems, $i, 1);
//                  array_unshift($newperms, $foo);
//                 //  print "count: " . count($newitems) . " " . join("", $newperms) . "\n";
//                 if(empty($newitems))
//                     array_push($arrayToFill, join("", $newperms));
//                 Helper::pc_permute($newitems, $newperms, $arrayToFill);
//                  print join("", $newperms) . " - " . count($items) . "\n";
//                 if(empty($newitems))
//                     $string .= join("", $newperms) . "|";
//                 //  print "count: " . count($newitems) . " " . join("", $newperms) . "\n";
                    
//              }
//             //  print "Termino per: " . join(" ", $perms) . "\n";
//              if(empty($perms))
//                 return $arrayToFill;
//         }
//  }

public static function pc_permute($items, $perms = array( )) {
    if (empty($items)) {
        $return = array($perms);
    }  else {
        $return = array();
        for ($i = count($items) - 1; $i >= 0; --$i) {
             $newitems = $items;
             $newperms = $perms;
         list($foo) = array_splice($newitems, $i, 1);
             array_unshift($newperms, $foo);
             $return = array_merge($return, Helper::pc_permute($newitems, $newperms));
         }
    }
    return $return;
}

public static function pc_permuteString($items, $quitarPrimerElementoDeTodasLasCombinaciones = false) {
    // if (empty($items)) {
    //     $return = "^".join("",$perms) . "$|";
    // }  else {
    //     $return = "";
    //     for ($i = count($items) - 1; $i >= 0; --$i) {
    //          $newitems = $items;
    //          $newperms = $perms;
    //      list($foo) = array_splice($newitems, $i, 1);
    //          array_unshift($newperms, $foo);
    //         //  $return = $return . "|" . join("", Helper::pc_permute($newitems, $newperms));
    //          $return = $return . Helper::pc_permuteString($newitems, $newperms) ;
    //         //  Helper::pc_permute($newitems, $newperms);
    //      }
    // }
    // // print count($return);
    // if(empty($perms)){
    //     //Asi queda el string al terminar ^678$|^768$|^687$| asi que debemos poner un parentesis al inicio
    //     // y despues quitar la ultima barra | y poner otro parentesis al final
    //     $ponerPrimerParentesis = "(" . $return;
    //     $quitarUlimaBarra = substr($ponerPrimerParentesis, 0, strlen($ponerPrimerParentesis) - 1);
    //     $ponerUltimoParentesis = $quitarUlimaBarra . ")";
    //     $return = $ponerUltimoParentesis;
    // }
    // return $return;
    $array = Helper::pc_permute($items);
    $permuteRegexString = "";

    if($quitarPrimerElementoDeTodasLasCombinaciones == false){
        for ($i=0; $i < count($array); $i++) { 
            $permuteToString = join("", $array[$i]);
            if((strpos($permuteRegexString, $permuteToString)) == false)
                $permuteRegexString .= "^" . join("", $array[$i]) . "$|";
        }
    }else{
        for ($i=0; $i < count($array); $i++) { 
            $permuteToString = join("", $array[$i]);
            if((strpos($permuteRegexString, $permuteToString)) == false){
                // este era el codigo anterior -> $permuteRegexString .= substr(join("", $array[$i]), 2, 4) . "|";

                //to match strings which have letters cat followed by mat, you can try: 
                //cat.*mat 
                //The above regular expression will match cat  and mat anywhere in the string
                // ['01', '02', '03'] convert to 01.*02.*03
                $arrayToString = join(".*", $array[$i]);
                //quitamos el primer par de numeros 01.* y quedara asi 02.*03 y asi se buscara el segundo numero sin importar su orden, osea, 
                //sin importar si el segundo numero esta ubicado en 2da o en 3ra
                $quitamosElPrimerCaracter = substr($arrayToString, 4, 8) . "|";
                $permuteRegexString .= $quitamosElPrimerCaracter;
            }
        }
    }

    //Asi queda el string al terminar ^678$|^768$|^687$| asi que debemos poner un parentesis al inicio
    // y despues quitar la ultima barra | y poner otro parentesis al final
    $ponerPrimerParentesis = "(" . $permuteRegexString;
    $quitarUlimaBarra = substr($ponerPrimerParentesis, 0, strlen($ponerPrimerParentesis) - 1);
    $ponerUltimoParentesis = $quitarUlimaBarra . ")";
    $permuteRegexString = $ponerUltimoParentesis;

    return $permuteRegexString;
}

public static function combinarPremiosParaTodosLosSorteosYconvertirlosAExpresionRegular($primera, $segunda, $tercera, $pick3, $pick4){
    $expresionRegular = "";
    if(!empty($primera) && !empty($segunda) && !empty($tercera)){
        $expresionRegularQuiniela = "^{$primera}$|^{$segunda}$|^{$tercera}$";
        $expresionRegularPale = Helper::combinarPremiosPaleYConvertirAExpresionRegular($primera, $segunda, $tercera);
        $expresionRegularTripleta = Helper::pc_permuteString([$primera, $segunda, $tercera]);
        $expresionRegularTripletaDosNumeros = Helper::pc_permuteString([$primera, $segunda, $tercera], true);
        $expresionRegular .= $expresionRegularQuiniela . "|" . Helper::quitarParentesis($expresionRegularPale) . "|" . Helper::quitarParentesis($expresionRegularTripleta) . "|" . Helper::quitarParentesis($expresionRegularTripletaDosNumeros);
    }

    if(!empty($pick3)){
        $expresionRegularPick3 = Helper::pc_permuteString([$pick3[0], $pick3[1], $pick3[2]]);
        $expresionRegular .= (empty($expresionRegular)) ? Helper::quitarParentesis($expresionRegularPick3) : "|" . Helper::quitarParentesis($expresionRegularPick3); 
    }

    if(!empty($pick4)){
        $expresionRegularPick4 = Helper::pc_permuteString([$pick4[0], $pick4[1], $pick4[2], $pick4[3]]);
        $expresionRegular .= (empty($expresionRegular)) ? Helper::quitarParentesis($expresionRegularPick4) : "|" . Helper::quitarParentesis($expresionRegularPick4); 
    }

    $ponerPrimerParentesis = "(" . $expresionRegular;
    $ponerUltimoParentesis = $ponerPrimerParentesis . ")";
    $expresionRegular = $ponerUltimoParentesis;

    return $expresionRegular;
}

public static function quitarParentesis($data){
    $quitarPrimerParentesis = str_replace("(", "", $data);
    $quitarUltimoParentesis = str_replace(")", "", $quitarPrimerParentesis);
    return $quitarUltimoParentesis;
}

static function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

public static function pruebaDuracion($datos, $fecha){
    $time_start = Helper::microtime_float();
    // Dormir por un momento
    // \App\Classes\AwardsClass::actualizarStatusDelTicket($servidor, $idLoteria, $fecha);
    // usleep(1000000);
        $servers = \App\Server::all();
        foreach ($servers as $server) {
            $datos["servidor"] = $server->descripcion;
            Helper::save($datos, $fecha);
        }
    
    $time_end = Helper::microtime_float();
    $time = $time_end - $time_start;
    print "No se hizo nada en $time segundos\n";

}

static function save($datos, $fecha){
    foreach($datos['loterias'] as $l):
        $loteria = \App\Lotteries::on($datos["servidor"])->whereDescripcion($l["descripcion"])->first();
        $l['id'] = $loteria->id;
        print "Loteria: " . $loteria->descripcion . " id: " . $l['id'] . " s: " . $datos["servidor"] . "\n";
        // $awardsClass = new \App\Classes\AwardsClass($datos["servidor"], $l['id']);
        // $awardsClass->fecha = $fecha;
        // $awardsClass->idUsuario = $datos['idUsuario'];
        // $awardsClass->primera = $l['primera'];
        // $awardsClass->segunda = $l['segunda'];
        // $awardsClass->tercera = $l['tercera'];
        // $awardsClass->pick3 = $l['pick3'];
        // $awardsClass->pick4 = $l['pick4'];
        // $awardsClass->numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];

        
        // if($awardsClass->combinacionesNula() == true){
        //     continue;
        // }
        // // if($awardsClass->datosValidos() == false){
        // //     return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);
        // // }
        // if(!$awardsClass->loteriaAbreDiaActual()){
        //     // return Response::json(['errores' => 1,'mensaje' => 'La loteria ' . $awardsClass->getLoteriaDescripcion() .' no abre este dia '], 201);
        // }  
        // if($awardsClass->insertarPremio() == false){
        //     // return Response::json(['errores' => 1,'mensaje' => 'Error al insertar premio'], 201);
        // }

           
        //     $c = 0;
        //     $colleccion = null;

        //     // return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);

            
        //     foreach($awardsClass->getJugadasDeFechaDada($l['id']) as $j){
    
        //         $j['premio'] = 0;
        //         $contador = 0;
        //         $busqueda1 = false;
        //         $busqueda2 = false;
        //         $busqueda3 = false;

        //         // abort(404, $j["jugada"]);

        //         $sorteo = \App\Draws::on($datos["servidor"])->whereId($j['idSorteo'])->first();

        //     // return Response::json(['errores' => 1,'mensaje' => 'Datos invalidos para la loteria ' . $awardsClass->getLoteriaDescripcion()], 201);
                
    
                
        //         if($sorteo->descripcion == "Directo"){
        //             if(!is_numeric($awardsClass->numerosGanadores)){
        //                 abort(404, 'Los numeros ganadores no son correctos');
        //             }
        //             $j['premio'] = $awardsClass->directoBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
        //         }
        //         else if($sorteo->descripcion == "Pale"){
        //             if(!is_numeric($awardsClass->numerosGanadores)){
        //                 abort(404, 'Los numeros ganadores no son correctos');
        //             }
        //             $j['premio'] = $awardsClass->paleBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], $j['idSorteo']);
        //         }
        //         // else if($sorteo->descripcion == "Super pale"){
        //         //     if(!is_numeric($awardsClass->numerosGanadores)){
        //         //         return Response::json(['errores' => 1,'mensaje' => 'Los numeros ganadores no son correctos'], 201);
        //         //     }
        //         //     $j['premio'] = $awardsClass->superPaleBuscarPremio($j['idVenta'], $l['id'], $j['idLoteriaSuperpale'], $j['jugada'], $j['monto'], $j['idSorteo']);
        //         // }
        //         else if($sorteo->descripcion == "Tripleta"){
        //             if(!is_numeric($awardsClass->numerosGanadores)){
        //                 abort(404, 'Los numeros ganadores no son correctos');
        //             }
        //             $j['premio'] = $awardsClass->tripletaBuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
        //         }
        //         else if($sorteo->descripcion == "Pick 3 Straight"){
        //             $j['premio'] = $awardsClass->pick3BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
        //         }
        //         else if($sorteo->descripcion == "Pick 3 Box"){
        //             $j['premio'] = $awardsClass->pick3BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], false);
        //         }
        //         else if($sorteo->descripcion == "Pick 4 Straight"){
        //             $j['premio'] = $awardsClass->pick4BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto']);
        //         }
        //         else if($sorteo->descripcion == "Pick 4 Box"){
        //             $j['premio'] = $awardsClass->pick4BuscarPremio($j['idVenta'], $l['id'], $j['jugada'], $j['monto'], false);
        //         }
    
    
        //         $j['status'] = 1;
        //         $j->save();
    
    
        //         // if($c == 0){
        //         //     $colleccion = collect([
        //         //         'busqueda_false' => gettype($busqueda),
        //         //         'busqueda' => $busqueda,
        //         //         'jugada' => $j['jugada'], 
        //         //         'premio' => $j['premio'], 
        //         //         'monto' => $j['monto'],
        //         //         'contador' => $contador,
        //         //         'busqueda1' => $busqueda1,
        //         //         'busqueda2' => $busqueda2,
        //         //         'busqueda3' => $busqueda3
        //         //     ]);
        //         // }else{
        //         //     $colleccion->push([
        //         //         'busqueda_false' => gettype($busqueda),
        //         //         'busqueda' => $busqueda,
        //         //         'jugada' => $j['jugada'], 
        //         //         'premio' => $j['premio'], 
        //         //         'monto' => $j['monto'],
        //         //         'contador' => $contador,
        //         //         'busqueda1' => $busqueda1,
        //         //         'busqueda2' => $busqueda2,
        //         //         'busqueda3' => $busqueda3
        //         //     ]);
        //         // }
    
        //         $c++;
        //     }


        //     //Buscar jugadas super pale de esa loteria, ya sea esta la loteria primaria o loteria superpale de la tabla salesdetails
        //     // return Response::json(['errores' => 1,'mensaje' => $awardsClass->getJugadasSuperpaleDeFechaDada($l['id'])], 201);
        //     foreach($awardsClass->getJugadasSuperpaleDeFechaDada($l['id']) as $j){
                
    
        //         $j['premio'] = 0;
        //         $contador = 0;
        //         $busqueda1 = false;
        //         $busqueda2 = false;
        //         $busqueda3 = false;

        //         if(!is_numeric($awardsClass->numerosGanadores)){
        //             abort(404,'Los numeros ganadores no son correctos');
        //         }

        //         //Si el premio superpale es igual a -1 entonces eso quiere decir que la otra loteria no ha salido, 
        //         //por lo tanto el status de la jugada seguira siendo igual a cero, indicando que todavia la jugada estara pendiente
        //         $premioSuperpale = $awardsClass->superPaleBuscarPremio($j['idVenta'], $l['id'], $j);
        //         // return Response::json(['errores' => 1,'mensaje' => "Dentro jugadas super pale premio: {$premioSuperpale}"], 201);
        //         if($premioSuperpale != -1){
        //             $j['premio'] = $premioSuperpale;
        //             $j['status'] = 1;
        //             $j->save();
        //         }else{
        //             $j['premio'] = 0;
        //             $j['status'] = 0;
        //             $j->save();
        //         }
                
    
        //         $c++;
        //     }
    
        // endforeach;


        // foreach($datos['loterias'] as $l):
        //     // $ventas = Sales::on($datos["servidor"])
        //     // ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))
        //     // ->whereNotIn('status', [0,5])
        //     // ->get();


        //     /************** MANERA NUEVA DE CAMBIAR STATUS DE LOS TICKETS ***********/
        //     \App\Classes\AwardsClass::actualizarStatusDelTicket($datos["servidor"], $l["id"], $fecha);



        //     /********* MANERA VIEJA DE CAMBIAR STATUS DE LOS TICKETS */
        //     // $ventas = AwardsClass::getVentasDeFechaDada($datos["servidor"], $l["id"], $fecha);
    
        //     // foreach($ventas as $v){
        //     //     $todas_las_jugadas = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id']])->count();
        //     //     $todas_las_jugadas_salientes = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->count();
        //     //     $cantidad_premios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->count();
                
                
        //     //     // abort(404, "Error todas: {$todas_las_jugadas} salientes: {$todas_las_jugadas_salientes} status: {$v['status']}");
    
        //     //     if($todas_las_jugadas == $todas_las_jugadas_salientes)
        //     //     {

        //     //         if($cantidad_premios > 0)
        //     //         {
        //     //             $montoPremios = Salesdetails::on($datos["servidor"])->where(['idVenta' => $v['id'], 'status' => 1])->where('premio', '>', 0)->sum("premio");
        //     //             $v['premios'] = $montoPremios;
        //     //             $v['status'] = 2;
        //     //         }                        
        //     //         else{
        //     //             $v['premios'] = 0;
        //     //             $v['status'] = 3;
        //     //         }
                        
    
        //     //         $v->save();
        //     //     }
        //     // }

        endforeach;
}

    public static function prueba(){
        $servers = \App\Server::on("mysql")->get();
        $fecha = getdate(strtotime("2020-12-24"));
        foreach($servers as $server):
        $loteria = \App\Lotteries::on($server->descripcion)->whereDescripcion("La Suerte")->first();
        \App\Classes\AwardsClass::actualizarStatusDelTicket($server->descripcion, $loteria->id, $fecha);
        endforeach;
    }

    public static function dbExists($servidor){
        $result = \DB::select("SHOW DATABASES LIKE '$servidor'");
        return count($result) > 0;
    }

    public static function createIdTicket($servidor, $idBanca, $uuid, $createNew = false){
        $ticket = null;
        if($createNew)
            $ticket = Tickets::on($servidor)->create([
                "idBanca" => $idBanca,
                "uuid" => $uuid,
                "codigoBarra" => '',
            ]);
        else{
            $ticket = Tickets::on($servidor)->where(["idBanca" => $idBanca, "uuid" => $uuid])->orderBy("id", "desc")->limit(1)->first();
            if($ticket == null)
                $ticket = Tickets::on($servidor)->create([
                    "idBanca" => $idBanca,
                    "uuid" => $uuid,
                    "codigoBarra" => '',
                ]);
            
            // if(Sales::on($servidor))

        }

        return $ticket;
    }
}