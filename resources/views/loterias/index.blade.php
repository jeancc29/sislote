@extends('header')

@section('content')
          
    


            <div class="main-panel" ng-init="load('{{session('idUsuario')}}')">
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
                      













<div class="container-fluid">
  
  <div class="col-md-12 col-12 mr-auto ml-auto">


   <!--      Wizard container        -->
    <div ng-show="datos.mostrarFormEditar" class="wizard-container">
      <div class="card card-wizard" data-color="blue" id="wizardProfile">
        <form novalidate>
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header">
            <div class="row">
            <div class="col-5">
              <button ng-click="datos.mostrarFormEditar = !datos.mostrarFormEditar" class="btn btn-just-icon btn-success btn-fab btn-round">
                    <i class="material-icons text_align-center">arrow_back</i>
              </button>
            </div>

             <div class="col-6">
              <h3 class="card-title">
                Bloqueos
              </h3>
            </div>
            </div>
           
          </div>
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a ng-init="mostrarPagos = false" ng-click="mostrarPagos = false" class="nav-link" href="#about" data-toggle="tab" role="tab">
                  Loteria
                </a>
              </li>
              <!-- <li class="nav-item">
                <a ng-click="mostrarPagos = true" class="nav-link" href="#account" data-toggle="tab" role="tab">
                  Jugadas
                </a>
              </li> -->
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="about">
                <!-- <h5 class="info-text"> Let's start with the basic information (with validation)</h5> -->
                <div class="row justify-content-center">
  
                <form novalidate>

                  <div class="col-12 col-sm-10">
                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">face</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="descripcion" class="bmd-label-floating">Nombre loteria</label>
                        <input ng-model="datos.descripcion" class="form-control" id="descripcion" name="descripcion">
                      </div>
                    </div>
                    <!-- <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">record_voice_over</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="abreviatura" class="bmd-label-floating">Abreviatura</label>
                        <input ng-model="datos.abreviatura" type="text" class="form-control" id="abreviatura" name="abreviatura">
                      </div>
                    </div> -->


                    <div class="row">
                      
                      <div class="col-7">
                        <div class="input-group form-control-lg">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="material-icons">record_voice_over</i>
                          </span>
                        </div>
                        <div class="form-group">
                          <label for="abreviatura" class="bmd-label-floating">Abreviatura</label>
                          <input ng-model="datos.abreviatura" type="text" class="form-control" id="abreviatura" name="abreviatura">
                        </div>
                      </div>

                          <!-- <div class="input-group form-control-lg">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="material-icons">av_timer</i>
                            </span>
                          </div>
                          <div class="form-group">
                            <label for="exampleInput1" class="bmd-label-floating">Hora de cierre</label>
                            <input ng-model="datos.horaCierre" id="horaCierre" type="text" class="form-control timepicker" value="10/05/2016" required>
                          </div>
                        </div> -->

                      </div>

                      <div class="col-3">
                        <div class="input-group form-control-lg">
                            <div class="form-group">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input ng-model="datos.status" class="form-check-input" type="checkbox" value="" checked> Activa
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </div>
                          </div>
                      </div>

                    </div>


                  </div>
                  <!-- END DIV FORMULARIO -->

                  
                  <!-- <div class="row justify-content-center">

                      <div class="col-12 text-center">
                        <h2>Dias</h2>
                      </div>

                      <div class="col-sm-12 checkbox-radios">

                        
                        <div ng-repeat="d in datos.ckbDias" class="form-check form-check-inline">
                          <label class="form-check-label">
                            <input ng-model="d.existe" ng-change="ckbDias_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                            <span class="form-check-sign">
                              <span class="check"></span>
                            </span>
                          </label>
                        </div>

                      

                      </div>
                </div> -->

                <div class="row justify-content-center">

                    <div class="col-12 text-center">
                      <h2>Jugadas</h2>
                    </div>

                    <div class="col-sm-12 checkbox-radios">

                      
                      <div ng-repeat="d in datos.ckbSorteos" class="form-check form-check-inline">
                        <label class="form-check-label">
                          <input ng-model="d.existe" ng-change="ckbSorteos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                          <span class="form-check-sign">
                            <span class="check"></span>
                          </span>
                        </label>
                      </div>



                    </div>
                </div>



                <div class="col-12">
                      <div class="row  mt-2">
                          <!-- <div class="col-12 col-sm-3 text-right mt-3">
                            <h5>relacionadas:</h5>
                          </div> -->
                          <div class="col-12 text-center">
                            <h2>Relacionadas</h2>
                          </div>
                          <div class="col-12 col-sm-8 text-center">
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
                                <div class="btn-group btn-group-sm">
                                    <button 
                                    ng-repeat="l in datos.loterias"
                                    ng-class="{'active2': l.seleccionada == 'true'}"
                                    ng-click="rbxLoteriasChanged(l, $index)"
                                    id="btnLoteria@{{$index}}"
                                    type="button" 
                                    class="btn btn-outline-info">@{{l.descripcion}}</button>
                                    <!-- <button type="button" class="btn btn-outline-info">6</button>
                                    <button type="button" class="btn btn-outline-info">7</button> -->
                                </div>
                                      <!-- ng-init="rbxLoteriasChanged(l, $first)" -->
                                
                              </div><!-- END COL-12 -->
                        </div> <!-- END ROW LOTERIAS -->
                </div><!-- END COL-12 LOTERIAS -->


                </form>
                 
                </div>
                <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 1 -->

              <div class="tab-pane" id="account">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                   
                   <form novalidate>

                  <div class="col-12">

                    <div class="row">
                    <div class="col-12 text-center font-weight-bold">
                        Directo
                      </div>
                    <div class="col-sm-4 input-group form-control-lg">
                      <div class="form-group">
                        <label for="Primera" class="bmd-label-floating">Primera</label>
                        <input ng-model="datos.primera" class="form-control" id="Primera" name="Primera">
                      </div>
                    </div>

                    <div class="col-sm-4 input-group form-control-lg">
                      <div class="form-group">
                        <label for="Segunda" class="bmd-label-floating">Segunda</label>
                        <input ng-model="datos.segunda" type="text" class="form-control" id="Segunda" name="Segunda">
                      </div>
                    </div>

                    <div class="col-sm-4 input-group form-control-lg">
                      <div class="form-group">
                        <label for="Tercera" class="bmd-label-floating">Tercera</label>
                        <input ng-model="datos.tercera" type="text" class="form-control" id="Tercera" name="Tercera">
                      </div>
                    </div>

                    </div>
                    <!-- END ROW QUINIELA O DIRECTO -->
                    

                    <div class="row">
                    <div class="col-12 text-center font-weight-bold">
                        Pale
                      </div>
                    <div class="col-sm-4 input-group form-control-lg">
                      <div class="form-group">
                        <label for="primeraSegunda" class="bmd-label-floating">Primera segunda</label>
                        <input ng-model="datos.primeraSegunda" type="text" class="form-control" id="primeraSegunda" name="primeraSegunda">
                      </div>
                    </div>

                     <div class="col-sm-4 input-group form-control-lg">
                      <div class="form-group">
                        <label for="primeraTercera" class="bmd-label-floating">@primeraTercera</label>
                        <input ng-model="datos.primeraTercera" type="text" class="form-control" id="primeraTercera" name="primeraTercera">
                      </div>
                    </div>

                    <div class="col-sm-4 input-group form-control-lg">
                      <div class="form-group">
                        <label for="segundaTercera" class="bmd-label-floating">segundaTercera</label>
                        <input ng-model="datos.segundaTercera" type="text" class="form-control" id="segundaTercera" name="segundaTercera">
                      </div>
                    </div>

                    </div>
                    <!-- END ROW PALE -->

                     <div class="row">
                      <div class="col-12 text-center font-weight-bold">
                        Tripleta
                      </div>
                     <div class="col-6 input-group form-control-lg">
                      <div class="form-group">
                        <label for="tresNumeros" class="bmd-label-floating">@tresNumeros</label>
                        <input ng-model="datos.tresNumeros" type="text" class="form-control" id="tresNumeros" name="tresNumeros">
                      </div>
                    </div>

                    <div class="col-6 input-group form-control-lg">
                      <div class="form-group">
                        <label for="dosNumeros" class="bmd-label-floating">@dosNumeros</label>
                        <input ng-model="datos.dosNumeros" type="text" class="form-control" id="dosNumeros" name="dosNumeros">
                      </div>
                    </div>

                     </div>
                    <!-- END ROW TRIPLETA -->



                  </div>
                  <!-- END DIV FORMULARIO -->

                  
                 


                </form>
                    
                    
                   


                     
                      
                    </div>
                  </div>
                </div>
              </div>
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
          <div class="card-footer">
            <div ng-show="!mostrarBloqueosJugadas" class="row justify-content-end w-100">
              <input ng-click="actualizar()" type="button" class="btn btn-info " name="guardar" value="Guardar">
            </div>
            <!-- <div class="mr-auto">
              <input type="button" class="btn btn-previous btn-fill btn-default btn-wd disabled" name="previous" value="Previous">
            </div>
            <div class="ml-auto">
              <input type="button" class="btn btn-next btn-fill btn-rose btn-wd" name="next" value="Next">
              <input type="button" class="btn btn-finish btn-fill btn-rose btn-wd" name="finish" value="Finish" style="display: none;">
            </div>
            <div class="clearfix"></div> -->
          </div>
        </form>
      </div>
    </div>
    <!-- wizard container -->










