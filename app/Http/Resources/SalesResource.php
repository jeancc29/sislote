<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Salesdetails;
use App\Draws;
use App\Users;
use App\Cancellations;
use App\Lotteries;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt; 

class SalesResource extends JsonResource
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
            'idUsuario' => $this->idUsuario,
            'usuario' => $this->usuario->usuario,
            'idBanca' => $this->idBanca,
            'codigo' => $this->banca->codigo,
            'banca' => $this->banca,
            'total' => $this->total,
            'descuentoPorcentaje' => $this->descuentoPorcentaje,
            'descuentoMonto' => $this->descuentoMonto,
            'hayDescuento' => $this->hayDescuento,
            'subTotal' => $this->subTotal,
            'idTicket' => $this->idTicket,
            'ticket' => $this->ticket->id,
            'codigoBarra' => $this->ticket->codigoBarra,
            'codigoQr' => base64_encode($this->ticket->codigoBarra),
            'status' => $this->status, 
            'created_at' => $this->created_at,
            'pagado' => $this->pagado,
            'premio' => Salesdetails::where('idVenta', $this->id)->sum('premio'),
            'razon' => Cancellations::where('idTicket', $this->idTicket)->value('razon'),
            'usuarioCancelacion' => Users::whereId(Cancellations::where('idTicket', $this->idTicket)->value('idUsuario'))->first(),
            'fechaCancelacion' => Cancellations::where('idTicket', $this->idTicket)->value('created_at'),
            'loterias' => Lotteries::whereIn(
                            'id',
                            Salesdetails::distinct()->select('idLoteria')->where('idVenta', $this->id)->get()->map(function($id){
                                return $id->idLoteria;
                            }) 
                        )->get(),
            'jugadas' => collect(Salesdetails::where('idVenta', $this->id)->get())->map(function($d){
                $sorteo = Draws::whereId($d['idSorteo'])->first()->descripcion;
                return ['id' => $d['id'], 'idVenta' => $d['idVenta'], 'jugada' => $d['jugada'], 'idLoteria' => $d['idLoteria'], 'idSorteo' => $d['idSorteo'], 'monto' => $d['monto'], 'premio' => $d['premio'], 'status' => $d['status'], 'sorteo' => $sorteo];
            }),
            'fecha' => (new Carbon($this->created_at))->toDateString() . " " . (new Carbon($this->created_at))->format('g:i A')
        ];
    }
}
