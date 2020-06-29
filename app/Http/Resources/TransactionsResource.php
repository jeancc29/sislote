<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Types;
use App\Branches;
use App\Entity;
use App\Http\Resources\BranchesResourceSmall;
use App\Http\Resources\EntityResource;

class TransactionsResource extends JsonResource
{
    protected $servidor;

    public function servidor($value){
        $this->servidor = $value;
        return $this;
    }

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
            'entidad1' => (Types::on($this->servidor)->where(['renglon' => 'entidad', 'id' => $this->idTipoEntidad1])->first()->descripcion == 'Banca') ? new BranchesResourceSmall(Branches::on($this->servidor)->whereId($this->idEntidad1)->first()) : Entity::on($this->servidor)->whereId($this->idEntidad1)->first(),
            'entidad2' => (Types::on($this->servidor)->where(['renglon' => 'entidad', 'id' => $this->idTipoEntidad2])->first()->descripcion == 'Banca') ? new BranchesResourceSmall(Branches::on($this->servidor)->whereId($this->idEntidad2)->first()) : Entity::on($this->servidor)->whereId($this->idEntidad2)->first(),
            'tipoEntidad2' => (Types::on($this->servidor)->where(['renglon' => 'entidad', 'id' => $this->idTipoEntidad2])->first()->descripcion == 'Banca') ? "Banca" : "Banco",
            'entidad1_saldo_inicial' => $this->entidad1_saldo_inicial,
            'entidad2_saldo_inicial' => $this->entidad2_saldo_inicial,
            'debito' => $this->debito,
            'credito' => $this->credito,
            'entidad1_saldo_final' => $this->entidad1_saldo_final,
            'entidad2_saldo_final' => $this->entidad2_saldo_final,
            'nota' => $this->nota,
            'nota_grupo' => $this->nota_grupo,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }

    public static function collection($resource){
        return new TransactionsResourceCollection($resource, get_called_class());
    }
}


class TransactionsResourceCollection extends ResourceCollection {

    protected $servidor;

    public function servidor($value){
        $this->servidor = $value;
        return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(TransactionsResource $resource) use($request){
            return $resource->servidor($this->servidor)->toArray($request);
    })->all();

    }
}
