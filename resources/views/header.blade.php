<?php

if($controlador != "login"){
    if(!isset($_SESSION['idUsuario'])){

    }
}

?>

<!DOCTYPE html>
<html lang="en" ng-app='myModule'>
    <head>



    <meta charset="utf-8" />
<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
<link rel="icon" type="image/png" href="../assets/img/favicon.png">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

<title>






</title>

<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />


<!-- Extra details for Live View on GitHub Pages -->

<link href="{{asset('assets/css/material-dashboard.css')}}" rel="stylesheet" />


<!--     Fonts and icons     -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
<!-- <link rel="stylesheet" type="text/css" href="{{asset('assets/css/google.css')}}" /> -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">

<!-- CSS Files -->











<!-- CSS Just for demo purpose, don't include it in your project -->
<link href="{!!asset('assets/demo/demo.css') !!}" rel="stylesheet" />

<script>
    window.rutaGlobal = (false) ? "<?php echo url('') ?>" : '';
    window.idUsuario = "<?php echo session('idUsuario') ?>";
    window.idBanca = "<?php echo session('idBanca') ?>";

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

<script src="{{asset('assets/js/angular.min.js')}}" ></script>
<script src="{{asset('assets/js/angular-route.min.js')}}" ></script>

    <?php if($controlador == "principal"):?>
        <script src="{{asset('assets/js/angular/principal.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "principal.pruebahttp" ):?>
        <script src="{{asset('assets/js/angular/pruebahttp.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "bancas"):?>
        <script src="{{asset('assets/js/angular/bancas.js')}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "home" || $controlador == ""):?>
        <script src="{{asset('assets/js/home.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "usuarios"):?>
        <script src="{{asset('assets/js/angular/usuarios.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "loterias"):?>
        <script src="{{asset('assets/js/angular/loteria.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "loterias.bloqueos" ):?>
        <script src="{{asset('assets/js/angular/bloqueos.js')}}" ></script>
    <?php endif; ?>


    <?php if($controlador == "premios"):?>
        <script src="{{asset('assets/js/angular/premios.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "reportes.jugadas"):?>
        <script src="{{asset('assets/js/angular/reportes.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "usuarios"):?>
        <script src="{{asset('assets/js/angular/usuarios.js')}}" ></script>
    <?php endif; ?>

    <?php if($controlador == "horarios"):?>
        <script src="{{asset('assets/js/angular/horarios.js')}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "dashboard"):?>
        <script src="{{asset('assets/js/angular/dashboard.js')}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "bloqueos"):?>
        <script src="{{asset('assets/js/angular/bloqueos2.js')}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "entidades"):?>
        <script src="{{asset('assets/js/angular/entidades.js')}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "transacciones" || $controlador == "transacciones"):?>
        <script src="{{asset('assets/js/angular/transacciones.js')}}" ></script>
    <?php endif; ?>
    <?php if($controlador == "transacciones.grupo"):?>
        <script src="{{asset('assets/js/angular/transacciones-grupo.js')}}" ></script>
    <?php endif; ?>

<style>
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
    ng-controller='myController' ng-init="ROOT_PATH = '/'" ng-init="load('<?php if (isset($_SESSION['idUsuario'])) echo $_SESSION['idUsuario']; ?>', '/')"
    class="<?php if($controlador == 'login') echo 'off-canvas-sidebar' ?>" 
    
    on-shift-tab="venta_guardar($event,'email')">
      <!-- ng-keyup="venta_guardar($event)" -->

<?php 


if(session('idUsuario') == null && $controlador != 'login'){
    
    redirect()->route('login');
}

?>
<!--  wrapper-full-page -->

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
        </a></div>

    <div class="sidebar-wrapper">
        
        <div class="user">
            <div class="photo">
                <img src="{{asset('assets/img/banco.jpg')}}" />
            </div>
            <div class="user-info">
                <a data-toggle="collapse" href="#collapseExample" class="username">
                    <span>
                       <!-- Tania Andrew  -->
                       <?php if(isset($_SESSION['usuario'])) echo $_SESSION['usuario'] . " - " . $_SESSION['tipoUsuario'];?>
                      <b class="caret"></b>
                    </span>
                </a>
                <div class="collapse" id="collapseExample">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                              <span class="sidebar-mini"> MP </span>
                              <span class="sidebar-normal"> My Profile </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                              <span class="sidebar-mini"> EP </span>
                              <span class="sidebar-normal"> Edit Profile </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                              <span class="sidebar-mini"> S </span>
                              <span class="sidebar-normal"> Settings </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <ul class="nav">

            <li class="nav-item ">
                <a class="nav-link" href="principal">
                    <i class="material-icons">dashboard</i>
                    <p> Principal </p>
                </a>
            </li>
            
            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#pagesExamples">
                    <i class="material-icons">image</i>
                    <p> Loterias 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="pagesExamples">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('loterias')}}">
                              <span class="sidebar-mini"> E </span>
                              <span class="sidebar-normal"> Editar </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="{{route('loterias.bloqueos')}}">
                              <span class="sidebar-mini"> B </span>
                              <span class="sidebar-normal"> Bloqueos </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link" href="{{route('bancas')}}">
                    <i class="material-icons">format_list_numbered</i>
                    <p> Bancas 
                       
                    </p>
                </a>
            </li>
            <li class="nav-item ">
                <a class="nav-link" href="{{route('horarios')}}">
                    <i class="material-icons">format_list_numbered</i>
                    <p> Horarios loterias 
                       
                    </p>
                </a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" data-toggle="collapse" href="#reportesToggle">
                    <i class="material-icons">image</i>
                    <p> Reportes 
                       <b class="caret"></b>
                    </p>
                </a>

                <div class="collapse" id="reportesToggle">
                    <ul class="nav">
                        <li class="nav-item ">
                            <a class="nav-link" href="">
                              <span class="sidebar-mini"> V </span>
                              <span class="sidebar-normal"> Ventas </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" href="">
                              <span class="sidebar-mini"> J </span>
                              <span class="sidebar-normal"> Jugadas </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item ">
                <a class="nav-link" href="{{route('cerrarSesion')}}?cerrar=si">
                    <i class="material-icons">format_list_numbered</i>
                    <p> Cerrar </p>
                </a>
            </li>

            
        
            
        </ul>
        

        
    </div>
</div>
@endif

@yield('content')
