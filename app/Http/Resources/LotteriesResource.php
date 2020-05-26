<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

use App\Blockslotteries;
use App\Salesdetails;

class LotteriesResource extends JsonResource
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
            'abreviatura' => $this->abreviatura,
            'horaCierre' => $this->horaCierre,
            'status' => $this->status,
            'dias' => $this->dias,
            'sorteos' => $this->sorteos,
            'pagosCombinaciones' => $this->pagosCombinaciones,
            'loteriasRelacionadas' => $this->drawRelations,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quiniela' => Blockslotteries::on($this->servidor)->where(['idLoteria' => $this->id, 'idSorteo' => 1])->value('monto'),
            'pale' => Blockslotteries::on($this->servidor)->where(['idLoteria' => $this->id, 'idSorteo' => 2])->value('monto'),
            'tripleta' => Blockslotteries::on($this->servidor)->where(['idLoteria' => $this->id, 'idSorteo' => 3])->value('monto'),
            'bloqueosjugadas' => $this->blocksplays()
                ->whereStatus(1)
                ->where('fechaDesde', '<=', getdate()['year'].'-'.getdate()['mon'].'-'.getdate()['mday'] . ' 00:00:00')
                ->where('fechaHasta', '>=', getdate()['year'].'-'.getdate()['mon'].'-'.getdate()['mday'] . ' 23:50:00')
                ->get(),
            // 'ventas' => Salesdetails::join('sales', 'salesdetails.idVenta', 'sales.id')->whereNotIn('status', [0,5])->where('salesdetails.idLoteria', $this->id)->sum('salesdetails.monto'),
            // 'premios' => Salesdetails::join('sales', 'salesdetails.idVenta', 'sales.id')->whereNotIn('status', [0,5])->where('salesdetails.idLoteria', $this->id)->sum('salesdetails.premio')
        ];
    }
    public static function collection($resource){
        return new LotteriesResourceCollection($resource, get_called_class());
    }
    
}

class LotteriesResourceCollection extends ResourceCollection {

    protected $servidor;

    public function servidor($value){
        $this->servidor = $value;
        return $this;
    }

    public function toArray($request){
        return $this->collection->map(function(LotteriesResource $resource) use($request){
            return $resource->servidor($this->servidor)->toArray($request);
    })->all();

    }
}