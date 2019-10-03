myApp
    .controller("myController", function($scope, $http, $timeout, helperService){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "idUsuario":0,
            "idTipo": null,
            "idTipoEntidad1": null,
            "idTipoEntidad2": null,
            "idEntidad1": null,
            "idEntidad2": null,
            "entidad1_saldo_inicial": null,
            "entidad2_saldo_inicial": null,
            "debito": null,
            "credito": null,
            "entidad1_saldo_final": null,
            "entidad2_saldo_final": null,
            "nota": '',
            "nota_grupo": '',
            "status":true,

            "addTransaccion" : [],

            "optionsTipos": [],
            "selectedTipo" : {},

            'selectedBanca' : {},
            'selectedEntidad' : {},
            "mostrarFormEditar" : false,
            "mostrarTotalDC" : false,

            "selectedGrupo" : {},
            "fechaDesde" : new Date(),
            "fechaHasta" : new Date(),
            'btnCargando' : false
        }

       

        $scope.empty = function(valor, tipo){
            if(tipo === 'number'){
                if(Number(valor) == undefined || valor == '' || valor == null || Number(valor) <= 0)
                    return true;
            }
            if(tipo === 'string'){
                if(valor == undefined || valor == '' || valor == null)
                    return true;
            }

            return false;
        }


        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }

        $scope.inicializarDatos = function(response){

            $('#divInputFechaDesde').addClass('is-filled');
            $('#divInputFechaHasta').addClass('is-filled');
           
                $http.get(rutaGlobal+"/api/transacciones/grupo")
                    .then(function(response){
                        console.log('Transacciones inicializarDatos: ', response.data);

                        $scope.datos.entidades = response.data.entidades;
                        $scope.datos.grupos = response.data.grupos;

                        $scope.datos.optionsTipos = response.data.tipos;
                        $scope.datos.selectedTipo = $scope.datos.optionsTipos[0];
                        $scope.datos.optionsBancas = response.data.bancas;
                        $scope.datos.selectedBanca = $scope.datos.optionsBancas[0];
                        $scope.datos.optionsEntidades = response.data.entidades;
                        $scope.datos.selectedEntidad = $scope.datos.optionsEntidades[0];
                        


                        $timeout(function() {
                            // anything you want can go here and will safely be run on the next digest.
                            $('.selectpicker').selectpicker("refresh");
                            $('#entidad1').selectpicker('val', [])
                            $('#entidad2').selectpicker('val', [])
                            
                          })

                        
                    });
            
       
        }

        $scope.buscar = function(){
            $http.post(rutaGlobal+"/api/transacciones/buscar", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
            .then(function(response){
               console.log(response.data);
               $scope.datos.grupos = response.data.grupos;
               
            },
            function(response) {
                // Handle error here
                console.log('Error jean: ', response);
                alert("Error");
                
            });
        }
        


        $scope.cbxBancasChange = function(banca){
            console.log('cbxBancas: ', $scope.datos.selectedBanca);
            if($scope.datos.selectedBanca == null || $scope.datos.selectedBanca == undefined)
                return;
            
            $scope.datos.id = $scope.datos.selectedBanca.id;
            $scope.datos.es_banca = true;
            $http.post(rutaGlobal+"/api/transacciones/saldo", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
                    .then(function(response){
                       console.log(response.data);
                       $('#entidad1_saldo_inicial').addClass('is-filled');
                       var credito = 0, debito = 0;
                       $scope.datos.addTransaccion.forEach(function(valor, indice, array){
                            if(array[indice].entidad1.id == $scope.datos.id){
                                debito += array[indice].debito;
                                credito += array[indice].credito;
                            }
                       });
                       $scope.datos.entidad1_saldo_inicial = response.data.saldo_inicial + (debito - credito);
                    });
        }

        $scope.cbxEntidadesChange = function(Entidad){
            console.log('cbxEntidads: ', $scope.datos.selectedEntidad);
            if($scope.datos.selectedEntidad == null || $scope.datos.selectedEntidad == undefined)
                return;
            
            $scope.datos.id = $scope.datos.selectedEntidad.id;
            $scope.datos.es_banca = false;
            $http.post(rutaGlobal+"/api/transacciones/saldo", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
                    .then(function(response){
                       console.log(response.data);
                       $('#entidad2_saldo_inicial').addClass('is-filled');
                       var credito = 0, debito = 0;
                       $scope.datos.addTransaccion.forEach(function(valor, indice, array){
                            if(array[indice].entidad2.id == $scope.datos.id){
                                debito += array[indice].debito;
                                credito += array[indice].credito;
                            }
                       });

                       $scope.datos.entidad2_saldo_inicial = response.data.saldo_inicial + (credito - debito);
                    });
        }

        $scope.cbxTiposChange = function(){
            if($scope.datos.selectedTipo == null || $scope.datos.selectedTipo == undefined)
                return;

            if($scope.datos.selectedTipo.descripcion == 'Cobro'){
                $scope.datos.debito = 0;
            }
            else if($scope.datos.selectedTipo.descripcion == 'Pago'){
                $scope.datos.credito = 0;
            }
        }

        $scope.total = function(es_debito, grupo = false){
            var debito = 0, credito = 0;
           if(grupo == false){
                $scope.datos.addTransaccion.forEach(function(valor, indice, array){
                    debito += array[indice].debito;
                    credito += array[indice].credito;
                });
           }else{
               if($scope.empty($scope.datos.selectedGrupo.transacciones, 'string'))
                    return;
                $scope.datos.selectedGrupo.transacciones.forEach(function(valor, indice, array){
                    debito += Number(array[indice].debito);
                    credito += Number(array[indice].credito);
                });
           }

           if(es_debito)
                return debito;
            else
                return credito;
        }

        $scope.saldoFinal = function(debito){
            console.log('saldoFinal:', $scope.datos.debito);
            $('#entidad2_saldo_final').addClass('is-filled');
            $('#entidad1_saldo_final').addClass('is-filled');
            if(debito){
                if(!$scope.empty($scope.datos.debito, 'number')){

                    console.log("entro: ");
                    
                    $scope.datos.entidad1_saldo_final = helperService.redondear($scope.datos.entidad1_saldo_inicial + Math.abs($scope.datos.debito));
                    $scope.datos.entidad2_saldo_final = helperService.redondear($scope.datos.entidad2_saldo_inicial - Math.abs($scope.datos.debito));
                }else{
                    $scope.datos.debito = 0;
                    $scope.datos.entidad1_saldo_final = 0;
                    $scope.datos.entidad2_saldo_final = 0;
                }
            }else{
                if(!$scope.empty($scope.datos.credito, 'number')){
                    $scope.datos.entidad1_saldo_final = helperService.redondear($scope.datos.entidad1_saldo_inicial - Math.abs($scope.datos.credito));
                    $scope.datos.entidad2_saldo_final = helperService.redondear($scope.datos.entidad2_saldo_inicial + Math.abs($scope.datos.credito));
                }else if($scope.datos.credito == 0){
                    $scope.datos.credito = 0;
                    $scope.datos.entidad1_saldo_final = 0;
                    $scope.datos.entidad2_saldo_final = 0;
                }
            }
        }

        $scope.addTransaccion = function(evento, sinevento = false){
            if(evento.keyCode != 13 && sinevento == false)
                return;

            

            if($scope.empty($scope.datos.selectedBanca, 'string')){
                alert("Debe seleccionar entidad #1");
                return;
            }
            if($scope.empty($scope.datos.selectedEntidad, 'string')){
                alert("Debe seleccionar entidad #2");
                return;
            }
            if($scope.datos.debito == 0 && $scope.datos.credito == 0){
                alert("El crédito o debito deben ser mayores que cero");
                return;
            }
            if($scope.empty($scope.datos.debito, 'number') == false && $scope.empty($scope.datos.credito, 'number') == false){
                alert("El crédito y el débito no pueden ser distintos de cero al mismo tiempo");
                return;
            }

            //LLamamos a esta funcion para actualizar los valores finales de las entidades
            if(!$scope.empty($scope.datos.debito, 'number'))
                $scope.saldoFinal(true);
            if(!$scope.empty($scope.datos.credito, 'number'))
                $scope.saldoFinal(false);

            console.log('addTra: ', $scope.datos.selectedBanca);
           

            var t = {};
            t.tipo = $scope.datos.selectedTipo;
            t.entidad1 = $scope.datos.selectedBanca;
            t.entidad2 = $scope.datos.selectedEntidad;
            t.entidad2 = $scope.datos.selectedEntidad;
            t.entidad1_saldo_inicial = $scope.datos.entidad1_saldo_inicial;
            t.entidad2_saldo_inicial = $scope.datos.entidad2_saldo_inicial;
            t.debito = (!$scope.empty($scope.datos.debito, 'number')) ? $scope.datos.debito : 0;
            t.credito = (!$scope.empty($scope.datos.credito, 'number')) ? $scope.datos.credito : 0;
            t.entidad1_saldo_final = $scope.datos.entidad1_saldo_final;
            t.entidad2_saldo_final = $scope.datos.entidad2_saldo_final;
            t.nota = $scope.datos.nota;
            t.nota_grupo = $scope.datos.nota_grupo;

            $scope.datos.addTransaccion.push(t);

            limpiar();
            
        }



        $scope.removeTransaction = function(index){
            
            // if(!$scope.empty(index, 'number')){
                $scope.datos.addTransaccion.splice(index,1);
                console.log('index remove: ', index);
                limpiar();
            // }
        }

        $scope.actualizar = function(){
            if(Object.keys($scope.datos.addTransaccion).length == 0){
                return;
            }

            $scope.datos.idUsuario = idUsuario;
            $scope.datos.btnCargando = true;
            $http.post(rutaGlobal+"/api/transacciones/guardar", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
            .then(function(response){
               console.log(response.data);
               if(response.data.errores != 1){
                    $scope.datos.grupos = response.data.grupos;
                    limpiar();
                    $scope.datos.addTransaccion = [];
                    $scope.datos.btnCargando = false;
                    alert('Se ha guardado correctamente');
                    $('#transactionGroupModal').modal('hide');
               }else{
                limpiar();
                $scope.datos.addTransaccion = [];
                $scope.datos.btnCargando = false;
                alert(response.data.mensajes);
               }
               
               
               
            },
            function(response) {
                // Handle error here
                console.log('Error jean: ', response);
                $scope.datos.btnCargando = false;
                alert('Error');
            });

            // ,
            // function(response) {
            //     // Handle error here
            //     console.log('Error jean: ', response);
            // }

        }

        // $scope.mostrar(fin){
        //     if(fin)
        //         $scope.datos.mostrarTotalDC = true;
        //     else
        //         $scope.datos.mostrarTotalDC = false;
        // }

        function limpiar(){

            $scope.datos.idUsuario =0;
            $scope.datos.idTipo = null;
            $scope.datos.idTipoEntidad1 = null;
            $scope.datos.idTipoEntidad2 = null;
            $scope.datos.idEntidad1 = null;
            $scope.datos.idEntidad2 = null;
            $scope.datos.entidad1_saldo_inicial = null;
            $scope.datos.entidad2_saldo_inicial = null;
            $scope.datos.debito = null;
            $scope.datos.credito = null;
            $scope.datos.entidad1_saldo_final = null;
            $scope.datos.entidad2_saldo_final = null;
            $scope.datos.nota = '';
            $scope.datos.nota_grupo = '';
            $scope.datos.status =true;

            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                $('.selectpicker').selectpicker("refresh");
                $('#entidad1').selectpicker('val', [])
                $('#entidad2').selectpicker('val', [])
                
              })

        }

        


        $scope.editar = function(esNuevo, d){
            
            
            console.log('editar: ', d, ' es nuevo: ', esNuevo);

            if(esNuevo){
                limpiar();     
            }
            else{
                $('.form-group').addClass('is-filled');
                $scope.datos.id = d.id;
                $scope.datos.nombre = d.nombre;
                $scope.datos.status = (d.status == 1) ? true : false;
                var idx = 0;
                if($scope.datos.optionsTipos.find(x => x.id == d.tipo.id) != undefined)
                    idx = $scope.datos.optionsTipos.findIndex(x => x.id == d.tipo.id);
                $scope.datos.selectedTipo = $scope.datos.optionsTipos[idx];
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                    
                  })
            }

            

            $scope.datos.mostrarFormEditar = true;
        }

        


        $scope.eliminar = function(d){
            $http.post(rutaGlobal+"/api/entidades/eliminar", {'action':'sp_loterias_elimnar', 'datos': d})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
                
            });
        }
       

      


        $scope.verTransacciones = function(grupo){
            $scope.datos.selectedGrupo = grupo;
            console.log('verTransacciones: ', $scope.datos.selectedGrupo);
        }



    })


    myApp.directive('selectAllOnClick', [function() {
        return {
          restrict: 'A',
          link: function(scope, element, attrs) {
            var hasSelectedAll = false;
            element.on('click', function($event) {
              if (!hasSelectedAll) {
                try {
                  //IOs, Safari, thows exception on Chrome etc
                  this.selectionStart = 0;
                  this.selectionEnd = this.value.length + 1;
                  hasSelectedAll = true;
                } catch (err) {
                  //Non IOs option if not supported, e.g. Chrome
                  this.select();
                  hasSelectedAll = true;
                }
              }
            });
            //On blur reset hasSelectedAll to allow full select
            element.on('blur', function($event) {
              hasSelectedAll = false;
            });
          }
        };
      }]);