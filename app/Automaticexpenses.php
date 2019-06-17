<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Automaticexpenses extends Model
{
    protected $fillable = [
        'descripcion', 
        // 'fechaInicio', 
        'idBanca', 
        'monto', 
        'idFrecuencia',
        'idDia',
    ];

    public function frecuencia()
    {
        return $this->hasOne('App\Frecuency', 'id', 'idFrecuencia');
    }
}
