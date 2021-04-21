<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MyMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mymigrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea migraciones para todos los servidores';

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

            $result = count(\DB::select("SHOW DATABASES LIKE '$servidor'"));
            if($result == 0){
                $this->info("mymigrate: $servidor nooo existe");
                continue;
            }
            
            $this->info("mymigrate: $servidor existe");
            // \Artisan::call("migrate --database=$servidor");
            \Artisan::call('migrate', array('--database' => $servidor));

        endforeach;
    }
}
