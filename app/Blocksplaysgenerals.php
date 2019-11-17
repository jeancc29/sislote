<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocksplaysgenerals extends Model
{
    protected $fillable = [
        'idLoteria', 
        'idSorteo', 
        'jugada', 
        'montoInicial',
        'monto',
        'fechaDesde',
        'fechaHasta',
        'idUsuario',
        'status',
        'ignorarDemasBloqueos'
    ];
}
