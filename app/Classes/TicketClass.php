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


class TicketClass{
    private $html;
    private $banca;
    private $venta;
    private $ventasDetalles;
    private $usuario;
    public $copia = false;
    private $contadorJugadas;
    private $codigoBarra;

    function __construct($idVenta){
        $this->venta = Sales::whereId($idVenta)->first();
        $this->ventaColeccion = SalesResource::collection(Sales::whereId($idVenta)->get());
        $this->banca = Branches::whereId($this->venta->idBanca)->first();
        $this->ventasDetalles = Salesdetails::where('idVenta', $this->venta->id)->orderBy('idLoteria', 'desc')->get();
        $this->ventasDetalles = collect($this->ventasDetalles);
        $this->usuario = Users::whereId($this->venta->idUsuario)->first();
        $this->codigoBarra = $this->ventaColeccion->map(function($v){
            return $v->codigoBarra;
        });;
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
                $loterias = $this->ventasDetalles->map(function($v){
                    return $v['idLoteria'];
                });;
                $loterias = Lotteries::whereIn('id', $loterias)->get();
                //return $loterias;
                foreach($loterias as $l){
                    $contadorJugadasLoteria = 0;
                    if($idLoteria != $l['id']){
                        $idLoteria = $l->id;
                        $total = $this->getTotalLoteria($l->id);
                        $this->setLoteriaTotal($l->descripcion, $total);
                    }
                    //if($this->contadorJugadas > 2){
                        $this->openColXs6();
                    // }else{
                    //     $this->openColXs12();
                    // }

                    $this->openTable();
                                $this->setTableHead();
                                $this->openTableBody();
                    foreach($this->ventasDetalles as $d){
                        if($l->id == $d['idLoteria']){
                            $loteria = Lotteries::whereId($d['idLoteria'])->first();
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
                        if($this->contadorJugadas > 2 && $contadorJugadasLoteria > round($this->contadorJugadas / 2)){
                                $this->closeTableBody();
                                $this->closeTable();
                            $this->closeCol();

                            

                            $this->openColXs6();
                            $this->openTable();
                                $this->setTableHead();
                                $this->openTableBody();
                        }
                        // else{
                        //     $this->openColXs12();
                        // }
                            // $this->openTable();
                            //     $this->setTableHead();
                            //     $this->openTableBody();
                                    $this->setJugada($d['jugada'], $d['monto']);
                        //         $this->closeTableBody();
                        //     $this->closeTable();
                        // $this->closeCol();

                        }//END IF loteria
                        
                    } //END foreach

                    

                    $this->closeTableBody();
                    $this->closeTable();
                $this->closeCol();

                if($this->contadorJugadas < 3){
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
        $this->closeTicket();
        $this->closeHeader();


        $output_file = public_path() . "\\assets\\ticket\\" . $this->venta->idTicket . ".html";
        $file = fopen($output_file, "wb");
        fwrite($file, $this->html);
        fclose($file);

        ob_start();
        $command = "C:\\loterias\\lote\\public\\assets\\ticket\\wkhtmltoimage --zoom 2.125 --width 314 ";
        $command .= "C:\\loterias\\lote\\public\\assets\\ticket\\" . $this->venta->idTicket . ".html ";
        $command .= "C:\\loterias\\lote\\public\\assets\\ticket\\img\\" . $this->venta->idTicket . ".png";
        system($command, $return_var);
        $salida = \ob_get_contents();
        \ob_end_clean();

        $ruta = public_path() . "\\assets\\ticket\\img\\" . $this->venta->idTicket . ".png";
        $img = \file_get_contents($ruta);
        $data = base64_encode($img);



        $output_file = public_path() . "\\assets\\ticket\\" . "ticket" . ".txt";
        $file = fopen($output_file, "wb");
        fwrite($file, $data);
        fclose($file);

        return $data;
    }

    function getTotalLoteria($id){
        $total = 0;
        $contadorJugadas = 0;
        foreach($this->ventasDetalles as $d){
            if($d['idLoteria'] == $id){
                $total += $d['monto'];
                $contadorJugadas++;
            }
        }

        $this->contadorJugadas = $contadorJugadas;

        return $total;
    }

    function openHeader()
    {
        $this->html = "<!DOCTYPE html>
        <html lang='en' ng-app='myModule' style='min-width: 300px; max-width: 302px;'>
        <head>
          <meta charset='UTF-8'>
          <title>Document</title>
        
          <!-- <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>
         <script src='script.js'></script>
         <script src='angular.min.js'></script> -->
        
         
         <link href='bootstrap3/dist/css/bootstrap.css' rel='stylesheet' />
        <!-- <script src='./bootstrap/css/bootstrap.min.css' type='text/javascript'></script> -->
        
        
        
        
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
        $this->html .= "<div class='row'>
        <div class='col-xs-12 text-center'>
          <h1>" . $this->banca->descripcion . "</h1>
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
        $this->html .= "<h5 class='text-center my-0'>**ORIGINAL**</h5>";
    }

    function setFirstFecha(){
        $fecha = new Carbon($this->venta->created_at);
        $hora = $fecha->format('g:i A');

        $fechaCompleta = str_replace('-', '/', $fecha->toDateString()) . " " . $hora;
        $this->html .= "<h5 class='text-center my-0'>". $fechaCompleta ."</h5>";
    }

    function setSecondFecha(){
        $fecha = new Carbon($this->venta->created_at);
        $hora = $fecha->format('g:i A');

        $fechaCompleta = str_replace('-', '/', $fecha->toDateString()) . " " . $hora;
        $this->html .= "<p class='text-center my-0'>Fecha:". $fechaCompleta ."</p>";
    }

    function setTicket(){
        $this->html .= "<p class='text-center my-0'>Ticket: ". $this->banca->codigo . "-" . (new Helper)->toSecuencia($this->venta->idTicket) ."</p>";
    }

    function setCodigoBarra(){
        $codigoBarra = Tickets::whereId($this->venta->idTicket)->first()->codigoBarra;
        $this->html .= "<h5 class='text-center my-0 font-weight-bold'>". $codigoBarra ."</h5>";
    }

    function openRowJugadas(){
        $this->html .= "<div class='row justify-content-center'>";
    }

    function closeRowJugadas(){
        $this->html .= "</div> <!-- END ROW JUGADAS -->";
    }

    function setLoteriaTotal($loteria, $total){
        $this->html .= "<div class='col-xs-12 text-center'>
        <p style='border-top-style: dashed; border-bottom-style: dashed; padding: 8px; width: 75%; margin: auto; font-size: 17px;'  class='text-center font-weight-bold py-1 mt-2 mb-0'>$loteria:$total</p>
      </div>";
    }

    function openColXs6(){
        $this->html .="<div class='col-xs-6'>";
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
        <tr>
            <th class='text-center' scope='col'>Jugada</th>
            <th class='text-center' scope='col'>Monto</th>
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
        <td class='text-center' style='font-size: 14px'>$jugada</td>
        <td class='text-center' style='font-size: 14px'>$monto</td>
    </tr> ";
    }

    function setTotal(){
        $this->html .="<div class='row'>";
        if((int)$this->venta->descuentoMonto > 0){
            $this->html .= "<h5 class='text-center my-0'> Descuento:". $this->venta->descuentoMonto ."</h5>";
            $this->html .= "<h5 class='text-center my-0'>subTotal:". $this->venta->subTotal ."</h5>";
        }
        $this->html .= "<h4 class='text-center my-0'>- Total:". $this->venta->total ." -</h4>";

        $this->html .="</div>";
    }
}