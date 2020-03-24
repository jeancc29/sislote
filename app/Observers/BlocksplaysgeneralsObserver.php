<?php

namespace App\Observers;

use App\Blocksplaysgenerals;
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
