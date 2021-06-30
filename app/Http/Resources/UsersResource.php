<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'email' => $this->email,
            'servidor' => $this->servidor,
            'usuario' => $this->usuario,
            'idTipoUsuario' => $this->idRole,
            'tipoUsuario' => $this->roles->descripcion,
            'tipoUsuarioObject' => $this->roles,
            'grupo' => $this->group,
            'idGrupo' => $this->idGrupo,
            'status' => $this->status,
            'permisos' => $this->permisos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
