<?php

if($controlador != "login"){
    if(!isset($_SESSION['idUsuario'])){

    }
}

?>

<!DOCTYPE html>
<html lang="en" ng-app='myModule' ng-controller='myController'>
<head>



    <meta charset="utf-8" />
<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
<link rel="icon" type="image/png" href="../assets/img/favicon.png">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">

<title>






</title>

<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />


<!-- Extra details for Live View on GitHub Pages -->

<link href="{{asset('assets/css/material-dashboard.css')}}" rel="stylesheet" />
<link href="{{asset('assets/css/prueba.css')}}" rel="stylesheet" />
<?php if($controlador == "dashboard"):?>
    <link href="{{asset('assets/css/morris.css')}}" rel="stylesheet" />
<?php endif; ?> 


<!--     Fonts and icons     -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
<!-- <link rel="stylesheet" type="text/css" href="{{asset('assets/css/google.css')}}" /> -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">

<!-- CSS Files -->











<!-- CSS Just for demo purpose, don't include it in your project -->
<link href="{!!asset('assets/demo/demo.css') !!}" rel="stylesheet" />

<script>
    window.rutaProgramaJavaParaImprimir = "<?php echo asset('assets/java/mavenproject1-1.0-jar-with-dependencies.jar')?>" ;
    window.rutaGlobal = (false) ? "<?php echo url('') ?>" : '';
    window.idUsuario = "<?php echo session('idUsuario') ?>";
    window.idUsuarioGlobal = "<?php echo session('idUsuario') ?>";
    window.apiKeyGlobal = "<?php echo session('apiKey') ?>";
    window.socketKeyGlobal = "<?php echo session('socketKey') ?>";
    window.servidorGlobal = "<?php echo session('servidor') ?>";
    window.idBanca = "<?php echo session('idBanca') ?>";
    window.idBancaGlobal = "<?php echo session('idBanca') ?>";
    window.permisosGlobal = "<?php echo session('permisos') ?>";
    window.monedasGlobal = <?php if(isset($monedas)) echo $monedas; else echo 'null'; ?>;
    window.bancasGlobal = <?php if(isset($bancas)) echo $bancas; else echo 'null'; ?>;
    window.bancosGlobal = <?php if(isset($bancos)) echo $bancos; else echo 'null'; ?>;
    window.loteriasGlobal = <?php if(isset($loterias)) echo $loterias; else echo 'null'; ?>;
    window.loteriasJugadasDashboardGlobal = <?php if(isset($loteriasJugadasDashboard)) echo $loteriasJugadasDashboard; else echo 'null'; ?>;
    window.sorteosGlobal = <?php if(isset($sorteos)) echo $sorteos; else echo 'null'; ?>;
    window.diasGlobal = <?php if(isset($dias)) echo $dias; else echo 'null'; ?>;
    window.frecuenciasGlobal = <?php if(isset($frecuencias)) echo $frecuencias; else echo 'null'; ?>;
    window.prestamosGlobal = <?php if(isset($prestamos)) echo $prestamos; else echo 'null'; ?>;
    window.tiposPagosGlobal = <?php if(isset($tiposPagos)) echo $tiposPagos; else echo 'null'; ?>;
    window.tiposEntidadesGlobal = <?php if(isset($tiposEntidades)) echo $tiposEntidades; else echo 'null'; ?>;
    window.vGlobal = <?php if(isset($ventas)) echo $ventas; else echo 'null'; ?>;
    window.loteriasPremiosModelGlobal = <?php if(isset($loteriasPremiosModal)) echo $loteriasPremiosModal; else echo 'null'; ?>;

    console.log('header:', bancasGlobal);
   

    window.toSecuencia = function(idTicket){
            var str = "" + idTicket;
            var pad = "000000000";
            var ans = pad.substring(0, pad.length - str.length) + str;
            return ans;
        }

    window.esPick3Pick4UOtro = function(jugada){
        if(jugada.length == 3){
            return 'pick3Straight'
        }
        else if(jugada.length == 4 && jugada.indexOf('+') != -1)
            return 'pick3Box'
        else if(jugada.length == 5 && jugada.indexOf('+') != -1)
            return 'pick4Box'
        else if(jugada.length == 5 && jugada.indexOf('-') != -1)
            return 'pick4Straight'
        else
            return 'otro';
        }

    var prueba = function(){
        alert("Preuba");
    }

    var send = function(titleImage, base64Image, sms = true){
        if(typeof Android != 'undefined'){
            alert('que es: ', typeof Android);
        }else{
            alert('que es: undefined');
        }
        
        console.log('Send que es:', typeof Android);
        //Android.sendSMS(titleImage, base64Image, sms);
    }
    // function setRuta(r){
    //     rutaGlobal = r;
    //     console.log('header ruta:', rutaGlobal);
    // }
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="{{asset('assets/js/angular.min.js')}}" ></script>
<script src="{{asset('assets/js/angular/angular-animate.js')}}" ></script>
<script src="{{asset('assets/js/angular-route.min.js')}}" ></script>
<script src="{{asset('assets/js/angular/myapp.js')}}" ></script>
<script src="{{asset('assets/js/js-qzprint/dependencies/rsvp-3.1.0.min.js')}}" ></script>
<script src="{{asset('assets/js/js-qzprint/dependencies/sha-256.min.js')}}" ></script>
<script src="{{asset('assets/js/js-qzprint/qz-tray.js')}}" ></script>
<script src="{{asset('assets/js/js-qzprint/cmd.js'). '?'.rand(1,50)}}}}" ></script>
<script src="{{asset('assets/js/angular/premios.modal.js'). '?'.rand(1,50)}}" ></script>
<script src="{{asset('assets/js/angular/impresora.js'). '?'.rand(1,50)}}" ></script>
<script src="{{asset('assets/js/angular/servicios/helper.js'). '?'.rand(1,50)}}" ></script>
<script src="{{asset('assets/js/angular/servicios/printer.js'). '?'.rand(1,50)}}" ></script>

    <?php if($controlador == "dashboard"):?>
        <link href="{!!asset('assets/css/loading-bouncing.css') !!}" rel="stylesheet" />
    <?php endif; ?>
    <?php if($controlador == "principal"):?>
        <script src="{{asset('assets/js/angular/principal.js'). '?'.rand(1,50)}}" ></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.dev.js"></script>
    <?php endif; ?>

    <?php if($controlador == "principal.pruebahttp" ):?>
        <script src="{{asset('assets/js/angular/pruebahttp.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "bancas"):?>
        <script src="{{asset('assets/js/angular/bancas.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "home" || $controlador == ""):?>
        <script src="{{asset('assets/js/home.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "usuarios"):?>
        <script src="{{asset('assets/js/angular/usuarios.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "usuarios.sesiones" ):?>
        <script src="{{asset('assets/js/angular/sesiones.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "loterias"):?>
        <script src="{{asset('assets/js/angular/loteria.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "loterias.bloqueos" ):?>
        <script src="{{asset('assets/js/angular/bloqueos.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>


    <?php if($controlador == "premios"):?>
        <script src="{{asset('assets/js/angular/premios.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "reportes.jugadas"):?>
        <script src="{{asset('assets/js/angular/reportes.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "usuarios"):?>
        <script src="{{asset('assets/js/angular/usuarios.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "horarios"):?>
        <script src="{{asset('assets/js/angular/horarios.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "dashboard"):?>
        <script src="{{asset('assets/js/angular/dashboard.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "bloqueos"):?>
        <script src="{{asset('assets/js/angular/bloqueos2.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "entidades"):?>
        <script src="{{asset('assets/js/angular/entidades.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "monedas"):?>
        <script src="{{asset('assets/js/angular/monedas.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "transacciones" || $controlador == "transacciones"):?>
        <script src="{{asset('assets/js/angular/transacciones.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "transacciones.grupo"):?>
        <script src="{{asset('assets/js/angular/transacciones-grupo.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "monitoreo.tickets" ):?>
        <script src="{{asset('assets/js/angular/monitoreo.tickets.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "reportes.historico" ):?>
        <script src="{{asset('assets/js/angular/reporte.historico.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "reportes.ventasporfecha" ):?>
        <script src="{{asset('assets/js/angular/reporte.ventasporfecha.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "balance.bancas" ):?>
        <script src="{{asset('assets/js/angular/balances.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "balance.bancos" ):?>
        <script src="{{asset('assets/js/angular/balance.bancos.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "prestamos" ):?>
        <script src="{{asset('assets/js/angular/prestamos.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "versiones" ):?>
        <script src="{{asset('assets/js/angular/versiones.js'). '?'.rand(1,50)}}" ></script>
    <?php endif; ?>

    <!-- <script src="{{asset('assets/js/angular/premios.modal.js'). '?'.rand(1,50)}}" ></script> -->

