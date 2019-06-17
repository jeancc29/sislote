<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Types;
use App\Branches;
use App\Entity;

class TransactionsResource extends JsonResource
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
            'tipo' => $this->tipo,
            'usuario' => $this->usuario,
            'idTipoEntidad1' => $this->idTipoEntidad1,
            'idTipoEntidad2' => $this->idTipoEntidad2,
            'idEntidad1' => $this->idEntidad1,
            'idEntidad2' => $this->idEntidad2,
            'entidad1' => (Types::where(['renglon' => 'entidad', 'id' => $this->idTipoEntidad1])->first()->descripcion == 'Banca') ? Branches::whereId($this->idEntidad1)->first() : Entity::whereId($this->idEntidad1)->first(),
            'entidad2' => (Types::where(['renglon' => 'entidad', 'id' => $this->idTipoEntidad2])->first()->descripcion == 'Banca') ? Branches::whereId($this->idEntidad2)->first() : Entity::whereId($this->idEntidad2)->first(),
            'entidad1_saldo_inicial' => $this->entidad1_saldo_inicial,
            'entidad2_saldo_inicial' => $this->entidad2_saldo_inicial,
            'debito' => $this->debito,
            'credito' => $this->credito,
            'entidad1_saldo_final' => $this->entidad1_saldo_final,
            'entidad2_saldo_final' => $this->entidad2_saldo_final,
            'nota' => $this->nota,
            'nota_grupo' => $this->nota_grupo,
            'status' => $this->status
        ];
    }
}
