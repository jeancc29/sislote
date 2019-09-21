<?php
namespace App\Classes;

use App\Awards;
use Request;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\Response;


use Faker\Generator as Faker;
use App\Lotteries;
use App\Generals;
use App\Sales;
use App\Salesdetails;
use App\Blockslotteries;
use App\Blocksplays;
use App\Stock;
use App\Tickets;
use App\Cancellations;
use App\Days;
use App\Payscombinations;
use App\Draws;
use App\Branches;
use App\Users;
use App\Roles;
use App\Commissions;
use App\Permissions;

use App\Http\Resources\LotteriesResource;
use App\Http\Resources\SalesResource;
use App\Http\Resources\BranchesResource;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UsersResource;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;


class TicketToHtmlClass{
    private $html;
    private $banca;
    private $venta;
    private $ventasDetalles;
    private $usuario;
    public $copia = false;
    private $contadorJugadas;
    private $codigoBarra;

    function __construct($data){
        if($data[0]->venta != null){
            $this->venta = json_decode($data[0]->venta);
            $this->venta = $this->venta[0];
        }

        
       
        $this->banca = $this->venta->banca;
        $this->ventasDetalles = $this->venta->jugadas;
        $this->ventasDetalles = collect($this->ventasDetalles);
        $this->usuario = $this->venta->usuarioObject;
        $this->codigoBarra = $this->venta->codigoBarra;
    }

    function hola(){
        return $this->venta;
    }

    function generate(){
        $this->openHeader();
        $this->setBanca();
        $this->openTicket();
            if(!$this->copia)
                $this->setOriginal();

            $this->setFirstFecha();
            $this->setTicket();
            $this->setSecondFecha();

            if(!$this->copia)
                $this->setCodigoBarra();
            
            $this->openRowJugadas();
                $ventasDetallesOrdenadasPorLoteria = $this->ventasDetalles->sortByDesc('idLoteria');
                $idLoteria = 0;
                $loterias = $this->venta->loterias;
                //$loterias = collect($loterias);
                //return $loterias;
                foreach($loterias as $l){
                    $contadorJugadasLoteria = 0;
                    $yaAbrioSegundaTabla = false;
                    if($idLoteria != $l->id){
                        $idLoteria = $l->id;
                        $total = $this->getTotalLoteria($l->id);
                        $this->setLoteriaTotal($l->descripcion, $total);
                    }
                    
                        $this->openColXs6();
                    

                    $this->openTable();
                                $this->setTableHead();
                                $this->openTableBody();
                    foreach($this->ventasDetalles as $d){
                        if($l->id == $d->idLoteria){
                            $loteria = $l;
                        $contadorJugadasLoteria++;
                        //Si la variable $idLoteria es diferente de $loteria->id entonces agregamos el header con el nombre de la loteria y su total
                        // if($idLoteria != $loteria->id){
                        //     $idLoteria = $loteria->id;
                        //     $total = $this->getTotalLoteria($loteria->id);
                        //     $this->setLoteriaTotal($loteria->descripcion, $total);
                        // }
                        
                        
                        // if($this->contadorJugadas > 3 && $contadorJugadasLoteria == 1){
                        //     $this->openColXs6();
                        // }
                        if($this->contadorJugadas >= 2 && $yaAbrioSegundaTabla == false && $contadorJugadasLoteria > round($this->contadorJugadas / 2)){
                                $this->closeTableBody();
                                $this->closeTable();
                            $this->closeCol();

                            

                            $this->openColXs6();
                            $this->openTable();
                                $this->setTableHead();
                                $this->openTableBody();
                            $yaAbrioSegundaTabla = true;
                        }
                        // else{
                        //     $this->openColXs12();
                        // }
                            // $this->openTable();
                            //     $this->setTableHead();
                            //     $this->openTableBody();
                                    $jugada = Helper::agregarGuion($d->jugada, $d->idSorteo);
                                    $this->setJugada($jugada, $d->monto);
                        //         $this->closeTableBody();
                        //     $this->closeTable();
                        // $this->closeCol();

                        }//END IF loteria
                        
                    } //END foreach

                    

                    $this->closeTableBody();
                    $this->closeTable();
                $this->closeCol();

                if($this->contadorJugadas < 2){
                        $this->openColXs6();
                                $this->openTable();
                                $this->setTableHead();
                                $this->openTableBody();

                                $this->closeTableBody();
                                $this->closeTable();
                            $this->closeCol();
                    }
                }

                
            $this->closeRowJugadas();
            $this->setTotal();
            $this->setPieDePagina();
        $this->closeTicket();
        $this->closeHeader();

        //Ruta servidor
        // $output_file = public_path() . "\\assets\\ticket\\" . $this->venta->idTicket . ".html";
        // $file = fopen($output_file, "wb");
        // fwrite($file, $this->html);
        // fclose($file);

        // ob_start();
        // $command = "C:\\loteria\\public\\assets\\ticket\\wkhtmltoimage --zoom 2.125 --width 314 ";
        // $command .= "C:\\loteria\\public\\assets\\ticket\\" . $this->venta->idTicket . ".html ";
        // $command .= "C:\\loteria\\public\\assets\\ticket\\img\\" . $this->venta->idTicket . ".png";
        // system($command, $return_var);
        // $salida = \ob_get_contents();
        // \ob_end_clean();

        // $ruta = public_path() . "\\assets\\ticket\\img\\" . $this->venta->idTicket . ".png";
        // $img = \file_get_contents($ruta);
        // $data = base64_encode($img);

        /*************** RUTA PC DEBUG **************************/
        // $output_file = public_path() . "\\assets\\ticket\\" . $this->venta->idTicket . ".html";
        // $file = fopen($output_file, "wb");
        // fwrite($file, $this->html);
        // fclose($file);

        // ob_start();
        // $command = "C:\\loterias\\lote\\public\\assets\\ticket\\wkhtmltoimage --zoom 2.125 --width 314 ";
        // $command .= "C:\\loterias\\lote\\public\\assets\\ticket\\" . $this->venta->idTicket . ".html ";
        // $command .= "C:\\loterias\\lote\\public\\assets\\ticket\\img\\" . $this->venta->idTicket . ".png";
        // system($command, $return_var);
        // $salida = \ob_get_contents();
        // \ob_end_clean();

        // $ruta = public_path() . "\\assets\\ticket\\img\\" . $this->venta->idTicket . ".png";
        // $img = \file_get_contents($ruta);
        // $data = base64_encode($img);

        return $this->html;
    }