<style>
html{
    /* Scroll para microsoft edge e internet explorer */
    -ms-overflow-style: -ms-autohiding-scrollbar;
}
.sample-show-hide {
  transition: all linear 0.5s;
}

.sample-show-hide.ng-show {
  opacity: 1;
}

.sample-show-hide.ng-hide {
    opacity: 0;
}

.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
                        /* background-color: #f5f2f2; */
                        background-color: #eae9e9;
                      }


                    
.panel.panel-chat
{
    position: fixed;
    bottom:0;
    right:0;
    max-width: 470px;
    width: 500px;
    margin-right: 10px;
    box-shadow: none;
    -webkit-box-shadow: none;
    z-index: 99999;
    
}

#mytable >tbody>tr>td{
    height:20px;
    padding:0px;
    border-top: 0px;
}

.bg-rosa{
  background: #FD9EBC;
}
.bg-disabled{
  background: #cdcbce;
}


.bg-success2{
  background: #bef1c0!important;
}

.panelPremios.panel-premios
{
    position: fixed;
    top:0;
    right:0;
    /* max-width: 570px; */
    /* width: 500px; */
    box-shadow: none;
    -webkit-box-shadow: none;
    z-index: 99999;
    
}

.max-width-570{
    max-width: 692px;
}

.dnone{
    display: none;
}

.dblock{
    display: block;
}

div:not(.btn-group-not).btn-group {
  display: flex;
  overflow: scroll;
}

.bg-bien{
    background: #add8e6!important;
}
.text-bien{
    color: #262290;
}

.bg-mal{
    background: #ffcccc!important;
}

.text-mal{
    color: #ff577b;
}


