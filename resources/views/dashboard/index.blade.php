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
                      













  <div class="container-fluid ">

    <!-- Loader -->
    <div ng-show="cargando" class="row justify-content-center ">
      <div class="spinner-border mt-5" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    

    <!-- Loader -->
    <div ng-cloak ng-show="cargandoOnFechaChanged" class="loader loader-bouncing is-active"></div>

    <!-- Content -->
    <div ng-cloak ng-show="cargando == false" class="col-md-12 col-12 mr-auto mx-0 px-0">
      <div class="row" style="z-index: 100000">
        <div class="mt-4 d-block d-lg-none col-12 ">
          <div class="row">

            <div class="col-3">
              <div class="input-group form-control-lg card-title w-50">
                <div  class="">
                  <!-- <label for="fechaBusqueda" style="font-size: 40px;" class="bmd-label-floating font-weight-bold" style="color: black;">Numero</label> -->
                  <input style="font-size: 20px;" placeholder="Fecha" ng-model="datos.fecha" id="fechaBusqueda" type="date" class="form-control vcenter " required>
                </div>
              </div>
            </div>

            <div class="col-6 ">
              <div class="input-group mt-2">
                
                <!-- <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Tipo regla</label>                               -->

                  <div  class=" col-sm-12 col-10">
                  <select 
                      ng-change="cbxTipoBloqueosJugadaChanged()"
                      ng-model="selectedMoneda"
                      ng-options="o.descripcion for o in optionsMonedas"
                      class="selectpicker col-12" 
                      data-style="select-with-transition" 
                      title="Seleccionar moneda">
                </select>
                </div>
              </div> <!-- END INPUT GROUP -->
            </div>

            <div class="col-3">
                <a ng-click="onFechaChanged()" href="#" class="btn btn-primary mt-3">Buscar</a>
            </div>

          </div>
        </div>

        <div class="d-none d-lg-block col-lg-12 ">
          <div class="row">

            <div class="col-3">
              <div class="input-group form-control-lg card-title w-50">
                <div  class="">
                  <!-- <label for="fechaBusqueda" style="font-size: 40px;" class="bmd-label-floating font-weight-bold" style="color: black;">Numero</label> -->
                  <input style="font-size: 20px;" placeholder="Fecha" ng-model="datos.fecha" id="fechaBusqueda" type="date" class="form-control vcenter " required>
                </div>
              </div>
            </div>

            <div class="col-6 ">
              <div class="input-group mt-2">
                  <div  class=" col-sm-12 col-10">
                    <select 
                        ng-change="cbxTipoBloqueosJugadaChanged()"
                        ng-model="selectedMoneda"
                        ng-options="o.descripcion for o in optionsMonedas"
                        class="selectpicker col-12" 
                        data-style="select-with-transition" 
                        title="Seleccionar moneda">
                  </select>
                </div>
              </div> <!-- END INPUT GROUP -->
            </div>

            <div class="col-3">
                <a ng-click="onFechaChanged()" href="#" class="btn btn-primary mt-4">Buscar</a>
            </div>

            

          </div>
        </div>

      </div>
      <div  class="row">
          

          <div class="d-sm-none col-lg-4 col-md-4 col-sm-4 d-flex align-items-center mt-4">
            <div id="input-fecha" class="">
              <!-- <label for="fechaBusqueda" style="font-size: 40px;" class="bmd-label-floating font-weight-bold" style="color: black;">Numero</label> -->
              <input ng-change="onFechaChanged()" style="font-size: 40px;" placeholder="Fecha" ng-model="datos.fecha" id="fechaBusqueda" type="date" class="form-control vcenter" required>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-4 ">
            <div class="card card-stats py-0 mb-0">
              <div class="card-header card-header-success card-header-icon">
                <div class="card-icon" style="padding: 1px!important">
                  <i class="material-icons">store</i>
                </div>
                <p class="card-category">Bancas con ventas</p>
                <h3 class="card-title">@{{bancasConVentas}}</h3>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> Hoy
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-4">
            <div class="card card-stats py-0 mb-0">
              <div class="card-header card-header-rose card-header-icon">
                <div class="card-icon" style="padding: 1px!important">
                  <i class="material-icons">cancel_presentation</i>
                </div>
                <p class="card-category">Bancas sin ventas</p>
                <h3 class="card-title">@{{bancasSinVentas}}</h3>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> Hoy
                </div>
              </div>
            </div>
          </div>
          
          
          
      </div>
      <div class="row py-0 mb-0">
        <div class="col-md-6 py-0 mb-0">
          <div class="card py-0 mb-1">
            <div class="card-header card-header-info card-header-icon">
              <div class="card-icon">
                <i class="material-icons">insert_chart</i>
              </div>
              <!-- <h4 class="card-title">Grafica de ventas neta <span style="font-size: 25px" class="text-success font-weight-bold text-right">@{{monedaAbreviatura}}$</span></h4> -->
              <div class="row">
                <h4 class="card-title col-9">Grafica de ventas neta </h4>
                <p style="font-size: 25px; color: @{{monedaColor}}" class="font-weight-bold text-right col-3 mt-3">@{{monedaAbreviatura}}</p>
              </div>
            </div>
            <div class="card-body ">
              <div class="row">
                <div class="col-md-12">
                <div id="myfirstchart" class="col-md-12" style="height: 250px;"></div>
                </div> <!-- END COL-MD-12 -->
                    <!-- <div class="col-md-6 ml-auto mr-auto">
                      <div id="worldMap" style="height: 300px;"></div>
                    </div> -->
              </div> <!-- END ROW -->
            </div> <!-- END CARD-BODY -->
          </div> <!-- END CARD -->
        </div> <!-- END COL-MD-6 -->

        <div class="col-md-6 py-0 mb-0">
          <div class="card py-0 mb-1">
            <div class="card-header card-header-info card-header-icon">
              <div class="card-icon">
                <i class="material-icons">assignment</i>
              </div>
              <!-- <h4 class="card-title">Ventas por lotería</h4>
              <p style="font-size: 25px" class="text-success font-weight-bold text-right">@{{monedaAbreviatura}}$</p> -->
              <div class="row">
                <h4 class="card-title col-9">Ventas por lotería</h4>
                <p style="font-size: 25px; color: @{{monedaColor}}" class="font-weight-bold text-right col-3 mt-3">@{{monedaAbreviatura}}</p>
              </div>
            </div>
            <div class="card-body ">
              <div class="row">
                <div class="col-12">

                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-fixed-dashboard">
                                <thead class="">
                                    <tr>
                                    <th class="font-weight-bold col-5 text-center">Loteria</th>
                                    <th class="font-weight-bold col-4 text-center">Venta total</th>
                                    <th class="font-weight-bold col-3 text-center">Premios</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                 <!-- ($loterias as $l) -->
                                <tr ng-repeat="l in loterias">
                                    <td class="col-5  text-center">@{{l.descripcion}}</td>
                                    <td class="col-4 text-center">@{{(l.ventas) ? l.ventas : 0}}</td>
                                    <td class="col-3 text-center">@{{(l.premios) ? l.premios : 0}}</td>
                                  </tr>
                                
                                    <!-- <tr>
                                    <td class="font-weight-bold col-5  text-center" style="font-size: 18px;">Total</td>
                                    <td class="font-weight-bold col-4 text-center" style="font-size: 18px;">200</td>
                                    <td class="font-weight-bold col-3 text-center" style="font-size: 18px;">200</td>
                                    </tr> -->
                                    
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
                        <div class="row mt-2">
                          <h4 class="font-weight-bold col-5  text-center" style="font-size: 18px;">Total</h4>
                          <h4 class="font-weight-bold col-4 text-center" style="font-size: 18px;">@{{(totalVentasLoterias) ? totalVentasLoterias : 0}}</h4>
                          <h4 class="font-weight-bold col-3 text-center" style="font-size: 18px;">@{{(totalPremiosLoterias) ? totalPremiosLoterias : 0}}</h4>
                        </div>
                         <!-- <h4 class="text-right">Total: @{{datos.total_palet | currency}}</h4> -->
                    </div> <!-- COL-3 -->

              </div> <!-- END ROW -->
            </div> <!-- END CARD-BODY -->
          </div> <!-- END CARD -->
        </div> <!-- END COL-MD-6 -->

      </div> <!-- END ROW -->

      <style>
          .fz-12{
            font-size: 12px;
          }
      </style>

      <div class="row justify-content-center">
      <div class="col-md-12">
      <div class="card ">
        <!-- <div class="card-header ">
          <h4 class="card-title">Loterias proximas a cerra -
            <small class="description">Horizontal Tabs</small>
          </h4>
        </div> -->
        <div class="card-body ">
          <ul class="nav nav-pills nav-pills-primary" role="tablist">
            <li ng-click="cambiarLoteria(l)" ng-init="cambiarLoteria(l, $first)" ng-repeat="l in loteriasJugadasDashboard track by l.id" class="nav-item">
              <a  ng-class="{'active': $first}" class="nav-link" data-toggle="tab" href="#link1" role="tablist">
                @{{l.descripcion}}
              </a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#link2" role="tablist">
                Settings
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#link3" role="tablist">
                Options
              </a>
            </li> -->
          </ul>
          
        </div>
      </div>
    </div>  <!-- END COL-12 --> 

        
        <div ng-repeat="s in datos.selectedLoteria.sorteos" class="card my-0 mb-2 mx-1 d-inline-block mx-0" style=" min-height: 290px; width: 23.5%;"> <!-- min-height: 455px; max-height: 455px; -->
                <div class="card-header card-header-info card-header-icon my-0 py-0">
                  <div class="card-icon" style="width:20%!important; height: 25%!important; padding: 2px; margin-right: 0px; margin-left: 0px; margin-top: 0px;">
                    <i class="material-icons" style="width: 0px; height: 0px; margin: 0 auto; padding: 0 auto; line-height: 0px; margin-top: 20px;" >assignment</i>
                  </div>
                  <h4 class="card-title py-0 my-0 text-center font-weight-bold">@{{s['descripcion']}}</h4>
                </div>
                <div class="card-body px-0 mx-0 pt-0 mt-0"> <!-- aqui va el overflow-y y el div con el precio va despues de la etiqueta table-->
                <div class="">
                    <table class="table table-fixed">
                    <thead>
                        <tr>
                        <th class="font-weight-bold text-center col-4" style="font-size: 15px">LOT</th>
                        <th class="font-weight-bold text-center col-4" style="font-size: 15px">NUM</th>
                        <th class="text-right font-weight-bold text-center col-4" style="font-size: 15px">MONT</th>
                        <!-- <th class="text-right font-weight-bold  d-md-block d-lg-none" style="font-size: 11px">MONT</th> -->
                        <!-- <th class="text-center col-1 col-sm-2" style="font-size: 15px">..</th> -->
                        </tr>
                    </thead>
                    <tbody class="">
                   
                        <tr ng-repeat="j in s.jugadas">
                        
                        
                        <td ng-if="s.descripcion == 'Tripleta'" class="text-center col-4" style="font-size: 12px;">@{{j['abreviatura']}}</td>
                        
                        
                        <td ng-if="s.descripcion != 'Tripleta'" class="text-center col-4" style="font-size: 15px;">@{{j['abreviatura']}}</td>
                        

                        
                        <td ng-if="s.descripcion == 'Tripleta'" class="text-center col-4 font-weight-bold" style="font-size: 12px;">@{{j['jugada']}}</td>
                        
                        
                        <td ng-if="s.descripcion != 'Tripleta'" class="text-center col-4" style="font-size: 15px;">@{{j['jugada']}}</td>
                       
                       
                        
                        <td ng-if="s.descripcion == 'Tripleta'" class="text-center col-4" style="font-size: 12px;">
                        @{{j['monto']}}
                           
                        </td>
                       
                        
                        <td ng-if="s.descripcion != 'Tripleta'" class="text-center col-4" style="font-size: 15px;">
                        @{{j['monto']}}
                           
                        </td>
                        
                        
                        
                        </tr>
                      
                        
                    </tbody>
                    </table>
                    
                </div>
                   
                </div>
            </div> <!-- END CARD -->
     
            <div id="modal-cargando" class="modal fade modal-cancelar-eliminar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                 <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">Cargando</h3>
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">

                   

                    <div class="row justify-content-center">
                    <div class="spinner-border" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                    </div>



                    

                    <div class="container">

                        <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                        </div> -->
                    </div>

                </div> <!-- END MODAL-BODY -->
                
            </div> <!-- END MODAL-CONTENT-->
        </div>
    </div>

            

      </div> <!-- END ROW -->
  
  
  </div> <!-- END COL-12 COL-MD-12 -->
