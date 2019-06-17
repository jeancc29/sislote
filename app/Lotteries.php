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
        return $this->belongsToMany('App\Days', 'day_lottery', 'idLoteria', 'idDia')->withPivot('horaApertura','horaCierre');
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


    public function cerrada(){
        $cerrado = false;
        $fecha = getdate();

        if($this->abreHoy()){
            $hora = explode(':',$this->dias()->whereWday($fecha['wday'])->first()->pivot->horaCierre);
            if((int)$fecha['hours'] > (int)$hora[0])
                $cerrado = true;
            else if((int)$hora[0] == (int)$fecha['hours']){
                //Validamos si los minutos actuales son mayores que los minutos horaCierre  
                if((int)$fecha['minutes'] > (int)$hora[1])
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

}
