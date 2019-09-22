<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Draws;
use App\Sales;
use App\Lotteries;
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
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;

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



        // $monto = (new Helper)->montodisponible("55", 1, 1);

        // //$h = new TicketClass(17);
        // $a = new AwardsClass(7);
        // $a->primera = "25";
        // $a->segunda = "02";
        // $a->tercera = "23";
        // $a->pick3 = 111;
        // $a->pick4 = 1234;

        $loteria = Lotteries::whereId(3)->first();
        $sorteo = Draws::whereDescripcion("Pick 3 Box")->first();
        // $this->info("Awardsclasss: " . Helper::decimalesDelMontoJugadoSonValidos("0.50", $loteria, $sorteo));
        // $this->info("Awardsclasss: " . Helper::determinarSorteo("123+", $loteria));
        $usuario = Users::whereUsuario('jean')->first();
        $usuario2 = Users::whereUsuario('u2')->first();
        // $this->info("Awardsclasss: " . Helper::loteriasOrdenadasPorHoraCierre($usuario));
        // $this->info("Awardsclasss2: " . Helper::loteriasOrdenadasPorHoraCierre($usuario2));


        // $awardsClass = new AwardsClass(3);
        // $awardsClass->fecha = "2019-09-05";
        // $awardsClass->idUsuario = 1;
        // $awardsClass->primera = "06";
        // $awardsClass->segunda = "09";
        // $awardsClass->tercera = "25";
        // $awardsClass->pick3 = null;
        // $awardsClass->pick4 = null;
        // $awardsClass->numerosGanadores = "694925";

        // $sorteo = Draws::whereDescripcion("Super pale")->first();
        // $premio = $awardsClass->paleBuscarPremio(1, 1, "0609", "1", $sorteo->id);

        // $this->info("1ra:" . Helper::amortizar(100, 0, 3, 0, 1, '2019-09-06', false, true));
        // $a = Helper::indexPost();
       // $a = Helper::guardarVenta(1, 11, '$2y$10$bWmfs4W10Rj.9r6GtIbsOOXl6eJfBVytTyMFJqSA6KBbpxYVo2nAa', 0, 0, 0, 10, '[{"idLoteria" : "1", "jugada" : "06", "monto" : 300}, {"idLoteria" : "2", "jugada" : "09", "monto" : 12}, {"idLoteria" : "2", "jugada" : "0998", "monto" : 1}]');

        
        // $v = new BranchesResource(Branches::find(1));
        $this->info("1ra:" . helper::premiosPorBanca(1));


        // $awardsClass = new AwardsClass($l['id']);
        // $awardsClass->idUsuario = $datos['idUsuario'];
        // $awardsClass->primera = $l['primera'];
        // $awardsClass->segunda = $l['segunda'];
        // $awardsClass->tercera = $l['tercera'];
        // $awardsClass->pick3 = $l['pick3'];
        // $awardsClass->pick4 = $l['pick4'];
        // $awardsClass->numerosGanadores = $l['primera'] . $l['segunda'] . $l['tercera'];

        //$a->pick3BuscarPremio(1, 1, '122', 1, false);

        $c = "bueno";

        try {
            //code...
            number_format("adfa");
        } catch (\Throwable $th) {
            //throw $th;
            $c = "malo";
        }



        // $this->info("Awardsclasss: " . Helper::comisionesPorBanca(1));
        // $this->info("Awardsclasss: " . Helper::comisionesPorLoteria( 1, '2019-07-31 00:00:00', '2019-07-31 23:00:00'));
        // $this->info("Awardsclasss: " . $a->combinacionesNula());
    //    $this->info("Awardsclasss: " . Helper::contarNumerosIdenticos("1211"));
    // $this->info("Awardsclasss pick3: " . $a->pick3BuscarPremio(1, 1, '123', 1, true));
    // $this->info("Awardsclasss pick3: " . $a->pick3BuscarPremio(1, 1, '131', 1, false));
    // $this->info("Awardsclasss pick3: " . $a->pick3BuscarPremio(1, 1, '123', 1, false));

    //    $this->info("Awardsclasss: " . $a->pick4BuscarPremio(1, 1, '1234', 1, true));
    //    $this->info("Awardsclasss: " . $a->pick4BuscarPremio(1, 1, '1222', 1, false));
    //    $this->info("Awardsclasss: " . $a->pick4BuscarPremio(1, 1, '1221', 1, false));
    //    $this->info("Awardsclasss: " . $a->pick4BuscarPremio(1, 1, '2324', 1, false));
    //    $this->info("Awardsclasss: " . $a->pick4BuscarPremio(1, 1, '4231', 1, false));
    //    $this->info("Awardsclasss: " . $a->pick3BuscarPremio(1, 1, '122', 1, false));
    // $this->info("Awardsclasss: " . $c); 


        // $this->info(public_path("assets") . "\\");
        // $this->info(".." . rand(1111111111, getrandmax()));

    }
}
