<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salesdetails extends Model
{
    protected $fillable = [
        'idLoteria', 'idSorteo', 'premio', 'monto', 'jugada', 'status', 'idVenta', 'comision', 'pagado'
    ];

    public static function loterias()
    {
        //Modelo, foreign key, local key
        return $this->hasMany('App\Lotteries', 'id', 'idLoteria');
    }
}
