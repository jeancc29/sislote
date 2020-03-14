<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        'nombre', 'status', 'idTipo', 'idMoneda'
    ];

    public function tipo()
    {
        return $this->hasOne('App\Types', 'id', 'idTipo');
    }
}
