<?php

namespace App\Observers;

use App\Blocksdirtygenerals;
use App\Events\BlocksdirtygeneralsEvent;

class BlocksdirtygeneralsObserver
{
    /**
     * Handle the Blocksdirtygenerals "created" event.
     *
     * @param  \App\Blocksdirtygenerals  $Blocksdirtygenerals
     * @return void
     */
    public function created(Blocksdirtygenerals $blocksdirtygenerals)
    {
        event(new BlocksdirtygeneralsEvent($blocksdirtygenerals));
    }

    /**
     * Handle the Blocksdirtygenerals "updated" event.
     *
     * @param  \App\Blocksdirtygenerals  $Blocksdirtygenerals
     * @return void
     */
    public function updated(Blocksdirtygenerals $blocksdirtygenerals)
    {
        event(new BlocksdirtygeneralsEvent($blocksdirtygenerals));
    }

    /**
     * Handle the Blocksdirtygenerals "deleted" event.
     *
     * @param  \App\Blocksdirtygenerals  $Blocksdirtygenerals
     * @return void
     */
    public function deleted(Blocksdirtygenerals $blocksdirtygenerals)
    {
        event(new BlocksdirtygeneralsEvent($blocksdirtygenerals, true));
    }

    /**
     * Handle the Blocksdirtygenerals "restored" event.
     *
     * @param  \App\Blocksdirtygenerals  $Blocksdirtygenerals
     * @return void
     */
    public function restored(Blocksdirtygenerals $blocksdirtygenerals)
    {
        //
    }

    /**
     * Handle the Blocksdirtygenerals "force deleted" event.
     *
     * @param  \App\Blocksdirtygenerals  $Blocksdirtygenerals
     * @return void
     */
    public function forceDeleted(Blocksdirtygenerals $blocksdirtygenerals)
    {
        //
    }
}
