<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocksdirtygenerals extends Model
{
    protected $fillable = [
        'idLoteria', 'idSorteo', 'idMoneda', 'cantidad'
    ];
}
