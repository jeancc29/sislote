<?php

namespace App\Observers;

class NotificationObserver
{
     /**
     * Handle the Notification "created" event.
     *
     * @param  \App\Notification  $notification
     * @return void
     */
    public function created(Notification $notification)
    {
        event(new NotificationEvent($notification));
    }

    /**
     * Handle the Notification "updated" event.
     *
     * @param  \App\Notification  $notification
     * @return void
     */
    public function updated(Notification $notification)
    {
        event(new NotificationEvent($notification));
    }

    /**
     * Handle the Notification "deleted" event.
     *
     * @param  \App\Notification  $notification
     * @return void
     */
    public function deleted(Notification $notification)
    {
        event(new NotificationEvent($notification, true));
    }

    /**
     * Handle the Notification "restored" event.
     *
     * @param  \App\Notification  $notification
     * @return void
     */
    public function restored(Notification $notification)
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     *
     * @param  \App\Notification  $notification
     * @return void
     */
    public function forceDeleted(Notification $notification)
    {
        //
    }
}
