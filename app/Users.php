<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Events\UsersEvent;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombres', 'email', 'password', 'status', 'usuario', 'idRole', "servidor"
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
        $bancaUsuario = Branches::on($this->servidor)->where(["idUsuario" => $this->id, "status" => 1])->first();
        if($bancaUsuario != null){
            if($bancaUsuario->id == $idBanca)
                return true;
        }
        return false;
    }

    // public static function boot(){
    //     parent::boot();

    //     static::updated(function($user){
    //         event(new UsersEvent($user));
    //     });
    // }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