</div> <!-- END CONTAINER-FLUID -->

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


<?php if($controlador == "dashboard"):?>
    <script src="{{asset('assets/js/raphael-min.js')}}" ></script>
    <script src="{{asset('assets/js/morris.min.js')}}" ></script>
<?php endif; ?> 



<script>
//     new Morris.Bar({
//   // ID of the element in which to draw the chart.
//   element: 'myfirstchart',
//   // Chart data records -- each entry in this array corresponds to a point on
//   // the chart.

//   // data: [
//   //   { year: '2008', sales: 200, value: 20 },
//   //   { year: '2009', sales: 100, value: 10 },
//   //   { year: '2010', sales: 70, value: 5 },
//   //   { year: '2011', sales: 50, value: 5 },
//   //   { year: '2012', sales: 200, value: 20 }
//   // ],
//   data: <?php echo $ventasGrafica ?>,
//   // The name of the data record attribute that contains x-values.
//   xkey: 'dia',
//   // A list of names of data record attributes that contain y-values.
//   ykeys: ['total', 'neto'],
//   // Labels for the ykeys -- will be displayed when you hover over the
//   // chart.
//   labels: ['total', 'neto'],
//   // stacked: true,
//   barSize: 30,
//   resize: true,
//   fillOpacity: 0.1,
//   hideHover: true,
//   behaveLikeLine: true,

