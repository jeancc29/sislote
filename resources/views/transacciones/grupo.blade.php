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
                    <div class="row justify-content-end">
                        <button class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Crear grupo</button>
                    </div>
                    <div class="row">
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
                              <div class="col-4">
                                <div class="form-group">
                                    <input ng-click="buscar()" type="submit" class="btn btn-info" value="Buscar">   
                                </div>
                            </div>
                    </div> <!-- END ROW FECHAS -->
                <table class="table table-sm">
                        <thead>
                            <tr>
                            <th scope="col" class="text-center">Numero</th>
                            <th scope="col" class="text-center">Fecha</th>
                            <!-- <th scope="col" class="text-center">Cerrado</th> -->
                            <th scope="col" class="text-center">Hora</th>
                            <th scope="col" class="text-center">Creado por</th>
                            <th scope="col" class="text-center">Automatico</th>
                            <th scope="col" class="text-center">Notas</th>
                           

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="c in datos.grupos | filter:datos.datosBusqueda">
                                <td ng-click="verTransacciones(c)" data-toggle="modal" data-target=".transacciones-modal-lg" scope="col" class="text-center" style="font-size: 14px">@{{c.id}}</td>
                                <td scope="col" class="text-center">@{{toFecha(c.created_at.date) | date:"dd/MM/yyyy hh:mm a"}}</td>
                                <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                <td scope="col" class="text-center">@{{toFecha(c.created_at.date) | date:"hh:mm a"}}</td>
                                <td scope="col" class="text-center" style="font-size: 14px">@{{c.usuario.usuario}}</td>
                                <td scope="col" class="text-center">@{{c.total}}</td>
                                <td scope="col" class="text-center">@{{c.premio}}</td>
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


        <!-- MODAL MONITOREO -->
    <style>
                .modal-lg {
            max-width: 90% !important;
            z-index: 90000000000;
        }

        .modal{
            z-index: 90000000000;
        }
    </style>

    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-1">
            <div class="modal-content">

                 <div class="modal-header">
                    <div class="col-sm-6">
                        <h3 class="modal-title" id="exampleModalLabel">Crear grupo de transacciones</h3>
                    </div>
                        <div class="col-6 text-right mt-2">
                              <div class="form-group">
                                <input ng-click="actualizar()" type="submit" class="btn btn-primary" value="Guardar">   
                            </div>
                          </div>
                    
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">


                   <div class="row justify-content-center">

                        <div class="col-4">

                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="porcentajecaid" class="bmd-label-floating">Concepto</label>
                                <!-- <input ng-model="datos.porcentajeCaida" class="form-control" id="porcentajecaid" name="porcentajecaid"> -->
                                <select 
                                        ng-model="datos.selectedTipo"
                                        ng-options="o.descripcion for o in datos.optionsTipos"
                                        ng-change="cbxTiposChange()"
                                        class="selectpicker w-100" 
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                                </div>
                            </div>

                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="porcentajecaid" class="bmd-label-floating">Entidad #1</label>
                                <!-- <input ng-model="datos.porcentajeCaida" class="form-control" id="porcentajecaid" name="porcentajecaid"> -->
                                <select 
                                        ng-model="datos.selectedBanca"
                                        ng-options="o.descripcion for o in datos.optionsBancas"
                                        ng-change="cbxBancasChange(o)"
                                        class="selectpicker w-100" 
                                        id="entidad1"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                                </div>
                            </div>


                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="porcentajecaid" class="bmd-label-floating">Entidad #2</label>
                                <!-- <input ng-model="datos.porcentajeCaida" class="form-control" id="porcentajecaid" name="porcentajecaid"> -->
                                <select 
                                        ng-model="datos.selectedEntidad"
                                        ng-options="o.nombre for o in datos.optionsEntidades"
                                        ng-change="cbxEntidadesChange(o)"
                                        class="selectpicker w-100" 
                                        id="entidad2"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                                </div>
                            </div>


                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="descontar" class="bmd-label-floating">Notas</label>
                                <input ng-model="datos.nota" type="text" class="form-control" id="notas" name="notas">
                                </div>
                            </div>

                            
                            <!-- <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="deCada" class="bmd-label-floating">De cada</label>
                                <input ng-model="datos.deCada" type="text" class="form-control" id="deCada" name="deCada">
                                </div>
                            </div>


                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="minutosCancelarTicket" class="bmd-label-floating">Minutos para cancelar ticket</label>
                                <input ng-model="datos.minutosCancelarTicket" type="text" class="form-control" id="minutosCancelarTicket" name="minutosCancelarTicket">
                                </div>
                            </div> -->

                        </div> <!-- END COL 6 -->

                  
                        <div class="col-4">

                            

                            <div class="input-group form-control-lg m-0 p-0">
                                <div id="entidad1_saldo_inicial" class="form-group w-100">
                                <label for="balanceDesactivacion" class="bmd-label-floating">Saldo inicial ENTIDAD #1</label>
                                <input disabled ng-model="datos.entidad1_saldo_inicial" type="text" class="form-control" id="balanceDesactivacion" name="balanceDesactivacion">
                                </div>
                            </div>
                            <div class="input-group form-control-lg m-0 p-0">
                                <div id="entidad2_saldo_inicial" class="form-group w-100">
                                <label for="entidad2_saldo_inicial" class="bmd-label-floating">Saldo inicial ENTIDAD #2</label>
                                <input disabled ng-model="datos.entidad2_saldo_inicial" type="text" class="form-control" id="entidad2_saldo_inicial" name="entidad2_saldo_inicial">
                                </div>
                            </div>


                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label  for="debito" class="bmd-label-floating">DEBITO</label>
                                <input 
                                    select-all-on-click
                                    ng-keyup="addTransaccion($event)"
                                    ng-blur="saldoFinal(true)" 
                                    ng-disabled="datos.selectedTipo.descripcion == 'Cobro'" ng-model="datos.debito" type="number" class="form-control" id="debito" name="debito">
                                </div>
                            </div>


                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="credito" class="bmd-label-floating">CREDITO</label>
                                <input 
                                    select-all-on-click
                                    ng-keyup="addTransaccion($event)"
                                    ng-blur="saldoFinal(false)" 
                                    ng-disabled="datos.selectedTipo.descripcion == 'Pago'" ng-model="datos.credito" type="text" class="form-control" id="credito" name="credito">
                                </div>
                            </div>


                             <div class="input-group form-control-lg m-0 p-0">
                                <div id="entidad1_saldo_final" class="form-group w-100">
                                <label for="entidad1_saldo_final" class="bmd-label-floating">Saldo final ENTIDAD #1</label>
                                <input disabled ng-model="datos.entidad1_saldo_final" type="text" class="form-control" id="entidad1_saldo_final" name="entidad1_saldo_final">
                                </div>
                            </div>
                            <div class="input-group form-control-lg m-0 p-0">
                                <div id="entidad2_saldo_final" class="form-group w-100">
                                <label for="entidad2_saldo_final" class="bmd-label-floating">Saldo final ENTIDAD #2</label>
                                <input disabled ng-model="datos.entidad2_saldo_final" type="text" class="form-control" id="entidad2_saldo_final" name="entidad2_saldo_final">
                                </div>
                            </div>

                        </div> <!-- END COL 6 -->




                    </div> <!-- END ROW CAMPOS -->
                   

                    <table class="table table-sm">
                        <thead class="thead-dark">
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
                            <tr ng-repeat="c in datos.addTransaccion">
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
                                <td scope="col" class="text-center" style="font-size: 12px">
                                    @{{c.nota}}
                                    <button ng-click="removeTransaction($index)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link m-0 p-0 d-inline ">
                                        <i class="material-icons">close</i>
                                    </button>
                                </td>
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

                    <div class="col-12 text-right mt-2">
                              <div class="form-group">
                                <input ng-click="actualizar()" type="submit" class="btn btn-primary" value="Guardar">   
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
    <!-- MODAL MONITOREO -->



    <!-- MODAL VISTA GRUPOS DE TRANSACCIONES -->
    <div class="modal fade transacciones-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-1">
            <div class="modal-content">

                 <div class="modal-header">
                 <h3 class="modal-title" id="exampleModalLabel">Transacciones</h3>
                       
                    
                    <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                    </div> -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>

                    <div class="modal-body">


                   
                   

                    <table class="table table-sm">
                        <thead class="thead-dark">
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
                            <tr ng-repeat="c in datos.selectedGrupo.transacciones">
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
                                <td scope="col" class="text-center" style="font-size: 12px">
                                    @{{c.nota}}
                                    <button ng-click="removeTransaction($index)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link m-0 p-0 d-inline ">
                                        <i class="material-icons">close</i>
                                    </button>
                                </td>
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">Marcar pago</td> -->
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">
                                    <a style="cursor: pointer" ng-click="eliminar(l)" class="ion-android-delete d-inline   bg-danger  text-white rounded"><i class="material-icons">delete_forever</i></a>
                                </td> -->
                            </tr>
                            <tr >
                                <td colspan="4"></td>
                                <td class="text-center font-weight-bold">TOTAL:</td>
                                <td class="text-center font-weight-bold">@{{total(true, true) | currency}}</td>
                                <td class="text-center font-weight-bold">@{{total(false, true) | currency}}</td>
                                <td colspan="4"></td>
                            </tr>
                            
                        </tbody>
                    </table>

                    

                    <div class="container">

                        <!-- <div style="display: @{{seleccionado}}" class="alert alert-primary d-inline ml-5 " role="alert">
                        @{{titulo_seleccionado}} : @{{seleccionado.nombre}} - @{{seleccionado.identificacion}}
                        </div> -->
                    </div>

                </div> <!-- END MODAL-BODY -->
                
            </div> <!-- END MODAL-CONTENT-->
        </div>
    </div>
    <!-- MODAL MONITOREO -->



        

        
        















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