.morris-hover{
    
}
    
 /********************* MEDIA QUERY QUE MANEJA MENU DESPLEGABLE ******************/
 
   @media (max-width:<?php if($controlador == 'principal') echo '1391px'; else echo '991px';?>) {
     .card .form-horizontal .checkbox-inline,
     .card .form-horizontal .checkbox-radios .checkbox:first-child,
     .card .form-horizontal .checkbox-radios .radio:first-child,
     .card .form-horizontal .form-group,
     .sidebar .nav-mobile-menu {
         margin-top: 0
     }
     .main-panel,
     .navbar .container,
     .navbar .container .navbar-toggler,
     .navbar .container .navbar-wrapper,
     .navbar-collapse,
     .wrapper-full-page {
         -webkit-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         -moz-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         -o-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         -ms-transition: all .33s cubic-bezier(.685, .0473, .346, 1)
     }
     .form-group textarea {
         padding-top: 15px
     }
     .nav-open .menu-on-left .main-panel {
         position: initial
     }
     body,
     html {
         overflow-x: hidden
     }
     .nav-open .menu-on-left .main-panel,
     .nav-open .menu-on-left .navbar-fixed>div,
     .nav-open .menu-on-left .wrapper-full-page {
         -webkit-transform: translate3d(260px, 0, 0);
         -moz-transform: translate3d(260px, 0, 0);
         -o-transform: translate3d(260px, 0, 0);
         -ms-transform: translate3d(260px, 0, 0);
         transform: translate3d(260px, 0, 0)
     }
     .menu-on-left .off-canvas-sidebar,
     .menu-on-left .sidebar {
         left: 0;
         right: auto;
         -webkit-transform: translate3d(-260px, 0, 0);
         -moz-transform: translate3d(-260px, 0, 0);
         -o-transform: translate3d(-260px, 0, 0);
         -ms-transform: translate3d(-260px, 0, 0);
         transform: translate3d(-260px, 0, 0)
     }
     .menu-on-left .close-layer {
         left: auto;
         right: 0
     }
     .timeline:before,
     .timeline>li>.timeline-badge {
         left: 5%
     }
     .timeline>li>.timeline-panel {
         float: right;
         width: 86%
     }
     .timeline>li>.timeline-panel:before {
         border-left-width: 0;
         border-right-width: 15px;
         left: -15px;
         right: auto
     }
     .timeline>li>.timeline-panel:after {
         border-left-width: 0;
         border-right-width: 14px;
         left: -14px;
         right: auto
     }
     .nav-mobile-menu .dropdown .dropdown-menu {
         display: none;
         position: static!important;
         background-color: transparent;
         width: auto;
         float: none;
         box-shadow: none
     }
     .nav-mobile-menu .dropdown .dropdown-menu.showing {
         animation: initial;
         animation-duration: 0s
     }
     .nav-mobile-menu .dropdown .dropdown-menu.hiding {
         transform: none;
         opacity: 1
     }
     .nav-mobile-menu .dropdown.show .dropdown-menu {
         display: block
     }
     .nav-mobile-menu li.active>a {
         background-color: rgba(255, 255, 255, .1)
     }
     .navbar-minimize {
         display: none
     }
     .card .form-horizontal .label-on-left,
     .card .form-horizontal .label-on-right {
         padding-left: 15px;
         padding-top: 8px
     }
     .card .form-horizontal .checkbox-radios {
         padding-bottom: 15px
     }
     .sidebar {
         box-shadow: none
     }
     .sidebar .sidebar-wrapper {
         padding-bottom: 60px
     }
     .sidebar .nav-mobile-menu .notification {
         float: left;
         line-height: 30px;
         margin-right: 8px
     }
     .sidebar .nav-mobile-menu .open .dropdown-menu {
         position: static;
         float: none;
         width: auto;
         margin-top: 0;
         background-color: transparent;
         border: 0;
         -webkit-box-shadow: none;
         box-shadow: none
     }
     .navbar-nav>li,
     body {
         position: relative
     }
     .main-panel {
         width: 100%
     }
     .navbar-transparent {
         padding-top: 15px;
         background-color: rgba(0, 0, 0, .45)
     }
     .nav-open .main-panel,
     .nav-open .navbar .container,
     .nav-open .navbar .container .navbar-toggler,
     .nav-open .navbar .container .navbar-wrapper,
     .nav-open .wrapper-full-page {
         left: 0;
         -webkit-transform: translate3d(-260px, 0, 0);
         -moz-transform: translate3d(-260px, 0, 0);
         -o-transform: translate3d(-260px, 0, 0);
         -ms-transform: translate3d(-260px, 0, 0);
         transform: translate3d(-260px, 0, 0)
     }
     .nav-open .sidebar {
         box-shadow: 0 16px 38px -12px rgba(0, 0, 0, .56), 0 4px 25px 0 rgba(0, 0, 0, .12), 0 8px 10px -5px rgba(0, 0, 0, .2)
     }
     .nav-open .off-canvas-sidebar .navbar-collapse,
     .nav-open .sidebar {
         -webkit-transform: translate3d(0, 0, 0);
         -moz-transform: translate3d(0, 0, 0);
         -o-transform: translate3d(0, 0, 0);
         -ms-transform: translate3d(0, 0, 0);
         transform: translate3d(0, 0, 0)
     }
     .navbar .container,
     .navbar .container .navbar-toggler,
     .navbar .container .navbar-wrapper,
     .wrapper-full-page {
         -webkit-transform: translate3d(0, 0, 0);
         -moz-transform: translate3d(0, 0, 0);
         -o-transform: translate3d(0, 0, 0);
         -ms-transform: translate3d(0, 0, 0);
         transform: translate3d(0, 0, 0);
         transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         left: 0
     }
     .off-canvas-sidebar .navbar .container {
         transform: none
     }
     .main-panel,
     .navbar-collapse {
         transition: all .33s cubic-bezier(.685, .0473, .346, 1)
     }
     .navbar .navbar-collapse.collapse,
     .navbar .navbar-collapse.collapse.in,
     .navbar .navbar-collapse.collapsing {
         display: none!important
     }
     .off-canvas-sidebar .navbar .navbar-collapse.collapse,
     .off-canvas-sidebar .navbar .navbar-collapse.collapse.in,
     .off-canvas-sidebar .navbar .navbar-collapse.collapsing {
         display: block!important
     }
     .navbar-nav>li {
         float: none;
         display: block
     }
     .off-canvas-sidebar nav .navbar-collapse {
         margin: 0
     }
     .off-canvas-sidebar nav .navbar-collapse>ul {
         margin-top: 19px
     }
     .off-canvas-sidebar nav .navbar-collapse,
     .sidebar {
         position: fixed;
         display: block;
         top: 0;
         height: 100vh;
         width: 260px;
         right: 0;
         left: auto;
         z-index: 1032;
         visibility: visible;
         background-color: #9A9A9A;
         overflow-y: visible;
         border-top: none;
         text-align: left;
         padding-right: 0;
         padding-left: 0;
         -webkit-transform: translate3d(260px, 0, 0);
         -moz-transform: translate3d(260px, 0, 0);
         -o-transform: translate3d(260px, 0, 0);
         -ms-transform: translate3d(260px, 0, 0);
         transform: translate3d(260px, 0, 0);
         -webkit-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         -moz-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         -o-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         -ms-transition: all .33s cubic-bezier(.685, .0473, .346, 1);
         transition: all .33s cubic-bezier(.685, .0473, .346, 1)
     }
     .off-canvas-sidebar nav .navbar-collapse>ul,
     .sidebar>ul {
         position: relative;
         z-index: 4;
         width: 100%
     }
     .off-canvas-sidebar nav .navbar-collapse::before,
     .sidebar::before {
         top: 0;
         left: 0;
         height: 100%;
         width: 100%;
         position: absolute;
         background-color: #282828;
         display: block;
         content: "";
         z-index: 1
     }
     .off-canvas-sidebar nav .navbar-collapse .logo,
     .sidebar .logo {
         position: relative;
         z-index: 4
     }
     .off-canvas-sidebar nav .navbar-collapse .navbar-form,
     .sidebar .navbar-form {
         margin: 10px 0;
         float: none!important;
         padding-top: 1px;
         padding-bottom: 1px;
         position: relative
     }
     .off-canvas-sidebar nav .navbar-collapse .table-responsive,
     .sidebar .table-responsive {
         width: 100%;
         margin-bottom: 15px;
         overflow-x: scroll;
         overflow-y: hidden;
         -ms-overflow-style: -ms-autohiding-scrollbar;
         -webkit-overflow-scrolling: touch
     }
     #bodyClick,
     .close-layer {
         left: auto;
         content: "";
         z-index: 9999;
         overflow-x: hidden
     }
     .form-group.form-search .form-control {
         font-size: 1.7em;
         height: 37px;
         width: 78%
     }
     .navbar-form .btn {
         position: absolute;
         top: -5px;
         right: -50px
     }
     .close-layer {
         height: 100%;
         width: 100%;
         position: absolute;
         opacity: 0;
         top: 0;
         background: rgba(0, 0, 0, .35);
         -webkit-transition: all 370ms ease-in;
         -moz-transition: all 370ms ease-in;
         -o-transition: all 370ms ease-in;
         -ms-transition: all 370ms ease-in;
         transition: all 370ms ease-in
     }
     .close-layer.visible,
     .navbar-toggler .icon-bar:nth-child(3) {
         opacity: 1
     }
     .navbar-toggler .icon-bar {
         display: block;
         position: relative;
         background: #555!important;
         width: 24px;
         height: 2px;
         border-radius: 1px;
         margin: 0 auto
     }
     .navbar-header .navbar-toggler {
         padding: 15px;
         margin-top: 4px;
         width: 40px;
         height: 40px
     }
     .bar1,
     .bar2,
     .bar3 {
         outline: transparent solid 1px
     }
     @keyframes topbar-x {
         0% {
             top: 0;
             transform: rotate(0)
         }
         45% {
             top: 6px;
             transform: rotate(145deg)
         }
         75% {
             transform: rotate(130deg)
         }
         100% {
             transform: rotate(135deg)
         }
     }
     @-webkit-keyframes topbar-x {
         0% {
             top: 0;
             -webkit-transform: rotate(0)
         }
         45% {
             top: 6px;
             -webkit-transform: rotate(145deg)
         }
         75% {
             -webkit-transform: rotate(130deg)
         }
         100% {
             -webkit-transform: rotate(135deg)
         }
     }
     @-moz-keyframes topbar-x {
         0% {
             top: 0;
             -moz-transform: rotate(0)
         }
         45% {
             top: 6px;
             -moz-transform: rotate(145deg)
         }
         75% {
             -moz-transform: rotate(130deg)
         }
         100% {
             -moz-transform: rotate(135deg)
         }
     }
     @keyframes topbar-back {
         0% {
             top: 6px;
             transform: rotate(135deg)
         }
         45% {
             transform: rotate(-10deg)
         }
         75% {
             transform: rotate(5deg)
         }
         100% {
             top: 0;
             transform: rotate(0)
         }
     }
     @-webkit-keyframes topbar-back {
         0% {
             top: 6px;
             -webkit-transform: rotate(135deg)
         }
         45% {
             -webkit-transform: rotate(-10deg)
         }
         75% {
             -webkit-transform: rotate(5deg)
         }
         100% {
             top: 0;
             -webkit-transform: rotate(0)
         }
     }
     @-moz-keyframes topbar-back {
         0% {
             top: 6px;
             -moz-transform: rotate(135deg)
         }
         45% {
             -moz-transform: rotate(-10deg)
         }
         75% {
             -moz-transform: rotate(5deg)
         }
         100% {
             top: 0;
             -moz-transform: rotate(0)
         }
     }
     @keyframes bottombar-x {
         0% {
             bottom: 0;
             transform: rotate(0)
         }
         45% {
             bottom: 6px;
             transform: rotate(-145deg)
         }
         75% {
             transform: rotate(-130deg)
         }
         100% {
             transform: rotate(-135deg)
         }
     }
     @-webkit-keyframes bottombar-x {
         0% {
             bottom: 0;
             -webkit-transform: rotate(0)
         }
         45% {
             bottom: 6px;
             -webkit-transform: rotate(-145deg)
         }
         75% {
             -webkit-transform: rotate(-130deg)
         }
         100% {
             -webkit-transform: rotate(-135deg)
         }
     }
     @-moz-keyframes bottombar-x {
         0% {
             bottom: 0;
             -moz-transform: rotate(0)
         }
         45% {
             bottom: 6px;
             -moz-transform: rotate(-145deg)
         }
         75% {
             -moz-transform: rotate(-130deg)
         }
         100% {
             -moz-transform: rotate(-135deg)
         }
     }
     @keyframes bottombar-back {
         0% {
             bottom: 6px;
             transform: rotate(-135deg)
         }
         45% {
             transform: rotate(10deg)
         }
         75% {
             transform: rotate(-5deg)
         }
         100% {
             bottom: 0;
             transform: rotate(0)
         }
     }
     @-webkit-keyframes bottombar-back {
         0% {
             bottom: 6px;
             -webkit-transform: rotate(-135deg)
         }
         45% {
             -webkit-transform: rotate(10deg)
         }
         75% {
             -webkit-transform: rotate(-5deg)
         }
         100% {
             bottom: 0;
             -webkit-transform: rotate(0)
         }
     }
     @-moz-keyframes bottombar-back {
         0% {
             bottom: 6px;
             -moz-transform: rotate(-135deg)
         }
         45% {
             -moz-transform: rotate(10deg)
         }
         75% {
             -moz-transform: rotate(-5deg)
         }
         100% {
             bottom: 0;
             -moz-transform: rotate(0)
         }
     }
     .navbar-toggler .icon-bar:nth-child(2) {
         top: 0;
         -webkit-animation: topbar-back .5s linear 0s;
         -moz-animation: topbar-back .5s linear 0s;
         animation: topbar-back .5s 0s;
         -webkit-animation-fill-mode: forwards;
         -moz-animation-fill-mode: forwards;
         animation-fill-mode: forwards
     }
     .navbar-toggler .icon-bar:nth-child(4) {
         bottom: 0;
         -webkit-animation: bottombar-back .5s linear 0s;
         -moz-animation: bottombar-back .5s linear 0s;
         animation: bottombar-back .5s 0s;
         -webkit-animation-fill-mode: forwards;
         -moz-animation-fill-mode: forwards;
         animation-fill-mode: forwards
     }
     .navbar-toggler.toggled .icon-bar:nth-child(2) {
         top: 6px;
         -webkit-animation: topbar-x .5s linear 0s;
         -moz-animation: topbar-x .5s linear 0s;
         animation: topbar-x .5s 0s;
         -webkit-animation-fill-mode: forwards;
         -moz-animation-fill-mode: forwards;
         animation-fill-mode: forwards
     }
     .navbar-toggler.toggled .icon-bar:nth-child(3) {
         opacity: 0
     }
     .navbar-toggler.toggled .icon-bar:nth-child(4) {
         bottom: 6px;
         -webkit-animation: bottombar-x .5s linear 0s;
         -moz-animation: bottombar-x .5s linear 0s;
         animation: bottombar-x .5s 0s;
         -webkit-animation-fill-mode: forwards;
         -moz-animation-fill-mode: forwards;
         animation-fill-mode: forwards
     }
     @-webkit-keyframes fadeIn {
         0% {
             opacity: 0
         }
         100% {
             opacity: 1
         }
     }
     @-moz-keyframes fadeIn {
         0% {
             opacity: 0
         }
         100% {
             opacity: 1
         }
     }
     @keyframes fadeIn {
         0% {
             opacity: 0
         }
         100% {
             opacity: 1
         }
     }
     .dropdown-menu .divider {
         background-color: rgba(229, 229, 229, .15)
     }
     .navbar-nav {
         margin: 1px 0
     }
     .navbar-nav .open .dropdown-menu>li>a {
         padding: 15px 15px 5px 50px
     }
     .navbar-nav .open .dropdown-menu>li:first-child>a {
         padding: 5px 15px 5px 50px
     }
     .navbar-nav .open .dropdown-menu>li:last-child>a {
         padding: 15px 15px 25px 50px
     }
     [class*=navbar-] .navbar-nav .active>a,
     [class*=navbar-] .navbar-nav .active>a:focus,
     [class*=navbar-] .navbar-nav .active>a:hover,
     [class*=navbar-] .navbar-nav .navbar-nav .open .dropdown-menu>li>a:active,
     [class*=navbar-] .navbar-nav .open .dropdown-menu>li>a,
     [class*=navbar-] .navbar-nav .open .dropdown-menu>li>a:focus,
     [class*=navbar-] .navbar-nav .open .dropdown-menu>li>a:hover,
     [class*=navbar-] .navbar-nav>li>a,
     [class*=navbar-] .navbar-nav>li>a:focus,
     [class*=navbar-] .navbar-nav>li>a:hover {
         color: #fff
     }
     [class*=navbar-] .navbar-nav .open .dropdown-menu>li>a,
     [class*=navbar-] .navbar-nav .open .dropdown-menu>li>a:focus,
     [class*=navbar-] .navbar-nav .open .dropdown-menu>li>a:hover,
     [class*=navbar-] .navbar-nav>li>a,
     [class*=navbar-] .navbar-nav>li>a:focus,
     [class*=navbar-] .navbar-nav>li>a:hover {
         opacity: .7;
         background: 0 0
     }
     [class*=navbar-] .navbar-nav.navbar-nav .open .dropdown-menu>li>a:active {
         opacity: 1
     }
     [class*=navbar-] .navbar-nav .dropdown>a:hover .caret {
         border-bottom-color: #777;
         border-top-color: #777
     }
     [class*=navbar-] .navbar-nav .dropdown>a:active .caret {
         border-bottom-color: #fff;
         border-top-color: #fff
     }
     .dropdown-menu {
         display: none
     }
     .navbar-fixed-top {
         -webkit-backface-visibility: hidden
     }
     #bodyClick {
         height: 100%;
         width: 100%;
         position: fixed;
         opacity: 0;
         top: 0;
         right: 260px
     }
     .social-line .btn,
     .subscribe-line .form-control {
         margin: 0 0 10px
     }
     .footer:not(.footer-big) nav>ul li,
     .social-line.pull-right {
         float: none
     }
     .media-post .author,
     .social-area.pull-right {
         float: none!important
     }
     .form-control+.form-control-feedback {
         margin-top: -8px
     }
     .navbar-toggle:focus,
     .navbar-toggle:hover {
         background-color: transparent!important
     }
     .media-post .author {
         width: 20%;
         display: block;
         margin: 0 auto 10px
     }
     .media-post .media-body {
         width: 100%
     }
     .navbar-collapse.collapse {
         height: 100%!important
     }
     .navbar-collapse.collapse.in {
         display: block
     }
     .navbar-header .collapse,
     .navbar-toggle {
         display: block!important
     }
     .navbar-header {
         float: none
     }
     .navbar-collapse .nav p {
         font-size: 1rem;
         margin: 0
     }
 }
