myApp
    .controller("myController", function($scope, $http, $timeout, helperService){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        
      

        // $scope.actualizarHoraAperturaCierre = function(){
        //     // flecha arriba click
        //     $(".fa-chevron-up").on("click", function(e){
        //         _convertir_apertura_y_cierre(false);
        //     });
        //     // flecha abajo click
        //     $(".fa-chevron-down").on("click", function(e){
        //         _convertir_apertura_y_cierre(false);
        //     });
        //     // button am o pm click
        //     $(".btn-primary").on("click", function(e){
        //         if($(".btn-primary").text() =="AM" || $(".btn-primary").text() == "PM"){
        //             _convertir_apertura_y_cierre(false);
        //         }
        //     });

            
        // }

        function hora_convertir(phora, _24 = true){
            //Si es verdadero la hora se convertira al formato 24 horas
            if(_24){
                //Si es verdadero eso quiere decir que es PM de lo contrario sera AM
                if(phora.indexOf("PM") != -1){
                    
                    //Aqui se le quitara el PM a la hora
                    var a = phora.replace(" PM", "");
                    //Aqui la hora se convertira en un arreglo para tener aparte la hora y los minutos
                    a = a.split(':');
                    //La variable hora va a contener el solamente la hora sin minutos ni segundos
                    var hora = parseInt(a[0]);
                    //Aqui se convierte la hora normal en el formato 24 horas
                    if(hora == 12)
                        phora = hora;
                    else
                        phora = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    phora = phora.toString() + ":" + a[1];
                    console.log('actualizar: convertido: ', phora); 
                }
                else if(phora.indexOf("AM") != -1){
                     //Aqui se le quitara el AM a la hora
                     var a = phora.replace(" AM", "");
                    //  phora = a;
                    //  console.log('actualizar: convertido: ', phora); 
                    var hora = parseInt(a.split(':')[0]);
                     if(hora == 12){
                        phora = hora + 12;
                        phora = phora.toString() + ":" + a.split(':')[1];
                     }else{
                        phora = a;
                     }
                }

            }
            else{
                var a = phora.split(":");
                var hora = parseInt(a[0]);
                if(hora > 12){
                    hora = hora - 12;
                    phora = hora.toString() + ':' + a[1] + ' PM';
                }
            }

            return phora;
        }

        function _convertir_apertura_y_cierre(_24, todas = false){
            let idx = $scope.datos.loterias.findIndex(x => x.id == $scope.datos.selectedLoteria.id);

         
            // if(todas == false){
                
            //         $scope.datos.loterias[idx].lunes.apertura = hora_convertir($scope.datos.loterias[idx].lunes.apertura, _24);
            //         $scope.datos.loterias[idx].lunes.cierre = hora_convertir($scope.datos.loterias[idx].lunes.cierre, _24);
            //         $scope.datos.loterias[idx].martes.apertura = hora_convertir($scope.datos.loterias[idx].martes.apertura, _24);
            //         $scope.datos.loterias[idx].martes.cierre = hora_convertir($scope.datos.loterias[idx].martes.cierre, _24);
            //         $scope.datos.loterias[idx].miercoles.apertura = hora_convertir($scope.datos.loterias[idx].miercoles.apertura, _24);
            //         $scope.datos.loterias[idx].miercoles.cierre = hora_convertir($scope.datos.loterias[idx].miercoles.cierre, _24);
            //         $scope.datos.loterias[idx].jueves.apertura = hora_convertir($scope.datos.loterias[idx].jueves.apertura, _24);
            //         $scope.datos.loterias[idx].jueves.cierre = hora_convertir($scope.datos.loterias[idx].jueves.cierre, _24);
            //         $scope.datos.loterias[idx].viernes.apertura = hora_convertir($scope.datos.loterias[idx].viernes.apertura, _24);
            //         $scope.datos.loterias[idx].viernes.cierre = hora_convertir($scope.datos.loterias[idx].viernes.cierre, _24);
            //         $scope.datos.loterias[idx].sabado.apertura = hora_convertir($scope.datos.loterias[idx].sabado.apertura, _24);
            //         $scope.datos.loterias[idx].sabado.cierre = hora_convertir($scope.datos.loterias[idx].sabado.cierre, _24);
            //         $scope.datos.loterias[idx].domingo.apertura = hora_convertir($scope.datos.loterias[idx].domingo.apertura, _24);
            //         $scope.datos.loterias[idx].domingo.cierre = hora_convertir($scope.datos.loterias[idx].domingo.cierre, _24);
            // }else{
                // $scope.datos.loterias.forEach(function(valor, indice, array){
                //     array[indice].lunes.apertura = hora_convertir(array[indice].lunes.apertura, _24);
                //     array[indice].lunes.cierre = hora_convertir(array[indice].lunes.cierre, _24);
                //     array[indice].martes.apertura = hora_convertir(array[indice].martes.apertura, _24);
                //     array[indice].martes.cierre = hora_convertir(array[indice].martes.cierre, _24);
                //     array[indice].miercoles.apertura = hora_convertir(array[indice].miercoles.apertura, _24);
                //     array[indice].miercoles.cierre = hora_convertir(array[indice].miercoles.cierre, _24);
                //     array[indice].jueves.apertura = hora_convertir(array[indice].jueves.apertura, _24);
                //     array[indice].jueves.cierre = hora_convertir(array[indice].jueves.cierre, _24);
                //     array[indice].viernes.apertura = hora_convertir(array[indice].viernes.apertura, _24);
                //     array[indice].viernes.cierre = hora_convertir(array[indice].viernes.cierre, _24);
                //     array[indice].sabado.apertura = hora_convertir(array[indice].sabado.apertura, _24);
                //     array[indice].sabado.cierre = hora_convertir(array[indice].sabado.cierre, _24);
                //     array[indice].domingo.apertura = hora_convertir(array[indice].domingo.apertura, _24);
                //     array[indice].domingo.cierre = hora_convertir(array[indice].domingo.cierre, _24);
                // });

                $scope.datos.loterias.forEach(function(valor, indice, array){
                    array[indice].lunes.aperturaGuardar = agregarCeroIzquierda(array[indice].lunes.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].lunes.apertura.getMinutes());
                    array[indice].lunes.cierreGuardar = agregarCeroIzquierda(array[indice].lunes.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].lunes.cierre.getMinutes());
                    array[indice].martes.aperturaGuardar = agregarCeroIzquierda(array[indice].martes.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].martes.apertura.getMinutes());
                    array[indice].martes.cierreGuardar = agregarCeroIzquierda(array[indice].martes.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].martes.cierre.getMinutes());
                    array[indice].miercoles.aperturaGuardar = agregarCeroIzquierda(array[indice].miercoles.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].miercoles.apertura.getMinutes());
                    array[indice].miercoles.cierreGuardar = agregarCeroIzquierda(array[indice].miercoles.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].miercoles.cierre.getMinutes());
                    array[indice].jueves.aperturaGuardar = agregarCeroIzquierda(array[indice].jueves.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].jueves.apertura.getMinutes());
                    array[indice].jueves.cierreGuardar = agregarCeroIzquierda(array[indice].jueves.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].jueves.cierre.getMinutes());
                    array[indice].viernes.aperturaGuardar = agregarCeroIzquierda(array[indice].viernes.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].viernes.apertura.getMinutes());
                    array[indice].viernes.cierreGuardar = agregarCeroIzquierda(array[indice].viernes.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].viernes.cierre.getMinutes());
                    array[indice].sabado.aperturaGuardar = agregarCeroIzquierda(array[indice].sabado.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].sabado.apertura.getMinutes());
                    array[indice].sabado.cierreGuardar = agregarCeroIzquierda(array[indice].sabado.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].sabado.cierre.getMinutes());
                    array[indice].domingo.aperturaGuardar = agregarCeroIzquierda(array[indice].domingo.apertura.getHours()) + ':' + agregarCeroIzquierda(array[indice].domingo.apertura.getMinutes());
                    array[indice].domingo.cierreGuardar = agregarCeroIzquierda(array[indice].domingo.cierre.getHours()) + ':' + agregarCeroIzquierda(array[indice].domingo.cierre.getMinutes());
                });
            // }
            
            
        }

        agregarCeroIzquierda = function(valor){
            var str = "" + valor;
            var pad = "00";
            var ans = pad.substring(0, pad.length - str.length) + str;
            return ans;
        }

        function hora_convertir2(phora){
            //Si es verdadero la hora se convertira al formato 24 horas
            //if(_24){
                //Si es verdadero eso quiere decir que es PM de lo contrario sera AM
                if(phora.indexOf("PM") != -1){
                    
                    //Aqui se le quitara el PM a la hora
                    var a = phora.replace(" PM", "");
                    //Aqui la hora se convertira en un arreglo para tener aparte la hora y los minutos
                    a = a.split(':');
                    //La variable hora va a contener el solamente la hora sin minutos ni segundos
                    var hora = parseInt(a[0]);
                    //Aqui se convierte la hora normal en el formato 24 horas
                    phora = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    phora = phora.toString() + ":" + a[1];
                    console.log('actualizar: convertido: ', phora); 
                }
                else{
                     //Aqui se le quitara el AM a la hora
                     var a = phora.replace(" AM", "");
                     //Si son las 12 AM entonces debo convertir la hora a formato 24
                     var hora = parseInt(a.split(':')[0]);
                     if(hora == 12){
                        phora = hora + 12;
                        phora = phora.toString() + ":" + a.split(':')[1];
                     }else{
                        phora = a;
                     }
                     
                     console.log('actualizar: convertido: ', phora); 
                }

                return phora;
            //}
            // else{
            //     var a = $scope.datos.horaCierre.split(":");
            //     var hora = parseInt(a[0]);
            //     if(hora > 12){
            //         hora = hora - 12;
            //         $scope.datos.horaCierre = hora.toString() + ':' + a[1] + ' PM';
            //     }
            // }
        }

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "idUsuario":null,
            "id":0,
            "descripcion": null,
            "codigo" : null,
            "ip" : null,
            "usuario" : null,
            "clave" : null,
            "idTipoUsuario" : 0,
            "estado":true,
            "permisos": [],
            "minutosCancelarTicket" : null,

            "piepagina1" : null,
            "piepagina2" : null,
            "piepagina3" : null,
            "piepagina4" : null,

            "bancas" : [],

            "ckbPermisosAdicionales": [],
            "mostrarFormEditar" : false,


            "optionsUsuariosTipos" : [],
            "selectedUsuariosTipos" : {},


            "loterias" : [],
            "ckbDias" : [],
            "loteriasSeleccionadas" : [],


           
            'comisiones' :{
                'loterias' : [],
                'selectedLoteria' : {}
            },
            'pagosCombinaciones' :{
                'loterias' : [],
                'selectedLoteria' : {}
            }
        }

        
        $scope.inicializarDatos = function(todos, idUsuario = 0){

            console.log('ruta: ', rutaGlobal);
            
            var jwt = helperService.createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario});
            $http.get(rutaGlobal+"/api/horarios?token=" + jwt)
             .then(function(response){
                console.log('Bancas: ', response.data);

                if(todos){
                    $scope.datos.id = 0;
                    $scope.datos.descripcion = null;
                    $scope.datos.codigo = null;
                    $scope.datos.ip = null;
                    $scope.datos.dueno = null;
                    $scope.datos.localidad = null;

                    $scope.datos.porcentajeCaida = null;
                    $scope.datos.balanceDesactivacion = null;
                    $scope.datos.limiteVenta = null;
                    $scope.datos.descontar = null;
                    $scope.datos.deCada = null;
                    $scope.datos.minutosCancelarTicket = null;
                    $scope.datos.piepagina1 = null;
                    $scope.datos.piepagina2 = null;
                    $scope.datos.piepagina3 = null;
                    $scope.datos.piepagina4 = null;

                    $scope.datos.estado = true;
                    $scope.datos.idTipoUsuario = 0;

                    $scope.datos.permisos = [];
                    $scope.datos.ckbPermisosAdicionales = [];
                   

                }


                var jsonLoterias = response.data.loterias;
                $scope.datos.loterias = [];
                $scope.datos.ckbDias = [];
                jsonLoterias.forEach(function(valor, indice, array){
                    array[indice].lunes = {};
                    array[indice].lunes.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].lunes.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].lunes.status = 1;
                    array[indice].lunes.minutosExtras = 0;
                    
                    array[indice].martes = {};
                    array[indice].martes.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].martes.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].martes.status = 1;
                    array[indice].martes.minutosExtras = 0;

                    array[indice].miercoles = {};
                    array[indice].miercoles.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].miercoles.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].miercoles.status = 1;
                    array[indice].miercoles.minutosExtras = 0;

                    array[indice].jueves = {};
                    array[indice].jueves.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].jueves.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].jueves.status = 1;
                    array[indice].jueves.minutosExtras = 0;

                    array[indice].viernes = {};
                    array[indice].viernes.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].viernes.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].viernes.status = 1;
                    array[indice].viernes.minutosExtras = 0;

                    array[indice].sabado = {};
                    array[indice].sabado.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].sabado.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].sabado.status = 1;
                    array[indice].sabado.minutosExtras = 0;

                    array[indice].domingo = {};
                    array[indice].domingo.apertura = new Date(1970, 0, 1, 1, 0, 0);
                    array[indice].domingo.cierre = new Date(1970, 0, 1, 23, 0, 0);
                    array[indice].domingo.status = 1;
                    array[indice].domingo.minutosExtras = 0;
                    

                });




                jsonLoterias.forEach(function(valor, indice, array){
                    if(array[indice].dias != undefined){
                        array[indice].lunes.status = 0;
                        array[indice].martes.status = 0;
                        array[indice].miercoles.status = 0;
                        array[indice].jueves.status = 0;
                        array[indice].viernes.status = 0;
                        array[indice].sabado.status = 0;
                        array[indice].domingo.status = 0;

                        array[indice].dias.forEach(function(valorDias, indiceDias, arrayDias){

                            if(arrayDias[indiceDias].descripcion == "Lunes"){
                                array[indice].lunes.status = 1;
                                // array[indice].lunes.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                // array[indice].lunes.cierre = arrayDias[indiceDias].pivot.horaCierre;
                                // a.split(':')
                                array[indice].lunes.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].lunes.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].lunes.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;
                                console.log("lunes: ",arrayDias[indiceDias].pivot.horaApertura);
                            }
                            else if(arrayDias[indiceDias].descripcion == "Martes"){
                                array[indice].martes.status = 1;
                                array[indice].martes.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].martes.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].martes.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;                            
                            }
                            else if(arrayDias[indiceDias].descripcion == "Miercoles"){
                                array[indice].miercoles.status = 1;
                                array[indice].miercoles.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].miercoles.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].miercoles.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;                            
                            }
                            else if(arrayDias[indiceDias].descripcion == "Jueves"){
                                array[indice].jueves.status = 1;
                                array[indice].jueves.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].jueves.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].jueves.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;                            
                            }
                            else if(arrayDias[indiceDias].descripcion == "Viernes"){
                                array[indice].viernes.status = 1;
                                array[indice].viernes.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].viernes.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].viernes.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;                            
                            }
                            else if(arrayDias[indiceDias].descripcion == "Sabado"){
                                array[indice].sabado.status = 1;
                                array[indice].sabado.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].sabado.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].sabado.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;                            
                            }
                            else{
                                array[indice].domingo.status = 1;
                                array[indice].domingo.apertura = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaApertura.split(':')[0], arrayDias[indiceDias].pivot.horaApertura.split(':')[1], 0);
                                array[indice].domingo.cierre = new Date(1970, 0, 1, arrayDias[indiceDias].pivot.horaCierre.split(':')[0], arrayDias[indiceDias].pivot.horaCierre.split(':')[1], 0);
                                array[indice].domingo.minutosExtras = arrayDias[indiceDias].pivot.minutosExtras;                            
                            }
                        });
                    }
                });

                $scope.datos.loterias = jsonLoterias;
                
                
                

                var jsonDias = response.data.dias;
                jsonDias.forEach(function(valor, indice, array){
                    $scope.datos.ckbDias.push({'id' :array[indice].id, 'descripcion': array[indice].descripcion, 'existe' : true});
                });



                

                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    // $('.selectpicker').selectpicker('val', [])
                  })
               
                
                
            });

           
           
       
        }
        

        $scope.load = function(codigo_usuario, ruta){
            $scope.inicializarDatos(true);
            $scope.datos.idUsuario = codigo_usuario;
 
        }


        



       
        hayHorasVacias = function(){
            var hayVacios = false;
             $scope.datos.loterias.forEach(function(valor, indice, array){
                    console.log('hayHorasVacias:', array[indice].lunes.status == 1);
                    console.log('vacio:', (helperService.empty(array[indice].lunes.apertura, 'date') == true));
                    if(array[indice].lunes.status == 1 && (helperService.empty(array[indice].lunes.apertura, 'date') == true || helperService.empty(array[indice].lunes.cierre, 'date') == true))
                        hayVacios = true;
                    if(array[indice].martes.status == 1 && (helperService.empty(array[indice].martes.apertura, 'date') == true || helperService.empty(array[indice].martes.cierre, 'date') == true))
                        hayVacios = true;
                    if(array[indice].miercoles.status == 1 && (helperService.empty(array[indice].miercoles.apertura, 'date') == true || helperService.empty(array[indice].miercoles.cierre, 'date') == true))
                        hayVacios = true;
                    if(array[indice].jueves.status == 1 && (helperService.empty(array[indice].jueves.apertura, 'date') == true || helperService.empty(array[indice].jueves.cierre, 'date') == true))
                        hayVacios = true;
                    if(array[indice].viernes.status == 1 && (helperService.empty(array[indice].viernes.apertura, 'date') == true || helperService.empty(array[indice].viernes.cierre, 'date') == true))
                        hayVacios = true;
                    if(array[indice].sabado.status == 1 && (helperService.empty(array[indice].sabado.apertura, 'date') == true || helperService.empty(array[indice].sabado.cierre, 'date') == true))
                        hayVacios = true;
                    if(array[indice].domingo.status == 1 && (helperService.empty(array[indice].domingo.apertura, 'date') == true || helperService.empty(array[indice].domingo.cierre, 'date') == true))
                        hayVacios = true;
                });


                return hayVacios;
        }
        

        $scope.actualizar = function(){
         
          
           
           

            if(($scope.datos.loterias.length > 0) == false){
                alert("No hay loterias");
                return;
            }
            
            if(hayHorasVacias() ==true){
                alert("Error: Hay campos vacios");
                return;
            }
 
            _convertir_apertura_y_cierre(true, true);

            
           console.log('actualizr:', $scope.datos.loterias);
        //    return;
        $scope.datos.servidor = servidorGlobal;
        var jwt = helperService.createJWT($scope.datos);
          $http.post(rutaGlobal+"/api/horarios/normal/guardar", {'action':'sp_bancas_actualizar', 'datos': jwt})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0){
                    
                            $scope.inicializarDatos(true);
                            alert("Se ha guardado correctamente");
                     
                }else{
                    alert(response.data.mensaje);
                    return;
                }
                
            });
        

        }


       


        $scope.ckbDias_changed = function(check, d){
            console.log('ckbSorteos_changed: ', d);
            if(d.existe){
                if(d.descripcion == "Lunes")
                    $scope.datos.selectedLoteria.lunes.status = 1;
                if(d.descripcion == "Martes")
                    $scope.datos.selectedLoteria.martes.status = 1;
                if(d.descripcion == "Miercoles")
                    $scope.datos.selectedLoteria.miercoles.status = 1;
                if(d.descripcion == "Jueves")
                    $scope.datos.selectedLoteria.jueves.status = 1;
                if(d.descripcion == "Viernes")
                    $scope.datos.selectedLoteria.viernes.status = 1;
                if(d.descripcion == "Sabado")
                    $scope.datos.selectedLoteria.sabado.status = 1;
                if(d.descripcion == "Domingo")
                    $scope.datos.selectedLoteria.domingo.status = 1;
            }
            else{
                if(d.descripcion == "Lunes")
                    $scope.datos.selectedLoteria.lunes.status = 0;
                if(d.descripcion == "Martes")
                    $scope.datos.selectedLoteria.martes.status = 0;
                if(d.descripcion == "Miercoles")
                    $scope.datos.selectedLoteria.miercoles.status = 0;
                if(d.descripcion == "Jueves")
                    $scope.datos.selectedLoteria.jueves.status = 0;
                if(d.descripcion == "Viernes")
                    $scope.datos.selectedLoteria.viernes.status = 0;
                if(d.descripcion == "Sabado")
                    $scope.datos.selectedLoteria.sabado.status = 0;
                if(d.descripcion == "Domingo")
                    $scope.datos.selectedLoteria.domingo.status = 0;
            }
            
        }


        $scope.ckbPermisosAdicionales_changed = function(check, d){
            console.log('ckbPermisosAdicionales changed: ', check);
            if(d.existe){
                $scope.datos.dias.push(d);
            }
            else{
                if($scope.datos.dias.find(x => x.idDia == d.idDia) != undefined){

                    let idx = $scope.datos.dias.findIndex(x => x.idDia == d.idDia);
                    $scope.datos.dias.splice(idx,1);
                }
            }
            
        }

        $scope.rbxLoteriasChanged = function(d, first = null){
            /* Si el parametro opcionar first es igual a "true" estonces eso quiere decir que es el primer elemento del ngRepeat loteria entonces la loteria dada se seleccionadara
               si el parametro opcionar first es igual a "null" eso quiere decir que el parametro no se ha usado entonces la loteria dada se seleccionadara
               de lo contrario la loteria no podra seleccionarse */

               console.log("hora antes: ", $scope.datos.selectedLoteria);
            
            if(first == true || first == null)
                $scope.datos.selectedLoteria = d;
            
                

            
            

            $scope.datos.ckbDias.forEach(function(valir, indice, array){
                if(array[indice].descripcion == "Lunes"){
                    if($scope.datos.selectedLoteria.lunes.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
                if(array[indice].descripcion == "Martes"){
                    if($scope.datos.selectedLoteria.martes.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
                if(array[indice].descripcion == "Miercoles"){
                    if($scope.datos.selectedLoteria.miercoles.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
                if(array[indice].descripcion == "Jueves"){
                    if($scope.datos.selectedLoteria.jueves.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
                if(array[indice].descripcion == "Viernes"){
                    if($scope.datos.selectedLoteria.viernes.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
                if(array[indice].descripcion == "Sabado"){
                    if($scope.datos.selectedLoteria.sabado.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
                if(array[indice].descripcion == "Domingo"){
                    if($scope.datos.selectedLoteria.domingo.status == 1){
                        array[indice].existe = true;
                    }else{
                        array[indice].existe = false;
                    }
                }
            });

            console.log("hora despues: ", $scope.datos.selectedLoteria);
               
        }

    

    

        $scope.existeSorteo = function(sorteo, es_comisiones = true){
            //console.log('existesorteo: ', $scope.datos.comisiones.selectedLoteria);
            var existe = false;
            if(es_comisiones){
                if($scope.datos.comisiones.selectedLoteria.sorteos == undefined)
                return false;
            
                $scope.datos.comisiones.selectedLoteria.sorteos.forEach(function(valor, indice, array){
                    //console.log('existesorteo: parametro: ', sorteo, ' varia: ', array[indice].descripcion);
                    if(sorteo == array[indice].descripcion)
                        existe = true;
                });
            }else{
                if($scope.datos.pagosCombinaciones.selectedLoteria.sorteos == undefined)
                return false;
            
                $scope.datos.pagosCombinaciones.selectedLoteria.sorteos.forEach(function(valor, indice, array){
                    //console.log('existesorteo: parametro: ', sorteo, ' varia: ', array[indice].descripcion);
                    if(sorteo == array[indice].descripcion)
                        existe = true;
                });
            }

            console.log('existe: ', existe);
            return existe;
        }
       


    })



  

myApp.directive('ngModelOnblur', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        priority: 1, // needed for angular 1.2.x
        link: function(scope, elm, attr, ngModelCtrl) {
            if (attr.type === 'radio' || attr.type === 'checkbox') return;

            elm.unbind('input').unbind('keydown').unbind('change');
            elm.bind('blur', function() {
                scope.$apply(function() {
                    ngModelCtrl.$setViewValue(elm.val());
                });         
            });
        }
    };
});