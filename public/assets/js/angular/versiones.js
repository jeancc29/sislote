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
            "version": null,
            "enlace": null,
            "status":true,
            "dias": [],
            "sorteos": [],
            "horaCierre": moment().format('YYYY/MM/DD'),
            "optionsTipos": [],
            "selectedTipo" : {},
            "mostrarFormEditar" : false
        }

        function limpiar(){
            $scope.datos.id = 0;
            $scope.datos.version = null;
            $scope.datos.enlace = null;
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
                $scope.datos.versiones = response.data.versiones;
                
              
            }else{
              var jwt = helperService.createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario})
                $http.post(rutaGlobal+"/api/versiones?token=" + jwt)
                    .then(function(response){
                        console.log('Versiones: ', response.data);

                        $scope.datos.versiones = response.data.versiones;
  

                        limpiar();


                        
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
                $scope.datos.version = d.version;
                $scope.datos.enlace = d.enlace;
                $scope.datos.status = (d.status == 1) ? true : false;
                
            }

            

            $scope.datos.mostrarFormEditar = true;
        }

        

        $scope.actualizar = function(){
         
            

            console.log('primera: ',Number($scope.datos.primera));

            if(helperService.empty($scope.datos.version) == true){
                alert("La version no debe estar vacio");
                return;
            }
            if(helperService.empty($scope.datos.enlace) == true){
                alert("El enlace no debe estar vacio");
                return;
            }

           
            



            $scope.datos.status = ($scope.datos.status) ? 1 : 0;
            $scope.datos.idUsuario = idUsuarioGlobal; 
            $scope.datos.servidor = servidorGlobal; 

            var jwt = helperService.createJWT($scope.datos);
          $http.post(rutaGlobal+"/api/versiones/guardar", {'action':'sp_loterias_actualiza', 'datos': jwt})
             .then(function(response){
                 console.log(response.data);
                if(response.data.errores == 0){
                    alert("Se ha guardado correctamente");
                    if($scope.datos.id == 0)
                        $scope.inicializarDatos(response);
                    else{
                        $scope.inicializarDatos(response);
                        $scope.datos.status = ($scope.datos.status == 1) ? true : false;
                    }
                }else{
                    alert(response.data.mensaje);
                }
            });
        

        }


        $scope.eliminar = function(d){
            d.servidor = servidorGlobal;
            var jwt = helperService.createJWT(d);
            $http.post(rutaGlobal+"/api/versiones/eliminar", {'action':'sp_loterias_elimnar', 'datos': jwt})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
                
            });
        }

        $scope.publicar = function(d){
            d.servidor = servidorGlobal;
            var jwt = helperService.createJWT(d);
            $http.post(rutaGlobal+"/api/versiones/publicar", {'action':'sp_loterias_elimnar', 'datos': jwt})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(response);
                    alert(response.data.mensaje);
                }else{
                    
                }
                
            });
        }
       

      






    })
