<?php

namespace App\Observers;

use App\Blocksgenerals;
use App\Events\BlocksgeneralsEvent;

class BlocksgeneralsObserver
{
    /**
     * Handle the blocksgenerals "created" event.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return void
     */
    public function created(Blocksgenerals $blocksgenerals)
    {
        event(new BlocksgeneralsEvent($blocksgenerals));
    }

    /**
     * Handle the blocksgenerals "updated" event.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return void
     */
    public function updated(Blocksgenerals $blocksgenerals)
    {
        event(new BlocksgeneralsEvent($blocksgenerals));
    }

    /**
     * Handle the blocksgenerals "deleted" event.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return void
     */
    public function deleted(Blocksgenerals $blocksgenerals)
    {
        event(new BlocksgeneralsEvent($blocksgenerals, true));
    }

    /**
     * Handle the blocksgenerals "restored" event.
     *
     * @param  \App\Blocksgenerals  $blocksgenerals
     * @return void
     */
    public function restored(Blocksgenerals $blocksgenerals)
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
