<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amortization extends Model
{
    protected $fillable = [
        'idPrestamo', 
        'fecha', 
        'numeroCuota', 
        'montoCuota', 
        'montoCapital', 
        'montoInteres',
        'amortizacion',
        'montoPagado',
        'montoPagadoCapital',
        'montoPagadoInteres',
    ];
}
