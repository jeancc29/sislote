<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transactions extends Model
{
    protected $fillable = [
        'idUsuario', 
        'idTipo',
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



    public function tipo()
    {
        return $this->hasOne('App\Types', 'id', 'idTipo');
    }

    public function usuario()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Users', 'id', 'idUsuario');
    }
    
}
