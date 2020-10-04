<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesdetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'idVenta' => $this->idVenta,
            'idLoteria' => $this->idLoteria,
            'idSorteo' => $this->idSorteo,
            'sorteo' => $this->sorteo->descripcion,
            'jugada' => $this->jugada,
            'monto' => $this->monto,
        ];
    }
}