    function getTotalLoteria($id){
        $total = 0;
        $contadorJugadas = 0;
        foreach($this->ventasDetalles as $d){
            if($d->idLoteria == $id){
                $total += $d->monto;
                $contadorJugadas++;
            }
        }

        $this->contadorJugadas = $contadorJugadas;

        return (float)$total;
    }

    function openHeader()
    {
        $this->html = "<!DOCTYPE html>
        <html lang='en' ng-app='myModule' style='min-width: 400px; max-width: 402px;'>
        <head>
          <meta charset='UTF-8'>
          <title>Document</title>
        
          <!-- <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>
         <script src='script.js'></script>
         <script src='angular.min.js'></script> -->
        
         
         <!-- <link href='bootstrap3/dist/css/bootstrap.css' rel='stylesheet' /> -->
        <!-- <script src='./bootstrap/css/bootstrap.min.css' type='text/javascript'></script> -->
        
        
        <style>
        #banca, #imprimir{
            width: 100%;
            text-align: center;
          }

          .loterias{
            margin: 0 auto;
            width: 90%;
            clear: both;
          }

          .loterias > h3{
            text-align: center;
          }

          .contenedor-tabla{
            width: 50%;
            float: left;
          }
          .contenedor-total{
            clear: both;
            display: block;
            width: 100%;
            text-align: center;
          }

            .table { 
    
                border-bottom:0px !important; 
            
            } 
            
            .table th, .table td { 
            
                border: 1px !important; 
            
            } 
            
            .fixed-table-container { 
            
                border:0px !important; 
            
            }
        </style>
        
