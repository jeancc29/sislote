<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Androidversions extends Model
{
    protected $fillable = [
        'version', 
        'urgente', 
        'enlace', 
        'status'
    ];
}
