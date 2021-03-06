myApp
    .controller("myController", function($scope, $http, $timeout, helperService){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "id":0,
            "nombre": null,
            "status":true,
            "dias": [],
            "sorteos": [],
            "horaCierre": moment().format('YYYY/MM/DD'),
            "optionsTipos": [],
            "selectedTipo" : {},
            "optionsMonedas": [],
            "selectedMoneda" : {},
            "mostrarFormEditar" : false
        }

        function limpiar(){
            $scope.datos.id = 0;
            $scope.datos.nombre = null;
            $scope.datos.status = true;
           // $scope.datos.selectedTipo = $scope.datos.optionsTipos[0];
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

        $scope.inicializarDatos = function(response){
            if($scope.empty(response, 'string')){
                $scope.datos.entidades = response.data.entidades;
                $scope.datos.optionsTipos = response.data.tipos;
                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                    helperService.actualizarScrollBar();
                  })
            }else{
                var jwt = helperService.createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario});
                $http.get(rutaGlobal+"/api/entidades?token=" + jwt)
                    .then(function(response){
                        console.log('Loteria ajav: ', response.data);

                        $scope.datos.entidades = response.data.entidades;
                        $scope.datos.optionsTipos = response.data.tipos;
                        $scope.datos.selectedTipo = $scope.datos.optionsTipos[0];
                        $scope.datos.optionsMonedas = response.data.monedas;
                        $scope.datos.selectedMoneda = $scope.datos.optionsMonedas[0];
                        console.log('entidad : ',  $scope.datos.optionsTipos);

                        limpiar();

                        $timeout(function() {
                            // anything you want can go here and will safely be run on the next digest.
                            $('.selectpicker').selectpicker("refresh");
                            helperService.actualizarScrollBar();
                          })
                        
                    });
            }

           
            
       
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

                console.log('idMoneda:', d);

                if($scope.datos.optionsMonedas.find(x => x.id == d.idMoneda) != undefined){
                    idx = $scope.datos.optionsMonedas.findIndex(x => x.id == d.idMoneda);
                    $scope.datos.selectedMoneda = $scope.datos.optionsMonedas[idx];
                }

                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                    
                  })
            }

            $scope.datos.mostrarFormEditar = true;
        }

        

        $scope.actualizar = function(){
         
            console.log('primera: ',Number($scope.datos.primera));

            if($scope.datos.nombre == undefined || $scope.datos.nombre == ""){
                alert("El nombre no debe estar vacio");
                return;
            }
            if(Object.keys($scope.datos.optionsTipos).length == 0){
                alert("Debe seleccionar un tipo de entidad");
                return;
            }
            if(Object.keys($scope.datos.optionsMonedas).length == 0){
                alert("Debe seleccionar una moneda");
                return;
            }

            $scope.datos.status = ($scope.datos.status) ? 1 : 0;
            $scope.datos.idTipo = $scope.datos.selectedTipo.id; 
            $scope.datos.idMoneda = $scope.datos.selectedMoneda.id; 
            $scope.datos.servidor = servidorGlobal; 
            var jwt = helperService.createJWT($scope.datos);
            $http.post(rutaGlobal+"/api/entidades/guardar", {'action':'sp_loterias_actualiza', 'datos': jwt})
                .then(function(response){
                    console.log(response.data);
                    if(response.data.errores == 0){
                        alert("Se ha guardado correctamente");
                        $scope.inicializarDatos(response);
                        $scope.datos.mostrarFormEditar = false;
                    }else{
                        alert(response.data.mensaje);
                    }
                });
        

        }


        $scope.eliminar = function(d){
            d.servidor = servidorGlobal; 
            var jwt = helperService.createJWT(d);
            $http.post(rutaGlobal+"/api/entidades/eliminar", {'action':'sp_loterias_elimnar', 'datos': jwt})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
                
            });
        }
       

      






    })
