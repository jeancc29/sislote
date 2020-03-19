<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Realtime;

class Stock extends Model
{
    protected $fillable = [
        'idLoteria', 'idSorteo', 'idBanca', 'montoInicial', 'monto', 'jugada', 'esBloqueoJugada', 'ignorarDemasBloqueos', 'idMoneda'
    ];

    // public static function boot(){
    //     parent::boot();

    //     static::updated(function($stock){
    //         Realtime::create(['idAfectado' => 999, 'tabla' => 'culooooo']);
    //     });
    // }
}
