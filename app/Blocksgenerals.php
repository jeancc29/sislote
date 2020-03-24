<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blocksgenerals extends Model
{
    protected $fillable = [
        'idDia', 
        'idLoteria', 
        'monto', 
        'idSorteo',
        'idMoneda'
    ];

    // public static function boot(){
    //     parent::boot();
    
    //     static::deleted(function($blocksgenerals){
    //         event(new BlocksgeneralsEvent($blocksgenerals, true));
    //     });
    // }
}
