<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lotteries extends Model
{
    protected $fillable = [
        'descripcion', 
        'abreviatura', 
        'horaCierre', 
        'status'
    ];

    // public function dias()
    // {
    //     return $this->belongsToMany('App\Days', 'day_lottery', 'idLoteria', 'idDia');
    // }

    public function dias()
    {
        return $this->belongsToMany('App\Days', 'day_lottery', 'idLoteria', 'idDia')->withPivot('horaApertura','horaCierre', 'minutosExtras');
    }

    public function sorteos()
    {
        return $this->belongsToMany('App\Draws', 'draw_lottery', 'idLoteria', 'idSorteo');
    }

    public function drawRelations()
    {
        return $this->belongsToMany('App\Draws', 'drawsrelations', 'idLoteriaPertenece', 'idSorteo')->withPivot('idLoteria');;
    }

    public function pagosCombinaciones()
    {
        return $this->hasOne('App\Payscombinations', 'idLoteria');
    }
    public function blocksplays()
    {
        return $this->hasOne('App\Blocksplays', 'idLoteria');
    }

    public function sorteoExiste($idSorteo){
        // if(strlen($jugada) == 2){
        //     $sorteo = 'Directo';
        // }
        // else if(strlen($jugada) == 4){
        //         $sorteo = 'Pale';
        // }
        // else if(strlen($jugada) == 6){
        //         $sorteo = 'Tripleta';
        // }else if(strlen($jugada) == 5){
        //     //Falta validar si el quinto caracter es un signo de mas
        //     $sorteo = 'Super pale';
        // }

        
        if($this->sorteos()->wherePivot('idSorteo', $idSorteo)->first() != null)
            return true;
        else
            return false;
    }

    public function abreHoy(){
        $abre = false;
        $fecha = getdate();
        if($this->dias()->whereWday($fecha['wday'])->first() != null)
            $abre = true;   
        
        return $abre;
    }


    public function cerrada($calcularHoraCierreMasMinutosExtras = false){
        $cerrado = false;
        $fecha = getdate();

        if($this->abreHoy()){
            // $hora = explode(':',$this->dias()->whereWday($fecha['wday'])->first()->pivot->horaCierre);
            $dia = $this->dias()->whereWday($fecha['wday'])->first();
            $hora = explode(":", $dia->pivot->horaCierre);
            if($calcularHoraCierreMasMinutosExtras)
                $hora[1] = (int)$hora[1] + $dia->pivot->minutosExtras; //Sumamos minutos extras a los minutos normales de la hora de cierre
            if((int)$fecha['hours'] > (int)$hora[0])
                $cerrado = true;
            else if((int)$hora[0] == (int)$fecha['hours']){
                //Validamos si los minutos actuales son mayores que los minutos horaCierre  
                if((int)$fecha['minutes'] >= (int)$hora[1])
                    $cerrado = true;
            }
        }else{
            $cerrado = true;
        }

        

        return $cerrado;
    }


    public function abierta(){
        $abierta = false;
        $fecha = getdate();

        if($this->abreHoy()){
            $hora = explode(':',$this->dias()->whereWday($fecha['wday'])->first()->pivot->horaApertura);
            if((int)$fecha['hours'] > (int)$hora[0])
                $abierta = true;
            else if((int)$hora[0] == (int)$fecha['hours']){
                //Validamos si los minutos actuales son mayores que los minutos horaCierre  
                if((int)$fecha['minutes'] > (int)$hora[1])
                    $abierta = true;
            }
        }

        return $abierta;
    }

    public static function customAll($servidor){
        return \DB::connection($servidor)->select("
            SELECT
                l.id,
                l.descripcion,
                l.abreviatura,
                (
                    SELECT 
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', draws.id,
                                'descripcion', draws.descripcion
                            )
                        )
                    FROM draws
                    INNER JOIN draw_lottery dl on dl.idSorteo = draws.id
                    WHERE dl.idLoteria = l.id
                ) sorteos
            FROM lotteries l
            WHERE l.status != 2
        ");
    }

}
