<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    public function permisos()
    {
        return $this->belongsToMany('App\Permissions', 'permission_role', 'idRole', 'idPermiso');
    }
}
