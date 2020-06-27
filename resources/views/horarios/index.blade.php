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
    <div class="wizard-container">
    <div class="card card-wizard" data-color="blue" id="wizardProfile">
        <form novalidate>
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header">
            <div class="row">
      

             <div class="col-12 text-center">
              <h3 class="card-title">
                Horario normal
              </h3>
            </div> <!-- END COL-12 -->
            </div> <!-- END ROW -->
           
          </div><!-- END CARD HEADER -->
        
          <div class="card-body">
            <div class="">
              
            

              <div class="" id="horarios">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                   
                    
                    
                   

                      <div class="col-12">
                        <form novalidate>

                        

                      <div class="row">
                      

                          <div class="col-12 text-center">
                                <style>
                                  .btn-outline-info.active{
                                    background-color: #00bcd4!important;
                                    color: #fff!important;
                                  }
                                </style>
                                      <!-- ng-init="rbxLoteriasChanged(l, $first)" -->
                                <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">
                                    <label class="btn btn-outline-info" 
                                      ng-repeat="l in datos.loterias"
                                      ng-init="rbxLoteriasChanged(l, $first)"
                                      ng-class="{'active': $first}"
                                      ng-click="rbxLoteriasChanged(l)">
                                      <input  type="radio" name="options" id="option@{{$index + 1}}" autocomplete="off" checked> @{{l.descripcion}}
                                    </label>
                                    <!-- <label class="btn btn-outline-info">
                                      <input type="radio" name="options" id="option2" autocomplete="off"> Radio
                                    </label>
                                    <label class="btn btn-outline-info">
                                      <input type="radio" name="options" id="option3" autocomplete="off"> Radio
                                    </label> -->
                                </div>
                              </div><!-- END COL-12 -->

                        <div class="col-12 col-md-2 ">
                          <div class="row justify-content-center">

                            <div class="col-12 ">
                              <h3>Dias</h3>
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
                          </div> <!-- END ROW -->
                        </div> <!-- END COL-4 -->
                        
                        <div class="col-12 col-sm-10">
                          <div ng-click="actualizarHoraAperturaCierre()" class="row justify-content-center">

                              


                              


                             <div class="col-9">
                              <div class="row mt-4">
                                <h4 class="font-weight-bold d-sm-block col-sm-2 text-right">Dias</h4>
                                <div class="col-sm-4">
                                <h4 class="font-weight-bold text-center">Apertura</h4>
                                </div>
                                <div class="col-sm-4 text-center">
                                  <h4 class="font-weight-bold">Cierre</h4>
                                </div>
                                <div class="col-sm-2 text-center">
                                  <h4 class="font-weight-bold">Min. extras</h4>
                                </div>
                              </div>
                            </div>

                            <div ng-show="datos.selectedLoteria.lunes.status == 1" class="col-9 m-0 py-0">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Lunes</label>
                                <div class="col-sm-4" id="hola">
                                  <div class="form-group">
                                    
                                    <!-- ng-model-options="{ updateOn: 'blur' }" -->
                                  <input ng-model-onblur ng-change="update()"  ng-model="datos.selectedLoteria.lunes.apertura" type="time" placeholder="HH:mm"
                                    name="lunesHoraApertura" id="lunesHoraApertura"  class="form-control">
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.lunes.cierre" type="time" placeholder="HH:mm"
                                    name="lunesHoraCierre" id="lunesHoraCierre" class="form-control " >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.lunes.minutosExtras" type="number"
                                    name="lunesMinutosExtra" id="lunesMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>

                          
                            <div ng-show="datos.selectedLoteria.martes.status == 1" class="col-9 m-0 py-0">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Martes</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.martes.apertura" type="time" placeholder="HH:mm"
                                    name="martesHoraApertura" id="martesHoraApertura"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.martes.cierre" type="time" placeholder="HH:mm"  name="martesHoraCierre" id="martesHoraCierre"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.martes.minutosExtras" type="number"
                                    name="martesMinutosExtra" id="martesMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>

                             <div ng-show="datos.selectedLoteria.miercoles.status == 1" class="col-9">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Miercoles</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.miercoles.apertura" type="time" placeholder="HH:mm"  name="miercolesHoraApertura" id="miercolesHoraApertura"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.miercoles.cierre" type="time" placeholder="HH:mm"  name="miercolesHoraCierre" id="miercolesHoraCierre"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.miercoles.minutosExtras" type="number"
                                    name="miercolesMinutosExtra" id="miercolesMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>


                             <div ng-show="datos.selectedLoteria.jueves.status == 1" class="col-9">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Jueves</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.jueves.apertura" type="time" placeholder="HH:mm"  name="juevesHoraApertura" id="juevesHoraApertura"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.jueves.cierre" type="time" placeholder="HH:mm"   name="juevesHoraCierre" id="juevesHoraCierre"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.jueves.minutosExtras" type="number"
                                    name="juevesMinutosExtra" id="juevesMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>


                             <div ng-show="datos.selectedLoteria.viernes.status == 1" class="col-9">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Viernes</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.viernes.apertura" type="time" placeholder="HH:mm"  name="viernesHoraApertura" id="viernesHoraApertura"  class="form-control " >
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.viernes.cierre" type="time" placeholder="HH:mm"  name="viernesHoraCierre" id="viernesHoraCierre"  class="form-control " >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.viernes.minutosExtras" type="number"
                                    name="viernesMinutosExtra" id="viernesMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>


                            <div ng-show="datos.selectedLoteria.sabado.status == 1" class="col-9">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Sabado</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.sabado.apertura" type="time" placeholder="HH:mm"  name="sabadoHoraApertura" id="sabadoHoraApertura"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.sabado.cierre" type="time" placeholder="HH:mm"  name="sabadoHoraCierre" id="sabadoHoraCierre"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.sabado.minutosExtras" type="number"
                                    name="sabadoMinutosExtra" id="sabadoMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>


                              <div ng-show="datos.selectedLoteria.domingo.status == 1" class="col-9">
                              <div class="row m-0 py-0">
                                <label class="d-none d-sm-block col-sm-2 col-form-label mt-2">Domingo</label>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.domingo.apertura" type="time" placeholder="HH:mm"  name="domingoHoraApertura" id="domingoHoraApertura"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="form-group">
                                  <input ng-model-onblur ng-change="update()" ng-model="datos.selectedLoteria.domingo.cierre" type="time" placeholder="HH:mm"  name="domingoHoraCierre" id="domingoHoraCierre"  class="form-control" >
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-group">
                                  <input ng-model="datos.selectedLoteria.domingo.minutosExtras" type="number"
                                    name="domingoMinutosExtra" id="domingoMinutosExtra" class="form-control " >
                                  </div>
                                </div>
                              </div>
                            </div>

                          

                          </div>
                        </div> <!-- END COL-6 -->

                      </div> <!-- END ROW -->


                        </form>

                      </div> <!-- END DIV FORMULARIO -->


                      
                    </div> <!-- END ROW SECUNDARIO PRINCIPAL -->
                  </div> <!-- END COL PRINCIPAL -->
                </div> <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 3 -->
              
          

            


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