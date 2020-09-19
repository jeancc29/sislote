<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\TransactionsResource;

class TransactionsgroupsResource extends JsonResource
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
            'usuario' => $this->usuario,
            'transacciones' => TransactionsResource::collection($this->transacciones)->servidor($this->servidor),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at
        ];
    }

    public static function collection($resource){
        return new TransactionsgroupsResourceCollection($resource, get_called_class());
    }
}


class TransactionsgroupsResourceCollection extends ResourceCollection {

    protected $servidor;

    public function servidor($value){
        $this->servidor = $value;
        return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(TransactionsgroupsResource $resource) use($request){
            return $resource->servidor($this->servidor)->toArray($request);
    })->all();

    }
}
