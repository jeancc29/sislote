<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commissions extends Model
{
    protected $fillable = [
        'idBanca', 
        'idLoteria', 
        'directo', 
        'pale',
        'tripleta',
        'superPale',
        'pick3Straight',
        'pick3Box',
        'pick4Straight',
        'pick4Box',
    ];
}
