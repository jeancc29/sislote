<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Draws;
use App\Branches;
use Carbon\Carbon;
use App\transactions;
use App\Types;
use App\Users;
use App\Entity;
use App\Classes\Helper;
use App\Classes\AwardsClass;
use App\Classes\TicketClass;
use App\Http\Resources\AutomaticexpensesResource;

class Prueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prueba:a';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gastos automaticos';

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
         // $fichero = 'gente.txt';
        // // Abre el fichero para obtener el contenido existente
        // $actual = file_get_contents($fichero);
        // // Añade una nueva persona al fichero
        // $actual .= "John Smith\n";
        // // Escribe el contenido al fichero
        // file_put_contents($fichero, $actual);

        rand(111111111, getrandmax());



        $monto = (new Helper)->montodisponible("55", 1, 1);

        $h = new TicketClass(2);
        $a = new AwardsClass(2);
        $a->primera = "";
        $a->segunda = 02;
        $a->tercera = 23;

        // $this->info("Awardsclasss: " . $a->combinacionesNula());
       $this->info("Awardsclasss: " . $h->generate()); 

        // $this->info(public_path("assets") . "\\");
        // $this->info(".." . rand(1111111111, getrandmax()));

    }
}
