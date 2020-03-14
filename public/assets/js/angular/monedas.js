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
            "descripcion": null,
            "abreviatura": null,
            "equivalenciaDeUnDolar": null,
            "permiteDecimales":true,
            "color":"#212529",
            "mostrarFormEditar" : false
        }

        function limpiar(){
            $scope.datos.id = 0;
            $scope.datos.descripcion = null;
            $scope.datos.abreviatura = null;
            $scope.datos.equivalenciaDeUnDolar = null;
            $scope.datos.permiteDecimales = false;
            $scope.datos.color = "#212529";
        }

        $scope.inicializarDatos = function(response){
            if(helperService.empty(response, 'string')){
                $scope.datos.monedas = response.data.monedas;
            }else{
                $http.get(rutaGlobal+"/api/monedas")
                    .then(function(response){
                        console.log('Loteria ajav: ', response.data);
                        limpiar();
                        $scope.datos.monedas = response.data.monedas;
                });
            }
        }
        
        $scope.editar = function(esNuevo, d){
            if(esNuevo){
                limpiar();     
            }
            else{
                $('.form-group').addClass('is-filled');
                $scope.datos.id = d.id;
                $scope.datos.descripcion = d.descripcion;
                $scope.datos.abreviatura = d.abreviatura;
                $scope.datos.equivalenciaDeUnDolar = helperService.redondear(d.equivalenciaDeUnDolar);
                $scope.datos.permiteDecimales = (d.permiteDecimales == 1) ? true : false;
                $scope.datos.color = d.color;
            }

            $scope.datos.mostrarFormEditar = true;
        }

        $scope.actualizar = function(){

            

            if(helperService.empty($scope.datos.descripcion, "string")){
                alert("El nombre no debe estar vacio");
                return;
            }
            if(helperService.empty($scope.datos.abreviatura, "string")){
                alert("La abreviatura no debe estar vacia");
                return;
            }

            if(helperService.empty($scope.datos.equivalenciaDeUnDolar, "number")){
                alert("La equivalencia de un dolar no debe estar vacia");
                return;
            }
            
            $scope.datos.permiteDecimales = ($scope.datos.permiteDecimales) ? 1 : 0;

            $http.post(rutaGlobal+"/api/monedas/guardar", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
                .then(function(response){
                    if(response.data.errores == 0){
                        alert("Se ha guardado correctamente");
                        $scope.inicializarDatos(response);
                        $scope.datos.mostrarFormEditar = false;
                    }else{
                        alert(response.data.mensaje);
                    }
            });
        

        }

        $scope.pordefecto = function(d){
            $http.post(rutaGlobal+"/api/monedas/pordefecto", {'action':'sp_loterias_elimnar', 'datos': d})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
            });
        }

        $scope.eliminar = function(d){
            $http.post(rutaGlobal+"/api/monedas/eliminar", {'action':'sp_loterias_elimnar', 'datos': d})
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
