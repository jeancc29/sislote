<?php

namespace App\Observers;
use App\Blockslotteries;
use App\Events\BlockslotteriesEvent;


class BlockslotteriesObserver
{
    /**
     * Handle the Blockslotteries "created" event.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return void
     */
    public function created(Blockslotteries $blockslotteries)
    {
        event(new BlockslotteriesEvent($blockslotteries));
    }

    /**
     * Handle the Blockslotteries "updated" event.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return void
     */
    public function updated(Blockslotteries $blockslotteries)
    {
        event(new BlockslotteriesEvent($blockslotteries));
    }

    /**
     * Handle the Blockslotteries "deleted" event.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return void
     */
    public function deleted(Blockslotteries $blockslotteries)
    {
        event(new BlockslotteriesEvent($blockslotteries, true));
    }

    /**
     * Handle the Blockslotteries "restored" event.
     *
     * @param  \App\Blockslotteries  $blockslotteries
     * @return void
     */
    public function restored(Blockslotteries $blockslotteries)
    {
        //
    }

    /**
     * Handle the blocksgenerals "force deleted" event.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return void
     */
    public function forceDeleted(Blocksgenerals $blocksgenerals)
    {
        //
    }
}

