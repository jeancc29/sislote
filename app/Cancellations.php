<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cancellations extends Model
{
    protected $fillable = [
        'idTicket', 'idUsuario', 'razon',
    ];
}
