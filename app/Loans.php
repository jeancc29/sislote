<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    protected $fillable = [
        'idUsuario',
        'idTipoEntidadPrestamo',
        'idTipoEntidadFondo',
        'idEntidadPrestamo',
        'idEntidadFondo',
        'montoPrestado',
        'montoCuotas',
        'numeroCuotas',
        'tasaInteres',
        'mora',
        'status',
        'diasGracia',
        'detalles',
        'idFrecuencia',
        'fechaInicio',
        'idTipoAmortizacion',
    ];
}
