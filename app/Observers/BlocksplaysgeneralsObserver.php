<?php

namespace App\Observers;

use App\Blocksplaysgenerals;
use App\Stock;
use App\Events\BlocksplaysgeneralsEvent;

class BlocksplaysgeneralsObserver
{
    /**
     * Handle the Blocksplaysgenerals "created" event.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return void
     */
    public function created(Blocksplaysgenerals $blocksplaysgenerals)
    {
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

        $blocksplaysgenerals = Blocksplaysgenerals::whereId($blocksplaysgenerals->id)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->first();

        if($blocksplaysgenerals != null)
            event(new BlocksplaysgeneralsEvent($blocksplaysgenerals));
    }

    /**
     * Handle the Blocksplaysgenerals "updated" event.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return void
     */
    public function updated(Blocksplaysgenerals $blocksplaysgenerals)
    {
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';

        $blocksplaysgenerals = Blocksplaysgenerals::whereId($blocksplaysgenerals->id)
        ->where('fechaDesde', '<=', $fechaInicial)
        ->where('fechaHasta', '>=', $fechaFinal)
        ->first();

        if($blocksplaysgenerals != null)
            event(new BlocksplaysgeneralsEvent($blocksplaysgenerals));
    }

    /**
     * Handle the Blocksplaysgenerals "deleted" event.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return void
     */
    public function deleted(Blocksplaysgenerals $blocksplaysgenerals)
    {
        $fecha = getdate();
        $fechaInicial = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 00:00:00';
        $fechaFinal = $fecha['year'].'-'.$fecha['mon'].'-'.$fecha['mday'] . ' 23:50:00';
        $stock = Stock::
            whereBetween('created_at', array($fechaInicial, $fechaFinal))
            ->where([
                'jugada' => $blocksplaysgenerals->jugada, 
                'esGeneral' => 1, 
                'idLoteria' => $blocksplaysgenerals->idLoteria,
                'idSorteo' => $blocksplaysgenerals->idSorteo,
                'idMoneda' => $blocksplaysgenerals->idMoneda,
                ])
            ->first();

        if($stock != null){
            $stock->delete();
        }
        event(new BlocksplaysgeneralsEvent($blocksplaysgenerals, true));
    }

    /**
     * Handle the Blocksplaysgenerals "restored" event.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return void
     */
    public function restored(Blocksplaysgenerals $blocksplaysgenerals)
    {
        //
    }

    /**
     * Handle the Blocksplaysgenerals "force deleted" event.
     *
     * @param  \App\Blocksplaysgenerals  $blocksplaysgenerals
     * @return void
     */
    public function forceDeleted(Blocksplaysgenerals $blocksplaysgenerals)
    {
        //
    }
}