//   pointFillColors: ['#ffffff'],
//   pointStrokeColors: ['black'],
//   lineColors: ['red', 'blue'],

//   barColors: function (row, series, type) {
//     console.log('row: ', row.y);
//     if(series.key == "neto" && row.y > 0)
//         return "#75b281";
//     if(series.key == "neto" && row.y < 0)
//       return "#dc2365";
//     else
//       // return "#c2c2d6";
//       return "#95999e";
//     }
// });





// var data = 
//     // [
//     //   { y: 'asdfgfddfgdfgdfgdfgdfggdf', a: 50, b: 90},
//     //   { y: '2015dfdfgfdgdfgfdggdfgdfg', a: 65,  b: 75},
//     //   { y: '2016dfdfgdfgdfgfdggdfgdfg', a: 50,  b: 50},
//     //   { y: '201dfgdfgdfgdfgdfgdfgdfgdfg7', a: 75,  b: 60},
//     //   { y: '2018', a: 80,  b: 65},
//     //   { y: '2019', a: 90,  b: 70},
//     //   { y: '20dfgdfgdfgdfgdfgdfg20', a: 100, b: 75},
//     //   { y: '2021', a: 115, b: 75},
//     //   { y: '2022', a: 120, b: 85},
//     //   { y: '2023', a: 145, b: 85},
//     //   { y: '2024', a: 160, b: 95}
//     // ]
//     <?php echo $ventasGrafica ?>
//     ,
//     formatY = function (neto) {
//       console.log("y: ", '$'+neto);
//             return '$'+neto;
//         },
//     formatX = function (x) {
//       console.log("x: ", x);
//             return x.src.dia;
//         },
    
