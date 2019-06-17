<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blockslotteries extends Model
{
    protected $fillable = [
        'idBanca', 
        'idDia', 
        'idLoteria', 
        'monto', 
        'idSorteo'
    ];


    public function sorteos()
    {
        //Modelo, foreign key, foreign key, local key, local key
        return $this->hasMany('App\Draws', 'id', 'idSorteo');
    }
}
