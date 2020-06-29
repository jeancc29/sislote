<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use App\Stock;

class DeleteStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eliminar:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se eliminaran todos los stock para obtimizar las busquedas en esta tabla cuando se intente guardar una venta';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $servidores = \App\Server::on("mysql")->get();
        foreach ($servidores as $servi):
        $servidor = $servi->descripcion;

            $prueba = new Carbon("2019-01-20 24:10");
            $fecha = Carbon::now();
            $this->info('Eliminar:stock: ' . $prueba->hour);


            if($fecha->hour != 0)
                return;

            Stock::on($servidor)->truncate();
       
        endforeach;
    }
}
