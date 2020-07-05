<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LotteriesSmallResource extends JsonResource
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
        $fecha = getdate();
        $dia = $this->dias()->whereWday($fecha['wday'])->first();
        return [
            'id' => $this->id,
            'descripcion' => $this->descripcion,
            'abreviatura' => $this->abreviatura,
            'sorteos' => $this->sorteos,
            'dias' => $this->dias,
            // 'horaCierre' => $dia->pivot->horaCierre,
            // 'minutosExtras' => $dia->pivot->minutosExtras,
        ];
    }
}
