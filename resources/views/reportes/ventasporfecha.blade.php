@extends('header')

@section('content')
          
    


            <div class="main-panel">
              <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top " id="navigation-example">
	<div class="container-fluid">
    <div class="navbar-wrapper">

    </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation" data-target="#navigation-example">
          <span class="sr-only">Toggle navigation</span>
                <span class="navbar-toggler-icon icon-bar"></span>
                <span class="navbar-toggler-icon icon-bar"></span>
                <span class="navbar-toggler-icon icon-bar"></span>
      </button>
	</div>
</nav>
<!-- End Navbar -->


              

                  <div class="content">
                      













<div class="container-fluid" ng-init="inicializarDatos()">
  
  <div class="col-md-12 col-12 mr-auto ml-auto">








<!-- TODAS LAS LOTERIAS -->
<div class="row justify-content-center">
  <div class="col-md-12">
      <div class="card " style="min-height: 1000px;">
        <div class="card-header card-header-info card-header-text">
          <div class="card-text">
            <h4 class="card-title">
              Ventas por fecha
              <div ng-show="cargando" class="ml-2 spinner-border" style="width: 1.7rem; height: 1.7rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </h4>
            
          </div>
        </div>
        <div class="card-body ">

            <div class="row ">

                    <div class="col-12 ">
                        <div class="row">
                        <div class="col-sm-2 mt-4">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating font-weight-bold" style="color: black;">Desde</label>
                            <input ng-model="datos.fechaDesde" id="fechaBusquedaDesde" type="date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-2 mt-4">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating font-weight-bold" style="color: black;">Hasta</label>
                            <input ng-model="datos.fechaHasta" id="fechaBusquedaHasta" type="date" class="form-control" required>
                            </div>
                        </div>
                        

                        <div class="col-3 text-center">
                            <div class="input-group">
                            <label  for="jugada" class="bmd-label-floating font-weight-bold" style="color: black;">Banca</label>
                              <!-- <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Dias</label>                               -->
                                <style>
                                  #multiselectDias{
                                    font-size: 8px;
                                  }
                                </style>
                                <!-- <div  class="col-9"> -->
                                  <select 
                                  ng-model="datos.selectedBancas"
                                      ng-options="o.descripcion for o in datos.optionsBancas track by o.id"
                                      class="selectpicker col-12" 
                                      data-style="select-with-transition" 
                                       title="Seleccionar banca"
                                       multiple
                                       id="multiselect"
                                      data-size="7" aria-setsize="2">
                                  </select>
                              <!-- </div> -->
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-3">
                                <div class="input-group ">
                                <label  for="jugada" class="bmd-label-floating font-weight-bold ml-4" style="color: black;">Moneda</label>
                                <!-- <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Tipo regla</label>                               -->
                                    <div  class=" col-sm-12 col-12">
                                        <select 
                                            ng-change="cbxMonedasChanged()"
                                            ng-model="selectedMoneda"
                                            ng-options="o.descripcion for o in optionsMonedas"
                                            class="selectpicker col-12" 
                                            data-style="select-with-transition" 
                                            title="Seleccionar moneda">
                                        </select>
                                    </div>
                                </div> <!-- END INPUT GROUP -->
                            </div>

                          <div class="col-2 text-center mt-4">
                          <style>
                                  .btn-outline-info.active2{
                                    background-color: #00bcd4!important;
                                    color: #fff!important;
                                  }

                                  .btn-group-toggle > .btn,
                                    .btn-group-toggle2 > .btn-group > .btn {
                                    margin-bottom: 0;
                                    }

                                    .btn-group-toggle2 > .btn input[type="radio"],
                                    .btn-group-toggle2 > .btn input[type="checkbox"],
                                    .btn-group-toggle2 > .btn-group > .btn input[type="radio"],
                                    .btn-group-toggle2 > .btn-group > .btn input[type="checkbox"] {
                                    position: absolute;
                                    clip: rect(0, 0, 0, 0);
                                    pointer-events: none;
                                    }

                                </style>
                                <div class="btn-group btn-group-sm  btn-group-not">
                                    <button 
                                    ng-click="seleccionarTodasLasBancas()" id="labelSeleccionarTodas"
                                    id="btnLoteria@{{$index}}"
                                    type="button" 
                                    class="btn btn-outline-info">Seleccionar todas</button>
                                    <!-- <button type="button" class="btn btn-outline-info">6</button>
                                    <button type="button" class="btn btn-outline-info">7</button> -->
                                </div>

                                      <!-- ng-init="rbxLoteriasChanged(l, $first)" -->
                               
                              </div><!-- END COL-12 -->


                      


                        <div class="form-group col-sm-3">
                            <input ng-click="buscar()" type="submit" class="btn btn-primary" value="Ver resumen de ventas">   
                        </div>
                        </div> <!-- END ROW -->
                    </div>

           

                    <div class="col-12">
                       <div class="row">
                       <div class="col-sm-3">
                            <div class="form-group">
                            <label for="numeroTicketBusqueda" class="bmd-label-floating">Filtrar</label>
                            <input ng-keyup="inputDescripcionBancaKeyUp()" ng-model="datos.fechaFiltro" id="numeroTicketBusqueda" type="text" class="form-control">
                            </div>
                        </div>
                       </div>
                        
                    </div>

                    <div class="col-12 t">
                    <table class="table table-sm table-striped" ng-init="mostrarVentanaTicket = false">
                        <thead>
                            <tr>
                            <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Fecha</th>
                            <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Ventas</th>
                            <!-- <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Cerrado</th> -->
                            <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Premios</th>
                            <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Comisiones</th>
                            <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Descuentos</th>
                            
                            <th scope="col" class="text-center font-weight-bold" style="font-size: 14px;">Neto</th>
                            


                            </tr>
                        </thead>
                        <tbody>
                            <tr   ng-repeat="c in datos.ventas | filter:greaterThan(datos.accionBusqueda)">
                                <td ng-click="seleccionarTicket(c)" scope="col" class="text-center" style="font-size: 13px">@{{c.fecha}}</td>
                                <td ng-click="seleccionarTicket(c)" scope="col" class="text-center" style="font-size: 13px">@{{c.ventas}}</td>
                                <!-- <td ng-click="seleccionarTicket(c)" scope="col" class="text-center" style="font-size: 13px">@{{Cerrado}}</td> -->
                                <td ng-click="seleccionarTicket(c)" scope="col" class="text-center" style="font-size: 13px">@{{c.premios}}</td>
                                <td ng-click="seleccionarTicket(c)" scope="col" class="text-center" style="font-size: 13px">@{{c.comisiones}}</td>
                                <td ng-click="seleccionarTicket(c)" scope="col" class="text-center" style="font-size: 13px">@{{c.descuentos}}</td>
                                <td ng-class="{'bg-bien text-bien': (c.totalNeto >= 0), 'bg-mal text-mal': (c.totalNeto < 0)}" ng-click="seleccionarTicket(c)" scope="col" class="text-center font-weight-bold" style="font-size: 13px">@{{c.totalNeto}}</td>
                            </tr>
                            <tr>
                                
                                <td class="text-center font-weight-bold">TOTAL:</td>
                                <td class="text-center font-weight-bold">@{{totalVentas | currency:monedaAbreviatura}}</td>
                                <td class="text-center font-weight-bold">@{{totalPremios | currency:monedaAbreviatura}}</td>
                                <td class="text-center font-weight-bold">@{{totalComisiones | currency:monedaAbreviatura}}</td>
                                <td class="text-center font-weight-bold">@{{totalDescuentos | currency:monedaAbreviatura}}</td>
                                <td class="text-center font-weight-bold">@{{totalNeto | currency:monedaAbreviatura}}</td>
                            </tr>
                            
                        </tbody>
                    </table>
                    </div>
                
            </div> <!-- END ROW PRINCIPAL CARD -->
          
        </div><!-- END CARD-BODY -->
      </div>
    </div>
  </div>



    <div class="panel panel-chat " ng-show="mostrarVentanaTicket">
        <div class="row">
            <div class="col-12">
                <div style="cursor:pointer" class="bg-success w-25 p-2 text-white text-center font-weight-bold" ng-click="mostrarVentanaTicket = !mostrarVentanaTicket">
                    Cerrar
                </div>
            </div>
            <div class="col-12">
            <div class="card my-0 mx-0 d-inline-block mx-0" style="min-height: 350px; max-height: 350px; width: 100%; background: #dddddd; overflow-y: auto;"> <!-- min-height: 455px; max-height: 455px; -->
                <div class="card-header card-header-info card-header-icon">
                
                  <!-- <h4 class="card-title">Pick4</h4> -->
                </div>
                <div class="card-body"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="table-responsive">
                  <h4 class="card-title text-center">Ticket:@{{datos.selectedTicket.codigo}}-@{{toSecuencia(datos.selectedTicket.idTicket)}}</h4>
                  <div class="row justify-content-end">
                    <div class="col-12 text-center">
                      <h3>Leyenda</h3>
                    </div>
                    <div class="col-3 bg-info text-center">
                      ganador
                    </div>
                    <div class="col-3 bg-rosa text-center">
                      perdedor
                    </div>
                    <div class="col-3 text-center">
                      pendiente
                    </div>
                  </div>

                  <div ng-repeat="l in datos.selectedTicket.loterias">
                  <!-- <div class='loterias col-xs-12 text-center mx-3' style='border-top-style: dashed; border-bottom-style: dashed; font-weight: lighter'> -->
                    <h5 style=' padding: 8px; width: 75%; margin: auto; '  class='text-center font-weight-bold py-1 mt-2 mb-0'>@{{l.descripcion}}</h5>
                  <!-- </div> -->

                    <table id="mytable" class="table table-sm">
                    <thead>
                        <tr>
                        <th class="text-center font-weight-bold" style="font-size: 12px">tipo sorteo</th>
                        <th class="text-center font-weight-bold" style="font-size: 12px">jugada</th>
                        <th class="text-center font-weight-bold " style="font-size: 12px">monto</th>
                        <th class="text-center font-weight-bold" style="font-size: 12px">premio</th>
                        <th class="text-center font-weight-bold" style="font-size: 12px">pagado</th>
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr class="font-weight-bold" ng-class="{'bg-rosa ': (c.status == 1 && c.premio <=0), 'bg-info': (c.status == 1 && c.premio >0)}" ng-repeat="c in datos.selectedTicket.jugadas ">
                        <td class="text-center" style="font-size: 12px;">@{{c.sorteo}}</td>
                        <td class="text-center" style="font-size: 12px;">
                            @{{agregar_guion(c.jugada, c.sorteo)}}
                            <!-- <small ng-if="c.sorteo == 'Pick 3 Box' || c.sorteo == 'Pick 4 Box'" class="text-danger font-weight-bold">B</small>
                            <small ng-if="c.sorteo == 'Pick 3 Straight' || c.sorteo == 'Pick 4 Straight'" class="text-primary font-weight-bold">S</small> -->
                        </td>
                        <td class="text-center" style="font-size: 12px;">
                            @{{c.monto}}
                        </td>
                        <td class="text-center" style="font-size: 12px;">
                            @{{c.premio}}
                        </td>
                        <td class="text-center" style="font-size: 12px;">
                            -
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

                    </div> <!-- END DIV NG-REPEAT LOTERIAS -->
                    
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
    </div>




    <div id="modal-cancelar-eliminar" class="modal fade modal-cancelar-eliminar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Razon</h3>
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
                            <div id="inputCodigoBarra" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Codigo</label>
                            <input disabled ng-model="datos.cancelar.codigoBarra" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating">Razon</label>
                            <input ng-model="datos.cancelar.razon" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group col-sm-3">
                            <input ng-click="cancelarEliminarDesdeModalRazon()" type="submit" class="btn btn-primary" value="Buscar">   
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



                  </div>

               
             </div>
          
        </div>
        
          
</div>

        
        















<!--   Core JS Files   -->
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





<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc --><script src="{{asset('assets/js/material-dashboard.min.js')}}" type="text/javascript"></script>
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
  });
</script>





    </body>

</html>


@endsection