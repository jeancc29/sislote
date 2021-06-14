<?php

namespace App\Observers;
use App\Branches;
use App\Events\BranchesEvent;

class BranchesObserver
{
    /**
     * Handle the Branches "created" event.
     *
     * @param  \App\Branches  $branch
     * @return void
     */
    public function created(Branches $branch)
    {
        event(new BranchesEvent($branch));
    }

    /**
     * Handle the Branches "updated" event.
     *
     * @param  \App\Branches  $branch
     * @return void
     */
    public function updated(Branches $branch)
    {
        event(new BranchesEvent($branch));
    }

    /**
     * Handle the Branches "deleted" event.
     *
     * @param  \App\Branches  $branch
     * @return void
     */
    public function deleted(Branches $branch)
    {
        event(new BranchesEvent($branch));
    }

    /**
     * Handle the Branches "restored" event.
     *
     * @param  \App\Branches  $branch
     * @return void
     */
    public function restored(Branches $branch)
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
