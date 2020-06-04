@extends('header')

@section('content')
          
    


            <div class="main-panel" ng-init="inicializarDatos('null')">
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
  
  <div class="col-sm-10 col-12 mr-auto ml-auto">


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
                Monedas
              </h3>
            </div>
            </div>
           
          </div>
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a ng-init="mostrarPagos = false" ng-click="mostrarPagos = false" class="nav-link" href="#about" data-toggle="tab" role="tab">
                  Crear | Editar
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
                  <div class="col-12 col-md-10 mt-5">
                    <div class="row">
                      
                        <div class="col-12">
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="material-icons">face</i>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion" class="bmd-label-floating">Moneda</label>
                                    <input ng-model="datos.descripcion" class="form-control" id="descripcion" name="descripcion">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="material-icons">face</i>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion" class="bmd-label-floating">Abreviatura</label>
                                    <input ng-model="datos.abreviatura" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="material-icons">face</i>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion" class="bmd-label-floating">Equivalencia de un dolar</label>
                                    <input type="number" ng-model="datos.equivalenciaDeUnDolar" class="form-control" id="equivalenciaDeUnDolar" name="equivalenciaDeUnDolar">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group form-control-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                    <i class="material-icons">face</i>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion" class="bmd-label-floating font-weight-bold" style="color: black;">Color</label>
                                    <input type="color" ng-model="datos.color" class="form-control" id="color" name="color">
                                </div>
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="input-group form-control-lg">
                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input ng-model="datos.permiteDecimales" class="form-check-input" type="checkbox" value="" checked> Permite decimales
                                            <span class="form-check-sign">
                                                <span class="check"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                          </div>
                        </div>

                    </div> <!-- END ROW DESCRIPCION Y ESTATUS -->

                  </div>
                </form>
                 
                </div>
                <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 1 -->
            </div>
          </div>
          <div class="card-footer">
            <div ng-show="!mostrarBloqueosJugadas" class="row justify-content-end w-100">
                <input ng-click="actualizar()" type="button" class="btn btn-info " name="guardar" value="Guardar">
            </div>
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
            <a ng-click="editar(true, {})" id="abrir-wizard-nuevo" class="btn btn-success text-white">Nueva moneda</a>
          </div>
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th class="text-center" scope="col">Moneda</th>
                <th class="text-center" scope="col">Abreviatura</th>
                <!-- <th class="text-center" scope="col">Hora cierre</th> -->
                <th class="text-center" scope="col">Equivalencia USD</th>
                <th class="text-center" scope="col">Por defecto</th>
                <th class="text-center" scope="col">Editar</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="l in datos.monedas">
                <th scope="row">@{{$index + 1}}</th>
                <td class="text-center">@{{l.descripcion}}</td>
                <td class="text-center font-weight-bold" style="color: @{{l.color}}">@{{l.abreviatura}}</td>
                <td class="text-center">@{{l.equivalenciaDeUnDolar}}</td>
                <td class="text-center">
                  <a ng-if="l.pordefecto == 1" style="cursor: pointer" class="d-inline ml-2 py-1  rounded abrir-wizard-editar"><i class="material-icons">radio_button_checked</i></a>
                  <a ng-if="l.pordefecto == 0" style="cursor: pointer" ng-click="pordefecto(l)" class="ion-android-delete d-inline  ml-2  py-1  rounded"><i class="material-icons">radio_button_unchecked</i></a>
                </td>
                <!-- <td class="text-center">@{{l.horaCierre}}</td> -->
                <td class="text-center">
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