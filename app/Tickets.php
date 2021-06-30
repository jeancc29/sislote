<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $fillable = [
        'idBanca', 'codigoBarra', 'imageBase64', 'codigoBarraAntiguo', 'uuid'
    ];
}
