<?php

namespace App\Http\Resources;
use App\Http\Resources\AutomaticexpensesResource;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Classes\Helper;

class BranchesResource extends JsonResource
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
            'descripcion' => $this->descripcion,
            'ip' => $this->ip,
            'codigo' => $this->codigo,
            'status' => $this->status,
            'imprimirCodigoQr' => $this->imprimirCodigoQr,
            'idUsuario' => $this->idUsuario,
            'usuario' => $this->usuario->usuario,
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
            'ventasDelDia' => Helper::ventasPorBanca($this->id),
            'descuentosDelDia' => Helper::descuentosPorBanca($this->id),
            'premiosDelDia' => Helper::premiosPorBanca($this->id),
            'comisionesDelDia' => Helper::comisionesPorBanca($this->id),
            'ticketsDelDia' => Helper::ticketsPorBanca($this->id)
        ];
    }
}