//     config = {
//       data: data,
//       xkey: 'dia',
//       ykeys: ['neto', 'total'],
//       labels: ['total', 'neto'],
//       fillOpacity: 0.6,
//       hideHover: 'auto',
//       stacked: true,
//       resize: true,
//       pointFillColors:['#ffffff'],
//       pointStrokeColors: ['black'],
//       barColors:['blue','green','orange'],
//       yLabelFormat:formatY,
//       xLabelFormat: formatX,
//       barSize: 30,
//       // hoverCallback: function (index, options, content, row) {
//       //   return 'custom 1';
//       // }
//       barColors: function (row, series, type) {
//         // console.log('row: ', series);
//         if(series.key == "neto" && row.y > 0)
//             return "#75b281";
//         if(series.key == "neto" && row.y < 0)
//           return "#dd9999";
//         else
//           return "#c2c2d6";
//       }
//   };



// var data = [
//       { y: 'asdfgfddfgdfgdfgdfgdfggdf', a: 50, b: 90},
//       { y: '2015dfdfgfdgdfgfdggdfgdfg', a: 65,  b: 75},
//       { y: '2016dfdfgdfgdfgfdggdfgdfg', a: 50,  b: 50},
//       { y: '201dfgdfgdfgdfgdfgdfgdfgdfg7', a: 75,  b: 60},
//       { y: '2018', a: 80,  b: 65},
//       { y: '2019', a: 90,  b: 70},
     
