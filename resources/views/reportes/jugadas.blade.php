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
      <div class="card ">
        <div class="card-header card-header-info card-header-text">
          <div class="card-text">
            <h4 class="card-title">Reporte jugadas</h4>
          </div>
        </div>
        <div class="card-body ">

            <div class="row justify-content-center">

                <div  class="col-10 mb-2">
                            <select 
                            id="multiselect"
                                ng-change="calcularTotal()"
                                ng-model="datos.bancas"
                                ng-options="o.descripcion for o in datos.optionsBancas track by o.id"
                                class="selectpicker col-12" 
                                data-style="select-with-transition" 
                                multiple title="Seleccionar bancas"
                                data-size="7" aria-setsize="2">
                            </select>
                    </div>
                   
                <div class="col-5">
                   <style>
                       .dropdown-menu{
                           width: 100%;
                       }
                   </style>

                     <!-- <select 
                         ng-change="cbxLoteriasChanged()"
                         ng-model="datos.selectedLoteria"
                         ng-options="o.descripcion for o in datos.optionsLoterias"
                         class="selectpicker w-100" 
                         id="cbxLoteriasBuscarJugada"
                          data-style="btn btn-secundary btn-round" 
                         title="Seleccionar loterias">
                     </select> -->

                      <div class="form-group m-0 p-0">
                        <select 
                            ng-model="datos.selectedLoteria" 
                            ng-options="o.descripcion for o in datos.optionsLoterias"
                            class="selectpicker w-100 m-0 p-0" 
                            data-style="select-with-transition" 
                            title="Select loteria">
                        </select>
                    </div>
                   </div>
                 
                     <div class="col-sm-2">
                         <div id="fechaBusqueda" class="form-group">
                         <label for="fechaBusqueda" class="bmd-label-floating">Fecha busqueda</label>
                         <input ng-model="datos.fecha" id="fechaBusqueda" type="date" class="form-control" required>
                         </div>
                     </div>
                     <div class="form-group col-sm-3">
                         <input ng-click="buscar_jugadas()" type="submit" class="btn btn-primary" value="Buscar">   
                     </div>
                 </div>

                 
                <div class="row justify-content-center">
                    <div ng-show="datos.monto_total > 0" class="col-12 text-center">
                        <h2>Total loteria @{{datos.selectedLoteria.descripcion}}: @{{datos.monto_total | currency}}</h2>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-3">
                        <h3 class="text-center">DIRECTO</h3>
                         <div class="row justify-content-center">
                            <div class="table-responsive">
                                <table class="table table-fixed">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-6 col-sm-6 text-center" style="font-size: 12px">JUGADA</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 12px">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.jugadas | filter: {tam: 2}">
                                    <td class="col-6 col-sm-5 text-center">@{{c.jugada}}</td>
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
                         </div> <!-- END ROW CONTENEDOR TABLA -->
                         <h4 class="text-right">Total: @{{datos.total_directo | currency}}</h4>
                    </div> <!-- COL-3 -->

                    <div class="col-12 col-sm-4 col-lg-3">
                        <h3 class="text-center">PALE</h3>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-fixed">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-6 col-sm-6 text-center" style="font-size: 12px">JUGADA</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 12px">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.jugadas | filter: {tam: 4}">
                                    <td class="col-6 col-sm-5 text-center">@{{agregar_guion(c.jugada)}}</td>
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
                        </div><!-- END ROW CONTENEDOR TABLA -->
                         <h4 class="text-right">Total: @{{datos.total_palet | currency}}</h4>
                    </div> <!-- COL-3 -->


                    <div class="col-12 col-sm-4 col-lg-3">
                        <h3 class="text-center">TRIPLETA</h3>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-fixed">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-6 col-sm-6 text-center" style="font-size: 12px">JUGADA</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 12px">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.jugadas | filter: {tam: 6}">
                                    <td class="col-6 col-sm-5 text-center">@{{agregar_guion(c.jugada)}}</td>
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
                        </div><!-- END ROW CONTENEDOR TABLA -->
                         

                         <h4 class="text-right">Total: @{{datos.total_tripleta | currency}}</h4>
                    </div> <!-- COL-3 -->
                </div>
          
        </div><!-- END CARD-BODY -->
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