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
        'nombres', 'email', 'password', 'status', 'usuario', 'idRole', "servidor", "idGrupo"
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

    public function group()
    {
        return $this->hasOne('App\Group', 'id', 'idGrupo');
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

    public function esAdministradorOProgramador(){
        $rol = $this->roles;
        if($rol == null)
            return false;

        return $rol->descripcion == "Programador" || $rol->descripcion == "Administrador"; 
    }

    public static function search($servidor, $data){
        return \DB::connection($servidor)->select("
            SELECT
                u.id,
                u.nombres,
                u.email,
                u.usuario,
                u.idRole as idTipoUsuario,
                u.status,
                t.descripcion tipoUsuario,
                JSON_OBJECT('id', t.id, 'descripcion', t.descripcion) tipoUsuarioObject,
                IF(g.id IS NULL, NULL, JSON_OBJECT('id', g.id, 'descripcion', g.descripcion, 'codigo', g.codigo)) grupo,
                u.idGrupo,
                u.created_at,
                u.updated_at,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', permissions.id, 
                                'descripcion', permissions.descripcion
                            )
                        )
                    FROM permissions INNER JOIN permission_user as pu ON pu.idPermiso = permissions.id
                    WHERE pu.idUsuario = u.id
                ) permisos

            FROM users u
            INNER JOIN types t ON t.id = u.idRole
            LEFT JOIN $servidor.groups g ON g.id = u.idGrupo
            WHERE 
                u.status != 2
                AND (u.nombres LIKE '%{$data}%' OR u.usuario LIKE '%{$data}%')
        ");
    }
}
