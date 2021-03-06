<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AutomaticExpenses::class,
        Commands\Transactionsdraws::class,
        Commands\Transactionsloans::class,
        Commands\Transactionsdiasnolaborados::class,
        Commands\DeleteStockCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('transacciones:gastos')
                 ->everyMinute();
        $schedule->command('transacciones:sorteos')
                 ->everyMinute();
        $schedule->command('transacciones:caidaAcumulada')
                 ->everyMinute();
        $schedule->command('transacciones:prestamos')
                 ->everyMinute();
        $schedule->command('transacciones:nolaborados')
                 ->everyMinute();
        $schedule->command('eliminar:stock')
                 ->everyMinute();
        $schedule->command('prueba:a')
                 ->everyMinute();
                //  ->hourly();
                // ->cron('* * * * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
