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
                           <div class="col-6  " ng-show="datos.editar == true">
                            <div class="input-group form-control-lg pt-0 mt-0">
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold mt-3" style=" font-size:15px;">Prestado a</label>                              
                                
                                <div class="form-group col-sm-8 col-10">
                                <input disabled ng-model="datos.selectedBanca.descripcion" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div>

                        <div class="col-6  " ng-show="datos.editar == true">
                            <div class="input-group form-control-lg pt-0 mt-0">
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold mt-3" style=" font-size:15px;">Fondo desde</label>                              
                                
                                <div class="form-group col-sm-8 col-10">
                                  <input ng-show="datos.selectedTipoEntidadFondo.descripcion == 'Banco'" disabled ng-model="datos.selectedBancoFondo.nombre" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                  <input ng-show="datos.selectedTipoEntidadFondo.descripcion == 'Banca'" disabled ng-model="datos.selectedBancaFondo.descripcion" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-center" ng-show="datos.editar == false">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-2 col-form-label  font-weight-bold " style="color: black;">Prestar a entidad</label>                              

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

                          <div class="col-6 text-center" ng-show="datos.editar == false">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black;">Tipo entidad fondo</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedTipoEntidadFondo"
                                    ng-options="o.descripcion for o in datos.optionsTiposEntidadesFondo"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-6 text-center" ng-show="datos.selectedTipoEntidadFondo.descripcion == 'Banco' && datos.editar == false">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black;">Emitir fondo desde</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedBancoFondo"
                                    ng-options="o.nombre for o in datos.optionsBancosFondos"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-6 text-center" ng-show="datos.selectedTipoEntidadFondo.descripcion == 'Banca' && datos.editar == false">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black;">Emitir fondo desde</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedBancaFondo"
                                    ng-options="o.descripcion for o in datos.optionsBancasFondos"
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
                                      <input ng-model="datos.montoPrestado" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>


                              </div>

                              <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;"># Cuotas</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-disabled="datos.editar == true && datos.tipoAmortizacion == 'Campo montoCuotas, ya sea con tasaInteres o no'" ng-model="datos.numeroCuotas" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                              <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Monto cuota</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-disabled="datos.editar == true && datos.tipoAmortizacion == 'Campo numeroCuotas, ya sea con tasaInteres o no'" ng-model="datos.montoCuotas" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                                

                                <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Tasa interes</label>                              
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-disabled="datos.editar == true && datos.tipoAmortizacion == 'Campo montoCuotas y numeroCuotas, se calcula la tasaInteres automatico'" ng-model="datos.tasaInteres" type="text" class="form-control" id="abreviatura" name="abreviatura">
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
                          <div class="text-center col-5" ng-show="datos.editar == false">
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

                              <div class="col-3  " ng-show="datos.editar == true">
                                <div class="input-group form-control-lg pt-0 mt-0">
                                  <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Frecuencia</label>                              
                                    
                                    <div class="form-group col-sm-8 col-10">
                                      <input disabled ng-model="datos.selectedFrecuencia.descripcion" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                    </div>
                                </div>
                            </div>

                             

                              <div class="col-6">
                                  <div class="input-group form-control-lg pt-0 mt-0">
                                    <label  class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:13px;">Fecha Primer Pago</label>                              
                                    <!-- <label  class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold " style="color: black; font-size:15px;">Fecha inicio</label>                               -->
                                      
                                      <div class="form-group col-sm-8 col-10">
                                      <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                      <input ng-disabled="datos.editar == true" ng-model="datos.fechaInicio" type="date" class="form-control" id="abreviatura" name="abreviatura">
                                      </div>
                                  </div>
                                </div>

                                

                                <div class="col-3 col-sm-1">
                                  <div class="input-group form-control-lg">
                                      <div class="form-group">
                                        <div class="form-check">
                                          <label class="form-check-label">
                                            <input ng-model="datos.status" class="form-check-input" type="checkbox" value="" checked> Activo
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
              <input ng-show="datos.editar == true" ng-click="aplazarCuota()" type="button" class="btn btn-success " name="guardar" value="Aplazar cuota">
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
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">#</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Banca</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Total prestado</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Tasa interes</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Total saldado</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Creado</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Ultimo pago</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold"># Cuotas pendientes</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Balance pendiente</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Monto cuota</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Frecuencia</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Fecha pago</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Estado</th>
                <th scope="col" style="font-size: 13px;" class="font-weight-bold">Editar</th>
              </tr>
            </thead>
            <tbody>
              <tr ng-repeat="l in datos.prestamos">
                <th scope="row">@{{$index + 1}}</th>
                <td>@{{l.banca}}</td>
                <td>@{{l.montoPrestado}}</td>
                <td>@{{l.tasaInteres}}</td>
                <td>@{{l.totalSaldado}}</td>
                <td>@{{toFecha(l.created_at) | date:"dd/MM/yyyy"}}</td>
                <td>-</td>
                <td>-</td>
                <td>@{{l.balancePendiente}}</td>                
                <td>@{{l.montoCuotas}}</td>
                <td>@{{l.frecuencia}}</td>
                <td>@{{l.fechaPagoProxima}}</td>
                <td>@{{(l.status == 1) ? 'Activo' : 'Desactivado'}}</td>
                <!-- <td>@{{l.horaCierre}}</td> -->
                <td>
                  <div class="row">
                    <a style="cursor: pointer" ng-click="editar(false, l)" class="ion-edit col-12 d-inline text-primary  text-white rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                    <!-- <a style="cursor: pointer" data-toggle="modal" data-target=".bd-example-modal-lg" class="ion-edit d-inline bg-success  text-white rounded abrir-wizard-editar"><i class="material-icons">payment</i></a> -->
                    <a style="cursor: pointer" ng-click="getPrestamo(l)" class="ion-edit col-12 d-inline text-success  text-white rounded abrir-wizard-editar"><i class="material-icons">payment</i></a>
                    <a style="cursor: pointer" ng-click="eliminar(l)" class="ion-android-delete col-12 d-inline  text-danger  text-white rounded"><i class="material-icons">delete_forever</i></a>
                  </div>
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
        
          
        


        <style>
                .modal-lg {
            max-width: 90% !important;
            z-index: 90000000000;
        }

        .modal{
            z-index: 90000000000;
        }
    </style>

        <div id="modal-prestamo" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg mt-1">
            <div class="modal-content">

                 <div class="modal-header">
                    <div class="col-sm-4 mx-0 px-0 ">
                        <h3 class="modal-title" id="exampleModalLabel">Pagar prestamo</h3>
                        
                    </div>

                    <div class="col-sm-4 text-info font-weight-bold mt-2">
                      <div class="">
                        <h2>Total: @{{(datos.totalAPagar) ? datos.totalAPagar : 0}}</h2>
                      </div>
                    </div>
                    
                        <div class="col-3 text-right mt-2">
                              <div class="form-group">
                                <input ng-click="pagar()" type="submit" class="btn btn-primary" value="Guardar">   
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

                        
                      <!-- <div class="col-6  ">
                            <div class="input-group form-control-lg pt-0 mt-0">
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold mt-3" style=" font-size:15px;">Prestado a</label>                              
                                
                                <div class="form-group col-sm-8 col-10">
                                <input disabled ng-model="datos.selectedPrestamoPagar.banca" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div>

                        <div class="col-6  ">
                            <div class="input-group form-control-lg pt-0 mt-0">
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold mt-3" style=" font-size:15px;">Monto prestado</label>                              
                                
                                <div class="form-group col-sm-8 col-10">
                                <input disabled ng-model="datos.selectedPrestamoPagar.montoPrestado" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div> -->

                        <div class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-2 col-form-label  font-weight-bold mt-2" style="color: black;">Banco</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedBancoCobrar"
                                    ng-options="o.nombre for o in datos.optionsBancosCobrar"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                        <div class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-2 col-form-label  font-weight-bold mt-2" style="color: black;">Tipo pago</label>                              

                                <div  class=" col-sm-8 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosJugadaChanged()"
                                    ng-model="datos.selectedTiposPagos"
                                    ng-options="o.descripcion for o in datos.optionsTiposPagos"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                        <div class="col-6  ">
                            <div class="input-group form-control-lg pt-0 mt-0">
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold mt-1" style="color: black; font-size:22px;">Pagar</label>                              
                                
                                <div class="form-group col-sm-8 col-10">
                                <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                <input ng-change="txtPagarChanged()" style="color: black; font-size:22px;" ng-model="datos.montoPagado" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div>

                        <div class="col-6  ">
                            <div class="input-group form-control-lg pt-0 mt-0">
                              <label class="d-none d-sm-block text-right col-sm-4 col-form-label  font-weight-bold mt-1" style="color: black; font-size:22px;">Devuelta</label>                              
                                
                                <div class="form-group col-sm-8 col-10">
                                <!-- <label for="abreviatura" class="bmd-label-floating font-weight-bold" style="color: black;">Monto prestamo</label> -->
                                <input disabled ng-model="datos.devuelta" type="text" class="form-control" id="abreviatura" name="abreviatura">
                                </div>
                            </div>
                        </div>





                    </div> <!-- END ROW CAMPOS -->
                   
                      <div class="row justify-content-center">
                        <div class=" text-center">
                                    <style>
                                      .btn-outline-info.active{
                                        background-color: #00bcd4!important;
                                        color: #fff!important;
                                      }
                                    </style>
                                          <!-- ng-init="rbxLoteriasChanged(l, $first)" -->
                                    <div class="btn-group btn-group-toggle btn-group-sm text-center btn-group-not" data-toggle="buttons">
                                        <label class="btn btn-outline-info" 
                                          ng-repeat="l in datos.radioNoPagadosPagados"
                                          ng-class="{'active': $first}"
                                          ng-click="radioNoPagadosPagadosChanged(l)"
                                          style="font-size: 15px;">
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
                      </div>

                    <table ng-show="datos.selectedNoPagadosPagados.descripcion == 'Sin pagar'" class="table table-sm mt-3">
                        <thead class="">
                            <tr>
                            <th scope="col" class="text-center font-weight-bold" >Fecha</th>
                            <!-- <th scope="col" class="text-center font-weight-bold" >Cerrado</th> -->
                            <th scope="col" class="text-center font-weight-bold" >Capital</th>
                            <th scope="col" class="text-center font-weight-bold" >Interes</th>
                            <th scope="col" class="text-center font-weight-bold" >Cuota</th>
                            <th scope="col" class="text-center font-weight-bold" >Pagar</th>
                           

                            <!--<th scope="col" class="text-center">Cancelar/Eliminar</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-click='seleccionarCuota(c)' ng-class="{'bg-disabled' : c.enable == false}" ng-repeat="c in datos.selectedPrestamoPagar.amortizacion">
                                <td scope="col" class="text-center" >@{{c.fecha}}</td>
                                <td scope="col" class="text-center" >@{{c.capitalAPagar}}</td>
                                <!-- <td scope="col" class="text-center" >@{{Cerrado}}</td> -->
                                <td scope="col" class="text-center" >@{{c.interesAPagar}}</td>
                                <td scope="col" class="text-center" >@{{c.cuotaAPagar}}</td>
                                <td scope="col" class="text-center" >
                                  <div class="form-check">
                                    <label class="form-check-label">
                                      <input ng-model="c.seleccionado" class="form-check-input" type="checkbox" value="">
                                      <span class="form-check-sign">
                                        <span  class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </td>
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">Marcar pago</td> -->
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">
                                    <a style="cursor: pointer" ng-click="eliminar(l)" class="ion-android-delete d-inline   bg-danger  text-white rounded"><i class="material-icons">delete_forever</i></a>
                                </td> -->
                            </tr>
                            <tr >
                                <td colspan="2"></td>
                                <td class="text-center font-weight-bold">TOTAL:</td>
                                <td class="text-center font-weight-bold">@{{total(true) | currency}}</td>
                                <td class="text-center font-weight-bold">@{{total(false) | currency}}</td>
                                <td colspan="4"></td>
                            </tr>
                            
                        </tbody>
                    </table>





                    <table ng-show="datos.selectedNoPagadosPagados.descripcion == 'Pagadas'" class="table table-sm mt-3">
                        <thead class="">
                            <tr>
                            <th scope="col" class="text-center font-weight-bold" >Fecha</th>
                            <!-- <th scope="col" class="text-center font-weight-bold" >Cerrado</th> -->
                            <th scope="col" class="text-center font-weight-bold" >Capital</th>
                            <th scope="col" class="text-center font-weight-bold" >Interes</th>
                            <th scope="col" class="text-center font-weight-bold" >Cuota</th>
                           

                            <!--<th scope="col" class="text-center">Cancelar/Eliminar</th> -->

                            </tr>
                        </thead>
                        <tbody>
                            <tr   ng-repeat="c in datos.selectedPrestamoPagar.amortizacionPagadas">
                                <td scope="col" class="text-center" >@{{c.fecha}}</td>
                                <td scope="col" class="text-center" >@{{c.montoCapital}}</td>
                                <!-- <td scope="col" class="text-center" >@{{Cerrado}}</td> -->
                                <td scope="col" class="text-center" >@{{c.montoInteres}}</td>
                                <td scope="col" class="text-center" >@{{c.montoCuota}}</td>
                                
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">Marcar pago</td> -->
                                <!-- <td scope="col" class="text-center" style="font-size: 12px">
                                    <a style="cursor: pointer" ng-click="eliminar(l)" class="ion-android-delete d-inline   bg-danger  text-white rounded"><i class="material-icons">delete_forever</i></a>
                                </td> -->
                            </tr>
                            <tr >
                                <td colspan="2"></td>
                                <td class="text-center font-weight-bold">TOTAL:</td>
                                <td class="text-center font-weight-bold">@{{total(true) | currency}}</td>
                                <td class="text-center font-weight-bold">@{{total(false) | currency}}</td>
                                <td colspan="4"></td>
                            </tr>
                            
                        </tbody>
                    </table>

                    <div class="col-12 text-right mt-2">
                              <div class="form-group">
                                <input ng-click="pagar()" type="submit" class="btn btn-primary" value="Guardar">   
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