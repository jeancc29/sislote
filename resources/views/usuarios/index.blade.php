@extends('header')

@section('content')



          
    


            <div class="main-panel" ng-init="load('{{ session('idUsuario')}}')">
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

             <div class="col-6 col-sm-3">
              <h3 class="card-title">
                Usuarios
              </h3>
            </div>

            <div class="col-sm-3 text-right">
            <div class="">
              <input ng-click="actualizar()" type="button" class="btn btn-info " name="guardar" value="Guardar">
            </div>
            </div>

            </div>
           
          </div>
          <!-- <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a ng-init="mostrarPagos = false" ng-click="mostrarPagos = false" class="nav-link" href="#about" data-toggle="tab" role="tab">
                  Datos
                </a>
              </li>
              <li class="nav-item">
                <a ng-click="mostrarPagos = true" class="nav-link" href="#account" data-toggle="tab" role="tab">
                  Permisos Adicionales
                </a>
              </li>
            </ul>
          </div> -->
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
                        <label for="nombre" class="bmd-label-floating">Nombre</label>
                        <input ng-model="datos.nombres" class="form-control" id="nombre" name="nombre">
                      </div>
                    </div>

                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">record_voice_over</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="codigo" class="bmd-label-floating">Email</label>
                        <input ng-model="datos.email" type="email" class="form-control" id="email" name="email">
                      </div>
                    </div>

                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">record_voice_over</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="codigo" class="bmd-label-floating">Usuario</label>
                        <input ng-model="datos.usuario" type="text" class="form-control" id="usuario" name="usuario">
                      </div>
                    </div>

                     <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">record_voice_over</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="codigo" class="bmd-label-floating">Contraseña</label>
                        <input ng-model="datos.password" type="password" class="form-control" id="password" name="password">
                      </div>
                    </div>

                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">record_voice_over</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="codigo" class="bmd-label-floating">Confirmar contraseña</label>
                        <input ng-model="datos.confirmar" type="password" class="form-control" id="confirmar" name="confirmar">
                      </div>
                    </div>

             

                


                     <div class="row">
                      
                      <!-- <div class="col-9">

                          <div class="input-group form-control-lg">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="material-icons">av_timer</i>
                            </span>
                          </div>
                          <div class="form-group">
                            <label for="clave" class="bmd-label-floating">Clave</label>
                            <input ng-model="datos.clave" id="clave" type="text" class="form-control">
                          </div>
                        </div>

                      </div> -->

                      <div class="col-9">
                        <div class="row">
                          <label class="d-none d-sm-block col-sm-3 col-md-2 text-right col-form-label mt-3 font-weight-bold" style="font-size: 16px;">Role: </label>
                          <div class="col-5 col-md-9">
                            <div class="form-group">
                                <style>
                                .dropdown-menu{
                                    width: 100%;
                                }
                            </style>
                              
                                  <select 
                                      id="cbxUsuariosTipos"
                                      ng-model="datos.selectedUsuariosTipos"
                                      ng-change="cbxUsuariosTipos_changed()"
                                      ng-options="o.descripcion for o in datos.optionsUsuariosTipos track by o.id"
                                      class="selectpicker w-100" 
                                      data-style="btn btn-secundary btn-round" 
                                      title="Seleccionar usuarios tipos">
                                  </select>
                            </div>
                          </div>
                        </div> <!-- END ROW -->
                      </div> <!-- END COL-9 -->

                      <div class="col-3">
                        <div class="input-group form-control-lg">
                            <div class="form-group">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input ng-model="datos.estado" class="form-check-input" type="checkbox" value="" checked> Activa
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </div>
                          </div>
                      </div>

                    </div>


                    <div class="row justify-content-center">

                      <div class="col-12 text-center mt-2">
                        <h2>Permisos</h2>
                        <hr>
                      </div>

                      <div class="col-sm-12 checkbox-radios">
                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Usuarios</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 1}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->
                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Tickets</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 2}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->
                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Bancas</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 3}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->
                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Jugar</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 4}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->
                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Ventas</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 5}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->
                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Acceso al sistema</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 6}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->

                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Transacciones</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 7}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->

                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Balances</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 8}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->

                        <div class="row">
                          <div class="col-12">
                            <h3 class="">Otros</h3>
                          </div>
                          <div ng-repeat="d in datos.ckbPermisos | filter: {idTipo: 9}" class="form-check form-check-inline">
                            <label class="form-check-label">
                              <input ng-model="d.existe" ng-change="ckbPermisos_changed(ckbDias, d)" class="form-check-input" type="checkbox" value=""> @{{d.descripcion}}
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                        </div> <!-- END ROW -->

                      </div> <!-- END COL-12 -->
                     </div> <!-- END ROW ROLE -->





                      <!-- <div class="row mt-2">
                        <label class="d-none d-sm-block col-sm-3 col-md-2 text-right col-form-label mt-3 font-weight-bold" style="font-size: 16px;">Role: </label>
                        <div class="col-5 col-md-9">
                          <div class="form-group">
                              <style>
                              .dropdown-menu{
                                  width: 100%;
                              }
                          </style>
                            
                                <select 
                                    id="cbxUsuariosTipos"
                                    ng-model="datos.selectedUsuariosTipos"
                                    ng-options="o.descripcion for o in datos.optionsUsuariosTipos track by o.idTipoUsuario"
                                    class="selectpicker w-100" 
                                    data-style="btn btn-secundary btn-round" 
                                    title="Seleccionar loterias">
                                </select>
                          </div>
                        </div>
                      </div> END ROW  -->


                      

                  </div> <!-- END DIV FORMULARIO -->
                  


                </form>
                 
                </div> <!-- END ROW PRINCIPAL -->
              </div>  <!-- END TAB 1 -->
             
              <div class="tab-pane" id="account">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                   
                   <form novalidate>

                    <div class="col-12">
                    
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
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Tipo</th>
                <th scope="col">Editar</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="l in datos.usuarios">
                <th scope="row">@{{$index + 1}}</th>
                <td>@{{l.nombres}}</td>
                <td>@{{l.tipoUsuario}}</td>
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





<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc --><script src="{{asset('assets/js/material-dashboard.js')}}" type="text/javascript"></script>
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="{{asset('assets/demo/demo.js')}}"></script>

<script src="{{asset('assets/js/jquery.fileDownload.js')}}" ></script>































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