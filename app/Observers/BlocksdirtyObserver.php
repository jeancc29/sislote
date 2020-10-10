<?php

namespace App\Observers;
use App\Blocksdirty;
use App\Events\BlocksdirtyEvent;

class BlocksdirtyObserver
{
    /**
     * Handle the Blocksdirty "created" event.
     *
     * @param  \App\Blocksdirty  $Blocksdirty
     * @return void
     */
    public function created(Blocksdirty $blocksdirty)
    {
        event(new BlocksdirtyEvent($blocksdirty));
    }

    /**
     * Handle the Blocksdirty "updated" event.
     *
     * @param  \App\Blocksdirty  $Blocksdirty
     * @return void
     */
    public function updated(Blocksdirty $blocksdirty)
    {
        event(new BlocksdirtyEvent($blocksdirty));
    }

    /**
     * Handle the Blocksdirty "deleted" event.
     *
     * @param  \App\Blocksdirty  $Blocksdirty
     * @return void
     */
    public function deleted(Blocksdirty $blocksdirty)
    {
        event(new BlocksdirtyEvent($blocksdirty, true));
    }

    /**
     * Handle the Blocksdirty "restored" event.
     *
     * @param  \App\Blocksdirty  $Blocksdirty
     * @return void
     */
    public function restored(Blocksdirty $blocksdirty)
    {
        //
    }

    /**
     * Handle the Blocksdirty "force deleted" event.
     *
     * @param  \App\Blocksdirty  $Blocksdirty
     * @return void
     */
    public function forceDeleted(Blocksdirty $blocksdirty)
    {
        //
    }
}
