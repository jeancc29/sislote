<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'idLoteria', 'idSorteo', 'idBanca', 'montoInicial', 'monto', 'jugada', 'esBloqueoJugada', 'ignorarDemasBloqueos'
    ];
}
