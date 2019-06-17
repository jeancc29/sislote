<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutomaticexpensesResource extends JsonResource
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
            'idDia' => $this->idDia,
            'descripcion' => $this->descripcion,
            'monto' => $this->monto,
            'fechaInicio' => $this->fechaInicio,
            'banca' => $this->banca,
            'frecuencia' => $this->frecuencia,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
