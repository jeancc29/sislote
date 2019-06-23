<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Days extends Model
{
    protected $fillable = [
        'descripcion', 'wday'
    ];

    public function bancas()
    {
        return $this->belongsToMany('App\Branches', 'branches_days', 'idDia', 'idBanca')->withPivot('horaApertura','horaCierre');
    }
}
