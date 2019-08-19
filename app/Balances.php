<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balances extends Model
{
    protected $fillable = [
        'idTipoEntidad', 'idEntidad', 'balance'
    ];
}
