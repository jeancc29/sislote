<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Draws extends Model
{
    protected $fillable = [
        'descripcion', 'bolos', 'cantidadNumeros', 'status'
    ];

    public function loteriasRelacionadas()
    {
        return $this->belongsToMany('App\Lotteries', 'drawsrelations', 'idSorteo', 'idLoteria')->withPivot('idLoteriaPertenece');;
    }

    public function determinarSorteo($jugada){
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
}
