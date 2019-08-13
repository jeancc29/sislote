<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $fillable = [
        'tabla', 
        'idRegistroTablaAccion',
        'idBanca',
        'idUsuario',
        'accion',
        'campo',
        'valor_viejo',
        'valor_nuevo',        
    ];

    public function usuario()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Users', 'id', 'idUsuario');
    }

}
