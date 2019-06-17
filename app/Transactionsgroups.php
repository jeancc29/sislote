<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactionsgroups extends Model
{
    protected $fillable = [
        'idUsuario',
    ];

    public function transacciones()
    {
        return $this->belongsToMany('App\Transactions', 'transaction_transactionsgroup', 'idGrupo', 'idTransaccion');
    }

    public function usuario()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Users', 'id', 'idUsuario');
    }
}
