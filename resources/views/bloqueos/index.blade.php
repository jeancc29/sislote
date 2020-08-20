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
                Bloqueos
              </h3>
            </div> <!-- END COL-12 -->
            </div> <!-- END ROW -->
           
          </div><!-- END CARD HEADER -->
          <div class="wizard-navigation">
            <ul class="nav nav-pills">
              <li ng-click="tabChange(tabActiva)" ng-init="tabActiva = 1" class="nav-item">
                <a ng-click="tabActiva = 1" class="nav-link" href="#buscar" data-toggle="tab" role="tab">
                  Buscar
                </a>
              </li>
              <li ng-click="tabChange(tabActiva)" class="nav-item">
                <a ng-click="tabActiva = 2" class="nav-link" href="#loterias" data-toggle="tab" role="tab">
                  Loterias
                </a>
              </li>
              <li ng-click="tabChange(tabActiva)" class="nav-item">
                <a ng-click="tabActiva = 3" class="nav-link" href="#jugadas" data-toggle="tab" role="tab">
                  Jugadas
                </a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              
              <div class="tab-pane active" id="buscar">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                   
                    
                    <div class="col-12">

                    

                    </div>
                    
                   

                      <div class="col-12">

                      <div class="row">
                        <div class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-2 col-form-label  font-weight-bold " style="color: black;">Tipo regla</label>                              

                                <div  class=" col-sm-9 col-9">
                                <select 
                                    ng-change="cbxTipoBloqueosChanged()"
                                    ng-model="datos.buscar.selectedTipoBloqueos"
                                    ng-options="o.descripcion for o in datos.buscar.optionsTipoBloqueos"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div><!-- END COL 12 -->

                          <div class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-2 col-form-label  font-weight-bold " style="color: black;">Moneda</label>                              

                                <div  class=" col-sm-9 col-9">
                                <select 
                                    ng-model="datos.buscar.selectedMoneda"
                                    ng-options="o.descripcion for o in datos.buscar.optionsMonedas"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div><!-- END COL 12 -->

                          <div ng-show="datos.buscar.selectedTipoBloqueos.descripcion != 'Por banca jugadas' && datos.buscar.selectedTipoBloqueos.descripcion != 'General jugadas'" ng-class="{'col-5': datos.buscar.selectedTipoBloqueos.descripcion == 'Por banca loterias' || datos.buscar.selectedTipoBloqueos.descripcion == 'Por banca jugadas', 'col-10': datos.buscar.selectedTipoBloqueos.descripcion == 'General loterias' || datos.buscar.selectedTipoBloqueos.descripcion == 'General jugadas'}" class="text-center">
                              <div class="input-group">
                              <!-- <label  for="jugada" class="bmd-label-floating font-weight-bold" style="color: black;">Dias</label> -->
                                <label class="d-none d-sm-block text-right col-sm-2 col-form-label  font-weight-bold " style="color: black;">Dias</label>                              
                                  <style>
                                    #multiselectDias{
                                      font-size: 8px;
                                    }
                                  </style>
                                  <div  class="col-9">
                                    <select 
                                    id="multiselectDias"
                                        ng-model="datos.buscar.dias"
                                        ng-options="o.descripcion for o in datos.buscar.optionsDias track by o.id"
                                        class="selectpicker col-12" 
                                        data-style="select-with-transition" 
                                        multiple title="Selecc. dias"
                                        data-size="7" aria-setsize="2">
                                    </select>
                                </div>
                              </div> <!-- END INPUT GROUP -->
                            </div><!-- END COL 5 -->

                            <div ng-show="datos.buscar.selectedTipoBloqueos.descripcion == 'Por banca loterias' || datos.buscar.selectedTipoBloqueos.descripcion == 'Por banca jugadas'" class="col-5">
                              <div class="input-group">
                                
                                <label class="d-none d-sm-block  col-sm-3 col-form-label  font-weight-bold " style="color: black;">Bancas</label>                              
                                <!-- <label  for="bancas" class="bmd-label-floating font-weight-bold" style="color: black;">Bancas</label> -->
                                  <div  class="col-9">
                                    <select 
                                    id="multiselectBancas"
                                        ng-model="datos.buscar.bancas"
                                        ng-options="o.descripcion for o in datos.buscar.optionsBancas track by o.id"
                                        class="selectpicker col-12" 
                                        data-style="select-with-transition" 
                                        multiple title="Selecc. bancas"
                                        data-size="7" aria-setsize="2">
                                    </select>
                                </div>
                              </div> <!-- END INPUT GROUP -->
                            </div><!-- END COL 5 -->

                          <div class="col-2">
                            <a href="#" class="btn btn-success mt-2" ng-click="buscar()">Buscar</a>
                          </div>
                      </div><!-- END ROW CONTENEDOR DATOS BUSQUEDA -->


                        <div class="card text-center" ng-show="datos.buscar.resultados.length > 0 && (datos.buscar.selectedTipoBloqueos.idTipoBloqueo == 1 || datos.buscar.selectedTipoBloqueos.idTipoBloqueo == 3)"><!-- CARD1 -->
                          <div class="card-header2" style="background-color: #f4f4f4;">
                            <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                              <li 
                                ng-repeat="d in datos.buscar.resultados" 
                                ng-click="tabDiasChanged(d)"
                                class="nav2-item" style="padding: 0px;">
                                <a ng-class="{'active': datos.tabSelectedDia.id == d.id}" class="nav2-link" href="#">@{{d.descripcion}}</a>
                              </li>
                              <!-- <li class="nav2-item" style="padding: 0px;">
                                <a class="nav2-link" href="#">Link</a>
                              </li>
                              <li class="nav2-item" style="padding: 0px;">
                                <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                              </li> -->
                            </ul>
                          </div>
                          <div class="card-body" style="padding-bottom: 0px;">
                            
                            <div ng-show="datos.buscar.selectedTipoBloqueos.idTipoBloqueo == 3" class="card text-center" style="padding-top: 0px; margin-top:0px;"><!-- CARD2 -->
                              <div class="card-header2" style="background-color: #f4f4f4;">
                                <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                                  <li ng-repeat="b in datos.tabSelectedDia.bancas" 
                                      ng-click="tabBancasChanged(b)" 
                                      class="nav2-item" style="padding: 0px;">
                                    <a ng-class="{'active': datos.tabSelectedBanca.id == b.id}" class="nav2-link" href="#">@{{b.descripcion}}</a>
                                  </li>
                                  <!-- <li class="nav2-item" style="padding: 0px;">
                                    <a class="nav2-link" href="#">Link</a>
                                  </li>
                                  <li class="nav2-item" style="padding: 0px;">
                                    <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                  </li> -->
                                </ul>
                              </div>
                              <div class="card-body">
                                
                                <div class="card text-center" style="padding-top: 0px; margin-top:0px;"><!-- CARD3 -->
                                  <div class="card-header2" style="background-color: #f4f4f4;">
                                    <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                                      <li ng-repeat="l in datos.tabSelectedBanca.loterias" 
                                          ng-click="tabLoteriasChanged(l)" 
                                      class="nav2-item" style="padding: 0px;">
                                        <a ng-class="{'active': datos.tabSelectedLoteria.id == l.id}" class="nav2-link" href="#">@{{l.descripcion}}<span ng-class="{'bg-danger text-white': l.cantidadDeBloqueos == 0}" class="rounded ml-2 p-1 font-weight-bold">@{{l.cantidadDeBloqueos}}</span></a>
                                      </li>
                                      <!-- <li class="nav2-item" style="padding: 0px;">
                                        <a class="nav2-link" href="#">Link</a>
                                      </li>
                                      <li class="nav2-item" style="padding: 0px;">
                                        <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                      </li> -->
                                    </ul>
                                  </div>
                                  <div class="card-body">
                                    <div class="col-12">
                                      <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                <th scope="col" class="text-center">ID</th>
                                                <th scope="col" class="text-center">SORTEO</th>
                                                <th scope="col" class="text-center">BLOQUEO</th>
                                                <th scope="col" class="text-center">ElIMINAR</th>
                                                <!-- <th scope="col" class="text-center"></th> -->
                                                <!-- <th scope="col" class="text-center">Cerrado</th> -->
                                                
                                                

                                                </tr>
                                            </thead>
                                            <tbody>
                                              <!-- | filter:datos.monitoreo.datosBusqueda -->
                                                <tr ng-repeat="c in datos.tabSelectedLoteria.sorteos">
                                                    <td scope="col" class="text-center" style="font-size: 14px">@{{$index + 1}}</td>
                                                    <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                                    <td scope="col" class="text-center">@{{c.descripcion}}</td>
                                                    <td scope="col" class="text-center">
                                                      <a style="cursor: pointer" ng-click="gastoEliminar(c)" class="d-inline  bg-primary p-1 text-white rounded">@{{c.bloqueo | currency}}</a>
                                                    </td>
                                                    <td class=" text-center">
                                                      <button ng-click="eliminarBloqueo(c, $index)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                                          <i class="material-icons font-weight-bold">close</i>
                                                      </button>
                                                    </td>
                                                    <!-- <td scope="col" class="text-center">@{{c.frecuencia.descripcion}}</td>
                                                    <td scope="col" class="text-center">@{{toFecha(c.fechaInicio) | date:"dd/MM/yyyy"}}</td>
                                                    <td scope="col" class="text-center">@{{toFecha(c.fechaProximoGasto) | date:"dd/MM/yyyy"}}</td>
                                                    -->
                                                    
                                                    <!-- <td>
                                                      <a style="cursor: pointer"  ng-click="gastoEditar(false, c)" class="ion-edit d-inline bg-primary py-1 text-white rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                                                      <a style="cursor: pointer" ng-click="gastoEliminar(c)" class="ion-android-delete d-inline  ml-2 bg-danger py-1 text-white rounded"><i class="material-icons">delete_forever</i></a>
                                                    </td> -->
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div> <!-- END COL-12 -->
                                  </div> <!-- END CARD-BODY -->
                                </div> <!-- END CARD3 -->

                            
                              </div><!-- END CARD-BODY -->
                            </div><!-- END CARD2 -->

                            <!---------------------------------- GENERAL POR LOTERIA EN EL CARD 1 -------------------------------------->
                              <div ng-show="datos.buscar.selectedTipoBloqueos.idTipoBloqueo == 1" class="card text-center" style="padding-top: 0px; margin-top:0px;"><!-- CARD3 -->
                                  <div class="card-header2" style="background-color: #f4f4f4;">
                                    <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                                      <li ng-repeat="l in datos.tabSelectedDia.loterias" 
                                          ng-click="tabLoteriasChanged(l)" 
                                      class="nav2-item" style="padding: 0px;">
                                        <a ng-class="{'active': datos.tabSelectedLoteria.id == l.id}" class="nav2-link" href="#">@{{l.descripcion}} <span ng-class="{'bg-danger text-white': l.cantidadDeBloqueos == 0}" class="rounded ml-2 p-1 font-weight-bold">@{{l.cantidadDeBloqueos}}</span></a>
                                      </li>
                                      <!-- <li class="nav2-item" style="padding: 0px;">
                                        <a class="nav2-link" href="#">Link</a>
                                      </li>
                                      <li class="nav2-item" style="padding: 0px;">
                                        <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                      </li> -->
                                    </ul>
                                  </div>
                                  <div class="card-body">
                                    <div class="col-12">
                                      <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                <th scope="col" class="text-center">ID</th>
                                                <th scope="col" class="text-center">SORTEO</th>
                                                <th scope="col" class="text-center">BLOQUEO</th>
                                                <th scope="col" class="text-center">ELIMINAR</th>
                                                <!-- <th scope="col" class="text-center"></th> -->
                                                <!-- <th scope="col" class="text-center">Cerrado</th> -->
                                                
                                                

                                                </tr>
                                            </thead>
                                            <tbody>
                                              <!-- | filter:datos.monitoreo.datosBusqueda -->
                                                <tr ng-repeat="c in datos.tabSelectedLoteria.sorteos">
                                                    <td scope="col" class="text-center" style="font-size: 14px">@{{$index + 1}}</td>
                                                    <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                                    <td scope="col" class="text-center">@{{c.descripcion}}</td>
                                                    <td scope="col" class="text-center">
                                                      <a style="cursor: pointer"  class="d-inline  bg-primary p-1 text-white rounded">@{{c.bloqueo | currency}}</a>
                                                    </td>
                                                    <!-- <td scope="col" class="text-center">@{{c.frecuencia.descripcion}}</td>
                                                    <td scope="col" class="text-center">@{{toFecha(c.fechaInicio) | date:"dd/MM/yyyy"}}</td>
                                                    <td scope="col" class="text-center">@{{toFecha(c.fechaProximoGasto) | date:"dd/MM/yyyy"}}</td>
                                                    -->
                                                    
                                                    <!-- <td>
                                                      <a style="cursor: pointer"  ng-click="gastoEditar(false, c)" class="ion-edit d-inline bg-primary py-1 text-white rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                                                      <a style="cursor: pointer" ng-click="gastoEliminar(c)" class="ion-android-delete d-inline  ml-2 bg-danger py-1 text-white rounded"><i class="material-icons">delete_forever</i></a>
                                                    </td> -->

                                                    <td class=" text-center">
                                                      <button ng-click="eliminarBloqueo(c, $index)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                                          <i class="material-icons font-weight-bold">close</i>
                                                      </button>
                                                    </td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>
                                    </div> <!-- END COL-12 -->
                                  </div> <!-- END CARD-BODY -->
                                </div> <!-- END CARD3 -->
                            
                          </div><!-- END CARD-BODY -->
                        </div><!-- END CARD1 -->






                        
                        <div id="blabla" ng-show="datos.buscar.selectedTipoBloqueos.idTipoBloqueo == 2" class="card text-center" style="padding-top: 0px; margin-top:0px;"><!-- CARD3 -->
                          <div class="card-header2" style="background-color: #f4f4f4;">
                            <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                              <li ng-repeat="l in datos.buscar.resultados" 
                                  ng-click="tabLoteriasChanged(l)" 
                              class="nav2-item" style="padding: 0px;">
                                <a ng-class="{'active': datos.tabSelectedLoteria.id == l.id}" class="nav2-link" href="#">@{{l.descripcion}}<span ng-class="{'bg-danger text-white': l.cantidadDeBloqueos == 0}" class="rounded ml-2 p-1 font-weight-bold">@{{l.cantidadDeBloqueos}}</span></a>
                              </li>
                              <!-- <li class="nav2-item" style="padding: 0px;">
                                <a class="nav2-link" href="#">Link</a>
                              </li>
                              <li class="nav2-item" style="padding: 0px;">
                                <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                              </li> -->
                            </ul>
                          </div>
                          <div class="card-body">
                            <div class="col-12">

                            <table class="table table-sm table-striped">
                                  <thead>
                                      <tr>
                                      <th scope="col" class="text-center">ID</th>
                                      <th scope="col" class="text-center">JUGADA</th>
                                      <th scope="col" class="text-center">BLOQUEO</th>
                                      <th scope="col" class="text-center">SORTEO</th>
                                      <th scope="col" class="text-center">DESDE</th>
                                      <th scope="col" class="text-center">HASTA</th>
                                      <th scope="col" class="text-center">ELIMINAR</th>
                                      <!-- <th scope="col" class="text-center"></th> -->
                                      <!-- <th scope="col" class="text-center">Cerrado</th> -->
                                      
                                      

                                      </tr>
                                  </thead>
                                  <tbody>
                                    <!-- | filter:datos.monitoreo.datosBusqueda -->
                                      <tr ng-repeat="c in datos.tabSelectedLoteria.jugadas">
                                          <td scope="col" class="text-center" style="font-size: 14px">@{{$index + 1}}</td>
                                          <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                          <td scope="col" class="text-center">@{{c.jugada}}</td>
                                          <td scope="col" class="text-center">
                                            <a style="cursor: pointer"  class="d-inline  bg-primary p-1 text-white rounded">@{{c.monto | currency}}</a>
                                          </td>
                                          <td scope="col" class="text-center">@{{c.sorteo}}</td>
                                          <td scope="col" class="text-center">@{{c.fechaDesde | date:"dd/MM/yyyy"}}</td>
                                          <td scope="col" class="text-center">@{{c.fechaHasta | date:"dd/MM/yyyy"}}</td>
                                          
                                          <!-- <td scope="col" class="text-center">@{{c.frecuencia.descripcion}}</td>
                                          <td scope="col" class="text-center">@{{toFecha(c.fechaInicio) | date:"dd/MM/yyyy"}}</td>
                                          <td scope="col" class="text-center">@{{toFecha(c.fechaProximoGasto) | date:"dd/MM/yyyy"}}</td>
                                          -->
                                          
                                          <!-- <td>
                                            <a style="cursor: pointer"  ng-click="gastoEditar(false, c)" class="ion-edit d-inline bg-primary py-1 text-white rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                                            <a style="cursor: pointer" ng-click="gastoEliminar(c)" class="ion-android-delete d-inline  ml-2 bg-danger py-1 text-white rounded"><i class="material-icons">delete_forever</i></a>
                                          </td> -->

                                          <td class=" text-center">
                                            <button ng-click="eliminarBloqueo(c, $index)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                                <i class="material-icons font-weight-bold">close</i>
                                            </button>
                                          </td>
                                      </tr>
                                      
                                  </tbody>
                              </table>
                            </div> <!-- END COL-12 -->
                          </div> <!-- END CARD-BODY -->
                        </div> <!-- END CARD3 -->

                        <div ng-show="datos.buscar.selectedTipoBloqueos.idTipoBloqueo == 4" class="card text-center" style="padding-top: 0px; margin-top:0px;"><!-- CARD2 -->
                              <div class="card-header2" style="background-color: #f4f4f4;">
                                <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                                  <li ng-repeat="b in datos.buscar.resultados" 
                                      ng-click="tabBancasChanged(b)" 
                                      class="nav2-item" style="padding: 0px;">
                                    <a ng-class="{'active': datos.tabSelectedBanca.id == b.id}" class="nav2-link" href="#">@{{b.descripcion}}</a>
                                  </li>
                                  <!-- <li class="nav2-item" style="padding: 0px;">
                                    <a class="nav2-link" href="#">Link</a>
                                  </li>
                                  <li class="nav2-item" style="padding: 0px;">
                                    <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                  </li> -->
                                </ul>
                              </div>
                              <div class="card-body">
                                
                                <div class="card text-center" style="padding-top: 0px; margin-top:0px;"><!-- CARD3 -->
                                  <div class="card-header2" style="background-color: #f4f4f4;">
                                    <ul class="nav2 nav2-tabs card-header-tabs" style="padding-bottom: 0px;">
                                      <li ng-repeat="l in datos.tabSelectedBanca.loterias" 
                                          ng-click="tabLoteriasChanged(l)" 
                                      class="nav2-item" style="padding: 0px;">
                                        <a ng-class="{'active': datos.tabSelectedLoteria.id == l.id}" class="nav2-link" href="#">@{{l.descripcion}}<span ng-class="{'bg-danger text-white': l.cantidadDeBloqueos == 0}" class="rounded ml-2 p-1 font-weight-bold">@{{l.cantidadDeBloqueos}}</span></a>
                                      </li>
                                      <!-- <li class="nav2-item" style="padding: 0px;">
                                        <a class="nav2-link" href="#">Link</a>
                                      </li>
                                      <li class="nav2-item" style="padding: 0px;">
                                        <a class="nav2-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                      </li> -->
                                    </ul>
                                  </div>
                                  <div class="card-body">
                                    <div class="col-12">
                                      <table class="table table-sm table-striped">
                                          <thead>
                                              <tr>
                                              <th scope="col" class="text-center">ID</th>
                                              <th scope="col" class="text-center">JUGADA</th>
                                              <th scope="col" class="text-center">BLOQUEO</th>
                                              <th scope="col" class="text-center">SORTEO</th>
                                              <th scope="col" class="text-center">DESDE</th>
                                              <th scope="col" class="text-center">HASTA</th>
                                              <th scope="col" class="text-center">ELIMINAR</th>
                                              <!-- <th scope="col" class="text-center"></th> -->
                                              <!-- <th scope="col" class="text-center">Cerrado</th> -->
                                              
                                              

                                              </tr>
                                          </thead>
                                          <tbody>
                                            <!-- | filter:datos.monitoreo.datosBusqueda -->
                                              <tr ng-repeat="c in datos.tabSelectedLoteria.jugadas">
                                                  <td scope="col" class="text-center" style="font-size: 14px">@{{$index + 1}}</td>
                                                  <!-- <td scope="col" class="text-center">@{{Cerrado}}</td> -->
                                                  <td scope="col" class="text-center">@{{c.jugada}}</td>
                                                  <td scope="col" class="text-center">
                                                    <a style="cursor: pointer"  class="d-inline  bg-primary p-1 text-white rounded">@{{c.monto | currency}}</a>
                                                  </td>
                                                  <td scope="col" class="text-center">@{{c.sorteo}}</td>
                                                  <td scope="col" class="text-center">@{{c.fechaDesde | date:"dd/MM/yyyy"}}</td>
                                                  <td scope="col" class="text-center">@{{c.fechaHasta | date:"dd/MM/yyyy"}}</td>
                                                  
                                                  <!-- <td scope="col" class="text-center">@{{c.frecuencia.descripcion}}</td>
                                                  <td scope="col" class="text-center">@{{toFecha(c.fechaInicio) | date:"dd/MM/yyyy"}}</td>
                                                  <td scope="col" class="text-center">@{{toFecha(c.fechaProximoGasto) | date:"dd/MM/yyyy"}}</td>
                                                  -->
                                                  
                                                  <!-- <td>
                                                    <a style="cursor: pointer"  ng-click="gastoEditar(false, c)" class="ion-edit d-inline bg-primary py-1 text-white rounded abrir-wizard-editar"><i class="material-icons">edit</i></a>
                                                    <a style="cursor: pointer" ng-click="gastoEliminar(c)" class="ion-android-delete d-inline  ml-2 bg-danger py-1 text-white rounded"><i class="material-icons">delete_forever</i></a>
                                                  </td> -->

                                                  <td class=" text-center">
                                                    <button ng-click="eliminarBloqueo(c, $index)" type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                                        <i class="material-icons font-weight-bold">close</i>
                                                    </button>
                                                  </td>
                                              </tr>
                                              
                                          </tbody>
                                      </table>
                                    </div> <!-- END COL-12 -->
                                  </div> <!-- END CARD-BODY -->
                                </div> <!-- END CARD2 -->

                            
                              </div><!-- END CARD-BODY -->
                            </div><!-- END CARD1 POR BANCAS JUGADAS -->
                       


                      </div> <!-- END DIV FORMULARIO -->


                      
                    </div> <!-- END ROW SECUNDARIO PRINCIPAL -->
                  </div> <!-- END COL PRINCIPAL -->
                </div> <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 3 -->

              <div class="tab-pane " id="loterias">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                   
                    
                    
                   

                      <div class="col-12">
                        <form novalidate>

                        

                      <div class="row">
                      

                          

                            
                          <div class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Tipo regla</label>                              

                                <div  class=" col-sm-8 col-10">
                                <select 
                                    ng-change="cbxTipoBloqueosChanged()"
                                    ng-model="datos.selectedTipoBloqueos"
                                    ng-options="o.descripcion for o in datos.optionsTipoBloqueos"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Moneda</label>                              

                                <div  class=" col-sm-8 col-10">
                                <select 
                                    
                                    ng-model="datos.selectedMoneda"
                                    ng-options="o.descripcion for o in datos.optionsMonedas"
                                    class="selectpicker col-12" 
                                    data-style="select-with-transition" 
                                    title="Select tipo regla">
                              </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>
                          

                          <div ng-show="datos.selectedTipoBloqueos.idTipoBloqueo == 2" class="col-12 text-center">
                            <div class="input-group">
                              
                              <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Bancas</label>                              

                                <div  class=" col-sm-8 col-10">
                                  <select 
                                  id="multiselect"
                                      ng-model="datos.bancas"
                                      ng-options="o.descripcion for o in datos.optionsBancas track by o.id"
                                      class="selectpicker col-12" 
                                      data-style="select-with-transition" 
                                      multiple title="Seleccionar bancas"
                                      data-size="7" aria-setsize="2">
                                  </select>
                              </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

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
                        
                        <div class="col-8">

                        <div class="row justify-content-center">
                        
                              <h3>Monto</h3>
                            
                        </div>

                          <div ng-repeat="d in datos.sorteos" class="row my-0 justify-content-center">
                            <div class="col-8">
                                <div class="row my-0">
                                  <label class="d-none d-sm-block text-right col-sm-3 col-form-label font-weight-bold mt-2" style="color: black;">@{{d.descripcion}}</label>
                                  <div class="col-sm-5">
                                    <div class="form-group">
                                    <input ng-model="datos.sorteos[$index].monto" type="text"  name="@{{d.descripcion}}" id="@{{d.descripcion}}" type="text" class="form-control" >
                                    </div>
                                  </div>
                                </div>
                              </div> <!-- END COL-6 -->
                          </div> <!-- END ROW -->

                          
                        
                        </div> <!-- END COL-8 SORTEOS -->

                        

                      </div> <!-- END ROW -->

                      <div class="row justify-content-center">
                          <div class="col-12 text-center">
                            <h3>Loterias</h3>
                          </div>
                          <div class="col-12 text-center">
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


                        </form>

                      </div> <!-- END DIV FORMULARIO -->


                      
                    </div> <!-- END ROW SECUNDARIO PRINCIPAL -->
                  </div> <!-- END COL PRINCIPAL -->
                </div> <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 3 -->
              
          
              <div class="tab-pane " id="jugadas">
                <!-- <h5 class="info-text"> What are you doing? (checkboxes) </h5> -->
                <div class="row justify-content-center">
                  <div class="col-lg-12">
                    <div class="row justify-content-center">

                   
                    
                    
                   

                      <div class="col-12">
                        <form novalidate>

                        

                      <div class="row">
                      

                          

                            
                          <div class="col-12 text-center">
                            <div class="input-group">
                              <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Tipo regla</label>                              
                                <div  class=" col-sm-8 col-10">
                                  <select 
                                      ng-change="cbxTipoBloqueosJugadaChanged()"
                                      ng-model="datos.bloqueoJugada.selectedTipoBloqueos"
                                      ng-options="o.descripcion for o in datos.bloqueoJugada.optionsTipoBloqueos"
                                      class="selectpicker col-12" 
                                      data-style="select-with-transition" 
                                      title="Select tipo regla">
                                  </select>
                                </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                          <div class="col-12 text-center">
                            <div class="input-group">
                              <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Moneda</label>                              
                                <div  class=" col-sm-8 col-10">
                                  <select 
                                      ng-model="datos.bloqueoJugada.selectedMoneda"
                                      ng-options="o.descripcion for o in datos.bloqueoJugada.optionsMonedas"
                                      class="selectpicker col-12" 
                                      data-style="select-with-transition" 
                                      title="Select tipo regla">
                                  </select>
                                </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>
                          
                          <div ng-show="datos.bloqueoJugada.selectedTipoBloqueos.idTipoBloqueo == 2" class="col-12 text-center">
                            <div class="input-group">
                              <label class="d-none d-sm-block text-right col-sm-3 col-form-label  font-weight-bold " style="color: black;">Bancas</label>                              
                                <div  class=" col-sm-8 col-10">
                                    <select 
                                        id="multiselect"
                                        ng-model="datos.bloqueoJugada.bancas"
                                        ng-options="o.descripcion for o in datos.bloqueoJugada.optionsBancas track by o.id"
                                        class="selectpicker col-12" 
                                        data-style="select-with-transition" 
                                        multiple title="Seleccionar bancas"
                                        data-size="7" aria-setsize="2">
                                    </select>
                                </div>
                            </div> <!-- END INPUT GROUP -->
                          </div>

                            
                          <div class="col-3">
                            <div class="row">
                              <div class="col-12 text-center">
                                <h3>Opciones</h3>
                              </div>
                              <div class="col-12">
                              
                              </div>

                              <div class="col-sm-12 checkbox-radios">
                                <div ng-show="datos.bloqueoJugada.selectedTipoBloqueos.idTipoBloqueo == 1" class="form-check form-check-inline">
                                  <label class="form-check-label">
                                    <input ng-model="datos.bloqueoJugada.ignorarDemasBloqueosTmp" class="form-check-input" type="checkbox" value=""> Ignorar demas bloqueos
                                    <span class="form-check-sign">
                                      <span class="check"></span>
                                    </span>
                                  </label>
                                </div>
                                <div  class="form-check form-check-inline">
                                  <label class="form-check-label">
                                    <input ng-change="seleccionarTodasChanged(seleccionarTodas)" ng-model="seleccionarTodas" class="form-check-input" type="checkbox" value=""> Todas las loterias
                                    <span class="form-check-sign">
                                      <span class="check"></span>
                                    </span>
                                  </label>
                                </div>
                              </div>

                             

                             

                            </div>
                          </div>
                          

                          <div class="col-6">

                          <div class="col-12 text-center">
                            <h3>Datos</h3>
                          </div>

                          <div class="row justify-content-center">
                              <div class="col-6">
                                  <div id="divInputFechaDesde" class="form-group">
                                      <label  for="jugada" class="bmd-label-floating">Fecha inicio</label>
                                      <input ng-model="datos.bloqueoJugada.fechaDesde" id="fechaDesde" type="date" class="form-control" value="10/06/2018" required>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div id="divInputFechaHasta" class="form-group">
                                      <label for="jugada" class="bmd-label-floating">Fecha fin</label>
                                      <input ng-model="datos.bloqueoJugada.fechaHasta"  id="fechaHasta" type="date" class="form-control" value="10/06/2018" required>
                                  </div>
                              </div>

                              <style>
                                .dropdown-menu{
                                    width: 100%;
                                }
                              </style>
                          <!-- <div class="col-12">

                            <div class="input-group form-control-lg m-0 p-0">
                                <div class="form-group w-100">
                                <label for="porcentajecaid" class="bmd-label-floating">Entidad #1</label>
                                <input ng-model="datos.porcentajeCaida" class="form-control" id="porcentajecaid" name="porcentajecaid">
                                <select 
                                ng-model="datos.selectedSorteo"
                                    ng-options="o.descripcion for o in datos.optionsSorteos"
                                        ng-change="cbxBancasChange(o)"
                                        class="selectpicker w-100" 
                                        id="entidad1"
                                        data-style="select-with-transition" 
                                        title="Select Usuario">
                                </select>
                                </div>
                            </div>
                          
                          </div>  -->

                              
                              

                              <div class="col-6 col-md-6">
                                  <div id="divInputJugada" class="form-group">
                                                <label  for="jugada" class="bmd-label-floating">Jugada</label>
                                                <input 
                                                select-all-on-click
                                                    ng-model="datos.bloqueoJugada.jugada"
                                                    ng-keyup="txtMontoFocus($event)"
                                                    autocomplete="off"
                                                    class="form-control h4" 
                                                    id="txtJugada" 
                                                    type="text" name="text" 
                                                    minLength="2" maxLength="6"  />
                                            </div>
                              </div>

                             

                              <div class="col-6 col-md-6">
                                  <div id="divInputJugada" class="form-group">
                                                <label  for="jugada" class="bmd-label-floating">Monto</label>
                                                <input 
                                                    id="txtMonto"
                                                    ng-model="datos.bloqueoJugada.monto"
                                                    autocomplete="off"
                                                    class="form-control h4" 
                                                    type="text" name="text" 
                                                    ng-keyup="insertarJugada($event)"
                                                />
                                            </div>
                              </div>

                              



                            </div> <!-- END ROW JUGADAS -->
                          
                          
                          </div> <!-- END COL-8 JUGADAS -->
                                
                         


                        

                      </div> <!-- END ROW -->

                      <div class="row justify-content-center">
                       <div ng-show="datos.jugadasReporte.monto_total > 0" class="col-12 text-center">
                           <h2>Total loteria @{{datos.jugadasReporte.selectedLoteria.descripcion}}: @{{datos.jugadasReporte.monto_total | currency}}</h2>
                       </div>
                       <div class="col-4 col-sm-3 col-lg-3">
                           <h4 class="text-center">DIRECTO</h4>
                            <div class="table-responsive">
                                <table class="table table-fixed table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">Jugada</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 14px">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-repeat="c in datos.bloqueoJugada.jugadas | filter: {tam: 2}">
                                    <td class="col-sm-5 text-center">@{{c.jugada}}</td>
                                    <td class="col-6 text-center">
                                      @{{c.monto}}
                                      <i  ng-click="eliminarJugada(c.jugada)" class="material-icons text-center  b-0 p-0" style="margin:0px!important; margin-top: 2px!important; padding:0px!important;font-size: 15px; cursor:pointer;">close</i>
                                    </td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td> -->
                                    </tr>
                                    
                                </tbody>
                                </table>
                                <hr class="mb-0">
                                <!-- <div class="float-right mt-3">
                                        <div style="font-size: 16px;" class="font-weight-bold">
                                            Total
                                            <small class="h4 ml-3">&euro;0</small>
                                        </div> 
                                        
                                </div> -->
                            </div> <!-- END RESPONSIVE TABLE -->
                       </div> <!-- COL-3 -->

                       <div class="col-4 col-sm-4 col-lg-3">
                           <h5 class="text-center">PALE Y TRIPLETA</h5>
                            <div class="table-responsive">
                                <table class="table table-fixed table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">Jugada</th>
                                    <th class="font-weight-bold col-5 text-center" style="font-size: 14px">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-if="(c.tam == 4 || c.tam == 6) && esPick3Pick4UOtro(c.jugada) == 'otro'" ng-repeat="c in datos.bloqueoJugada.jugadas">
                                    <td class="col-sm-6 text-center font-weight-bold" style="font-size: 11px">@{{agregar_guion(c.jugada)}}</td>
                                    <td style="font-size: 12px" class="col-5 text-center font-weight-bold">
                                    @{{c.monto}}
                                    <i ng-click="eliminarJugada(c.jugada)" class="material-icons text-center  b-0 p-0" style="margin:0px!important; margin-top: 2px!important; padding:0px!important;font-size: 15px; cursor:pointer;">close</i>
                                    </td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td>
                                    </tr> -->
                                    
                                </tbody>
                                </table>
                                <hr class="mb-0">
                                <!-- <div class="float-right mt-3">
                                        <div style="font-size: 16px;" class="font-weight-bold">
                                            Total
                                            <small class="h4 ml-3">&euro;0</small>
                                        </div> 
                                        
                                </div> -->
                            </div> <!-- END RESPONSIVE TABLE -->
                       </div> <!-- COL-3 -->


                       <div class="col-4 col-sm-4 col-lg-3">
                           <h4 class="text-center">PICK 3</h4>
                            <div class="table-responsive">
                                <table class="table table-fixed table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">Jugada</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 14px">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-if="esPick3Pick4UOtro(c.jugada) == 'pick3Straight' || esPick3Pick4UOtro(c.jugada) == 'pick3Box'" ng-repeat="c in datos.bloqueoJugada.jugadas">
                                    <td class="col-sm-5 text-center">
                                      @{{agregar_guion(c.jugada)}}
                                      <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick3Box'" class="text-danger font-weight-bold">B</small>
                                      <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick3Straight'" class="text-primary font-weight-bold">S</small>
                                    </td>
                                    <td class="col-6 text-center">
                                      @{{c.monto}}
                                      <i  ng-click="eliminarJugada(c.jugada)" class="material-icons text-center  b-0 p-0" style="margin:0px!important; margin-top: 2px!important; padding:0px!important;font-size: 15px; cursor:pointer;">close</i>
                                    </td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td> -->
                                    </tr>
                                    
                                </tbody>
                                </table>
                                <hr class="mb-0">
                                <!-- <div class="float-right mt-3">
                                        <div style="font-size: 16px;" class="font-weight-bold">
                                            Total
                                            <small class="h4 ml-3">&euro;0</small>
                                        </div> 
                                        
                                </div> -->
                            </div> <!-- END RESPONSIVE TABLE -->

                       </div> <!-- COL-3 -->

                       <div class="col-4 col-sm-4 col-lg-3">
                           <h4 class="text-center">PICK 4</h4>
                            <div class="table-responsive">
                                <table class="table table-fixed table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                    <th class="font-weight-bold col-2 col-sm-6 text-center" style="font-size: 14px">Jugada</th>
                                    <th class="font-weight-bold col-6 text-center" style="font-size: 14px">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="">
                                    <tr ng-if="esPick3Pick4UOtro(c.jugada) == 'pick4Straight' || esPick3Pick4UOtro(c.jugada) == 'pick4Box'" ng-repeat="c in datos.bloqueoJugada.jugadas">
                                    <td style="font-size: 13px" class="col-sm-6 text-center">
                                      @{{agregar_guion(c.jugada)}}
                                      <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick4Box'" class="text-danger font-weight-bold">B</small>
                                      <small ng-if="esPick3Pick4UOtro(c.jugada) == 'pick4Straight'" class="text-primary font-weight-bold">S</small>
                                    </td>

                                    <td class="col-6 text-center">
                                      @{{c.monto}}
                                      <i ng-click="eliminarJugada(c.jugada)" class="material-icons text-center  b-0 p-0" style="margin:0px!important; margin-top: 2px!important; padding:0px!important;font-size: 15px; cursor:pointer;">close</i>
                                    </td>
                                    <!-- <td class="td-actions text-center col-1">
                                        <button type="button" rel="tooltip" data-placement="left" title="Remove item" class="btn btn-link">
                                            <i class="material-icons">close</i>
                                        </button>
                                        </td> -->
                                    </tr>
                                    
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

                       </div> <!-- COL-3 -->
                   </div>

                      <div class="row justify-content-center">
                          <div class="col-12 text-center">
                            <h3>Loterias</h3>
                          </div>
                          
                          <div class="col-12 text-center">
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
                                    ng-repeat="l in datos.bloqueoJugada.loterias"
                                    ng-class="{'active2': l.seleccionada == 'true'}"
                                    ng-click="rbxLoteriasJugadasChanged(l, $index)"
                                    id="btnLoteriaJugada@{{$index}}"
                                    type="button" 
                                    class="btn btn-outline-info">@{{l.descripcion}}</button>
                                    <!-- <button type="button" class="btn btn-outline-info">6</button>
                                    <button type="button" class="btn btn-outline-info">7</button> -->
                                </div>
                                      <!-- ng-init="rbxLoteriasChanged(l, $first)" -->
                                
                              </div><!-- END COL-12 -->
                        </div> <!-- END ROW LOTERIAS -->


                        </form>

                      </div> <!-- END DIV FORMULARIO -->


                      
                    </div> <!-- END ROW SECUNDARIO PRINCIPAL -->
                  </div> <!-- END COL PRINCIPAL -->
                </div> <!-- END ROW PRINCIPAL -->
              </div> <!-- END TAB 3 -->

            


            </div>
          </div>
          <div class="card-footer">
            <div ng-show="tabActiva == 2" class="row justify-content-end w-100">
              <input ng-click="actualizar()" type="button" class="btn btn-info " name="guardar" value="Guardar">
            </div>

            <div ng-show="tabActiva == 3" class="row justify-content-end w-100">
              <input ng-click="actualizarJugadas()" type="button" class="btn btn-success " name="guardar" value="Guardar">
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