<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SalesSmallResource extends JsonResource
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
            'idUsuario' => $this->idUsuario,
            'usuario' => $this->usuario->usuario,
            'idBanca' => $this->idBanca,
            'codigo' => $this->banca->codigo,
            'banca' => $this->banca,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'descuentoPorcentaje' => $this->descuentoPorcentaje,
            'descuentoMonto' => $this->descuentoMonto,
            'hayDescuento' => $this->hayDescuento,
            'subTotal' => $this->subTotal,
            'idTicket' => $this->idTicket,
            'ticket' => $this->ticket->id,
            'codigoBarra' => $this->ticket->codigoBarra,
            'codigoQr' => base64_encode($this->ticket->codigoBarra),
            'status' => $this->status, 
            'pagado' => $this->pagado,
            'montoPagado' => \App\Salesdetails::on($this->getConnectionName())->where(['idVenta' => $this->id, 'pagado' => 1])->sum('premio'),
            'premio' => \App\Salesdetails::on($this->getConnectionName())->where('idVenta', $this->id)->sum('premio'),
            'montoAPagar' => \App\Salesdetails::on($this->getConnectionName())->where(['idVenta' => $this->id, 'pagado' => 0])->sum('premio'),
            'razon' => \App\Cancellations::on($this->getConnectionName())->where('idTicket', $this->idTicket)->value('razon'),
            'usuarioCancelacion' => \App\Users::on($this->getConnectionName())->whereId(\App\Cancellations::on($this->getConnectionName())->where('idTicket', $this->idTicket)->value('idUsuario'))->first(),
            'fechaCancelacion' => \App\Cancellations::on($this->getConnectionName())->where('idTicket', $this->idTicket)->value('created_at'),
            'fecha' => (new Carbon($this->created_at))->toDateString() . " " . (new Carbon($this->created_at))->format('g:i A'),
        ];
    }
}
