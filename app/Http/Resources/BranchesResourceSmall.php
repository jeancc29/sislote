<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchesResourceSmall extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $moneda = $this->moneda;
        return [
            'id' => $this->id,
            'descripcion' => $this->descripcion,
            'codigo' => $this->codigo,
            'idMoneda' => $this->idMoneda,
            'descontar' => $this->descontar,
            'deCada' => $this->deCada,
            'limiteVenta' => $this->limiteVenta,
            'moneda' => ($moneda != null) ? $moneda->descripcion : null,
            'monedaAbreviatura' => ($moneda != null) ? $moneda->abreviatura : null,
            'monedaColor' => ($moneda != null) ? $moneda->color : null,
        ];
    }
}
