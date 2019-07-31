myApp
    .controller("myController", function($scope, $http, $timeout){
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
                    phora = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    phora = phora.toString() + ":" + a[1];
                    console.log('actualizar: convertido: ', phora); 
                }
                else{
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

         
            if(todas == false){
                
                    $scope.datos.loterias[idx].lunes.apertura = hora_convertir($scope.datos.loterias[idx].lunes.apertura, _24);
                    $scope.datos.loterias[idx].lunes.cierre = hora_convertir($scope.datos.loterias[idx].lunes.cierre, _24);
                    $scope.datos.loterias[idx].martes.apertura = hora_convertir($scope.datos.loterias[idx].martes.apertura, _24);
                    $scope.datos.loterias[idx].martes.cierre = hora_convertir($scope.datos.loterias[idx].martes.cierre, _24);
                    $scope.datos.loterias[idx].miercoles.apertura = hora_convertir($scope.datos.loterias[idx].miercoles.apertura, _24);
                    $scope.datos.loterias[idx].miercoles.cierre = hora_convertir($scope.datos.loterias[idx].miercoles.cierre, _24);
                    $scope.datos.loterias[idx].jueves.apertura = hora_convertir($scope.datos.loterias[idx].jueves.apertura, _24);
                    $scope.datos.loterias[idx].jueves.cierre = hora_convertir($scope.datos.loterias[idx].jueves.cierre, _24);
                    $scope.datos.loterias[idx].viernes.apertura = hora_convertir($scope.datos.loterias[idx].viernes.apertura, _24);
                    $scope.datos.loterias[idx].viernes.cierre = hora_convertir($scope.datos.loterias[idx].viernes.cierre, _24);
                    $scope.datos.loterias[idx].sabado.apertura = hora_convertir($scope.datos.loterias[idx].sabado.apertura, _24);
                    $scope.datos.loterias[idx].sabado.cierre = hora_convertir($scope.datos.loterias[idx].sabado.cierre, _24);
                    $scope.datos.loterias[idx].domingo.apertura = hora_convertir($scope.datos.loterias[idx].domingo.apertura, _24);
                    $scope.datos.loterias[idx].domingo.cierre = hora_convertir($scope.datos.loterias[idx].domingo.cierre, _24);
            }else{
                $scope.datos.loterias.forEach(function(valor, indice, array){
                    array[indice].lunes.apertura = hora_convertir(array[indice].lunes.apertura, _24);
                    array[indice].lunes.cierre = hora_convertir(array[indice].lunes.cierre, _24);
                    array[indice].martes.apertura = hora_convertir(array[indice].martes.apertura, _24);
                    array[indice].martes.cierre = hora_convertir(array[indice].martes.cierre, _24);
                    array[indice].miercoles.apertura = hora_convertir(array[indice].miercoles.apertura, _24);
                    array[indice].miercoles.cierre = hora_convertir(array[indice].miercoles.cierre, _24);
                    array[indice].jueves.apertura = hora_convertir(array[indice].jueves.apertura, _24);
                    array[indice].jueves.cierre = hora_convertir(array[indice].jueves.cierre, _24);
                    array[indice].viernes.apertura = hora_convertir(array[indice].viernes.apertura, _24);
                    array[indice].viernes.cierre = hora_convertir(array[indice].viernes.cierre, _24);
                    array[indice].sabado.apertura = hora_convertir(array[indice].sabado.apertura, _24);
                    array[indice].sabado.cierre = hora_convertir(array[indice].sabado.cierre, _24);
                    array[indice].domingo.apertura = hora_convertir(array[indice].domingo.apertura, _24);
                    array[indice].domingo.cierre = hora_convertir(array[indice].domingo.cierre, _24);
                });
            }
            
            
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
            
               
            $http.get(rutaGlobal+"/api/horarios")
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
                    array[indice].lunes.apertura = hora_convertir("01:00:00");
                    array[indice].lunes.cierre = hora_convertir("23:00:00");
                    array[indice].lunes.status = 1;
                    
                    array[indice].martes = {};
                    array[indice].martes.apertura = hora_convertir("01:00:00");
                    array[indice].martes.cierre = hora_convertir("23:00:00");
                    array[indice].martes.status = 1;

                    array[indice].miercoles = {};
                    array[indice].miercoles.apertura = hora_convertir("01:00:00");
                    array[indice].miercoles.cierre = hora_convertir("23:00:00");
                    array[indice].miercoles.status = 1;

                    array[indice].jueves = {};
                    array[indice].jueves.apertura = hora_convertir("01:00:00");
                    array[indice].jueves.cierre = hora_convertir("23:00:00");
                    array[indice].jueves.status = 1;

                    array[indice].viernes = {};
                    array[indice].viernes.apertura = hora_convertir("01:00:00");
                    array[indice].viernes.cierre = hora_convertir("23:00:00");
                    array[indice].viernes.status = 1;

                    array[indice].sabado = {};
                    array[indice].sabado.apertura = hora_convertir("01:00:00");
                    array[indice].sabado.cierre = hora_convertir("23:00:00");
                    array[indice].sabado.status = 1;

                    array[indice].domingo = {};
                    array[indice].domingo.apertura = hora_convertir("01:00:00");
                    array[indice].domingo.cierre = hora_convertir("23:00:00");
                    array[indice].domingo.status = 1;
                    

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
                                array[indice].lunes.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].lunes.cierre = arrayDias[indiceDias].pivot.horaCierre;
                                console.log("lunes: ",arrayDias[indiceDias].pivot.horaApertura);
                            }
                            else if(arrayDias[indiceDias].descripcion == "Martes"){
                                array[indice].martes.status = 1;
                                array[indice].martes.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].martes.cierre = arrayDias[indiceDias].pivot.horaCierre;
                            }
                            else if(arrayDias[indiceDias].descripcion == "Miercoles"){
                                array[indice].miercoles.status = 1;
                                array[indice].miercoles.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].miercoles.cierre = arrayDias[indiceDias].pivot.horaCierre;
                            }
                            else if(arrayDias[indiceDias].descripcion == "Jueves"){
                                array[indice].jueves.status = 1;
                                array[indice].jueves.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].jueves.cierre = arrayDias[indiceDias].pivot.horaCierre;
                            }
                            else if(arrayDias[indiceDias].descripcion == "Viernes"){
                                array[indice].viernes.status = 1;
                                array[indice].viernes.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].viernes.cierre = arrayDias[indiceDias].pivot.horaCierre;
                            }
                            else if(arrayDias[indiceDias].descripcion == "Sabado"){
                                array[indice].sabado.status = 1;
                                array[indice].sabado.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].sabado.cierre = arrayDias[indiceDias].pivot.horaCierre;
                            }
                            else{
                                array[indice].domingo.status = 1;
                                array[indice].domingo.apertura = arrayDias[indiceDias].pivot.horaApertura;
                                array[indice].domingo.cierre = arrayDias[indiceDias].pivot.horaCierre;
                            }
                        });
                    }
                });

                $scope.datos.loterias = jsonLoterias;
                
                
                

                var jsonDias = response.data.dias;
                jsonDias.forEach(function(valor, indice, array){
                    $scope.datos.ckbDias.push({'id' :array[indice].id, 'descripcion': array[indice].descripcion, 'existe' : true});
                });



                //$scope.editar(false, $scope.selectedLoteria);
                // $scope.datos.ckbLoterias = [];
                // jsonLoterias.forEach(function(valor, indice, array){
                //         array[indice].existe = true;
                //         $scope.datos.ckbLoterias.push(array[indice]);
                //     });

                


                // $scope.datos.loteriasSeleccionadas = $scope.datos.ckbLoterias;
                // $scope.datos.comisiones.loterias = $scope.datos.loteriasSeleccionadas;
                // $scope.datos.pagosCombinaciones.loterias = $scope.datos.loteriasSeleccionadas;



              
                
                

              
                

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


        


        $scope.editar = function(esNuevo, d){
            
            $scope.datos.mostrarFormEditar = true;

            if(esNuevo){
                $scope.inicializarDatos(true);

                $scope.datos.ckbLoterias.forEach(function(valor, indice, array){

                    array[indice].existe = true;

                 });
            }
            else{
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;

                $('.form-group').addClass('is-filled');


                $scope.rbxLoteriasChanged(d);
                $scope.selectedLoteria.lunes.status = 0;
                $scope.selectedLoteria.martes.status = 0;
                $scope.selectedLoteria.miercoles.status = 0;
                $scope.selectedLoteria.jueves.status = 0;
                $scope.selectedLoteria.viernes.status = 0;
                $scope.selectedLoteria.sabado.status = 0;
                $scope.selectedLoteria.domingo.status = 0;

                
                d.dias.forEach(function(valor, indice, array){
                    if(array[indice].descripcion == "Lunes"){
                        $scope.selectedLoteria.lunes.status = 1;
                        $scope.selectedLoteria.lunes.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.lunes.apertura = array[indice].pivot.horaCierre;
                    }
                    else if(array[indice].descripcion == "Martes"){
                        $scope.selectedLoteria.martes.status = 1;
                        $scope.selectedLoteria.martes.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.martes.apertura = array[indice].pivot.horaCierre;
                    }
                    else if(array[indice].descripcion == "Miercoles"){
                        $scope.selectedLoteria.miercoles.status = 1;
                        $scope.selectedLoteria.miercoles.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.miercoles.apertura = array[indice].pivot.horaCierre;
                    }
                    else if(array[indice].descripcion == "Jueves"){
                        $scope.selectedLoteria.jueves.status = 1;
                        $scope.selectedLoteria.jueves.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.jueves.apertura = array[indice].pivot.horaCierre;
                    }
                    else if(array[indice].descripcion == "Viernes"){
                        $scope.selectedLoteria.viernes.status = 1;
                        $scope.selectedLoteria.viernes.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.viernes.apertura = array[indice].pivot.horaCierre;
                    }
                    else if(array[indice].descripcion == "Sabado"){
                        $scope.selectedLoteria.sabado.status = 1;
                        $scope.selectedLoteria.sabado.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.sabado.apertura = array[indice].pivot.horaCierre;
                    }
                    else{
                        $scope.selectedLoteria.domingo.status = 1;
                        $scope.selectedLoteria.domingo.apertura = array[indice].pivot.horaApertura;
                        $scope.selectedLoteria.domingo.apertura = array[indice].pivot.horaCierre;
                    }
                });

               
                

                $scope.datos.ckbDias.forEach(function(valor, indice, array){

                    if(d.dias.find(x => x.id == array[indice].id) == undefined)
                        array[indice].existe = false;

                 });


                


                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                  });
            }
        }

       
        

        $scope.actualizar = function(){
         
          
           
           

            if(($scope.datos.loterias.length > 0) == false){
                alert("No hay loterias");
                return;
            }
            

 
            _convertir_apertura_y_cierre(true, true);

            
           
          $http.post(rutaGlobal+"/api/horarios/normal/guardar", {'action':'sp_bancas_actualizar', 'datos': $scope.datos})
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


        $scope.eliminar = function(d){
            console.log('bancas eliminar: ',d);
            $http.post(rutaGlobal+"/api/bancas/eliminar", {'action':'sp_bancas_elimnar', 'datos': d})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
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

    

        $scope.comisionSorteo = function(monto, idLoteria, idSorteo){
            let idx = $scope.datos.comisiones.selectedLoteria.sorteos.findIndex(x => x.id == idSorteo && x.pivot.idLoteria == idLoteria);
            $scope.datos.comisiones.selectedLoteria.sorteos[idx].monto = monto;
            console.log($scope.datos.comisiones.selectedLoteria.sorteos[idx]);
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