</style>


</head>
<body 
     ng-init="ROOT_PATH = '/'" ng-init="load('<?php if (isset($_SESSION['idUsuario'])) echo $_SESSION['idUsuario']; ?>', '/')"
    class="<?php if($controlador == 'login') echo 'off-canvas-sidebar' ?>" 
    
    on-shift-tab="venta_guardar($event,'email')"
    ng-keypress="keyPressGuardarVenta($event)">
      <!-- ng-keyup="venta_guardar($event)" -->

<?php 


if(session('idUsuario') == null && $controlador != 'login'){
    
    redirect()->route('login');
}

?>
<!--  wrapper-full-page -->
@if($controlador != 'login' && \App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar resultados"))
<div ng-controller='controllerPremiosModal'>

<div ng-class="{'dblock': (mostrarModalPremios == true), 'dnone': (mostrarModalPremios == false)}" class="dnone row panelPremios panel-premios max-width-570" ng-init="loadPremiosModal('<?php if (isset($_SESSION['idUsuario'])) echo $_SESSION['idUsuario']; ?>', '/')">
      <div class="col-12" ng-init>
                    <div class="card card-stats px-0 py-0 mb-0" style="border: 5px solid; border-color: #e2e2e2;">
                      <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                          <i class="material-icons">store</i>
                        </div>
                        <!-- <p class="card-category">Bancas con ventas</p> -->
                        <h3 class="card-title" style="cursor: pointer;" ng-click="hola()"><i class="material-icons">close</i></h3>
                        <h3 class="card-title">Resultados</h3>
                      </div>
                      <div class="card-body ">
                      <div class="row ">
                        <div class="col-12">
                          <div class="row">
                            <div class="col-4 mt-1">
                              <div class="form-group">
                                <select 
                                    ng-model="datosPremiosModal.selectedLoteriaPremiosModal"
                                    ng-change="cbxLoteriasPremiosModalChanged()"
                                        ng-options="o.descripcion for o in datosPremiosModal.optionsLoteriasPremiosModal"
                                    class="selectpicker col-12 mx-0 px-0" 
                                    data-style="select-with-transition" 
                                    title="Select loteria">
                                </select>
                              </div>
                            </div>
                            <div ng-class="{'col-2 col-sm-2': existeSorteoPremiosModal('Super pale', datosPremiosModal.selectedLoteriaPremiosModal) == true, 'col-2 col-sm-1' : existeSorteoPremiosModal('Super pale', datosPremiosModal.selectedLoteriaPremiosModal) == false}" class="" ng-show="existeSorteoPremiosModal('Pale', datosPremiosModal.selectedLoteriaPremiosModal) 
                                || existeSorteoPremiosModal('Directo', datosPremiosModal.selectedLoteriaPremiosModal) 
                                || existeSorteoPremiosModal('Tripleta', datosPremiosModal.selectedLoteriaPremiosModal)
                                || existeSorteoPremiosModal('Super pale', datosPremiosModal.selectedLoteriaPremiosModal) || datosPremiosModal.selectedLoteriaPremiosModal == null">
                              <div class="input-group form-control-lg">
                                <div id="primeraPremiosModal" class="form-group">
                                  <label for="primera" class="bmd-label-floating">1era</label>
                                  <input
                                      ng-disabled="existeSorteoPremiosModal('Pick 3 Box', datosPremiosModal.selectedLoteriaPremiosModal) || existeSorteoPremiosModal('Pick 3 Straight', datosPremiosModal.selectedLoteriaPremiosModal)" 
                                      maxLength="2" 
                                      select-all-on-click ng-keyup="changeFocusPremiosModal($event, 'datosPremiosModal-segunda', 2, datosPremiosModal.primera)" 
                                      ng-model="datosPremiosModal.primera" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="primera">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            <div ng-class="{'col-2 col-sm-2': existeSorteoPremiosModal('Super pale', datosPremiosModal.selectedLoteriaPremiosModal) == true, 'col-2 col-sm-1' : existeSorteoPremiosModal('Super pale', datosPremiosModal.selectedLoteriaPremiosModal) == false}" class="" 
                              ng-show="
                                existeSorteoPremiosModal('Pale', datosPremiosModal.selectedLoteriaPremiosModal) 
                                || existeSorteoPremiosModal('Directo', datosPremiosModal.selectedLoteriaPremiosModal) 
                                || existeSorteoPremiosModal('Tripleta', datosPremiosModal.selectedLoteriaPremiosModal)
                                || existeSorteoPremiosModal('Super pale', datosPremiosModal.selectedLoteriaPremiosModal) || datosPremiosModal.selectedLoteriaPremiosModal == null">
                              <div class="input-group form-control-lg">
                                <div id="segundaPremiosModal" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">2da</label>
                                  <input 
                                          ng-disabled="existeSorteoPremiosModal('Pick 4 Box', datosPremiosModal.selectedLoteriaPremiosModal) || existeSorteoPremiosModal('Pick 4 Straight', datosPremiosModal.selectedLoteriaPremiosModal)"
                                          select-all-on-click 
                                          maxLength="2"
                                          id="datosPremiosModal-segunda" 
                                          ng-keyup="changeFocusPremiosModal($event, 'datosPremiosModal-tercera', 2, datosPremiosModal.segunda)" 
                                          ng-model="datosPremiosModal.segunda" 
                                          autocomplete="off" 
                                          type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            <div class="col-2 col-sm-1" ng-show="existeSorteoPremiosModal('Pale', datosPremiosModal.selectedLoteriaPremiosModal) 
                                || existeSorteoPremiosModal('Directo', datosPremiosModal.selectedLoteriaPremiosModal) 
                                || existeSorteoPremiosModal('Tripleta', datosPremiosModal.selectedLoteriaPremiosModal)">
                              <div class="input-group form-control-lg">
                                <div id="terceraPremiosModal" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">3era</label>
                                  <input 
                                  ng-disabled="existeSorteoPremiosModal('Pick 4 Box', datosPremiosModal.selectedLoteriaPremiosModal) || existeSorteoPremiosModal('Pick 4 Straight', datosPremiosModal.selectedLoteriaPremiosModal)"
                                  select-all-on-click id="datosPremiosModal-tercera" ng-keyup="changeFocusPremiosModal($event, 'datosPremiosModal-pick3', 2, datosPremiosModal.tercera)" ng-model="datosPremiosModal.tercera" maxlength="2" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-2 col-md-2" ng-show="existeSorteoPremiosModal('Pick 3 Box', datosPremiosModal.selectedLoteriaPremiosModal) || existeSorteoPremiosModal('Pick 3 Straight', datosPremiosModal.selectedLoteriaPremiosModal)">
                              <div class="input-group form-control-lg">
                                <div id="pick3PremiosModal" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">Pick3</label>
                                  <input 
                                      maxLength="3" 
                                      select-all-on-click id="datosPremiosModal-pick3" 
                                      ng-keyup="changeFocusPremiosModal($event, 'datosPremiosModal-pick4', 3, datosPremiosModal.pick3, 'datosPick3')" 
                                      ng-model="datosPremiosModal.pick3" 
                                      autocomplete="off" type="text"  class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-2 col-md-2" ng-show="existeSorteoPremiosModal('Pick 4 Box', datosPremiosModal.selectedLoteriaPremiosModal) || existeSorteoPremiosModal('Pick 4 Straight', datosPremiosModal.selectedLoteriaPremiosModal)">
                              <div class="input-group form-control-lg">
                                <div id="pick4PremiosModal" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">Pick4</label>
                                  <input 
                                    maxLength="4" 
                                    select-all-on-click 
                                    ng-keyup="changeFocusPremiosModal($event, 'no', 4, datosPremiosModal.pick4, 'datosPick4')" 
                                    id="datosPremiosModal-pick4" 
                                    ng-model="datosPremiosModal.pick4" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-1 text-center mt-3 " ng-click="actualizarPremiosModal(true)" style="cursor: pointer;">
                              <i class="material-icons text-white p-2 bg-success rounded mr-1">save</i>
                            </div>
                           </div> <!-- END ROW -->
                        </div> <!-- END COL-23 -->
                    </div> <!-- END ROW -->
                      </div> <!-- END CARD-BODY -->
                      <div class="card-footer">
                        <div class="stats">
                          <i class="material-icons">date_range</i> Hoy
                        </div>
                      </div>
                    </div>
                  </div>
                 
      </div>

      <style>
          /* .fixed-plugin2 {
    position: fixed;
    top: 115px;
    right: 0;
    width: 64px;
    background: rgba(0, 0, 0, .3);
    z-index: 1031;
    border-radius: 8px 0 0 8px;
    text-align: center;
}
          .fixed-plugin2 .fa-cog {
            color: #FFF;
            padding: 10px;
            border-radius: 0 0 6px 6px;
            width: auto;
        }
        .fa-2x {
            font-size: 2em;
        }

        .fa {
            display: inline-block;
            font: normal normal normal 14px/1 FontAwesome;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        } */
      </style>
      <div class="fixed-plugin" >
        <div class="dropdown show-dropdown" >
            <a href="#" ng-click="hola()">
            <i class="fa fa-cog fa-2x" > </i>
            </a>
            
        </div>
    </div>
    
</div>
@endif





<div style="width: 100%;" class="wrapper">
          <!-- ********************* MEDIA QUERY QUE MANEJA MENU DESPLEGABLE ******************/ -->
    @if($controlador != 'login')
    <div 
        id="menu"
        style="display:<?php if($controlador == 'login') echo 'none'; ?>"
        class="sidebar" 
        data-color="rose" 
        data-background-color="black" 
        data-image="../assets/img/sidebar-1.jpg">
    <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->

    <div class="logo">

        <a href="#" class="text-center">
          <div class="navbar-minimize">
              <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
                  <i class="material-icons text_align-center visible-on-sidebar-regular">arrow_back</i>
                  <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">arrow_forward</i>
              </button>
            </div>
        </a>
    </div>

    <div class="sidebar-wrapper">
        
        
        <div class="user">
            <div class="photo">
                <img src="{{asset('assets/img/banco.jpg')}}" />
            </div>
            <div class="user-info">
                <a data-toggle="collapse" href="#collapseExample" class="username">
                    <span>
                       <!-- Tania Andrew  -->
                       <?php echo session('usuario') . " (". session("servidor") . ")";?>
                      <b class="caret"></b>
                    </span>
                </a>
                @if(session("tipoUsuario") == "Programador")
                <div class="collapse" id="collapseExample">
                    <ul class="nav">
                    @foreach(session("servidores") as $servidor)
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('cambiarServidor')}}?token={{\App\Classes\Helper::jwtEncodeServidor($servidor['descripcion'], session('usuario'))}}">
                              <span class="sidebar-mini"> MP </span>
                              <span class="sidebar-normal"> {{$servidor["descripcion"]}} </span>
                            </a>
                        </li>
                    @endforeach
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="#">
                              <span class="sidebar-mini"> EP </span>
                              <span class="sidebar-normal"> Edit Profile </span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="#">
                              <span class="sidebar-mini"> S </span>
                              <span class="sidebar-normal"> Settings </span>
                            </a>
                        </li> -->
                    </ul>
                </div>
                @endif;
            </div>
        </div>
        
        <ul class="nav">

            <li id="btnImpresora" class="nav-item ">
                <a class="nav-link"  >
                    <i class="material-icons">printer</i>
                    <p> Impresora </p>
                </a>
            </li>
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver Dashboard"))
            <li class="nav-item ">
                <a class="nav-link" href="{{route('dashboard')}}">
                    <i class="material-icons">dashboard</i>
                    <p> Dashboard </p>
                </a>
            </li>
            @endif
            <li class="nav-item ">
                <a class="nav-link" href="{{route('principal')}}">
                    <i class="material-icons">attach_money</i>
                    <p> Vender </p>
                </a>
            </li>
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver ventas") == true || \App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver historico ventas") == true)
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#reportesToggle">
                    <i class="material-icons">insert_chart_outlined</i>
                    <p> Ventas 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="reportesToggle">
                    <ul class="nav">
                        @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver historico ventas"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('reportes.historico')}}">
                              <span class="sidebar-mini"> H </span>
                              <span class="sidebar-normal"> Historico </span>
                            </a>
                        </li>
                        @endif
                        @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver ventas"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('reportes.ventasporfecha')}}">
                              <span class="sidebar-mini"> V </span>
                              <span class="sidebar-normal"> Ventas por fecha </span>
                            </a>
                        </li>
                        @endif

                        <!-- <li class="nav-item ">
                            <a class="nav-link" href="">
                              <span class="sidebar-mini"> J </span>
                              <span class="sidebar-normal"> Jugadas </span>
                            </a>
                        </li> -->
                    </ul>
                </div>
            </li>
            @endif

            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Monitorear ticket"))
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#monitoreoToggle">
                    <i class="material-icons">image</i>
                    <p> Monitoreo 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="monitoreoToggle">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('monitoreo.tickets')}}">
                              <span class="sidebar-mini"> T </span>
                              <span class="sidebar-normal"> Tickets </span>
                            </a>
                        </li>
                        <!-- <li class="nav-item ">
                            <a class="nav-link" href="{{route('usuarios.sesiones')}}">
                              <span class="sidebar-mini"> B </span>
                              <span class="sidebar-normal"> Sesiones de usuarios </span>
                            </a>
                        </li> -->
                    </ul>
                </div>
            </li>
            @endif
            
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar transacciones"))
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#transaccionesToggle">
                    <i class="material-icons">image</i>
                    <p> Transacciones 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="transaccionesToggle">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('transacciones')}}">
                              <span class="sidebar-mini"> T </span>
                              <span class="sidebar-normal"> Transacciones </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('transacciones.grupo')}}">
                              <span class="sidebar-mini"> G </span>
                              <span class="sidebar-normal"> Grupo transacciones </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver lista de balances de bancas") == true || \App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver lista de balances de bancos") == true)
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#balanceToggle">
                    <i class="material-icons">account_balance_wallet</i>
                    <p> Balance 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="balanceToggle">
                    <ul class="nav">
                    @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver lista de balances de bancas"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('balance.bancas')}}">
                              <span class="sidebar-mini"> B </span>
                              <span class="sidebar-normal"> Bancas </span>
                            </a>
                        </li>
                    @endif
                    @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver lista de balances de bancos"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('balance.bancos')}}">
                              <span class="sidebar-mini"> B </span>
                              <span class="sidebar-normal"> Bancos </span>
                            </a>
                        </li>
                    @endif
                    </ul>
                </div>
            </li>
            @endif

            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar prestamos"))
            <li class="nav-item ">
                <a class="nav-link" href="{{route('prestamos')}}">
                    <i class="material-icons">format_list_numbered</i>
                    <p> Prestamos 
                       
                    </p>
                </a>
            </li>
            @endif

            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar entidades contables"))
            <li class="nav-item ">
                <a class="nav-link" href="{{route('entidades')}}">
                    <i class="material-icons">format_list_numbered</i>
                    <p> Entidades 
                       
                    </p>
                </a>
            </li>
            @endif
            
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar loterias") == true || \App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar reglas"))
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#pagesExamples">
                    <i class="material-icons">image</i>
                    <p> Loterias 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="pagesExamples">
                    <ul class="nav">
                    @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar loterias"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('loterias')}}">
                              <span class="sidebar-mini"> E </span>
                              <span class="sidebar-normal"> Editar </span>
                            </a>
                        </li>
                    @endif
                    @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar reglas"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('bloqueos')}}">
                              <span class="sidebar-mini"> B </span>
                              <span class="sidebar-normal"> Bloqueos </span>
                            </a>
                        </li>
                    @endif
                    </ul>
                </div>
            </li>
            @endif
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar bancas"))
            <li class="nav-item ">
                <a class="nav-link" href="{{route('bancas')}}">
                    <i class="material-icons">store</i>
                    <p> Bancas 
                       
                    </p>
                </a>
            </li>
            @endif
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar resultados"))
            <li class="nav-item ">
                <a class="nav-link" href="{{route('premios')}}">
                    <i class="material-icons">format_list_numbered</i>
                    <p> Resultados 
                       
                    </p>
                </a>
            </li>
            @endif
            
            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar horarios de loterias"))
            <li class="nav-item ">
                <a class="nav-link" href="{{route('horarios')}}">
                    <i class="material-icons">av_timer</i>
                    <p> Horarios loterias 
                       
                    </p>
                </a>
            </li>
            @endif

            @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar usuarios") == true || \App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver inicios de sesion"))
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#usuariosToggle">
                    <i class="material-icons">image</i>
                    <p> Usuarios 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="usuariosToggle">
                    <ul class="nav">
                    @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar usuarios"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('usuarios')}}">
                              <span class="sidebar-mini"> E </span>
                              <span class="sidebar-normal"> Editar </span>
                            </a>
                        </li>
                    @endif
                    @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Ver inicios de sesion"))
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('usuarios.sesiones')}}">
                              <span class="sidebar-mini"> S </span>
                              <span class="sidebar-normal"> Sesiones de usuarios </span>
                            </a>
                        </li>
                    @endif
                    </ul>
                </div>
            </li>
           @endif
           @if(\App\Classes\Helper::tienePermiso(session("idUsuario"), "Manejar monedas")) 
            <li class="nav-item ">
                <a class="nav-link" href="{{route('monedas')}}">
                    <i class="material-icons">attach_money</i>
                    <p> Monedas </p>
                </a>
            </li>
            @endif
            

            

            <li class="nav-item ">
                <a class="nav-link" href="{{route('cerrarSesion')}}?cerrar=si">
                    <i class="material-icons">clear</i>
                    <p> Cerrar </p>
                </a>
            </li>

            
        
            
        </ul>
        

        
    </div>
