<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocksgenerals extends Model
{
    protected $fillable = [
        'idDia', 
        'idLoteria', 
        'monto', 
        'idSorteo',
        'idMoneda'
    ];
}
