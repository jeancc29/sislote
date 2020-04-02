<?php

namespace App\Observers;

use App\Blocksplays;
use App\Stock;
use App\Events\BlocksplaysEvent;

class BlocksplaysObserver
{
    /**
     * Handle the Blocksplays "created" event.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return void
     */
    public function created(Blocksplays $blocksplays)
    {
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

        $blocksplays = Blocksplays::whereId($blocksplays->id)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->first();
        if($blocksplays != null)
            event(new BlocksplaysEvent($blocksplays));
    }

    /**
     * Handle the Blocksplays "updated" event.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return void
     */
    public function updated(Blocksplays $blocksplays)
    {
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

        $blocksplays = Blocksplays::whereId($blocksplays->id)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->first();
        if($blocksplays != null)
            event(new BlocksplaysEvent($blocksplays));
    }

    /**
     * Handle the Blocksplays "deleted" event.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return void
     */
    public function deleted(Blocksplays $blocksplays)
    {
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        $stock = Stock::
            whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->where([
                'jugada' => $blocksplays->jugada, 
                'esGeneral' => 0, 
                'idBanca' => $blocksplays->idBanca,
                'idLoteria' => $blocksplays->idLoteria,
                'idSorteo' => $blocksplays->idSorteo,
                'idMoneda' => $blocksplays->idMoneda,
                ])
            ->first();

        if($stock != null){
            $stock->delete();
        }
        event(new BlocksplaysEvent($blocksplays, true));
    }

    /**
     * Handle the Blocksplays "restored" event.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return void
     */
    public function restored(Blocksplays $blocksplays)
    {
        //
    }

    /**
     * Handle the Blocksplays "force deleted" event.
     *
     * @param  \App\Blocksplays  $blocksplays
     * @return void
     */
    public function forceDeleted(Blocksplays $blocksplays)
    {
        //
    }
}
