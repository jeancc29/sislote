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
        <form action="" method="">
          <!--        You can switch " data-color="primary" "  with one of the next bright colors: "green", "orange", "red", "blue"       -->
          <div class="card-header text-center">
            <h3 class="card-title">
              Bloqueos
            </h3>
           
          </div>
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a ng-init="mostrarBloqueosJugadas = false" ng-click="mostrarBloqueosJugadas = false" class="nav-link active" href="#about" data-toggle="tab" role="tab">
                  Loteria
                </a>
              </li>
              <li class="nav-item">
                <a ng-click="mostrarBloqueosJugadas = true" class="nav-link" href="#account" data-toggle="tab" role="tab">
                  Jugadas
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="about">
                <h5 class="info-text"> Let's start with the basic information (with validation)</h5>
                <div class="row justify-content-center">
 
                  <div class="col-12 col-sm-8">

                    <div class="row">
                      <div class="col-12">
                      <style>
                          .dropdown-menu{
                              width: 100%;
                          }
                      </style>

                        <select 
                            ng-change="cbxLoteriasChanged()"
                            ng-model="datos.selectedLoteria"
                            ng-options="o.descripcion for o in datos.optionsLoterias"
                            class="selectpicker w-100" 
                             data-style="btn btn-info btn-round" 
                            title="Seleccionar loterias">
                        </select>
                      </div>
                    </div>
                    
                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">money</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="exampleInput1" class="bmd-label-floating">Quiniela o directo</label>
                        <input ng-model="datos.quiniela" type="text" class="form-control" id="exampleInput1" name="monto" required>
                      </div>
                    </div>

                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">money</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="exampleInput1" class="bmd-label-floating">Pale</label>
                        <input ng-model="datos.pale" type="text" class="form-control" id="exampleInput1" name="monto" required>
                      </div>
                    </div>

                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">money</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="exampleInput1" class="bmd-label-floating">Tripleta</label>
                        <input ng-model="datos.tripleta" type="text" class="form-control" id="exampleInput1" name="monto" required>
                      </div>
                    </div>
                    


                  </div>
                  <!-- <div class="col-lg-7 mt-3">
                    <div class="input-group form-control-lg">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">av_timer</i>
                        </span>
                      </div>
                      <div class="form-group">
                        <label for="exampleInput1" class="bmd-label-floating">Hora de cierre</label>
                        <input type="text" class="form-control timepicker" value="10/05/2016" required>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 mt-4">
                    <div class="input-group form-control-lg">
                      <div class="form-group">
                        <div class="form-check">
                          <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" value="" checked> Activa
                            <span class="form-check-sign">
                              <span class="check"></span>
                            </span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div> -->
                </div>
              </div>
              <div class="tab-pane" id="account">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row">

                   
                    <form novalidate autocomplete="off">
                    <div class="col-12 mb-2">
                        <select 
                            ng-change="cbxLoteriasChanged2()"
                            ng-model="datos.bloqueoJugada.selectedLoteria"
                            ng-options="o.descripcion for o in datos.bloqueoJugada.optionsLoterias"
                            class="selectpicker w-100" 
                            data-size="7" data-style="btn btn-primary" 
                            title="Single Select">
                        </select>
                      </div>

                      <div class="col-3 col-md-2">
                          <div id="divInputJugada" class="form-group">
                                        <label  for="jugada" class="bmd-label-floating">Jugada</label>
                                        <input 
                                            ng-model="datos.bloqueoJugada.jugada"
                                            autocomplete="off"
                                            class="form-control h4" 
                                            id="inputJugada" 
                                            type="text" name="text" 
                                            minLength="2" maxLength="6"  />
                                    </div>
                      </div>

                      <div class="col-3 col-md-2">
                          <div id="divInputJugada" class="form-group">
                                        <label  for="jugada" class="bmd-label-floating">Monto</label>
                                        <input 
                                            ng-model="datos.bloqueoJugada.monto"
                                            autocomplete="off"
                                            class="form-control h4" 
                                            type="text" name="text" 
                                         />
                                    </div>
                      </div>

                      <div class="col-3">
                          <div id="divInputFechaDesde" class="form-group">
                              <label  for="jugada" class="bmd-label-floating">Fecha inicio</label>
                              <input ng-model="datos.bloqueoJugada.fechaDesde" id="fechaDesde" type="date" class="form-control" value="10/06/2018" required>
                          </div>
                      </div>

                      <div class="col-3">
                          <div id="divInputFechaHasta" class="form-group">
                              <label for="jugada" class="bmd-label-floating">Fecha fin</label>
                              <input ng-model="datos.bloqueoJugada.fechaHasta"  id="fechaHasta" type="date" class="form-control" value="10/06/2018" required>
                          </div>
                      </div>

                      <div class="col-1">
                          <!-- <a class="col-1 p-2 rounded-circle bg-primary">+</a> -->
                          <a ng-click="actualizar_bloqueo_jugada()" class="btn btn-outline-success">add</a>
                      </div>

                    </form> 
                   

                    <div class="col-12">
                         <!-- INICIO TABLA-->
                        <table 
                        ng-class="{'table-fixed-2': p() <= 3, 'table-fixed-3': p() >= 4}"
                        class="table mt-1 ">
                        <thead class="thead-dark">
                            <tr>
                            <th class="text-center font-weight-bold col-2 col-sm-3" style="font-size: 13px">JUGADA</th>
                            <th class="text-center font-weight-bold col-2 col-sm-2" style="font-size: 13px">MONTO</th>
                            <th class="text-center font-weight-bold col-3 col-sm-3" style="font-size: 13px">INICIO</th>
                            <th class="text-center font-weight-bold col-3 col-sm-3" style="font-size: 13px">HASTA</th>
                            <th class="text-center font-weight-bold col-3 col-sm-1" style="font-size: 13px">X</th>
                            <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                            </tr>
                        </thead>
                        <tbody id="table_body" class="">
                            <tr ng-repeat="c in datos.bloqueoJugada.bloqueosJugadas" @{{p($last)}}>
                            <td class="text-center font-weight-bold col-2 col-sm-3" style="font-size: 14px">@{{agregar_guion(c.jugada)}}</td>
                            <td class="text-center font-weight-bold col-2 col-sm-2" style="font-size: 14px">@{{c.monto}}</td>
                            <td class="text-center font-weight-bold col-2 col-sm-3" style="font-size: 14px">@{{c.fechaDesde}}</td>
                            <td class="text-center font-weight-bold col-2 col-sm-3" style="font-size: 14px">
                                @{{c.fechaHasta}}
                                <!-- <button ng-click="jugada_eliminar(c.jugada)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="mr-3 btn btn-link m-0 p-0 d-inline ">
                                        <i class="material-icons">close</i>
                                </button> -->
                            </td>
                            <td class="text-center font-weight-bold col-2 col-sm-1" style="font-size: 14px">
                                <button ng-click="eliminar_bloqueo_jugada(c)" type="button" rel="tooltip" data-placement="left" class=" btn btn-outline-danger m-0 p-1 d-inline ">
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

                         <hr class="m-0">
                        
                        <!-- FIN TABLA -->
                    </div>

                    
                    
                   


                     
                      
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
  </div>
</div>

                  </div>

                  <footer class="footer" >
    <div class="container-fluid">
        <nav class="float-left">
          <ul>
              <li>
                  <a href="https://www.creative-tim.com">
                      Creative Tim
                  </a>
              </li>
              <li>
                  <a href="https://creative-tim.com/presentation">
                      About Us
                  </a>
              </li>
              <li>
                  <a href="http://blog.creative-tim.com">
                      Blog
                  </a>
              </li>
              <li>
                  <a href="https://www.creative-tim.com/license">
                      Licenses
                  </a>
              </li>
          </ul>
        </nav>
        <div class="copyright float-right">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>, made with <i class="material-icons">favorite</i> by
            <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a> for a better web.
        </div>
    </div>
</footer>

               
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





<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc --><script src="{{asset('assets/js/material-dashboard.js')}}" type="text/javascript"></script>
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


    (function($){
      $.fn.hasScrollBar = function(){
        return this.get(0).scrollHeight > this.height();
      }
    })(jQuery);

  });
</script>





    </body>

</html>



@endsection