<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Realtime extends Model
{
    protected $fillable = [
        'idAfectado', 'tabla'
    ];
}
