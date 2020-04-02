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
        event(new RealtimeStockEvent(false, $stock));
    }

    /**
     * Handle the stock "updated" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function updated(Stock $stock)
    {
        event(new RealtimeStockEvent(false, $stock));
    }

    /**
     * Handle the stock "deleted" event.
     *
     * @param  \App\Stock  $stock
     * @return void
     */
    public function deleted(Stock $stock)
    {
        event(new RealtimeStockEvent(false, $stock, true));
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
