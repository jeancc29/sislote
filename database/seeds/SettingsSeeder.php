<?php

use Illuminate\Database\Seeder;
use App\Settings as s;
use App\Coins as c;
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $servidores = \App\Server::on("mysql")->get();
        

        //creamos o actualizamos, los usuarios jean y sistema en las DB correspondientes a cada cliente
        foreach ($servidores as $ser):
            $servidor = $ser->descripcion;

            if(\App\Classes\Helper::dbExists($servidor) == false)
                continue;

                // $moneda = c::whereDescripcion("Dolar")->first();
                $tipoFormato1 = \App\Types::on($servidor)->where(["descripcion" => "Formato de ticket 1", "renglon" => "ticket"])->first();
                $tipoFormato2 = \App\Types::on($servidor)->where(["descripcion" => "Formato de ticket 2", "renglon" => "ticket"])->first();
                $settings = \App\Settings::on($servidor)->first();
                $moneda = \App\Coins::on($servidor)->orderBy("pordefecto", "desc")->first();

                if($settings != null){
                    if($settings->idTipoFormatoTicket == null)
                        $settings->idTipoFormatoTicket = ($tipoFormato1 != null) ? $tipoFormato1->id : null;

                    $settings->save();
                }else{
                    \App\Settings::on($servidor)->create([
                        "idMoneda" => $moneda->id,
                        "consorcio" => "",
                        "idTipoFormatoTicket" => ($tipoFormato1 != null) ? $tipoFormato1->id : null,
                    ]);
                }
                

        endforeach;

        
    }
}
