<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Terceros extends Model
{
    protected $fillable = [
        'nombres', 'status'
    ];
}
