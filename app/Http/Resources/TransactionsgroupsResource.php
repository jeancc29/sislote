<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TransactionsResource;

class TransactionsgroupsResource extends JsonResource
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
            'usuario' => $this->usuario,
            'transacciones' => TransactionsResource::collection($this->transacciones),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
