@extends('header')

@section('content')

<div class="main-panel" ng-init="load('{{session('idUsuario')}}', '{{url('principal/ticket')}}')">



<!-- Navbar  bg-info -->
<nav class="navbar navbar-expand-lg mx-2 mb-2 d-none d-md-flex" id="navigation-example">
        <div class="container-fluid">
        <div class="navbar-wrapper">
          
            
            <!-- <div class="navbar-minimize">
              <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
                  <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                  <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
              </button>
            </div> -->
          
                

                <a href="#" class="navbar-brand font-weight-bold">
                   Ventas del dia: <span class="bg-info p-2 text-white rounded">@{{datos.estadisticas_ventas.total | currency}}</span>
                </a>
                <a href="#" class="navbar-brand font-weight-bold">
                   Jugadas del dia: <span class="bg-info p-2 text-white rounded">@{{datos.estadisticas_ventas.total_jugadas | number:2}}</span>
                </a>
                <a href="#" class="navbar-brand font-weight-bold">
                   Fecha: <span class="bg-secondary p-2 text-white rounded">@{{datos.fecha}}</span>
                </a>

                <!-- <a href="#" class="navbar-brand font-weight-bold">
                    <small>Total jugadas</small>: 20000
                </a> -->

                <!-- <ul class="navbar-nav float-right">
                <li class="nav-item">
                    General
                  </li>
                  <li class="nav-item">
                    General
                  </li>
                </ul> -->
                
            <!--            AQUI EMPIEZA          -->
                     



            <!--            AQUI TERMINA          -->



            </div>

            

            <!-- <div class="navbar-toggle w-25">
            <ul class="nav nav-pills float-right">
                <li class="nav-item">
                    General
                  </li>
                  <li class="nav-item ml-5">
                    General
                  </li>
                </ul>
            </div> -->

           
            <button style="display: block;" class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
          <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon icon-bar"></span>
                <span class="navbar-toggler-icon icon-bar"></span>
                <span class="navbar-toggler-icon icon-bar"></span>
            </button>


<!--             
            <div class="collapse navbar-collapse justify-content-end">
                
            </div> -->
        </div>
    </nav>
    <!-- End Navbar -->
    
<!-- End Navbar -->


              

<div class="content">
                      <!-- <div class="container-fluid"> -->
