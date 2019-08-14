<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Idventatemporal extends Model
{
    protected $fillable = [
        'idBanca',
        'idVenta',
        'idVentaHash',
    ];
}
