<?php

namespace App\Observers;

use App\blocksplays;

class BlocksplaysObserver
{
    /**
     * Handle the blocksplays "created" event.
     *
     * @param  \App\blocksplays  $blocksplays
     * @return void
     */
    public function created(blocksplays $blocksplays)
    {
        //
    }

    /**
     * Handle the blocksplays "updated" event.
     *
     * @param  \App\blocksplays  $blocksplays
     * @return void
     */
    public function updated(blocksplays $blocksplays)
    {
        //
    }

    /**
     * Handle the blocksplays "deleted" event.
     *
     * @param  \App\blocksplays  $blocksplays
     * @return void
     */
    public function deleted(blocksplays $blocksplays)
    {
        //
    }

    /**
     * Handle the blocksplays "restored" event.
     *
     * @param  \App\blocksplays  $blocksplays
     * @return void
     */
    public function restored(blocksplays $blocksplays)
    {
        //
    }

    /**
     * Handle the blocksplays "force deleted" event.
     *
     * @param  \App\blocksplays  $blocksplays
     * @return void
     */
    public function forceDeleted(blocksplays $blocksplays)
    {
        //
    }
}
