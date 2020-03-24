<?php

namespace App\Observers;

use App\Blocksplays;
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
