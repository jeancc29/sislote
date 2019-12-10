<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactionscheduled extends Model
{
    protected $fillable = [
        'idUsuario', 
        'idTipo',
        'fecha',
        'idTipoEntidad1',
        'idTipoEntidad2',
        'idEntidad1',
        'idEntidad2',
        'entidad1_saldo_inicial',
        'entidad2_saldo_inicial',
        'debito',
        'credito',
        'entidad1_saldo_final',
        'entidad2_saldo_final',
        'nota',
        'nota_grupo',
        'status',
        'idGasto',
        'idPrestamo',
        'idAmortizacion',
    ];
}
