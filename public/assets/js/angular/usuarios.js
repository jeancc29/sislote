var myApp = angular
    .module("myModule", [])
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "id":0,
            "idTercero":0,
            "nombres": null,
            "email" : null,
            "usuario" : null,
            "password" : null,
            "confirmar" : null,
            "idTipoUsuario" : 0,
            "estado":true,
            "permisos": [],

            "bancas" : [],

            "ckbPermisosAdicionales": [],
            "mostrarFormEditar" : false,


            "optionsUsuariosTipos" : [],
            "selectedUsuariosTipos" : {},
            "permisosSeleccionados" : []
        }

        
        $scope.inicializarDatos = function(todos, idRole = 0){
               
            $http.get(rutaGlobal+"/api/usuarios")
             .then(function(response){
                console.log('Loteria ajav: ', response.data);

                if(todos){
                    $scope.datos.id = 0;
                    $scope.datos.nombres = null;
                    $scope.datos.email = null;
                    $scope.datos.password = null;
                    $scope.datos.confirmar = null;
                    $scope.datos.usuario = null;
                    $scope.datos.estado = true;
                    $scope.datos.idTipoUsuario = 0;

                    $scope.datos.permisos = [];
                    $scope.datos.ckbPermisos = [];
                    
                   }

                $scope.datos.usuarios =response.data.usuarios;
                $scope.datos.optionsUsuariosTipos = response.data.usuariosTipos;
                $scope.datos.ckbPermisos = [];

               
                response.data.permisos.forEach(function(valor, indice, array){
                        array[indice].existe = false;
                        $scope.datos.ckbPermisos.push(array[indice]);
                    });

                var idx = 0;
                if($scope.datos.id != 0)
                    idx = $scope.datos.optionsUsuariosTipos.findIndex(x => x.id == $scope.datos.idTipoUsuario);

                $scope.datos.selectedUsuariosTipos = $scope.datos.optionsUsuariosTipos[idx];
                $scope.cbxUsuariosTipos_changed();

                $timeout(function() {
                    $scope.$digest();
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                   // $('.selectpicker').selectpicker('val', [])
                });
               
            
                
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

       
        

        $scope.actualizar = function(){
         
            //$scope.datos.horaCierre = moment($scope.datos.horaCierre, ['HH:mm']).format('HH:mm');
            console.log('actualizar: ' , $scope.datos);

            

            if($scope.datos.nombres == undefined || $scope.datos.nombres == ""){
                alert("El nombre no debe estar vacio");
                return;
            }
            if($scope.datos.email == undefined || $scope.datos.email == ""){
                alert("El correo no debe estar vacio");
                return;
            }
            
            if($scope.datos.usuario == undefined || $scope.datos.usuario == ""){
                alert("El usuario no debe estar vacio");
                return;
            }
            
            if($scope.datos.id == 0){
                if($scope.datos.password == undefined || $scope.datos.password == ""){
                    alert("La contraseña no debe estar vacia");
                    return;
                }
                if($scope.datos.password != $scope.datos.confirmar){
                    alert("La contraseña no es igual");
                    return;
                }
            }

            if($scope.datos.permisos.length == 0){
                alert("Debe seleccionar aunque sea 1 permiso");
                return;
            }
            


            $scope.datos.idTipoUsuario = $scope.datos.selectedUsuariosTipos.id;
            $scope.datos.status = ($scope.datos.estado) ? 1 : 0;
           

          $http.post(rutaGlobal+"/api/usuarios/guardar", {'action':'sp_bancas_actualizar', 'datos': $scope.datos})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0){
                    
                    if($scope.datos.id == 0){
                        $scope.inicializarDatos(true);
                        $scope.datos.mostrarFormEditar = false;
                        alert("Se ha guardado correctamente");
                    }
                    else{
                        $scope.inicializarDatos(false);
                        $scope.datos.estado = ($scope.datos.estado == 1) ? true : false;
                        alert("Se ha guardado correctamente");
                    }
                }else{
                    alert(response.data.mensaje);
                    return;
                }
                
            });
        

        }


        $scope.eliminar = function(d){
            console.log('usuarios eliminar: ',d);
            $http.post(rutaGlobal+"/api/usuarios/eliminar", {'action':'sp_loterias_elimnar', 'datos': d})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
                
            });
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



  

