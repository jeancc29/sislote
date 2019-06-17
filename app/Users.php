<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombres', 'email', 'password', 'status', 'usuario', 'idRole'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function permisos()
    {
        return $this->belongsToMany('App\Permissions', 'permission_user', 'idUsuario', 'idPermiso');
    }

    public function roles()
    {
        return $this->hasOne('App\Roles', 'id', 'idRole');
    }

    public function tienePermiso($permiso){
        if($this->permisos()->whereDescripcion($permiso)->first() != null && $this->status == 1)
            return true;
        else
            return false;
    }

    public function esBancaAsignada($idBanca){
        $banca = Branches::whereId($idBanca)->first();
        if($banca != null){
            if($banca->id == $idBanca)
                return true;
        }
        return false;
    }
}
