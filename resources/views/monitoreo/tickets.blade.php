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
            <h4 class="card-title">Monitoreo de tickets</h4>
          </div>
        </div>
        <div class="card-body ">

            <div class="row ">

                    <div class="col-12 ">
                        <div class="row">
                        <div class="col-sm-3 mt-4">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating font-weight-bold" style="color: black;">Fecha busqueda</label>
                            <input ng-model="datos.monitoreo.fecha" id="fechaBusqueda" type="date" class="form-control" required>
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
                                  id="multiselectDias"
                                  ng-model="datos.selectedBanca"
                                      ng-options="o.descripcion for o in datos.optionsBancas track by o.id"
                                      class="selectpicker w-100" 
                                      data-style="select-with-transition" 
                                       title="Seleccionar dias"
                                      data-size="7" aria-setsize="2">
                                  </select>
                              <!-- </div> -->
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-3 text-center">
                            <div class="input-group">
                            <label  for="jugada" class="bmd-label-floating font-weight-bold" style="color: black;">Loteria</label>
                              <!-- <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Dias</label>                               -->
                                <style>
                                  #multiselectDias{
                                    font-size: 8px;
                                  }
                                </style>
                                <!-- <div  class="col-9"> -->
                                  <select 
                                  id="multiselectDias"
                                  ng-model="datos.selectedLoteria"
                                      ng-options="o.descripcion for o in datos.optionsLoterias track by o.id"
                                      class="selectpicker w-100" 
                                      data-style="select-with-transition" 
                                       title="Seleccionar loteria"
                                      data-size="7" aria-setsize="2">
                                  </select>
                              <!-- </div> -->
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-3 text-center">
                            <div class="input-group">
                            <label  for="jugada" class="bmd-label-floating font-weight-bold" style="color: black;">Loteria</label>
                              <!-- <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Dias</label>                               -->
                                <style>
                                  #multiselectDias{
                                    font-size: 8px;
                                  }
                                </style>
                                <!-- <div  class="col-9"> -->
                                <select 
                                  id="multiselectDias"
                                  ng-model="datos.selectedSorteo"
                                      ng-options="o.descripcion for o in datos.optionsSorteos"
                                          ng-change="cbxBancasChange(o)"
                                          class="selectpicker w-100" 
                                          id="entidad1"
                                          data-style="select-with-transition" 
                                          title="Select sorteo">
                                  </select>
                              <!-- </div> -->
                            </div> <!-- END INPUT GROUP -->
                          </div>

                         

                          <div class="col-sm-3 mt-4">
                            <div id="fechaBusqueda" class="form-group">
                            <label for="fechaBusqueda" class="bmd-label-floating font-weight-bold" style="color: black;">Numero</label>
                            <input ng-model="datos.jugada" id="fechaBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group col-sm-3">
                            <input ng-click="buscar()" type="submit" class="btn btn-primary" value="Buscar">   
                        </div>
                        </div> <!-- END ROW -->
                    </div>

                    <div class="col-12">
                      <div class="row justify-content-first">
                          <div class="btn-group" role="group" aria-label="Basic example">
                              <button ng-click="buscarpor_ticket_estado(5)" type="button" class="btn btn-info">Todos <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_todos}}</span></button>
                              <button ng-click="buscarpor_ticket_estado(2)" type="button" class="btn btn-info">Ganadores <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_ganadores }}</span></button>
                              <button ng-click="buscarpor_ticket_estado(3)" type="button" class="btn btn-info">Perdedores <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_perdedores }}</span></button>
                              <button ng-click="buscarpor_ticket_estado(1)" type="button" class="btn btn-info">Pendientes <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_pendientes }}</span></button>
                              <button ng-click="buscarpor_ticket_estado(0)" type="button" class="btn btn-info">Cancelados <span class="bg-white rounded text-primary p-1">@{{datos.monitoreo.total_cancelados }}</span></button>

                          </div>
                      </div>
                    </div>

                    <div class="col-12">
                       <div class="row">
                       <div class="col-sm-3">
                            <div class="form-group">
                            <label for="numeroTicketBusqueda" class="bmd-label-floating">Numero ticket</label>
                            <input ng-keyup="buscarpor_ticket_estado(null)" ng-model="datos.monitoreo.idTicket" id="numeroTicketBusqueda" type="text" class="form-control" required>
                            </div>
                        </div>
                       </div>
                        
                    </div>

                    <div class="col-6 t">
                    <table class="table table-sm" ng-init="mostrarVentanaTicket = false">
                        <thead>
                            <tr>
                            <th scope="col" class="text-center" style="font-size: 12px;">Numero</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Creado</th>
                            <!-- <th scope="col" class="text-center" style="font-size: 12px;">Cerrado</th> -->
                            <th scope="col" class="text-center" style="font-size: 12px;">Usuario</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Monto</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Premio</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Cancelado por</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Fecha cancelado</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Estado</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Marcar pago</th>
                            <!-- <th scope="col" class="text-center" style="font-size: 12px;">Marcar pago</th> -->
                            <th scope="col" class="text-center" style="font-size: 12px;">Imprimir</th>

                            <!--<th scope="col" class="text-center">Cancelar/Eliminar</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-click="seleccionarTicket(c)"  ng-repeat="c in datos.monitoreo.ventas | filter:datos.monitoreo.datosBusqueda">
                                <td scope="col" class="text-center" style="font-size: 14px">@{{c.codigo}}-@{{toSecuencia(c.idTicket)}}</td>
                                <td scope="col" class="text-center">@{{toFecha(c.created_at.date) | date:"dd/MM/yyyy hh:mm a"}}</td>
                                <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                <td scope="col" class="text-center">@{{c.usuario}}</td>
                                <td scope="col" class="text-center">@{{c.subTotal}}</td>
                                <td scope="col" class="text-center">@{{c.premio}}</td>
                                <td scope="col" class="text-center">@{{c.razon}}</td>
                                <td scope="col" class="text-center">@{{toFecha(c.fechaCancelacion.date) | date:"dd/MM/yyyy hh:mm a"}}</td>
                                <td scope="col" class="text-center">@{{estado(c.status)}}</td>
                                <td scope="col" class="text-center">@{{(c.pagado == 1) ? 'si' : 'no'}}</td>
                                <!-- <td scope="col" class="text-center">Marcar pago</td> -->
                                <td scope="col" class="text-center">
                                    <a ng-click="imprimirTicket(c)" href="javascript:void(0)" class="btn btn-outline-primary px-1 py-1"><i class="material-icons">print</i></a>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                    </div>
                
            </div> <!-- END ROW PRINCIPAL CARD -->
          
        </div><!-- END CARD-BODY -->
      </div>
    </div>
  </div>

  <style>
  .panel.panel-chat
        {
            position: fixed;
            bottom:0;
            right:0;
            max-width: 470px;
            width: 500px;
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
    </style>

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
                  <h4 class="card-title text-center">Pick4</h4>

                  <div ng-repeat="l in datos.selectedTicket.loterias">
                  <!-- <div class='loterias col-xs-12 text-center mx-3' style='border-top-style: dashed; border-bottom-style: dashed; font-weight: lighter'> -->
                    <h5 style=' padding: 8px; width: 75%; margin: auto; '  class='text-center font-weight-bold py-1 mt-2 mb-0'>@{{l.descripcion}}</h5>
                  <!-- </div> -->

                    <table id="mytable" class="table table-sm">
                    <thead>
                        <tr>
                        <th class="font-weight-bold" style="font-size: 12px">tipo sorteo</th>
                        <th class="font-weight-bold" style="font-size: 12px">jugada</th>
                        <th class="text-center font-weight-bold" style="font-size: 12px">monto</th>
                        <th class="text-center font-weight-bold" style="font-size: 12px">premio</th>
                        <th class="text-center font-weight-bold" style="font-size: 12px">pagado</th>
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                        <tr class="font-weight-bold" ng-class="{'bg-rosa ': (c.status == 1 && c.premio <=0), 'bg-info': (c.status == 1 && c.premio >0)}" ng-repeat="c in datos.selectedTicket.jugadas ">
                        <td class="" style="font-size: 12px;">@{{c.sorteo}}</td>
                        <td class="" style="font-size: 12px;">
                            @{{agregar_guion(c.jugada, c.sorteo)}}
                            <small ng-if="c.sorteo == 'Pick 3 Box' || c.sorteo == 'Pick 4 Box'" class="text-danger font-weight-bold">B</small>
                            <small ng-if="c.sorteo == 'Pick 3 Straight' || c.sorteo == 'Pick 4 Straight'" class="text-primary font-weight-bold">S</small>
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






                  </div>

               
             </div>
          
        </div>
        
            <div class="fixed-plugin">
    <div class="dropdown show-dropdown">
        <a href="#" data-toggle="dropdown">
        <i class="fa fa-cog fa-2x"> </i>
        </a>
        <ul class="dropdown-menu">
			<li class="header-title"> Sidebar Filters</li>
            <li class="adjustments-line">
                <a href="javascript:void(0)" class="switch-trigger active-color">
                  <div class="badge-colors ml-auto mr-auto">
                    <span class="badge filter badge-purple" data-color="purple"></span>
                    <span class="badge filter badge-azure" data-color="azure"></span>
                    <span class="badge filter badge-green" data-color="green"></span>
                    <span class="badge filter badge-warning" data-color="orange"></span>
                    <span class="badge filter badge-danger" data-color="danger"></span>
                    <span class="badge filter badge-rose active" data-color="rose"></span>
                  </div>
                  <div class="clearfix"></div>
                </a>
            </li>

            
            <li class="header-title">Sidebar Background</li>
              <li class="adjustments-line">
                  <a href="javascript:void(0)" class="switch-trigger background-color">
                      <div class="ml-auto mr-auto">
                        <span class="badge filter badge-black active" data-background-color="black"></span>
                        <span class="badge filter badge-white" data-background-color="white"></span>
                        <span class="badge filter badge-red" data-background-color="red"></span>
                      </div>
                      <div class="clearfix"></div>
                  </a>
              </li>

              <li class="adjustments-line">
                  <a href="javascript:void(0)" class="switch-trigger">
                      <p>Sidebar Mini</p>
                      <label class="ml-auto">
                        <div class="togglebutton switch-sidebar-mini">
                          <label>
                            <input type="checkbox">
                            <span class="toggle"></span>
                          </label>
                        </div>
                      </label>
                      <div class="clearfix"></div>
                  </a>
              </li>

              <li class="adjustments-line">
                  <a href="javascript:void(0)" class="switch-trigger">
                      <p>Sidebar Images</p>
                      <label class="switch-mini ml-auto">
                        <div class="togglebutton switch-sidebar-image">
                          <label>
                            <input type="checkbox" checked="">
                            <span class="toggle"></span>
                          </label>
                        </div>
                      </label>
                      <div class="clearfix"></div>
                  </a>
              </li>

              <li class="header-title">Images</li>

              <li class="active">
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                  <img src="../../assets/img/sidebar-1.jpg" alt="">
                </a>
              </li>
              <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                  <img src="../../assets/img/sidebar-2.jpg" alt="">
                </a>
              </li>
              <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                  <img src="../../assets/img/sidebar-3.jpg" alt="">
                </a>
              </li>
              <li>
                <a class="img-holder switch-trigger" href="javascript:void(0)">
                  <img src="../../assets/img/sidebar-4.jpg" alt="">
                </a>
              </li>


            <li class="button-container">
              <a href="https://www.creative-tim.com/product/material-dashboard-pro" target="_blank" class="btn btn-rose btn-block btn-fill">Buy Now</a>
              <a href="https://demos.creative-tim.com/material-dashboard-pro/docs/2.0/getting-started/introduction.html" target="_blank" class="btn btn-default btn-block">
                  Documentation
              </a>
              <a href="https://www.creative-tim.com/product/material-dashboard" target="_blank" class="btn btn-info btn-block">
                  Get Free Demo!
              </a>
            </li>
            

            
            <li class="button-container github-star">
                <a class="github-button" href="https://github.com/creativetimofficial/ct-material-dashboard-pro" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star ntkme/github-buttons on GitHub">Star</a>
            </li>
            <li class="header-title">Thank you for 95 shares!</li>

            <li class="button-container text-center">
                <button id="twitter" class="btn btn-round btn-twitter"><i class="fa fa-twitter"></i> &middot; 45</button>
                <button id="facebook" class="btn btn-round btn-facebook"><i class="fa fa-facebook-f"></i> &middot; 50</button>
                <br>
                <br>
            </li>
        </ul>
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