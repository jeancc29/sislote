<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salesdetails extends Model
{
    protected $fillable = [
        'idLoteria', 'idLoteriaSuperpale', 'idSorteo', 'premio', 'monto', 'jugada', 'status', 'idVenta', 'comision', 'pagado'
    ];

    public function sorteo()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Draws', 'id', 'idSorteo');
    }

    public static function loterias()
    {
        //Modelo, foreign key, local key
        return $this->hasMany('App\Lotteries', 'id', 'idLoteria');
    }
}