//     ],
//     formatY = function (y) {
//             return '$'+y;
//         },
//     formatX = function (x) {
//             return x.src.y;
//         },
    
//     config = {
//       data: data,
//       xkey: 'y',
//       ykeys: ['a', 'b' ],
//       labels: ['Total Income', 'Total Outcome'],
//       fillOpacity: 0.6,
//       hideHover: 'auto',
//       stacked: true,
//       resize: true,
//       pointFillColors:['#ffffff'],
//       pointStrokeColors: ['black'],
//       barColors:['blue','green','orange'],
//       yLabelFormat:formatY,
//       xLabelFormat: formatX,
      
//   };
    

    
// config.element = 'myfirstchart';
// new Morris.Bar(config);























// var data = 
//     [
//       { y: 'asdfgfddfgdfgdfgdfgdfggdf', a: 50, b: 90},
//       { y: '2015dfdfgfdgdfgfdggdfgdfg', a: 65,  b: 75},
//       { y: '2016dfdfgdfgdfgfdggdfgdfg', a: 50,  b: 50},
//       { y: '201dfgdfgdfgdfgdfgdfgdfgdfg7', a: 75,  b: 60},
//       { y: '2018', a: 80,  b: 65},
//       { y: '2019', a: 90,  b: 70},
//       { y: '20dfgdfgdfgdfgdfgdfg20', a: 100, b: 75},
//       { y: '2021', a: 115, b: 75},
//       { y: '2022', a: 120, b: 85},
//       { y: '2023', a: 145, b: 85},
//       { y: '2024', a: 160, b: 95}
//     ]
//     ,
//     formatY = function (y) {
//       console.log("y: ", '$'+y);
//             return '$'+y;
//         },
//     formatX = function (x) {
//       console.log("x: ", x);
//             return x.src.y;
//         },
    
//     config = {
//       data: data,
//       xkey: 'y',
//       ykeys: ['a', 'b'],
//       labels: ['Total Income', 'Total Outcome'],
//       fillOpacity: 0.6,
//       hideHover: 'auto',
//       stacked: true,
//       resize: true,
//       pointFillColors:['#ffffff'],
//       pointStrokeColors: ['black'],
//       barColors:['blue','green','orange'],
//       yLabelFormat:formatY,
//       xLabelFormat: formatX,
//       // hoverCallback: function (index, options, content, row) {
//       //   return 'custom 1';
//       // }
//       barColors: function (row, series, type) {
//         // console.log('row: ', series);
//         if(series.key == "neto" && row.y > 0)
//             return "#75b281";
//         if(series.key == "neto" && row.y < 0)
//           return "#dd9999";
//         else
//           return "#c2c2d6";
//       }
//   };
    </script>



























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