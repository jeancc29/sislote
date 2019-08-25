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
                Prestamos
              </h3>
            </div>
            </div>
           
          </div>
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a ng-init="mostrarPagos = false" ng-click="mostrarPagos = false" class="nav-link" href="#about" data-toggle="tab" role="tab">
                  Agregar o editar
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
                  <div class="row">
                    

                  <div class="col-12">
                    <div class="row">
                    <div class="col-12">
                            <h3>Datos entidades</h3>
                          </div>
                    <div class="col-6 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black;">Prestar a entidad</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedBanca"
                                    ng-options="o.descripcion for o in datos.optionsBancas"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                    <div class="col-6 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black;">Emitir fondo desde</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedBanco"
                                    ng-options="o.nombre for o in datos.optionsBancos"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>
                    </div>
                  </div>
                  
                  <div class="col-12">
                    <div class="row">
                      <div class="col-12">
                        <h3>Datos prestamo</h3>
                      </div>

                      <div class="col-6  ">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Monto prestado</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-model="datos.abreviatura" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>


                              </div>

                              <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;"># Cuotas</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-model="datos.abreviatura" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                              <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Monto cuota</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-model="datos.abreviatura" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                                

                                <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Tasa interes</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-model="datos.abreviatura" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                             

                             

                                

                    </div> <!-- END ROW PRESTAMOS -->
                  </div> <!-- END COL-12 PRESTAMOS -->

                  <div class="col-12">
                    <div class="row">
                    <div class="col-12 ">
                        <h3>Datos frecuencia</h3>
                      </div>
                          <div class="text-center col-6" >
                                <div class="input-group">
                                  
                                  <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Frecuencia</label>                              

                                    <div  class=" col-sm-9 col-10">
                                    <select 
                                        ng-change="cbxTipoBloqueosJugadaChanged()"
                                        ng-model="datos.selectedFrecuencia"
                                        ng-options="o.descripcion for o in datos.optionsFrecuencias"
                                        class="selectpicker col-12" 
                                        data-style="select-with-transition" 
                                        title="Select tipo regla">
                                  </select>
                                  </div>
                                </div> <!-- END INPUT GROUP -->
                              </div>

                             

                              <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Fecha inicio</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-model="datos.fechaInicio" type="date" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                    </div>
                  </div>
                    

                       

                    </div>
                  </div>
                 

              

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