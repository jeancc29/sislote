<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Stock;
use App\Observers\PruebaStockObserver;
use App\Blockslotteries;
use App\Observers\BlockslotteriesObserver;
use App\Blocksplays;
use App\Observers\BlocksplaysObserver;
use App\Blocksgenerals;
use App\Observers\BlocksgeneralsObserver;
use App\Blocksplaysgenerals;
use App\Observers\BlocksplaysgeneralsObserver;
use App\Users;
// use App\Observers\UsersObserver;

use App\Observers\BlocksdirtyObserver;
use App\Observers\BlocksdirtygeneralsObserver;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Stock::observe(PruebaStockObserver::class);
        Blockslotteries::observe(BlockslotteriesObserver::class);
        Blocksplays::observe(BlocksplaysObserver::class);
        Blocksgenerals::observe(BlocksgeneralsObserver::class);
        Blocksplaysgenerals::observe(BlocksplaysgeneralsObserver::class);
        \App\Blocksdirty::observe(BlocksdirtyObserver::class);
        \App\Blocksdirtygenerals::observe(BlocksdirtygeneralsObserver::class);
        // Users::observe(UsersObserver::class);
    }
}