<!-- TODAS LAS LOTERIAS -->
<div ng-show="datos.mostrarFormEditar == false" class="row justify-content-center">
  <div class="col-md-12">
      <div class="card ">
        <div class="card-header card-header-info card-header-text">
          <div class="card-text">
            <h4 class="card-title">Todas</h4>
          </div>
        </div>
        <div class="card-body ">
          <div class="row justify-content-end">
            <!-- .abrir-wizard la uso en el archivo demo.js para obtener los datos reales del wizard al momento de quitarle el display none -->
            <a ng-click="editar(true, {})" id="abrir-wizard-nuevo" class="btn btn-success text-white">Nueva loteria</a>
          </div>
          <table class="table table-sm">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Loteria</th>
                <th scope="col">Abreviatura</th>
                <!-- <th scope="col">Hora cierre</th> -->
                <th scope="col">Editar</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="l in datos.loterias">
                <th scope="row">@{{$index + 1}}</th>
                <td>@{{l.descripcion}}</td>
                <td>@{{l.abreviatura}}</td>
                <!-- <td>@{{l.horaCierre}}</td> -->
                <td>
                  <a style="cursor: pointer" ng-click="editar(false, l)" class="ion-edit d-inline bg-primary py-1 text-white rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                  <a style="cursor: pointer" ng-click="eliminar(l)" class="ion-android-delete d-inline  ml-2 bg-danger py-1 text-white rounded"><i class="material-icons">delete_forever</i></a>
                </td>
              </tr>
             
            </tbody>
          </table>
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