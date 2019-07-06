<?php
namespace App\Classes;

use App\transactions;
use App\Types;
use App\Days;
use App\Lotteries;
use App\Stock;
use App\Blocksplays;
use App\Blockslotteries;

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


    function existe_sesion()
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
       else if(strlen($jugada) == 4){
           if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
                $idSorteo = 2;
            else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
                $idSorteo = 4;
       }
       else if(strlen($jugada) == 6){
            $idSorteo = 3;
       }

       return $idSorteo;
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
       else if(strlen($jugada) == 4){
            if($loteria->sorteos()->whereDescripcion('Super pale')->first() == null || $loteria->drawRelations->count() <= 1)
                $idSorteo = 2;
            else if($loteria->sorteos()->whereDescripcion('Super pale')->first() != null || $loteria->drawRelations->count() >= 2)
                $idSorteo = 4;
       }
       else if(strlen($jugada) == 6){
            $idSorteo = 3;
       }
    
       $bloqueo = Stock::where([   
           'idLoteria' => $idLoteria, 
           'idBanca' => $idBanca, 
           'jugada' => $jugada
        ])
       ->whereBetween('created_at', array($fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00', $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00'))->value('monto');
       
    //Verificamos que la variable $stock no sea nula
    if($bloqueo == null){
        $bloqueo = Blocksplays::where(
            [
                'idBanca' => $idBanca,
                'idLoteria' => $idLoteria, 
                'jugada' => $jugada,
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

    function isNumber($number){
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