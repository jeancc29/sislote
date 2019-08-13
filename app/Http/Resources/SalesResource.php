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
use App\Classes\TicketPrintClass;
use App\Logs;

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
            'montoPagado' => Salesdetails::where(['idVenta' => $this->id, 'pagado' => 1])->sum('premio'),
            'premio' => Salesdetails::where('idVenta', $this->id)->sum('premio'),
            'montoAPagar' => Salesdetails::where(['idVenta' => $this->id, 'pagado' => 0])->sum('premio'),
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
                $pagadoPor = null;
                $fechaPagado = null;
                if($d['pagado'] == 1){
                    $logs = Logs::where(['tabla' => 'salesdetails', 'idRegistroTablaAccion' => $d['id']])->first();
                    if($logs != null){
                        $pagadoPor = $logs->usuario->usuario;
                        $fechaPagado = $logs->created_at;
                    }
                }
                return ['id' => $d['id'], 'idVenta' => $d['idVenta'], 'jugada' => $d['jugada'], 'idLoteria' => $d['idLoteria'], 'idSorteo' => $d['idSorteo'], 'monto' => $d['monto'], 'premio' => $d['premio'], 'pagado' => $d['pagado'], 'status' => $d['status'], 'sorteo' => $sorteo, 'pagadoPor' => $pagadoPor, 'fechaPagado' => $fechaPagado];
            }),
            'fecha' => (new Carbon($this->created_at))->toDateString() . " " . (new Carbon($this->created_at))->format('g:i A'),
            'img' =>  (new TicketPrintClass($this->id))->generate()
        ];
    }
}
