<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userssesions extends Model
{
    protected $fillable = [
        'idUsuario', 'esCelular'
    ];
}
