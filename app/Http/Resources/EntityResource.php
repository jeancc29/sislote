<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Coins;

class EntityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $moneda = Coins::whereId($this->idMoneda)->first();
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'status' => $this->status,
            'idMoneda' => $this->status,
            'moneda' => ($moneda != null) ? $moneda->descripcion : null,
            'tipo' => $this->tipo
        ];
    }
}
