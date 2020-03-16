<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocksplays extends Model
{
    protected $fillable = [
        'idBanca', 
        'idLoteria', 
        'idSorteo', 
        'jugada', 
        'montoInicial',
        'monto',
        'fechaDesde',
        'fechaHasta',
        'idUsuario',
        'status',
        'idMoneda'
    ];
}