</div>
@endif

<!-- <div ng-controller='controllerImpresora'> -->
<div style="z-index:1000000000" id="modal-impresora" class="modal fade modal-impresora" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Registrar impresora</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row justify-content-center">
                        <div class="col-sm-7">
                            <div id="txtImpresoraGroup" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Impresora</label>
                            <input  id="txtImpresora" type="text" class="form-control" required>
                            </div>
                        </div>
                        <!-- <div class="form-group col-sm-3">
                            <input id="btnImpresoraGuardar" type="submit" class="btn btn-primary" value="Guardar">   
                        </div> -->
                        
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="exampleRadios" value="papelPequeno" id="radioPapelPequeno"> Papel peque&ntilde;o
                                <span class="circle">
                                <span class="check"></span>
                                </span>
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="exampleRadios" value="papelGrande" id="radioPapelGrande" checked> Papel grande
                                <span class="circle">
                                    <span class="check"></span>
                                </span>
                            </label>
                        </div>
                        
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group">
                            <input id="btnImpresoraGuardar" type="submit" class="btn btn-primary" value="Guardar">   
                        </div>
                    </div>

                        <div class="row my-3 justify-content-center">
                            <h4>Pasos para descargar app para imprimir desde escritorio</h4>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-2 mt-3">
                                <p>Primer paso</p>
                            </div>
                            <div class="col-3">
                                <a target="_blank" href="https://www.oracle.com/java/technologies/javase/jdk14-archive-downloads.html" class="btn btn-outline-info">Descargar java SDK 14</a>
                            </div>
                           
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-2 mt-3">
                                <p>Segundo paso</p>
                            </div>
                            <div class="form-group col-sm-3">
                                <input id="btnImpresoraDescargar" type="submit" class="btn btn-outline-primary" value="Descargar app para imprimir">   
                            </div>
                        </div>

                    <div class="container">

                        <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                        </div> -->
                    </div>

                </div> <!-- END MODAL-BODY -->
                
            </div> <!-- END MODAL-CONTENT-->
        </div>
    </div>
<!-- </div> -->
    <!-- END MODAL DUPLICAR TICKET -->

@yield('content')
