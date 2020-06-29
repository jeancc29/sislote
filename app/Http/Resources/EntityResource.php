<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Coins;

class EntityResource extends JsonResource
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
        $moneda = Coins::on($this->servidor)->whereId($this->idMoneda)->first();
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'status' => $this->status,
            'idMoneda' => $this->status,
            'moneda' => ($moneda != null) ? $moneda->descripcion : null,
            'tipo' => $this->tipo
        ];
    }

public static function collection($resource){
        return new EntityResourceCollection($resource, get_called_class());
    }
    
}

class EntityResourceCollection extends ResourceCollection {

    protected $servidor;

    public function servidor($value){
        $this->servidor = $value;
        return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(EntityResource $resource) use($request){
            return $resource->servidor($this->servidor)->toArray($request);
    })->all();

    }
}