<div class="">
    <div class="row d-none d-md-flex">
        <div class="col-md-12">
        <div class="card mt-0 mb-2 p-0">
            <div class="card-body">
               

                <div class="row">
                       

                    <div class="col-sm-1 col-2 text-center">
                                <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input ng-model="datos.hayDescuento" ng-change="calcularTotal()" class="form-check-input" type="checkbox" value=""> Desc
                                    <span class="form-check-sign">
                                    <span class="check"></span>
                                    </span>
                                </label>
                                </div>
                    </div>

                    <div  class="col-lg-4 col-md-4 col-sm-4 col-10">
                    <!-- o.descripcion disable when validarHora(o.horaCierre, o.descripcion) for o in datos.optionsLoterias track by o.id -->
                            <select 
                            id="multiselect"
                                ng-change="calcularTotal()"
                                ng-model="datos.loterias"
                                ng-options="o.descripcion for o in datos.optionsLoterias track by o.id"
                                class="selectpicker col-12" 
                                data-style="select-with-transition" 
                                multiple title="Seleccionar loteria"
                                data-size="7" aria-setsize="2">
                            </select>
                    </div>

                    
                        
                            <div class="col-sm-2 col-4">
                                <form>
                                    <div id="divInputJugada" class="form-group">
                                        <label  for="jugada" class="bmd-label-floating">Jugada</label>
                                        <input 

                                            
                                            ng-blur="monto_disponible(true)"
                                            ng-model="datos.jugada"
                                            ng-keyup="inputJugadaKeyup($event)"
                                            
                                            class="form-control h4" 
                                            id="inputJugada" 
                                            type="text" name="text" 
                                            minLength="2" maxLength="6"  required="true" />
                                    </div>
                                </form>
                            </div>

                            <!-- <div class="form-group"> -->
                                
                                <div style="font-size: 16px" class="col-3 col-sm-2 mt-2">
                                <input disabled ng-model="datos.montoExistente" type="text" class="form-control" id="inputPassword" placeholder="0.00">
                                </div>
                            <!-- </div> -->

                        
                            <div class="col-sm-2 col-3">    
                                <div class="form-group">
                                    <label for="monto" class="bmd-label-floating">Monto</label>
                                    <input
                                        ng-model="datos.monto"
                                        ng-keyup="jugada_insertar($event)"
                                        id="inputMonto"
                                        class="form-control h4" id="monto" 
                                        type="number" name="number" number="true" minLength="1" maxLength="4" required="true" />
                                </div>
                            </div>
                    

                </div>


                <div class="row justify-content-md-center" >
                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Monto: <span class="bg-info p-1 text-white rounded">@{{datos.monto_a_pagar | currency}}</span>
                        </h4>
                    </div>
                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Jugadas: <span class="bg-info p-1 text-white rounded">@{{datos.total_jugadas | currency}}</span>
                        </h4>
                    </div>
                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Descuento: <span class="bg-info p-1 text-white rounded">@{{datos.descuentoMonto | currency}}</span>
                        </h4>
                    </div>
                </div>

            </div>
        </div>
        </div>
        
    </div>


    <!--      Wizard container        -->
        <!-- class="wizard-container" -->
    <div class="row bg-primary d-md-none" style="margin-top: 0px!important; margin-bottom: 0px!important; height: 100vh;">
      <div 
            class="card card-wizard mt-0 m-0" 
            data-color="blue" 
            id="wizardProfile"
            style="height: 100vh;"
        >
        <form novalidate>
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header m-0 p-0 mb-1">
            <div class="row justify-content-center m-0 p-0">

            <style>
                .dropdown{
                    margin: 0px!important;
                    padding: 0px!important;
                }
            </style>
           
              
              @if($usuario->tienePermiso("Jugar como cualquier banca"))
              <div  class="col-5 m-0 p-0">
                    <!-- o.descripcion disable when validarHora(o.horaCierre, o.descripcion) for o in datos.optionsLoterias track by o.id -->
                            <select 
                            id="multiselect2"
                                ng-model="datos.selectedBancas"
                                ng-options="o.descripcion for o in datos.optionsBancas track by o.id"
                                class="selectpicker w-100" 
                                data-style="select-with-transition" 
                                title="Seleccionar loteria"
                                data-size="7" aria-setsize="2">
                            </select>
                    </div>
                @endif


                    <div class="col-sm-1 col-2 ">
                                <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input ng-model="datos.print"  class="form-check-input" type="checkbox" value=""> print
                                    <!-- <i class="material-icons text-success" style="font-size: 24px;">chat</i> -->
                                    
                                    <span class="form-check-sign">
                                    <span class="check"></span>
                                    </span>
                                </label>
                                </div>
                    </div>

                    <div class="col-sm-1 col-2 ">
                                <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input ng-model="datos.sms"  class="form-check-input" type="checkbox" value=""> sms
                                    <!-- <i class="material-icons text-success" style="font-size: 24px;">chat</i> -->
                                    
                                    <span class="form-check-sign">
                                    <span class="check"></span>
                                    </span>
                                </label>
                                </div>
                    </div>

                     <div class="col-sm-1 col-2 text-right">
                                <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input ng-model="datos.whatsapp" class="form-check-input" type="checkbox" value=""> wtsapp
                                    <!-- <i class="fa fa-whatsapp text-success" style="font-size: 24px" ></i> -->
                                    
                                    <span class="form-check-sign">
                                    <span class="check"></span>
                                    </span>
                                </label>
                                </div>
                    </div>
              <!--
              <h5 class="card-title m-0 p-0">
                @{{datos.selectedBancas.descripcion}}
              </h5>
               -->
           
            </div>
           
          </div>
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li ng-click="quitarOPonerClaseActive()" id="nav-pills-menu" class="nav-item p-0">
                <a class="nav-link navbar-toggler" aria-disabled="true" href="#" data-toggle="tab" role="tab" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
                  Menu
                </a>
              </li>
              <li id="nav-pills-jugar" class="nav-item active p-0">
                <a ng-init="mostrarPagos = false" ng-click="mostrarPagos = false" class="nav-link" href="#about" data-toggle="tab" role="tab">
                  Jugar
                </a>
              </li>
              <li class="nav-item">
                <a ng-click="mostrarPagos = true" class="nav-link" href="#account" data-toggle="tab" role="tab">
                  Jugadas
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="about">
                <!-- <h5 class="info-text"> Let's start with the basic information (with validation)</h5> -->
                <div class="row p-0 m-0">
                    <iframe id="iframeOcultoMovil" name="iframeOcultoMovil"  style="width:0px; height:0px; border:0px; margin:0px;"></iframe>
                    <div class="col-sm-1 col-3 text-center">
                                <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input ng-model="datos.hayDescuento" ng-change="calcularTotal()" class="form-check-input" type="checkbox" value=""> Desc
                                    <span class="form-check-sign">
                                    <span class="check"></span>
                                    </span>
                                </label>
                                </div>
                    </div>

                    <div  class="col-9 m-0 p-0">
                    <!-- o.descripcion disable when validarHora(o.horaCierre, o.descripcion) for o in datos.optionsLoterias track by o.id -->
                            <select 
                            id="multiselect2"
                                ng-change="calcularTotal()"
                                ng-model="datos.loterias"
                                ng-options="o.descripcion for o in datos.optionsLoterias track by o.id"
                                class="selectpicker w-100" 
                                data-style="select-with-transition" 
                                multiple title="Seleccionar loteria"
                                data-size="7" aria-setsize="2">
                            </select>
                    </div>
                 
                </div>

                <div class="col-12 mt-2">
                    <style>
                        .txtActive {
                            outline: dotted 15px;
                            outline: -webkit-focus-ring-color auto 15px
                        }
                    </style>
                    <div class="row">
                        <div 
                            ng-click="txtActive = 1"
                            ng-class="{'txtActive': (txtActive == 1)}"
                            class="col-4 text-center py-2 border"
                            style="cursor: pointer;">
                            <p style="font-size: 16px;" class="mt-2 font-weight-bold">@{{(datos.jugada != undefined && datos.jugada != '') ? datos.jugada : 'Jugada'}}</p>
                        </div>
                        <div 
                            class="col-4 text-center py-2 border">
                            <p style="font-size: 16px; color:grey" class="mt-2 font-weight-bold text-secundary">@{{(datos.montoExistente != undefined) ? datos.montoExistente : 'Disponible'}}</p>
                        </div>
                        <div 
                            ng-click="txtActive = 2"
                            ng-class="{'txtActive': (txtActive == 2)}"
                            class="col-4 text-center py-2 border"
                            style="cursor: pointer;">
                            <p style="font-size: 16px;" class="mt-2 font-weight-bold">@{{(datos.monto != undefined && datos.monto != '') ? datos.monto : 'Monto'}}</p>
                        </div>

                    </div>
                                
                </div><!-- END COL-12 -->

                <div class="col-12 mt-0">
                    <div class="row py-0 my-0">
                        <div ng-click="tecladoClick('.')" class="col-2  text-center btn btn-warning mr-2">
                            <h4 class="mt-1">.</h4>
                        </div>
                        <div ng-click="tecladoClick('D')" class="col-2  text-center btn btn-warning mr-2">
                            <h4 class="mt-1">D</h4>
                        </div>
                        <div ng-click="tecladoClick('Q')" class="col-2  text-center btn btn-warning mr-2">
                            <h4 class="mt-1">Q</h4>
                        </div>
                        <div ng-click="tecladoClick('BACKSPACE')" class="col-5 text-center btn btn-secundary">
                            <h4 class="mt-1">BACKSPACE</h4>
                        </div>
                    </div>
                    <div class="row py-0 my-0">
                        <div ng-click="tecladoClick('7')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">7</h4>
                        </div>
                        <div ng-click="tecladoClick('8')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">8</h4>
                        </div>
                        <div ng-click="tecladoClick('9')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">9</h4>
                        </div>
                        <div ng-click="tecladoClick('/')" class="col-5 text-center btn btn-secundary">
                            <h4 class="mt-1">/</h4>
                        </div>
                    </div>
                    <div class="row py-0 my-0">
                        <div ng-click="tecladoClick('4')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">4</h4>
                        </div>
                        <div ng-click="tecladoClick('5')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">5</h4>
                        </div>
                        <div ng-click="tecladoClick('6')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">6</h4>
                        </div>
                        <div ng-click="tecladoClick('-')" class="col-5 text-center btn btn-secundary">
                            <h4 class="mt-1">-</h4>
                        </div>
                    </div>
                    <div class="row py-0 my-0">
                        <div ng-click="tecladoClick('1')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">1</h4>
                        </div>
                        <div ng-click="tecladoClick('2')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">2</h4>
                        </div>
                        <div ng-click="tecladoClick('3')" class="col-2  text-center btn btn-info mr-2">
                            <h4 class="mt-1">3</h4>
                        </div>
                        <div ng-click="tecladoClick('+')" class="col-5 text-center btn btn-secundary">
                            <h4 class="mt-1">+</h4>
                        </div>
                    </div>
                    <div class="row py-0 my-0">
                        <div ng-click="tecladoClick('0')" class="col-5  text-center btn btn-info mr-2 mb-0">
                            <h4 class="mt-1">0</h4>
                        </div>
                        <div ng-click="tecladoClick('ENTER')" class="col-6 text-center btn btn-secundary ml-3  mb-0">
                            <h4 class="mt-1">ENTER</h4>
                        </div>
                    </div>
                    <div class="row py-0 my-0">
                        <div class="col-3 text-center" ng-click="venta_guardar(1)" style="cursor: pointer">
                            <i class="material-icons text-success mt-2" style="font-size: 37px;">print</i>
                        </div>
                        <div class="col-9">
                            <div class="row">
                                <div  
                                    class="input-group form-control-lg m-0 p-0">
                                    <div class="input-group-prepend" ng-click="cancelarDesdeMovil()">
                                        <span class="input-group-text">
                                        <i class="material-icons text-danger" style="font-size: 35px;">delete_forever</i>
                                        </span>
                                    </div>
                                    <div class="form-group col-9 m-0 p-0">
                                        <select 
                                            ng-model="datos.selectedVentas" 
                                            ng-options="o.codigoBarra for o in datos.optionsVentas | orderBy:id:'desc'"
                                            class="selectpicker w-100" 
                                            data-style="select-with-transition" 
                                            title="Select Ticket">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- END COL-6 -->
                    </div>
                                
                </div><!-- END COL-12 -->
                <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 1 -->

        

              <div class="tab-pane" id="account">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center ">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                             <div class="table-responsive">
                    <table class="table table-fixed-grid-pequeno">
                    <thead>
                        <tr>
                        <th style="font-size:13px" class="text-center font-weight-bold col-3 col-sm-5">LOTERIA</th>
                        <th style="font-size:13px" class="text-center font-weight-bold col-3">JUGADA</th>
                        <th style="font-size:13px" class="text-center font-weight-bold col-3">MONTO</th>
                        <th style="font-size:13px" class="text-center font-weight-bold col-3">ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody id="tbody" class="">
                    
                        <tr ng-repeat="c in datos.jugadas ">
                        <td style="font-size:12px" class="col-3 text-center col-sm-5">@{{c.abreviatura}}</td>
                        <td style="font-size:12px" class="col-3 text-center">@{{agregar_guion(c.jugada)}}</td>
                        <td style="font-size:12px" class="text-center col-3">@{{c.monto}}</td>
                        <td style="font-size:12px; cursor: pointer;" class="text-center col-3" ng-click="jugada_eliminar(c.jugada)">
                            <!-- <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link text-center btn-danger">
                                <i class="material-icons text-center">close</i>
                            </button> -->
                            <i class="material-icons text-center b-0 p-0" style="margin:0px!important;padding:0px!important;font-size: 15px;">close</i>
                            </td>
                        </tr>
                        
                    </tbody>
                    </table>
                    <hr class="mb-0">
                    <div class="float-right">
                            <div style="font-size: 17px;" class="font-weight-bold">
                                Total
                                <small class="">@{{datos.monto_a_pagar | currency}}</small>
                            </div>   
                    </div>
                    <!-- <div class="float-right mt-3">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="h3 ml-3">&euro;0</small>
                            </div> 
                            
                    </div> -->
                    
                </div>
                      
                    </div> <!-- END ROW SECUNDARIO -->
                  </div> <!-- END COL-LG-12 PRINCIPAL -->
                </div> <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB -->
              <div class="tab-pane" id="address">
                <div class="row justify-content-center">
                  <div class="col-sm-12">
                    <h5 class="info-text"> Are you living in a nice area? </h5>
                  </div>
                  <div class="col-sm-7">
                    <div class="form-group">
                      <label>Street Name</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label>Street No.</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-5">
                    <div class="form-group">
                      <label>City</label>
                      <input type="text" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-5">
                    <div class="form-group select-wizard">
                      <label>Country</label>
                      <select class="selectpicker" data-size="7" data-style="select-with-transition" title="Single Select">
                        <option value="Afghanistan"> Afghanistan </option>
                        <option value="Albania"> Albania </option>
                        <option value="Algeria"> Algeria </option>
                        <option value="American Samoa"> American Samoa </option>
                        <option value="Andorra"> Andorra </option>
                        <option value="Angola"> Angola </option>
                        <option value="Anguilla"> Anguilla </option>
                        <option value="Antarctica"> Antarctica </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </form>
      </div>
    </div>
    <!-- wizard container -->
    

    <div class="row justify-content-center">
        <div class="col-md-12 d-none d-sm-none d-md-block text-center">
            <div class="card my-0 mx-0 d-inline-block " style="min-height: 370px; max-height: 370px; width: 24.7%;"> <!-- min-height: 455px; max-height: 455px; ultima modificacion min-height: 370px; max-height: 370px; width: 24.7%;-->
                <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">assignment</i>
                </div>
                <h4 class="card-title">Directo</h4>
                </div>
                <div class="card-body"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="table-responsive">
                    <table class="table table-fixed">
                    <thead>
                        <tr>
                        <th class="font-weight-bold col-2 col-sm-3" style="font-size: 14px">LOT</th>
                        <th class="font-weight-bold col-4" style="font-size: 14px">NUM</th>
                        <th class="text-right font-weight-bold col-4 d-md-none d-lg-block" style="font-size: 14px">MONTO</th>
                        <th class="text-right font-weight-bold col-4 d-md-block d-lg-none" style="font-size: 14px">MONT</th>
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr ng-repeat="c in datos.jugadas | filter: {tam: 2}">
                        <td class="col-sm-3" style="font-size: 12px;">@{{c.abreviatura}}</td>
                        <td class="col-4">@{{c.jugada}}</td>
                        <td class="text-right col-4">
                            @{{c.monto}}
                            <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link m-0 p-0 d-inline ">
                                    <i class="material-icons">close</i>
                            </button>
                        </td>
                        <!-- <td class="td-actions text-center col-1">
                            <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                <i class="material-icons">close</i>
                            </button>
                            </td> -->
                        </tr>
                        <tr>
                        
                        
                        <!-- <tr>
                        <td ></td>
                        <td class="td-total">
                            Total
                        </td>
                        <td class="td-price">
                            <small>&euro;</small>12,999
                        </td>
                        <td></td>
                        </tr> -->
                    </tbody>
                    </table>
                    <hr class="mb-0">
                    <!-- <div class="float-right mt-3">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="h3 ml-3">&euro;0</small>
                            </div> 
                            
                    </div> -->
                </div>
                    <div class="float-right">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="">@{{datos.total_directo | currency}}</small>
                            </div>   
                    </div>
                </div>
            </div>
            <div class="card my-0 mx-0 d-inline-block mx-0" style="min-height: 370px; max-height: 370px; width: 24.7%;"> <!-- min-height: 455px; max-height: 455px; -->
                <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">assignment</i>
                </div>
                <h4 class="card-title">Pale y tripleta</h4>
                </div>
                <div class="card-body"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="table-responsive">
                    <table class="table table-fixed">
                    <thead>
                        <tr>
                        <th class="font-weight-bold col-2 col-sm-3" style="font-size: 14px">LOT</th>
                        <th class="font-weight-bold col-4" style="font-size: 14px">NUM</th>
                        <th class="text-right font-weight-bold col-4 d-md-none d-lg-block" style="font-size: 14px">MONTO</th>
                        <th class="text-right font-weight-bold col-4 d-md-block d-lg-none" style="font-size: 14px">MONT</th>
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr ng-if="(c.tam == 4 || c.tam == 6) && esPick3Pick4UOtro(c.jugada) == 'otro'" ng-repeat="c in datos.jugadas ">
                            <td class="col-sm-3" style="font-size: 12px;">@{{c.abreviatura}}</td>
                            <td class="col-4">@{{agregar_guion(c.jugada)}}</td>
                            <td class="text-right col-4">
                                @{{c.monto}}
                                <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link m-0 p-0 d-inline ">
                                        <i class="material-icons">close</i>
                                </button>
                            </td>
                        <!-- <td class="td-actions text-center col-1">
                            <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                <i class="material-icons">close</i>
                            </button>
                            </td> -->
                        </tr>
                        
                    </tbody>
                    </table>
                    <hr class="mb-0">
                    
                    <!-- <div class="float-right">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="">&euro;0</small>
                            </div>   
                    </div> -->
                </div>
                    <div class="float-right">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="">@{{datos.total_pale | currency}}</small>
                            </div>   
                    </div>
                </div>
            </div>

            <div class="card my-0 mx-0 d-inline-block mx-0" style="min-height: 370px; max-height: 370px; width: 24.7%;"> <!-- min-height: 455px; max-height: 455px; -->
                <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">assignment</i>
                </div>
                <h4 class="card-title">Pick3</h4>
                </div>
                <div class="card-body"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="table-responsive">
                    <table class="table table-fixed">
                    <thead>
                        <tr>
                        <th class="font-weight-bold col-2 col-sm-3" style="font-size: 14px">LOT</th>
                        <th class="font-weight-bold col-4" style="font-size: 14px">NUM</th>
                        <th class="text-right font-weight-bold col-4 d-md-none d-lg-block" style="font-size: 14px">MONTO</th>
                        <th class="text-right font-weight-bold col-4 d-md-block d-lg-none" style="font-size: 14px">MONT</th>
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                    <tr ng-if="esPick3Pick4UOtro(c.jugada) == 'pick3Straight' || esPick3Pick4UOtro(c.jugada) == 'pick3Box'" ng-repeat="c in datos.jugadas ">
                        <td class="col-sm-3" style="font-size: 12px;">@{{c.abreviatura}}</td>
                        <td class="col-4">
                            @{{agregar_guion(c.jugada)}}
                            <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick3Box'" class="text-danger font-weight-bold">B</small>
                            <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick3Straight'" class="text-primary font-weight-bold">S</small>
                        </td>
                        <td class="text-right col-4">
                            @{{c.monto}}
                            <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link m-0 p-0 d-inline ">
                                    <i class="material-icons">close</i>
                            </button>
                        </td>
                        <!-- <td class="td-actions text-center col-1">
                            <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                <i class="material-icons">close</i>
                            </button>
                            </td> -->
                        </tr>
                        
                        
                        <!-- <tr>
                        <td ></td>
                        <td class="td-total">
                            Total
                        </td>
                        <td class="td-price">
                            <small>&euro;</small>12,999
                        </td>
                        <td></td>
                        </tr> -->
                    </tbody>
                    </table>
                    <hr class="mb-0">
                    <!-- <div class="float-right mt-3">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="h3 ml-3">&euro;0</small>
                            </div> 
                            
                    </div> -->
                </div>
                <div class="float-right">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="">@{{datos.total_pick3 | currency}}</small>
                            </div>   
                    </div>
                </div>
            </div>

            <div class="card my-0 mx-0 d-inline-block mx-0" style="min-height: 370px; max-height: 370px; width: 24.7%;"> <!-- min-height: 455px; max-height: 455px; -->
                <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">assignment</i>
                </div>
                <h4 class="card-title">Pick4</h4>
                </div>
                <div class="card-body"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="table-responsive">
                    <table class="table table-fixed">
                    <thead>
                        <tr>
                        <th class="font-weight-bold col-2 col-sm-3" style="font-size: 14px">LOT</th>
                        <th class="font-weight-bold col-4" style="font-size: 14px">NUM</th>
                        <th class="text-right font-weight-bold col-4 d-md-none d-lg-block" style="font-size: 14px">MONTO</th>
                        <th class="text-right font-weight-bold col-4 d-md-block d-lg-none" style="font-size: 14px">MONT</th>
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr ng-if="esPick3Pick4UOtro(c.jugada) == 'pick4Straight' || esPick3Pick4UOtro(c.jugada) == 'pick4Box'" ng-repeat="c in datos.jugadas ">
                        <td class="col-sm-3" style="font-size: 12px;">@{{c.abreviatura}}</td>
                        <td class="col-4">
                            @{{agregar_guion(c.jugada)}}
                            <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick4Box'" class="text-danger font-weight-bold">B</small>
                            <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick4Straight'" class="text-primary font-weight-bold">S</small>
                        </td>
                        <td class="text-right col-4">
                            @{{c.monto}}
                            <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link m-0 p-0 d-inline ">
                                    <i class="material-icons">close</i>
                            </button>
                        </td>
                        <!-- <td class="td-actions text-center col-1">
                            <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                <i class="material-icons">close</i>
                            </button>
                            </td> -->
                        </tr>
                        
                    </tbody>
                    </table>
                    <hr class="mb-0">
                    
                    <!-- <div class="float-right">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="">&euro;0</small>
                            </div>   
                    </div> -->
                </div>
                    <div class="float-right">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="">@{{datos.total_pick4 | currency}}</small>
                            </div>   
                    </div>
                </div>
            </div>


            </div>

    </div>

      

    <!-- Grid grande -->
    <div class="row d-none">
        <div class="col-md-12 d-lg-none">
            <div class="card my-0" style="min-height: 400px; max-height: 200px;"> <!-- min-height: 455px; max-height: 455px; -->
                <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                    <i class="material-icons">assignment</i>
                </div>
                <h4 class="card-title">Striped Table</h4>
                </div>
                <div class="card-body"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="table-responsive">
                    <table class="table table-fixed">
                    <thead>
                        <tr>
                        <th style="font-size:15px" class="font-weight-bold col-2 col-sm-5">LOT</th>
                        <th style="font-size:15px" class="font-weight-bold col-2">NUM</th>
                        <th style="font-size:15px" class="text-right font-weight-bold col-4">Monto</th>
                        <th style="font-size:15px" class="text-right font-weight-bold">Quit</th>
                        </tr>
                    </thead>
                    <tbody class="">
                    
                        <tr ng-repeat="c in datos.jugadas ">
                        <td style="font-size:12px" class="col-sm-5">@{{c.abreviatura}}</td>
                        <td style="font-size:12px" class="col-2">@{{agregar_guion(c.jugada)}}</td>
                        <td style="font-size:12px" class="text-right col-4">@{{c.monto}}</td>
                        <td style="font-size:12px" class="td-actions text-center col-1">
                            <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                <i class="material-icons">close</i>
                            </button>
                            </td>
                        </tr>
                        
                    </tbody>
                    </table>
                    <hr class="mb-0">
                    <div class="float-right">
                            <div style="font-size: 17px;" class="font-weight-bold">
                                Total
                                <small class="">@{{datos.monto_a_pagar | currency}}</small>
                            </div>   
                    </div>
                    <!-- <div class="float-right mt-3">
                            <div style="font-size: 16px;" class="font-weight-bold">
                                Total
                                <small class="h3 ml-3">&euro;0</small>
                            </div> 
                            
                    </div> -->
                    
                </div>
                   
                </div>
                   
            </div>
            </div>
    </div>

    <!-- END GRID GRANDE -->




    <!-- BOTONES REPORTES -->

    <div class="row d-none d-md-flex">
        <div class="col-12 text-center" >
            <button class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Monitoreo</button>
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".modal-ventas">Ventas</button>
            <button ng-click="venta_guardar(2)" class="btn btn-success">Crear ticket</button>
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".modal-duplicar">Duplicar</button>
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".modal-jugadas">Jugadas</button>
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".modal-pagar">Pagar</button>
            <button class="btn btn-success" data-toggle="modal" data-target=".modal-cancelar">Cancelar</button>
        </div>
        <!-- <div class="col-1">
            <button ng-click="buscar()" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Monitoreo</button>
        </div>
        <div class="col-2">
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Ventas historicas</button>
        </div>
        <div class="col-1">
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Duplicar</button>
        </div>
        <div class="col-2">
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Jugadas</button>
        </div>
        <div class="col-1">
            <button ng-click="" class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Pagar</button>
        </div> -->
        <iframe id="iframeOculto" name="iframeOcultoDX"  style="width:0px; height:0px; border:0px; margin:0px;"></iframe>

    </div>
    <!-- <iframe id="iframeOculto" name="iframeOcultoDX"  style="width:0px; height:0px; border:0px; margin:0px;"></iframe> -->


    <!-- FIN BOTONES REPORTES -->



    <!-- MODAL MONITOREO -->
    <style>
                .modal-lg {
            max-width: 90% !important;
        }
    </style>

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-1">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Manejar tickets</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                        <div class="col-sm-2">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Fecha busqueda</label>
                            <input ng-model="datos.monitoreo.fecha" id="fechaBusqueda" type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="buscar()" type="submit" class="btn btn-primary" value="Buscar">   
                        </div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button ng-click="buscarpor_ticket_estado(5)" type="button" class="btn btn-info">Todos <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_todos}}</span></button>
                            <button ng-click="buscarpor_ticket_estado(2)" type="button" class="btn btn-info">Ganadores <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_ganadores }}</span></button>
                            <button ng-click="buscarpor_ticket_estado(3)" type="button" class="btn btn-info">Perdedores <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_perdedores }}</span></button>
                            <button ng-click="buscarpor_ticket_estado(1)" type="button" class="btn btn-info">Pendientes <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_pendientes }}</span></button>
                            <button ng-click="buscarpor_ticket_estado(0)" type="button" class="btn btn-info">Cancelados <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_cancelados }}</span></button>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-1 text-right mt-3">
                            <h4 class="font-weight-bold">Filtrar:</h4>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                            <label for="numeroTicketBusqueda" class="bmd-label-floating">Numero ticket</label>
                            <input ng-keyup="buscarpor_ticket_estado(null)" ng-model="datos.monitoreo.idTicket" id="numeroTicketBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <table class="table table-sm">
                        <thead>
                            <tr>
                            <th scope="col" class="text-center">Numero</th>
                            <th scope="col" class="text-center">Creado</th>
                            <!-- <th scope="col" class="text-center">Cerrado</th> -->
                            <th scope="col" class="text-center">Usuario</th>
                            <th scope="col" class="text-center">Monto</th>
                            <th scope="col" class="text-center">Premio</th>
                            <th scope="col" class="text-center">Cancelado por</th>
                            <th scope="col" class="text-center">Fecha cancelado</th>
                            <th scope="col" class="text-center">Estado</th>
                            <!-- <th scope="col" class="text-center">Marcar pago</th> -->
                            <th scope="col" class="text-center">Imprimir</th>

                            <!--<th scope="col" class="text-center">Cancelar/Eliminar</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="c in datos.monitoreo.ventas | filter:datos.monitoreo.datosBusqueda">
                                <td scope="col" class="text-center" style="font-size: 14px">@{{c.codigo}}-@{{toSecuencia(c.idTicket)}}</td>
                                <td scope="col" class="text-center">@{{toFecha(c.created_at.date) | date:"dd/MM/yyyy hh:mm a"}}</td>
                                <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                <td scope="col" class="text-center">@{{c.usuario}}</td>
                                <td scope="col" class="text-center">@{{c.subTotal}}</td>
                                <td scope="col" class="text-center">@{{c.premio}}</td>
                                <td scope="col" class="text-center">@{{c.razon}}</td>
                                <td scope="col" class="text-center">@{{toFecha(c.fechaCancelacion.date) | date:"dd/MM/yyyy hh:mm a"}}</td>
                                <td scope="col" class="text-center">@{{estado(c.status)}}</td>
                                <!-- <td scope="col" class="text-center">Marcar pago</td> -->
                                <td scope="col" class="text-center">
                                    <a ng-click="imprimirTicket(c)" href="javascript:void(0)" class="btn btn-outline-primary px-1 py-1"><i class="material-icons">print</i></a>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>

                    <div class="container">

                        <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                        </div> -->
                    </div>

                </div> <!-- END MODAL-BODY -->
                
            </div> <!-- END MODAL-CONTENT-->
        </div>
    </div>
    <!-- MODAL MONITOREO -->
    




    <!-- MODAL DUPLICAR TICKET -->

    <div id="modal-duplicar" class="modal fade modal-duplicar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Manejar tickets</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                        <div class="col-sm-8">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Numero ticket</label>
                            <input ng-model="datos.duplicar.numeroticket" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="duplicar()" type="submit" class="btn btn-primary" value="Duplicar">   
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

    <!-- END MODAL DUPLICAR TICKET -->





    
    <!-- MODAL JUGADAS -->
   

    <div class="modal fade modal-jugadas" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-1">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Jugadas</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                   
                      <div class="col-3">
                      <style>
                          .dropdown-menu{
                              width: 100%;
                          }
                      </style>

                        <select 
                            ng-change="cbxLoteriasChanged()"
                            ng-model="datos.jugadasReporte.selectedLoteria"
                            ng-options="o.descripcion for o in datos.jugadasReporte.optionsLoterias"
                            class="selectpicker w-100" 
                            id="cbxLoteriasBuscarJugada"
                             data-style="btn btn-secundary btn-round" 
                            title="Seleccionar loterias">
                        </select>
                      </div>
                    
                        <div class="col-sm-2">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Fecha busqueda</label>
                            <input ng-model="datos.jugadasReporte.fecha" id="fechaBusqueda" type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="buscar_jugadas()" type="submit" class="btn btn-primary" value="Buscar">   
                        </div>
                    </div>

                    
                   <div class="row justify-content-center">
                       <div ng-show="datos.jugadasReporte.monto_total > 0" class="col-12 text-center">
                           <h2>Total loteria @{{datos.jugadasReporte.selectedLoteria.descripcion}}: @{{datos.jugadasReporte.monto_total | currency}}</h2>
                       </div>
                       <div class="col-4 col-sm-3 col-lg-3">
                           <h3 class="text-center">DIRECTO</h3>
                            <div class="table-responsive">
                                <table class="table table-fixed">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">JUGADA</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 14px">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.jugadasReporte.jugadas | filter: {tam: 2}">
                                    <td class="col-sm-5 text-center">@{{c.jugada}}</td>
                                    <td class="col-6 text-center">@{{c.monto}}</td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td> -->
                                    </tr>
                                    
                                </tbody>
                                </table>
                                <hr class="mb-0">
                                <!-- <div class="float-right mt-3">
                                        <div style="font-size: 16px;" class="font-weight-bold">
                                            Total
                                            <small class="h3 ml-3">&euro;0</small>
                                        </div> 
                                        
                                </div> -->
                            </div> <!-- END RESPONSIVE TABLE -->
                            <h4 class="text-right">Total: @{{datos.jugadasReporte.total_directo | currency}}</h4>
                       </div> <!-- COL-3 -->

                       <div class="col-4 col-sm-4 col-lg-3">
                           <h3 class="text-center">PALE</h3>
                            <div class="table-responsive">
                                <table class="table table-fixed">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">JUGADA</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 14px">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.jugadasReporte.jugadas | filter: {tam: 4}">
                                    <td class="col-sm-5 text-center">@{{agregar_guion(c.jugada)}}</td>
                                    <td class="col-6 text-center">@{{c.monto}}</td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td> -->
                                    </tr>
                                    
                                </tbody>
                                </table>
                                <hr class="mb-0">
                                <!-- <div class="float-right mt-3">
                                        <div style="font-size: 16px;" class="font-weight-bold">
                                            Total
                                            <small class="h3 ml-3">&euro;0</small>
                                        </div> 
                                        
                                </div> -->
                            </div> <!-- END RESPONSIVE TABLE -->
                            <h4 class="text-right">Total: @{{datos.jugadasReporte.total_palet | currency}}</h4>
                       </div> <!-- COL-3 -->


                       <div class="col-4 col-sm-4 col-lg-3">
                           <h3 class="text-center">TRIPLETA</h3>
                            <div class="table-responsive">
                                <table class="table table-fixed">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">JUGADA</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 14px">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.jugadasReporte.jugadas | filter: {tam: 6}">
                                    <td class="col-sm-5 text-center">@{{agregar_guion(c.jugada)}}</td>
                                    <td class="col-6 text-center">@{{c.monto}}</td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td> -->
                                    </tr>
                                    
                                </tbody>
                                </table>
                                <hr class="mb-0">
                                <!-- <div class="float-right mt-3">
                                        <div style="font-size: 16px;" class="font-weight-bold">
                                            Total
                                            <small class="h3 ml-3">&euro;0</small>
                                        </div> 
                                        
                                </div> -->
                            </div> <!-- END RESPONSIVE TABLE -->

                            <h4 class="text-right">Total: @{{datos.jugadasReporte.total_tripleta | currency}}</h4>
                       </div> <!-- COL-3 -->
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
    <!-- MODAL JUGADAS -->



    <!-- MODAL PAGAR TICKET -->

    <div id="modal-pagar" class="modal fade modal-pagar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Pagar</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                        <div class="col-sm-8">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Codigo</label>
                            <input ng-model="datos.pagar.codigoBarra" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="pagar()" type="submit" class="btn btn-primary" value="Buscar">   
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

    <!-- END MODAL PAGAR TICKET -->


    <!-- MODAL MONITOREO -->
    

    <div class="modal fade modal-ventas" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-1">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Manejar tickets</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                        <div class="col-sm-2">
                            <div id="fechaVentasReporte" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Fecha busqueda</label>
                            <input ng-model="datos.ventasReporte.fecha" id="fechaBusqueda" type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="ventasReporte_buscar()" type="submit" class="btn btn-primary" value="Buscar">   
                        </div>
                    </div>


                           <div class="row justify-content-center">
                        <div class="col-6">
                            <h2 class="text-center">Resumen de ventas</h2>
                            <table class="table table-striped table-sm">
                                <!-- <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                    </tr> -->
                                </thead>
                                <tbody>
                                    <tr>
                                    <th scope="row" colspan="3"  class="text-center">Balance a la fecha: </th>
                                    
                                    </tr>
                                    <tr>
                                    <th scope="row" colspan="3"  class="text-center">Total en prestamos: 1000</th>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Balance hasta la fecha</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.balanceHastaLaFecha | currency}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Banca</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.banca.descripcion}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Codigo</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.banca.codigo}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Pendientes</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.pendientes}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Perdedores</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.perdedores}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Ganadores</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.ganadores}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Total</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.total}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Ventas</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.ventas}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Comisiones</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.comisiones}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Descuentos</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.descuentos}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Premios</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.premios}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Neto</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.neto}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Final</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.neto}}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-center">Balance</th>
                                        <td class="text-center">@{{datos.ventasReporte.ventas.balanceActual | currency}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> <!-- END COL -->


                        <div class="col-12">
                            <h2 class="text-center">Totales por loteria</h2>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                    <th scope="col" class="text-center">Loteria</th>
                                    <th scope="col" class="text-center">Venta total</th>
                                    <th scope="col" class="text-center">Comisiones</th>
                                    <th scope="col" class="text-center">Premios</th>
                                    <th scope="col" class="text-center">Neto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="c in datos.ventasReporte.loterias">
                                    <th scope="row" class="text-center">@{{c.descripcion}}</th>
                                    <td class="text-center">@{{c.ventas ? c.ventas : 0}}</td>
                                    <td class="text-center">@{{c.comisiones ? c.comisiones : 0}}</td>
                                    <td class="text-center">@{{ (c.premios > 0) ? c.premios : 0}}</td>
                                    <td class="text-center" ng-init="neto = c.ventas - c.premios" ng-class="{'bg-rosado text-rosado-oscuro': (neto < 0), 'bg-azul-claro text-azul-oscuro': (neto > 0)}">@{{c.neto}}</td>
                                    </tr>
                                    
                                    
                                </tbody>
                            </table>
                        </div> <!-- END COL -->
                    </div> <!-- END ROW -->




                       <div class="row">
                        <div class="col-6">
                                <h2 class="text-center">Numeros ganadores</h2>
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                        <th scope="col" class="text-center">Loteria</th>
                                        <th scope="col" class="text-center">1era</th>
                                        <th scope="col" class="text-center">2da</th>
                                        <th scope="col" class="text-center">3era</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="c in datos.ventasReporte.loterias">
                                        <th scope="row" class="text-center">@{{c.descripcion}}</th>
                                        <td class="text-center">@{{c.primera}}</td>
                                        <td class="text-center">@{{c.segunda}}</td>
                                        <td class="text-center">@{{c.tercera}}</td>
                                        </tr>
                                        
                                        
                                    </tbody>
                                </table>
                            </div> <!-- END COL -->

                            <div class="col-6">
                                <h2 class="text-center">Tickets ganadores</h2>
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                        <th scope="col" class="text-center">Fecha</th>
                                        <th scope="col" class="text-center">Numero de ticket</th>
                                        <th scope="col" class="text-center">A pagar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="c in datos.ventasReporte.ticketsGanadores">
                                        <th scope="row" class="text-center">@{{toFecha(c.created_at.date) | date:"dd/MM/yyyy hh:mm a"}}</th>
                                        <td class="text-center">@{{c.idTicket}}</td>
                                        <td class="text-center">@{{c.premio}}</td>
                                        </tr>
                                        
                                        
                                    </tbody>
                                </table>
                            </div> <!-- END COL -->
                       </div> <!-- END ROW -->
                    



                    <div class="container">

                        <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                        </div> -->
                    </div>

                </div> <!-- END MODAL-BODY -->
                
            </div> <!-- END MODAL-CONTENT-->
        </div>
    </div>
    <!-- MODAL VENTAS -->
    

      <!-- MODAL CANCELAR TICKET -->

    <div id="modal-cancelar" class="modal fade modal-cancelar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Pagar</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                        <div ng-show="es_movil == false" class="col-sm-3">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Codigo</label>
                            <input ng-model="datos.cancelar.codigoBarra" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Razon</label>
                            <input ng-model="datos.cancelar.razon" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="cancelar()" type="submit" class="btn btn-primary" value="Buscar">   
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

    <!-- END MODAL CANCELAR TICKET -->


      <!-- MODAL ENVIAR TICKET SMS O WHATSAPP-->

    <div id="modal-sms" class="modal fade modal-sms" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" >
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">SMS</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                    <!-- <form>
                        <div class="form-group mb-2">
                        <input type="text" class="form-control b-none" id="recipient-name" placeholder="Nombre completo">
                        </div>
                        <div class="form-group my-2">
                        <input type="email" name="" value="" placeholder="Correo electronico" class="form-control">
                        </div>
                        <div class="form-group my-2">
                        <input type="password" name="" value="" placeholder="Password..." class="form-control">
                        </div>
                        <input type="submit" name="guardar" value="Guardar" class="btn btn-primary btn-block p-2">
                    </form> -->

                    <div class="row">
                        <div class="col-sm-12">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">SMS</label>
                            <input ng-disabled="datos.sms != true" ng-model="datos.numSms" id="fechaBusqueda" type="number" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">WHATSAPP</label>
                            <input ng-disabled="datos.whatsapp != true" ng-model="datos.numWhatsapp" id="fechaBusqueda" type="number" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="enviarSMS()" type="submit" class="btn btn-primary" value="Enviar ticket">   
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

    <!-- END MODAL DUPLICAR TICKET -->



    
</div>
</div>


               
</div>
          
</div>

<script src="{{asset('assets/js/core/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/core/popper.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/core/bootstrap-material-design.min.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/js/plugins/perfect-scrollbar.jquery.min.js')}}" ></script>


<!-- Plugin for the momentJs  -->
<script src="{{asset('assets/js/plugins/moment.min.js')}}"></script>

<!--  Plugin for Sweet Alert -->
<script src="{{asset('assets/js/plugins/sweetalert2.js')}}"></script>

<!-- Forms Validations Plugin -->
<script src="{{asset('assets/js/plugins/jquery.validate.min.js')}}"></script>

<!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
<script src="{{asset('assets/js/plugins/jquery.bootstrap-wizard.js')}}"></script>

<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
<script src="{{asset('assets/js/plugins/bootstrap-selectpicker.js')}}" ></script>

<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
<script src="{{asset('assets/js/plugins/bootstrap-datetimepicker.min.js')}}"></script>

<!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
<script src="{{asset('assets/js/plugins/jquery.dataTables.min.js')}}"></script>

<!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
<script src="{{asset('assets/js/plugins/bootstrap-tagsinput.js')}}"></script>

<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="{{asset('assets/js/plugins/jasny-bootstrap.min.js')}}"></script>

<!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
<script src="{{asset('assets/js/plugins/fullcalendar.min.js')}}"></script>

<!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
<script src="{{asset('assets/js/plugins/jquery-jvectormap.js')}}"></script>

<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="{{asset('assets/js/plugins/nouislider.min.js')}}" ></script>

<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>

<!-- Library for adding dinamically elements -->
<script src="{{asset('assets/js/plugins/arrive.min.js')}}"></script>


<!--  Google Maps Plugin    -->

<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2Yno10-YTnLjjn_Vtk0V8cdcY5lC4plU"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>


<!-- Chartist JS -->
<script src="{{asset('assets/js/plugins/chartist.min.js')}}"></script>

<!--  Notifications Plugin    -->
<script src="{{asset('assets/js/plugins/bootstrap-notify.js')}}"></script>





<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{asset('assets/js/material-dashboard.js')}}" type="text/javascript"></script>
<!-- <script src="{{asset('assets/js/prueba_jquery.js')}}" type="text/javascript"></script> -->
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="{{asset('assets/demo/demo.js')}}"></script>




  
  <script>
  $(document).ready(function(){

    // Initialise the wizard
    demo.initMaterialWizard();
    setTimeout(function() {
      $('.card.card-wizard').addClass('active');
    }, 600);


     // initialise Datetimepicker and Sliders
     md.initFormExtendedDatetimepickers();
    if($('.slider').length != 0){
      md.initSliders();
    }

   

    
    
    $('#facebook').sharrre({
  share: {
    facebook: true
  },
  enableHover: false,
  enableTracking: false,
  enableCounter: false,
  click: function(api, options){
    api.simulateClick();
    api.openPopup('facebook');
  },
  template: '<i class="fab fa-facebook-f"></i> Facebook',
  url: 'https://demos.creative-tim.com/material-dashboard-pro/examples/dashboard.html'
});

    $('#google').sharrre({
  share: {
    googlePlus: true
  },
  enableCounter: false,
  enableHover: false,
  enableTracking: true,
  click: function(api, options){
    api.simulateClick();
    api.openPopup('googlePlus');
  },
  template: '<i class="fab fa-google-plus"></i> Google',
  url: 'https://demos.creative-tim.com/material-dashboard-pro/examples/dashboard.html'
});

    $('#twitter').sharrre({
  share: {
    twitter: true
  },
  enableHover: false,
  enableTracking: false,
  enableCounter: false,
  buttons: { twitter: {via: 'CreativeTim'}},
  click: function(api, options){
    api.simulateClick();
    api.openPopup('twitter');
  },
  template: '<i class="fab fa-twitter"></i> Twitter',
  url: 'https://demos.creative-tim.com/material-dashboard-pro/examples/dashboard.html'
});

    
    var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-46172202-1']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(ga, s);
})();

    // Facebook Pixel Code Don't Delete
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');

try{
  fbq('init', '111649226022273');
  fbq('track', "PageView");

}catch(err) {
  console.log('Facebook Track Error:', err);
}

  });
</script>
<noscript>
  <img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=111649226022273&ev=PageView&noscript=1"
/>

</noscript>











    </body>

</html>



@endsection