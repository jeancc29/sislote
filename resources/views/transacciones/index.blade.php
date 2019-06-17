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
            <h4 class="card-title">Lista de grupo de transacciones</h4>
          </div>
        </div>
        <div class="card-body ">

            <div class="row justify-content-center">
                
                <div class="col-12">

                    <div class="row justify-content-center">
                        <div class="col-4">
                                  <div id="divInputFechaDesde" class="form-group">
                                      <label  for="jugada" class="bmd-label-floating">Fecha inicio</label>
                                      <input ng-model="datos.fechaDesde" id="fechaDesde" type="date" class="form-control" value="10/06/2018" required>
                                  </div>
                              </div>

                              <div class="col-4">
                                  <div id="divInputFechaHasta" class="form-group">
                                      <label for="jugada" class="bmd-label-floating">Fecha fin</label>
                                      <input ng-model="datos.fechaHasta"  id="fechaHasta" type="date" class="form-control" value="10/06/2018" required>
                                  </div>
                              </div>
                              
                    </div> <!-- END ROW FECHAS -->
                    <div class="row">
                        <label class="d-none d-sm-block col-sm-2 col-form-label">Tipo entidad</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select 
                                        ng-model="datos.selectedTipoEntidad"
                                        ng-options="o.descripcion for o in datos.optionsTipoEntidad"
                                        ng-change="cbxTipoEntidadChange(o)"
                                        class="selectpicker w-100" 
                                        id="tipoEntidad"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                            </div>
                        </div>
                    </div> <!-- END ROW -->
                    <div class="row">
                        <label class="d-none d-sm-block col-sm-2 col-form-label">Entidad</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select 
                                        ng-model="datos.selectedEntidad"
                                        ng-options="o.descripcion for o in datos.optionsEntidades"
                                        ng-change="cbxEntidadesChange(o)"
                                        class="selectpicker w-100" 
                                        id="entidades"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                            </div>
                        </div>
                    </div> <!-- END ROW -->
                    <div class="row">
                        <label class="d-none d-sm-block col-sm-2 col-form-label">Concepto</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select 
                                        ng-model="datos.selectedTipo"
                                        ng-options="o.descripcion for o in datos.optionsTipos"
                                        ng-change="cbxEntidadesChange(o)"
                                        class="selectpicker w-100" 
                                        id="tipos"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                            </div>
                        </div>
                    </div> <!-- END ROW -->
                    <div class="row">
                        <label class="d-none d-sm-block col-sm-2 col-form-label">Creado por</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select 
                                        ng-model="datos.selectedUsuario"
                                        ng-options="o.usuario for o in datos.optionsUsuarios"
                                        class="selectpicker w-100" 
                                        id="usuarios"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                            </div>
                        </div>
                    </div> <!-- END ROW -->

                   


                    <div class="row">
                        <div class="col-6 text-center">
                            <button ng-click="buscar()" class="btn btn-success btn-large">Buscar</button>
                        </div>
                    </div>
                    
                    <table class="table table-sm">
                        <thead>
                            <tr>
                            <th scope="col" class="text-center" style="font-size: 12px;">Tipo</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Entidad #1</th>
                            <!-- <th scope="col" class="text-center" style="font-size: 12px;">Cerrado</th> -->
                            <th scope="col" class="text-center" style="font-size: 12px;">Entidad #2</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Saldo inicial entidad #1</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Saldo inicial entidad #2</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Debito</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Credito</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Saldo final entidad #1</th>
                            <!-- <th scope="col" class="text-center" style="font-size: 12px;">Marcar pago</th> -->
                            <th scope="col" class="text-center" style="font-size: 12px;">Saldo final entidad #2</th>
                            <th scope="col" class="text-center" style="font-size: 12px;">Notas</th>

                            <!--<th scope="col" class="text-center">Cancelar/Eliminar</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="c in datos.transacciones">
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.tipo.descripcion}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.entidad1.descripcion}}</td>
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">@{{Cerrado}}</td> -->
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.entidad2.nombre}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.entidad1_saldo_inicial}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.entidad2_saldo_inicial}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.debito}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.credito}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.entidad1_saldo_final}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px">@{{c.entidad2_saldo_final}}</td>
                                <td scope="col" class="text-center" style="font-size: 12px"> @{{c.nota}}</td>
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">Marcar pago</td> -->
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">
                                    <a style="cursor: pointer" ng-click="eliminar(l)" class="ion-android-delete d-inline   bg-danger  text-white rounded"><i class="material-icons">delete_forever</i></a>
                                </td> -->
                            </tr>
                            <tr >
                                <td colspan="4"></td>
                                <td class="text-center font-weight-bold">TOTAL:</td>
                                <td class="text-center font-weight-bold">@{{total(true) | currency}}</td>
                                <td class="text-center font-weight-bold">@{{total(false) | currency}}</td>
                                <td colspan="4"></td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div><!-- END COL-12 -->
            </div> <!-- END ROW PRINCIPAL -->
          
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