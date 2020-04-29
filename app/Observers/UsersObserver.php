<?php

namespace App\Observers;
use App\Users;
use App\Events\UsersEvent;

class UsersObserver
{
    /**
     * Handle the Users "created" event.
     *
     * @param  \App\Users  $user
     * @return void
     */
    public function created(Users $user)
    {
        event(new UsersEvent($user));
    }

    /**
     * Handle the Users "updated" event.
     *
     * @param  \App\Users  $user
     * @return void
     */
    public function updated(Users $user)
    {
        event(new UsersEvent($user));
    }

    /**
     * Handle the Users "deleted" event.
     *
     * @param  \App\Users  $user
     * @return void
     */
    public function deleted(Users $user)
    {
        // event(new UsersEvent($userusers, true));
    }

    /**
     * Handle the Users "restored" event.
     *
     * @param  \App\Users  $user
     * @return void
     */
    public function restored(Users $user)
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
