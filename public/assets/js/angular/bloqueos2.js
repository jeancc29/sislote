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
            },
            'bloqueoJugada' : {
                "jugada" : null,
                "monto" : null,
                "fechaDesde" : new Date(),
                "fechaHasta" : new Date(),
            },
            'buscar' : {
                "dias" : [],
                "bancas" : [],
                "fechaDesde" : new Date(),
                "fechaHasta" : new Date(),
            },
            "tabSelectedDia" : {},
            "tabSelectedBanca" : {},
            "tabSelectedLoteria" : {}
        }

        
        $scope.inicializarDatos = function(idUsuario = 0, response = null, responseJugadas = null){
               
            if(response != null){
                 
                $scope.datos.optionsTipoBloqueos = [{'idTipoBloqueo' : 1, 'descripcion' : 'General para grupo'}, {'idTipoBloqueo': 2, 'descripcion' : 'General por banca'}];
                $scope.datos.selectedTipoBloqueos = $scope.datos.optionsTipoBloqueos[0];

                var jsonLoterias = response.data.loterias;
                $scope.datos.optionsBancas = [];
                $scope.datos.optionsBancas = response.data.bancas;
                $scope.datos.selectedBanca = $scope.datos.optionsBancas[0];

                $scope.datos.buscar.optionsBancas = response.data.bancas;
                $scope.datos.buscar.optionsDias = response.data.dias;
                


                $scope.datos.loterias= [];
                jsonLoterias.forEach(function(valor, indice, array){
                    array[indice].seleccionado = false;
                    $scope.datos.loterias.push(array[indice]);
                });


                $scope.datos.ckbDias = [];
                var jsonDias = response.data.dias;
                jsonDias.forEach(function(valor, indice, array){
                    $scope.datos.ckbDias.push({'id' :array[indice].id, 'descripcion': array[indice].descripcion, 'existe' : true});
                });

                var jsonSorteos = response.data.sorteos;
                $scope.datos.sorteos = [];
                jsonSorteos.forEach(function(valor, indice, array){
                    array[indice].monto = '';
                    $scope.datos.sorteos.push(array[indice]);
                });
                $scope.datos.optionsSorteos = jsonSorteos;
                $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];

                console.log('inicializarDatos bloqueo2.js: ', $scope.datos.optionsSorteos);



              
                

                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    // $('#cbxBanca').selectpicker('val', [])
                  })

                return;
            }
            if(responseJugadas != null){
                 
                $scope.datos.bloqueoJugada.optionsTipoBloqueos = [{'idTipoBloqueo' : 1, 'descripcion' : 'General para grupo'}, {'idTipoBloqueo': 2, 'descripcion' : 'General por banca'}];
                $scope.datos.bloqueoJugada.selectedTipoBloqueos = $scope.datos.bloqueoJugada.optionsTipoBloqueos[0];

                var jsonLoterias = responseJugadas.data.loterias;
                $scope.datos.bloqueoJugada.optionsBancas = [];
                $scope.datos.bloqueoJugada.optionsBancas = responseJugadas.data.bancas;
                $scope.datos.bloqueoJugada.selectedBanca = $scope.datos.bloqueoJugada.optionsBancas[0];

                // $scope.datos.buscar.optionsBancas = response.data.bancas;
                // $scope.datos.buscar.optionsDias = response.data.dias;

                $scope.datos.bloqueoJugada.loterias= [];
                jsonLoterias.forEach(function(valor, indice, array){
                    array[indice].seleccionado = false;
                    $scope.datos.bloqueoJugada.loterias.push(array[indice]);
                });

                var jsonSorteos = responseJugadas.data.sorteos;
                $scope.datos.sorteos = [];
                jsonSorteos.forEach(function(valor, indice, array){
                    array[indice].monto = '';
                    $scope.datos.sorteos.push(array[indice]);
                });
                $scope.datos.optionsSorteos = jsonSorteos;
                $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];

                console.log('inicializarDatos bloqueo2.js: ', $scope.datos.optionsSorteos);


                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    // $('#cbxBanca').selectpicker('val', [])
                  })

                return;
            }
            $http.get(rutaGlobal+"/api/bloqueos")
             .then(function(response){
                console.log('Bancas: ', response.bancas);

                

                
                $scope.datos.optionsTipoBloqueos = [{'idTipoBloqueo' : 1, 'descripcion' : 'General para grupo'}, {'idTipoBloqueo': 2, 'descripcion' : 'General por banca'}];
                $scope.datos.selectedTipoBloqueos = $scope.datos.optionsTipoBloqueos[0];

                var jsonLoterias = response.data.loterias;
                $scope.datos.optionsBancas = response.data.bancas;
                $scope.datos.selectedBanca = $scope.datos.optionsBancas[0];

                $scope.datos.buscar.optionsBancas = response.data.bancas;
                $scope.datos.buscar.optionsDias = response.data.dias;



                jsonLoterias.forEach(function(valor, indice, array){
                    array[indice].seleccionado = false;
                    $scope.datos.loterias.push(array[indice]);
                });


                var jsonDias = response.data.dias;
                jsonDias.forEach(function(valor, indice, array){
                    $scope.datos.ckbDias.push({'id' :array[indice].id, 'descripcion': array[indice].descripcion, 'existe' : true});
                });

                var jsonSorteos = response.data.sorteos;
                $scope.datos.sorteos = [];
                jsonSorteos.forEach(function(valor, indice, array){
                    array[indice].monto = '';
                    $scope.datos.sorteos.push(array[indice]);
                });



              
                $scope.datos.bloqueoJugada.optionsTipoBloqueos = [{'idTipoBloqueo' : 1, 'descripcion' : 'General para grupo'}, {'idTipoBloqueo': 2, 'descripcion' : 'General por banca'}];
                $scope.datos.bloqueoJugada.selectedTipoBloqueos = $scope.datos.bloqueoJugada.optionsTipoBloqueos[0];

                jsonLoterias = response.data.loterias;
                $scope.datos.bloqueoJugada.optionsBancas = [];
                $scope.datos.bloqueoJugada.optionsBancas = response.data.bancas;
                $scope.datos.bloqueoJugada.selectedBanca = $scope.datos.bloqueoJugada.optionsBancas[0];



                $scope.datos.bloqueoJugada.loterias= [];
                jsonLoterias.forEach(function(valor, indice, array){
                    array[indice].seleccionado = false;
                    $scope.datos.bloqueoJugada.loterias.push(array[indice]);
                });

                var jsonSorteos = response.data.sorteos;
                $scope.datos.sorteos = [];
                jsonSorteos.forEach(function(valor, indice, array){
                    array[indice].monto = '';
                    $scope.datos.sorteos.push(array[indice]);
                });
                $scope.datos.optionsSorteos = jsonSorteos;
                $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];

                console.log('inicializarDatos bloqueo2.js: ', $scope.datos.optionsSorteos);



                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    // $('#cbxBanca').selectpicker('val', [])
                  })
               
                
                
            });

           
           
       
        }
        

        $scope.load = function(codigo_usuario){
            $scope.inicializarDatos(true);
            $scope.datos.idUsuario = codigo_usuario;
 
        }


        



       
        

        $scope.actualizar = function(){
         
        //   var contador = 0;
        //     $scope.datos.loterias.forEach(function(valor, indice, array){
        //         if(array[indice].seleccionado == true)
        //             contador++;
        //     });

        //     if(contador == 0){
        //         alert("Debe seleccionar al menos una loteria");
        //         return;
        //     }
           
        //     contador = 0;
        //     $scope.datos.sorteos.forEach(function(valor, indice, array){
        //         if(array[indice].monto != undefined && array[indice].monto == null && array[indice].monto == '' && Number(array[indice].monto) == array[indice].monto)
        //             contador++;
        //     });

        //     if(contador == 0){
        //         alert("Todos los campos estan vacios");
        //         return;
        //     }

        //     contador = 0;
        //     $scope.datos.ckbDias.forEach(function(valor, indice, array){
        //         if(array[indice].monto == true)
        //             contador++;
        //     });

        //     if(contador == 0){
        //         alert("Debe seleccionar los dias");
        //         return;
        //     }
            
        console.log('sorteos: ', $scope.datos.sorteos);


        
        if($scope.datos.selectedTipoBloqueos.idTipoBloqueo == 1){
            $scope.datos.bancas =  $scope.datos.optionsBancas;
        }else{
            if(Object.keys($scope.datos.bancas).length == 0){
                alert("Debe seleccionar una banca")
                return;
            }
        }
            
          $http.post(rutaGlobal+"/api/bloqueos/loterias/guardar", {'action':'sp_bancas_actualizar', 'datos': $scope.datos})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0){
                    
                            $scope.inicializarDatos($scope.datos.idUsuario, response);
                            alert("Se ha guardado correctamente");
                     
                }else{
                    alert(response.data.mensaje);
                    return;
                }
                
            });
        

        }

        $scope.actualizarJugadas = function(){
         
            if($scope.datos.bloqueoJugada.selectedTipoBloqueos.idTipoBloqueo == 1){
                $scope.datos.bloqueoJugada.bancas =  $scope.datos.bloqueoJugada.optionsBancas;
            }else{
                if(Object.keys($scope.datos.bloqueoJugada.bancas).length == 0){
                    alert("Debe seleccionar una banca")
                    return;
                }
            }

            if(helperService.empty($scope.datos.bloqueoJugada.monto, 'number') == true){
                alert("El campo monto no puede estar vacio y ser numerico")
                    return;
            }

            $scope.datos.bloqueoJugada.idUsuario = $scope.datos.idUsuario;
            $scope.datos.bloqueoJugada.idSorteo = $scope.datos.selectedSorteo.id;
                
            $http.post(rutaGlobal+"/api/bloqueos/jugadas/guardar", {'action':'sp_bancas_actualizar', 'datos': $scope.datos.bloqueoJugada})
                .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    
                            $scope.inicializarDatos($scope.datos.idUsuario, null, response);
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
            console.log('ckbDias changed: ', d);
            // if(d.existe){
            //     $scope.datos.dias.push(d);
            // }
            // else{
            //     if($scope.datos.dias.find(x => x.id == d.id) != undefined){

            //         let idx = $scope.datos.dias.findIndex(x => x.id == d.id);
            //         $scope.datos.dias.splice(idx,1);
            //     }
            // }
            
        }

        $scope.cbxTipoBloqueosChanged = function(){
           
               $scope.datos.bancas = [];

               $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                $('#multiselect').selectpicker("refresh");
               
              })
           
            
        }

        $scope.cbxTipoBloqueosJugadaChanged = function(){
           
               $scope.datos.bloqueoJugada.bancas = [];

               $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                $('#multiselect').selectpicker("refresh");
               
              })
           
            
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

        $scope.rbxLoteriasChanged = function(d, idx){

            $('#optionLabel'+ idx).removeClass('focus');
            
            if($scope.datos.loterias.find(x => x.id == d.id) != undefined){
                let idx = $scope.datos.loterias.findIndex(x => x.id == d.id);
                if($scope.datos.loterias[idx].seleccionado == true){
                    $scope.datos.loterias[idx].seleccionado = false;
                    console.log("rbxLoteriasChanged false, select: ",$scope.datos.loterias[idx].seleccionado , " hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
                    $('#btnLoteria'+ idx).removeClass('active2');
                   
                    // if($('#optionLabel'+ idx).hasClass('active2'))
                    //     {
                    //         $('#optionLabel'+ idx).removeClass('active2');
                    //         $('#optionLabel'+ idx).removeClass('active');
                    //         $('#optionLabel'+ idx).removeClass('focus');
                    //     }
                }
                else
                    {
                        // $('#optionLabel'+ idx).addClass('active2');
                        // if($('#optionLabel'+ idx).hasClass('active2') == false)
                        //     {
                        //         $('#optionLabel'+ idx).addClass('active2');
                        //         $('#optionLabel'+ idx).removeClass('focus');
                        //         $('#optionLabel'+ idx).removeClass('active');
                        //     }
                        
                        $scope.datos.loterias[idx].seleccionado = true;
                        $('#btnLoteria'+ idx).addClass('active2');
                        console.log("rbxLoteriasChanged false, select: ",$scope.datos.loterias[idx].seleccionado , " hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
               
                    }
            }


            

            console.log("rbxLoteriasChanged, hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
               
        }

        $scope.rbxLoteriasJugadasChanged = function(d, idx){

            $('#optionLabel'+ idx).removeClass('focus');
            
            if($scope.datos.loterias.find(x => x.id == d.id) != undefined){
                let idx = $scope.datos.loterias.findIndex(x => x.id == d.id);
                if($scope.datos.bloqueoJugada.loterias[idx].seleccionado == true){
                    $scope.datos.bloqueoJugada.loterias[idx].seleccionado = false;
                    console.log("rbxLoteriasChanged false, select: ",$scope.datos.loterias[idx].seleccionado , " hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
                    $('#btnLoteriaJugada'+ idx).removeClass('active2');
                   
                    // if($('#optionLabel'+ idx).hasClass('active2'))
                    //     {
                    //         $('#optionLabel'+ idx).removeClass('active2');
                    //         $('#optionLabel'+ idx).removeClass('active');
                    //         $('#optionLabel'+ idx).removeClass('focus');
                    //     }
                }
                else
                    {
                        // $('#optionLabel'+ idx).addClass('active2');
                        // if($('#optionLabel'+ idx).hasClass('active2') == false)
                        //     {
                        //         $('#optionLabel'+ idx).addClass('active2');
                        //         $('#optionLabel'+ idx).removeClass('focus');
                        //         $('#optionLabel'+ idx).removeClass('active');
                        //     }
                        
                        $scope.datos.bloqueoJugada.loterias[idx].seleccionado = true;
                        $('#btnLoteriaJugada'+ idx).addClass('active2');
                        console.log("rbxLoteriasChanged false, select: ",$scope.datos.loterias[idx].seleccionado , " hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
               
                    }
            }


            

            console.log("rbxLoteriasChanged, hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
               
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


        $scope.buscar = function(){
            if(helperService.empty($scope.datos.buscar.dias, 'object') == true){
                alert("Debe seleccionar los dias");
                return;
            }
            if(helperService.empty($scope.datos.buscar.bancas, 'object') == true){
                alert("Debe seleccionar las bancas");
                return;
            }
            $scope.datos.buscar.idUsuario = idUsuario;
            $http.post(rutaGlobal+"/api/bloqueos/loterias/buscar", {'action':'sp_bancas_actualizar', 'datos': $scope.datos.buscar})
             .then(function(response){
                console.log(response.data);
                $scope.datos.buscar.resultados = response.data.dias;
                $scope.tabDiasChanged($scope.datos.buscar.resultados[0]);
                $scope.tabBancasChanged($scope.datos.tabSelectedDia.bancas[0]);
                $scope.tabLoteriasChanged($scope.datos.tabSelectedBanca.loterias[0]);
                if(response.data.errores == 0){
                    
                            // $scope.inicializarDatos($scope.datos.idUsuario, response);
                            // alert("Se ha guardado correctamente");
                     
                }else{
                    //alert(response.data.mensaje);
                    return;
                }
                
            });
        }

        $scope.contador = 0;
        $scope.tabDiasChanged = function(dia, first = null){
            $scope.contador++;
            $scope.datos.tabSelectedDia = dia;
            $scope.tabBancasChanged($scope.datos.tabSelectedDia.bancas[0])
            $scope.tabLoteriasChanged($scope.datos.tabSelectedBanca.loterias[0]);

            // $scope.datos.indexLoteriaComisiones = $scope.datos.ckbLoterias.findIndex( x => x.id == loteria.id);
        }
    
        $scope.tabBancasChanged = function(banca, first = null){
            $scope.datos.tabSelectedBanca = banca;
            console.log("tabBancasChanged",$scope.datos.tabSelectedDia);
            // $scope.datos.indexLoteriaComisiones = $scope.datos.ckbLoterias.findIndex( x => x.id == loteria.id);
        }
    
        $scope.tabLoteriasChanged = function(loteria, first = null){
            $scope.datos.tabSelectedLoteria = loteria;
            // $scope.datos.indexLoteriaComisiones = $scope.datos.ckbLoterias.findIndex( x => x.id == loteria.id);
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