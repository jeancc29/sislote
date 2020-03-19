<?php

namespace App\Observers;

use App\Stock;
use App\Realtime;
use App\Events\RealtimeStockEvent;


class PruebaStockObserver
{
    /**
     * Handle the stock "created" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function created(Stock $stock)
    {
    }

    /**
     * Handle the stock "updated" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function updated(Stock $stock)
    {
        Realtime::create(['idAfectado' => $stock->id, 'tabla' => 'stock']);
        event(RealtimeStockEvent());
    }

    /**
     * Handle the stock "deleted" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function deleted(Stock $stock)
    {
        //
    }

    /**
     * Handle the stock "restored" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function restored(Stock $stock)
    {
        //
    }

    /**
     * Handle the stock "force deleted" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function forceDeleted(Stock $stock)
    {
        //
    }
}