        </head>
        <body>";
    }

    function closeHeader()
    {
        $this->html .= "</body>
        </html>";
    }

    function setBanca()
    {
        $this->html .= "<div id='banca' class='row'>
        <div class='col-xs-12 text-center'>
          <h1 style='margin-top: 0px; margin-bottom:0px;'>" . $this->banca->descripcion . "</h1>
        </div>
      </div>";
    }

    function openTicket()
    {
        $this->html .= "<div class='row'>
        <div id='imprimir' class='col-xs-12 col-sm-4 text-center'>";
    }

    function closeTicket(){
        $this->html .= "</div> <!-- COL PRINCIPAL -->
        </div>";
    }

    function setOriginal(){
        $this->html .= "<h2 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'><strong>** ORIGINAL **</strong></h2>";
    }

    function setFirstFecha(){
        $fecha = new Carbon($this->venta->created_at);
        $hora = $fecha->format('g:i A');

        $fechaCompleta = str_replace('-', '/', $fecha->toDateString()) . " " . $hora;
        $this->html .= "<h3 class='text-center' style='margin-top: 0px; margin-bottom:0px;'><strong>". $fechaCompleta ."</strong></h3>";
    }

    function setSecondFecha(){
        $fecha = new Carbon($this->venta->created_at);
        $hora = $fecha->format('g:i A');

        $fechaCompleta = str_replace('-', '/', $fecha->toDateString()) . " " . $hora;
        $this->html .= "<h3 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>Fecha: ". $fechaCompleta ."</h3>";
    }

    function setTicket(){
        $this->html .= "<h3 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>Ticket: ". (new Helper)->toSecuencia($this->venta->idTicket) ."</h3>";
    }

    function setCodigoBarra(){
        //$codigoBarra = Tickets::whereId($this->venta->idTicket)->first()->codigoBarra;
        $this->html .= "<h2 class='text-center my-0'><strong>". $this->codigoBarra ."</strong></h2>";
    }

    function openRowJugadas(){
        $this->html .= "<div  class='row justify-content-center'>";
    }

    function closeRowJugadas(){
        $this->html .= "</div> <!-- END ROW JUGADAS -->";
    }

    function setLoteriaTotal($loteria, $total){
        $loteria = strtoupper($loteria);
        $this->html .= "<div class='loterias col-xs-12 text-center' style='border-top-style: dashed; border-bottom-style: dashed;'>
        <h3 style=' padding: 8px; width: 75%; margin: auto; '  class='text-center font-weight-bold py-1 mt-2 mb-0'>$loteria: $total</h3>
      </div>";
    }

    function openColXs6(){
        $this->html .="<div class='col-xs-6 contenedor-tabla' >";
    }

    function openColXs12(){
        $this->html .="<div class='col-xs-12'>";
    }

    function closeCol(){
        $this->html .="</div>";
    }

    function openTable(){
        $this->html .= "<table class='table borderless table-sm'>";
    }

    function closeTable(){
        $this->html .= "</table> <!-- TABLA -->";
    }

    function setTableHead(){
        $this->html .= "<thead>
        <tr style='width: 100%;'>
            <th class='text-center' scope='col' style='font-size: 18px; margin-left: 0px; margin-right: 0px;'>JUGADA</th>
            <th class='text-center' scope='col' style='font-size: 18px; margin-left: 0px; margin-right: 0px;'>MONTO</th>
        </tr>
        </thead>";
    }

    function openTableBody(){
        $this->html .= "<tbody>";
    }

    function closeTableBody(){
        $this->html .= "</tbody>";
    }

    function setJugada($jugada, $monto){
        $this->html .= "<tr >
        <td class='text-center' style='font-size: 20px'>$jugada</td>
        <td class='text-center' style='font-size: 20px'>$monto</td>
    </tr> ";
    }

    function setTotal(){
        $this->html .="<div class='row contenedor-total' style='margin-bottom: 2px;'>";
        $total = (int)$this->venta->total;
        if((int)$this->venta->descuentoMonto > 0){
            $total -= (int)$this->venta->descuentoMonto;
            $this->html .= "<h3 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>subTotal:".  $this->venta->total ."</h3>";
            $this->html .= "<h3 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'> Descuento:". $this->venta->descuentoMonto ."</h3>";
        }
        $this->html .= "<h2 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'><strong>- Total:". $total ." -</strong></h2>";

        $this->html .="</div>";
    }

    function setPieDePagina(){
        $this->html .="<div class='row contenedor-total' style='margin-bottom: 150px;'>";
        if($this->banca->piepagina1 != null){
            $this->html .= "<h4 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>". $this->banca->piepagina1 ."</h4>";
        }
        if($this->banca->piepagina2 != null){
            $this->html .= "<h4 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>". $this->banca->piepagina2 ."</h4>";
        }
        if($this->banca->piepagina3 != null){
            $this->html .= "<h4 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>". $this->banca->piepagina3 ."</h4>";
        }
        if($this->banca->piepagina4 != null){
            $this->html .= "<h4 class='text-center my-0' style='margin-top: 0px; margin-bottom:0px;'>". $this->banca->piepagina4 ."</h4>";
        }
        
        $this->html .="</div>";
    }
}