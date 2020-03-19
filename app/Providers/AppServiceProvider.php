<?php

namespace App\Providers;
use Carbon\Carbon;

use Illuminate\Support\ServiceProvider;
use App\Stock;
use App\Observers\PruebaStockObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale("es");
        Stock::observe(PruebaStockObserve::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
