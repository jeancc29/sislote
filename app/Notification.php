<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'titulo', 
        'subtitulo', 
        'contenido', 
        'estado'
    ];
}
