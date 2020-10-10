<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocksdirty extends Model
{
    protected $fillable = [
        'idBanca', 'idLoteria', 'idSorteo', 'idMoneda', 'cantidad'
    ];
}
