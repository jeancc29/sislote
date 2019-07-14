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
              Numeros ganadores
            </h3>
           
          </div>
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li class="nav-item">
                <a ng-init="mostrarBloqueosJugadas = false" ng-click="mostrarBloqueosJugadas = false" class="nav-link active" href="#about" data-toggle="tab" role="tab">
                  Vista completa
                </a>
              </li>
              <li class="nav-item">
                <a ng-click="mostrarBloqueosJugadas = true" class="nav-link" href="#account" data-toggle="tab" role="tab">
                  Vista sencilla
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="about">
                <h5 class="info-text"> Let's start with the basic information (with validation)</h5>
                <div class="row justify-content-center">
 
                  <div class="col-12 col-md-12">

                   
                    
                    <div class="row">
                        <div ng-repeat="l in datos.loterias" class="col-12">
                          <div class="row">
                            <div class="col-3 col-md-2 text-right mt-4">
                              <h6>@{{l.descripcion}}</h6>
                            </div>
                            <div class="col-2 col-md-1">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">1era</label>
                                  <input ng-model="l.primera" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            <div class="col-2 col-md-1">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">2da</label>
                                  <input ng-model="l.segunda" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            <div class="col-2 col-md-1" ng-hide="existeSorteo('Super pale', l)">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">3era</label>
                                  <input ng-model="l.tercera" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            

                            <div class="col-2 col-md-1" ng-show="existeSorteo('Pick 3 Box', l) || existeSorteo('Pick 3 Straight', l)">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">Pick3</label>
                                  <input ng-model="l.pick3" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-2 col-md-1" ng-show="existeSorteo('Pick 4 Box', l) || existeSorteo('Pick 4 Straight', l)">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">Pick4</label>
                                  <input ng-model="l.pick4" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                           </div> <!-- END ROW -->
                        </div> <!-- END COL-23 -->
                    </div> <!-- END ROW -->


                  </div> <!-- END COL-12 -->
                  
                </div>
              </div>
              <div class="tab-pane" id="account">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row">

                   
                    <form novalidate autocomplete="off">
                    

                      <!-- <div class="col-3 col-md-4">
                          <div id="divInputJugada" class="form-group">
                                        <label  for="jugada" class="bmd-label-floating">Loteria</label>
                                        <input 
                                            ng-model="datos.loteria"
                                            autocomplete="off"
                                            class="form-control h4" 
                                            id="inputJugada" 
                                            type="text" name="text" 
                                            minLength="2" maxLength="6"  />
                                    </div>
                      </div> -->

                      

                      <div class="col-12">
                        <div class="row">
                          <div class="col-3">
                              <div id="divInputFechaDesde" class="form-group">
                                  <label  for="jugada" class="bmd-label-floating">Fecha</label>
                                  <input ng-model="datos.desde" id="fechaDesde" type="date" class="form-control" value="10/06/2018" required>
                              </div>
                          </div>

                          

                          <div class="col-3">
                            <div class="togglebutton mt-3">
                              <label style="color: black;">
                                <input type="checkbox" checked="">
                                <span class="toggle"></span>
                                Procesar jugadas
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>

                      


                      <div class="row">
                        <div class="col-12">
                          <div class="row">
                            <div class="col-3 mt-1 ml-4">
                              <div class="form-group">
                                  <select 
                                      ng-model="datos.selectedLoteria"
                                      ng-change="cbxLoteriasChanged()"
                                      ng-options="o.descripcion for o in datos.optionsLoterias"
                                      class="selectpicker w-100" 
                                      data-style="select-with-transition" 
                                      title="Select loteria">
                                </select>
                              </div>
                            </div>
                            <div class="col-2" ng-show="existeSorteo('Pale', datos.selectedLoteria) 
                                || existeSorteo('Directo', datos.selectedLoteria) 
                                || existeSorteo('Tripleta', datos.selectedLoteria)
                                || existeSorteo('Super pale', datos.selectedLoteria)">
                              <div class="input-group form-control-lg">
                                <div id="primeraVentanaSencilla" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">1era</label>
                                  <input ng-model="datos.primera" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            <div class="col-2" ng-show="existeSorteo('Pale', datos.selectedLoteria) 
                                || existeSorteo('Directo', datos.selectedLoteria) 
                                || existeSorteo('Tripleta', datos.selectedLoteria)
                                || existeSorteo('Super pale', datos.selectedLoteria)">
                              <div class="input-group form-control-lg">
                                <div id="segundaVentanaSencilla" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">2da</label>
                                  <input ng-model="datos.segunda" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->
                            <div class="col-2" ng-show="existeSorteo('Pale', datos.selectedLoteria) 
                                || existeSorteo('Directo', datos.selectedLoteria) 
                                || existeSorteo('Tripleta', datos.selectedLoteria)">
                              <div class="input-group form-control-lg">
                                <div id="terceraVentanaSencilla" class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">3era</label>
                                  <input ng-model="datos.tercera" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-2 col-md-2" ng-show="existeSorteo('Pick 3 Box', datos.selectedLoteria) || existeSorteo('Pick 3 Straight', datos.selectedLoteria)">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">Pick3</label>
                                  <input ng-model="l.pick3" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-2 col-md-2" ng-show="existeSorteo('Pick 4 Box', datos.selectedLoteria) || existeSorteo('Pick 4 Straight', datos.selectedLoteria)">
                              <div class="input-group form-control-lg">
                                <div class="form-group">
                                  <label for="exampleInput1" class="bmd-label-floating">Pick4</label>
                                  <input ng-model="l.pick4" autocomplete="off" type="text" class="form-control" id="exampleInput1" name="monto">
                                </div>
                              </div>
                            </div> <!-- END COL-2 -->

                            <div class="col-1 text-center">
                              <a ng-click="actualizar(true)" class="btn btn-success">Add</a>
                            </div>
                           </div> <!-- END ROW -->
                        </div> <!-- END COL-23 -->
                    </div> <!-- END ROW -->

                    </form> 
                   
                    <style>
                      .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
                        background-color: #f5f2f2;
                      }
                      /* .table-striped{
                        background: #cccaca!important;
                      } */
                    
                    </style>
                    <div class="col-12">
                         <!-- INICIO TABLA-->
                        <table 
                    
                        class="table table-striped mt-1 ">
                        <thead class="">
                            <tr>
                            <th class="text-center font-weight-bold " style="font-size: 15px">Lot</th>
                            <th class="text-center font-weight-bold " style="font-size: 15px">1ra</th>
                            <th class="text-center font-weight-bold" style="font-size: 15px">2da</th>
                            <th class="text-center font-weight-bold" style="font-size: 15px">3ra</th>
                            <th class="text-center font-weight-bold" style="font-size: 15px">Pick 3</th>
                            <th class="text-center font-weight-bold" style="font-size: 15px">Pick 4</th>
                            <!-- <th class="text-center font-weight-bold" style="font-size: 15px">HASTA</th> -->
                            <th class="text-center font-weight-bold" style="font-size: 15px">Editar</th>
                            <th class="text-center font-weight-bold" style="font-size: 15px">Limpiar</th>
                            <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                            </tr>
                        </thead>
                        <tbody id="table_body" class="">
                            <tr ng-repeat="c in datos.loterias" @{{p($last)}}>
                            <td class="text-center font-weight-bold " style="font-size: 14px">@{{c.descripcion}}</td>
                            <td class="text-center font-weight-bold " style="font-size: 14px">@{{c.primera}}</td>
                            <td class="text-center font-weight-bold " style="font-size: 14px">@{{c.segunda}}</td>
                            <td class="text-center font-weight-bold" style="font-size: 14px">@{{c.tercera}}</td>
                            <td class="text-center font-weight-bold" style="font-size: 14px">@{{c.pick3}}</td>
                            <td class="text-center font-weight-bold" style="font-size: 14px">@{{c.pick4}}</td>
                            <!-- <td class="text-center font-weight-bold " style="font-size: 14px">
                                @{{c.fechaHasta}}
                               
                            </td> -->
                            <td class="text-center font-weight-bold " style="font-size: 14px">
                              <a style="cursor: pointer" ng-click="editarPremio(c.id)" class="ion-edit d-inline  py-1 text-success rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                            </td>
                            <td class="text-center font-weight-bold " style="font-size: 14px">
                              <a style="cursor: pointer" ng-click="borrar(c.id)" class="ion-edit d-inline  py-1 text-danger rounded abrir-wizard-editar"><i class="material-icons">delete_outline</i></a>
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