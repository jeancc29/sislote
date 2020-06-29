<?php

namespace App\Http\Resources;
use App\Http\Resources\AutomaticexpensesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Classes\Helper;

class BranchesResource extends JsonResource
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
            'descripcion' => $this->descripcion,
            'ip' => $this->ip,
            'codigo' => $this->codigo,
            'status' => $this->status,
            'imprimirCodigoQr' => $this->imprimirCodigoQr,
            'idUsuario' => $this->idUsuario,
            'usuario' => $this->usuario->usuario,
            'idMoneda' => $this->idMoneda,
            'moneda' => $this->moneda->descripcion,
            'dueno' => $this->dueno,
            'localidad' => $this->localidad,

            'balanceDesactivacion' => $this->balanceDesactivacion,
            'limiteVenta' => $this->limiteVenta,
            'descontar' => $this->descontar,
            'deCada' => $this->deCada,
            'minutosCancelarTicket' => $this->minutosCancelarTicket,
            'piepagina1' => $this->piepagina1,
            'piepagina2' => $this->piepagina2,
            'piepagina3' => $this->piepagina3,
            'piepagina4' => $this->piepagina4,

            'dias' => $this->dias,
            'loterias' => $this->loterias,
            'pagosCombinaciones' => $this->pagosCombinaciones,
            'comisiones' => $this->comisiones,
            'gastos' => AutomaticexpensesResource::collection($this->gastos),
            'ventasDelDia' => Helper::ventasPorBanca($this->servidor, $this->id),
            'descuentosDelDia' => Helper::descuentosPorBanca($this->servidor, $this->id),
            'premiosDelDia' => Helper::premiosPorBanca($this->servidor, $this->id),
            'comisionesDelDia' => Helper::comisionesPorBanca($this->servidor, $this->id),
            'ticketsDelDia' => Helper::ticketsPorBanca($this->servidor, $this->id)
        ];
    }

    public static function collection($resource){
        return new BranchesResourceCollection($resource, get_called_class());
    }
    
}

class BranchesResourceCollection extends ResourceCollection {

    protected $servidor;

    public function servidor($value){
        $this->servidor = $value;
        return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(BranchesResource $resource) use($request){
            return $resource->servidor($this->servidor)->toArray($request);
    })->all();

    }
}
