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
            "fecha" : new Date(),
            "sesiones" : []
        }

        
        $scope.inicializarDatos = function(todos, idRole = 0){

            $scope.datos.idUsuario = idUsuario;
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $http.post(rutaGlobal+"/api/usuarios/sesiones", {'action':'sp_bancas_actualizar', 'datos': jwt})
            .then(function(response){
               console.log(response.data);
               $scope.datos.sesiones = response.data.sesiones;
              
               $timeout(function() {
                $scope.$digest();
                helperService.actualizarScrollBar();
               // $('.selectpicker').selectpicker('val', [])
            });
               
           });
               
         
       
        }

        $scope.buscar = function(){

            $scope.datos.idUsuario = idUsuario;
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $http.post(rutaGlobal+"/api/usuarios/sesiones", {'action':'sp_bancas_actualizar', 'datos': jwt})
            .then(function(response){
               console.log(response.data);
               $scope.datos.sesiones = response.data.sesiones;
              
               
           });
               
         
       
        }
        

        $scope.load = function(codigo_usuario){
            $scope.inicializarDatos(true);
            $scope.datos.idUsuario = codigo_usuario;
        }


        


        $scope.editar = function(esNuevo, d){
            
            $scope.datos.mostrarFormEditar = true;

            if(esNuevo){
                $scope.inicializarDatos(true);
                
                //$('.form-group').removeClass('is-filled');
                
                // $scope.datos.ckbDias.forEach(function(valor, indice, array){

                //     array[indice].existe = false;

                //  });
        
            }
            else{
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;

                $('.form-group').addClass('is-filled');

                $scope.datos.id = d.id;
                $scope.datos.nombres = d.nombres;
                $scope.datos.email = d.email;
                $scope.datos.usuario = d.usuario;
                $scope.datos.password = null;
                $scope.datos.confirmar = null;
                $scope.datos.idTipoUsuario = d.idTipoUsuario;
                $scope.datos.estado = (d.status == 1) ? true : false;
                
                var idx = $scope.datos.optionsUsuariosTipos.findIndex(x => x.id == parseInt(d.idTipoUsuario));
                $scope.datos.selectedUsuariosTipos = $scope.datos.optionsUsuariosTipos[idx];

                $timeout(function() {
                    $scope.$digest();
                    $('.selectpicker').selectpicker('refresh');
                });

                $scope.datos.ckbPermisos.forEach(function(valor, indice, array){

                    array[indice].existe = false;

                 });

                   
                console.log(d.permisos, ' permisos');

                if(d.permisos != undefined){

                    $scope.datos.permisos = d.permisos;

                    $scope.datos.permisos.forEach(function(valor, indice, array){

                        if($scope.datos.ckbPermisos.find(x => x.id == array[indice].id) != undefined){
                            let idx = $scope.datos.ckbPermisos.findIndex(x => x.id == parseInt(array[indice].id));
                            $scope.datos.ckbPermisos[idx].existe = true;
                        }

                     });
                }



                
            }
        }

       
        

        

       
       


        $scope.cbxUsuariosTipos_changed = function(){
            console.log($scope.datos.selectedUsuariosTipos);
            if($scope.datos.selectedUsuariosTipos != undefined){

                $scope.datos.permisos = [];
                $scope.datos.ckbPermisos.forEach(function(valor, indice, array){

                    array[indice].existe = false;

                 });

                $scope.datos.selectedUsuariosTipos.permisos.forEach(function(valor, indice, array){

                    if($scope.datos.ckbPermisos.find(x => x.id == array[indice].id) != undefined){
                        let idx = $scope.datos.ckbPermisos.findIndex(x => x.id == parseInt(array[indice].id));
                        $scope.datos.ckbPermisos[idx].existe = true;
                        $scope.datos.permisos.push($scope.datos.ckbPermisos[idx]);
                    }

                 });
            }
            
        }

        $scope.ckbPermisos_changed = function(check, d){
            console.log('ckbPermisos changed: ', d.existe);
            if(d.existe){
                //Si el permiso no esta en los permisos seleccionados entonces lo agregamos
                if($scope.datos.permisos.find(x => x.id == d.id) == undefined){
                    $scope.datos.permisos.push(d);
                }
                
            }
            else{
                if($scope.datos.permisos.find(x => x.id == d.id) != undefined){

                    let idx = $scope.datos.permisos.findIndex(x => x.id == d.id);
                    $scope.datos.permisos.splice(idx,1);
                }
            }
            
        }


       


    })



  

