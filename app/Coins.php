<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{
    //

    protected $fillable = [
        'descripcion', 'permiteDecimales', 'equivalenciaDeUnDolar', 'abreviatura', 'pordefecto', 'color'
    ];
   
}
