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
            'selectedUsuario' : {},
            'selectedTipoEntidad' : {},
            "mostrarFormEditar" : false,
            "mostrarTotalDC" : false,

            "selectedGrupo" : {},
            "fechaDesde" : new Date(),
            "fechaHasta" : new Date(),
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
           
                var jwt = helperService.createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario})
                $http.get(rutaGlobal+"/api/transacciones?token=" + jwt)
                    .then(function(response){
                        console.log('Transacciones inicializarDatos: ', response.data);

                        $scope.datos.entidades = response.data.entidades;
                        $scope.datos.transacciones = response.data.transacciones;
                        

                        $scope.datos.optionsTipoEntidad = response.data.entidades;
                        $scope.datos.optionsTipos = response.data.tipos;
                        $scope.datos.optionsUsuarios = response.data.usuarios;

                        var na = {};
                        na.id = 0;
                        na.descripcion = "N/A";
                        na.usuario = "N/A";
                        na.entidades = [];
                        $scope.datos.optionsTipoEntidad.unshift(na);
                        $scope.datos.optionsTipos.unshift(na);
                        $scope.datos.optionsUsuarios.unshift(na);

                        $scope.datos.optionsEntidades = [];
                        $scope.datos.optionsEntidades.unshift(na);
                        $scope.datos.selectedEntidad = $scope.datos.optionsEntidades[0];

                        $scope.datos.selectedTipoEntidad = $scope.datos.optionsTipoEntidad[0];
                        $scope.datos.selectedTipo = $scope.datos.optionsTipos[0];
                        $scope.datos.selectedUsuario = $scope.datos.optionsUsuarios[0];
                        // $scope.datos.optionsEntidades = response.data.entidades;
                        // $scope.datos.selectedEntidad = $scope.datos.optionsEntidades[0];
                        


                        $timeout(function() {
                            // anything you want can go here and will safely be run on the next digest.
                            $('.selectpicker').selectpicker("refresh");
                            $('#entidad1').selectpicker('val', [])
                            $('#entidad2').selectpicker('val', [])
                            
                          })

                        
                    });
            
       
        }

       
        


       
        
        $scope.cbxTipoEntidadChange = function(){
            console.log('cbxTipoEntidad: ', $scope.datos.selectedTipoEntidad.entidades);
            if(!$scope.empty($scope.datos.selectedTipoEntidad.entidades, 'string')){
                $scope.datos.optionsEntidades = $scope.datos.selectedTipoEntidad.entidades;
                $scope.datos.selectedEntidad = $scope.datos.optionsEntidades[0];
            }else{
                var a = {};
                a.id = 0;
                a.descripcion = "N/A";
                $scope.datos.optionsEntidades = [];
                $scope.datos.optionsEntidades.unshift(a);
                $scope.datos.selectedEntidad = $scope.datos.optionsEntidades[0];
            }

            console.log('cbxTipoEntidad');

            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                $('.selectpicker').selectpicker("refresh");
                
              })
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
                    
                    $scope.datos.entidad1_saldo_final = $scope.datos.entidad1_saldo_inicial + Math.abs($scope.datos.debito);
                    $scope.datos.entidad2_saldo_final = $scope.datos.entidad2_saldo_inicial - Math.abs($scope.datos.debito);
                }else{
                    $scope.datos.debito = 0;
                    $scope.datos.entidad1_saldo_final = 0;
                    $scope.datos.entidad2_saldo_final = 0;
                }
            }else{
                if(!$scope.empty($scope.datos.credito, 'number')){
                    $scope.datos.entidad1_saldo_final = $scope.datos.entidad1_saldo_inicial - Math.abs($scope.datos.credito);
                    $scope.datos.entidad2_saldo_final = $scope.datos.entidad2_saldo_inicial + Math.abs($scope.datos.credito);
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

        $scope.buscar = function(){
            $scope.datos.idTipoEntidad = $scope.datos.selectedTipoEntidad.id;
            $scope.datos.idEntidad = $scope.datos.selectedEntidad.id;
            $scope.datos.idTipo = $scope.datos.selectedTipo.id;
            $scope.datos.idUsuario = $scope.datos.selectedUsuario.id;
            $scope.datos.servidor = servidorGlobal;

            
          console.log('buscar: ', $scope.datos);
          
            var jwt = helperService.createJWT($scope.datos);
            $http.post(rutaGlobal+"/api/transacciones/buscarTransaccion", {'action':'sp_loterias_actualiza', 'datos': jwt})
            .then(function(response){
               console.log(response.data);
               $scope.datos.transacciones = response.data.transacciones
            },
            function(response) {
                // Handle error here
                console.log('Error jean: ', response);
                alert('Error');
            });

           

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