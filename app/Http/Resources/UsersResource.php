<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'usuario' => $this->usuario,
            'idTipoUsuario' => $this->idRole,
            'tipoUsuario' => $this->roles->descripcion,
            'status' => $this->status,
            'permisos' => $this->permisos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
