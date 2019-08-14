<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'id', 'idUsuario', 'idBanca', 'total', 'descuentoMonto', 'descuentoPorcentaje', 'hayDescuento', 'subTotal', 'idLoteria', 'idTicket'
    ];


    public function usuario()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Users', 'id', 'idUsuario');
    }

    public function banca()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Branches', 'id', 'idBanca');
    }

    public function ticket()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Tickets', 'id', 'idTicket');
    }

    public function cancelacion()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Cancellations', 'id', 'idTicket');
    }
}
