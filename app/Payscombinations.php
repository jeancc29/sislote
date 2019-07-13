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
        'primerPago',
        'pick3TodosEnSecuencia',
        'pick33Way',
        'pick36Way',
        'pick4TodosEnSecuencia',
        'pick44Way',
        'pick46Way',
        'pick412Way',
        'pick424Way',
    ];
}
