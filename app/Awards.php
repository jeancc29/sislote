<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Awards extends Model
{
    protected $fillable = [
        'idUsuario', 'numeroGanador', 'idLoteria', 'idSorteo', 'primera', 'segunda', 
        'tercera', 'pick3', 'pick4'
    ];
}
