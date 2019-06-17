<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payscombinations extends Model
{
    protected $fillable = [
        'idBanca', 
        'idLoteria', 
        'primera', 
        'segunda', 
        'tercera', 
        'primeraSegunda', 
        'primeraTercera', 
        'segundaTercera', 
        'tresNumeros', 
        'dosNumeros',
        'primerPago'
    ];
}
