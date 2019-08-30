myApp
    .controller("myController", function($scope, $http, helperService){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "id":0,
            'idUsuario' : 0,
            "montoPrestado": null,
            "numeroCuotas" : null,
            "montoCuotas" : null,
            "tasaInteres" : null,
            "idFrecuencia" : null,
            "idEntidadPrestamo" : null,
            "idEntidadFondo" : null,
            "detalles" : null,
            'fechaInicio' : new Date(),
            "status":true,
            

            "primera" : null,
            "segunda" : null,
            "tercera" : null,
            "primeraSegunda" : null,
            "primeraTercera" : null,
            "segundaTercera" : null,
            "tresNumeros" : null,
            "dosNumeros" : null,

            "optionsBancas": [],
            "optionsBancos": [],
            "optionsFrecuencias": [],
            "mostrarFormEditar" : false,
            'selectedBanca' : {},
            'selectedDia' : {},
            'selectedBanco' : {},
            'selectedFrecuencia' : {}
        }

        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }

        $scope.inicializarDatos = function(todos){
            $scope.datos.prestamos = prestamosGlobal;
            $scope.datos.optionsBancas = bancasGlobal;
            $scope.datos.optionsDias = diasGlobal;
            $scope.datos.optionsBancos = bancosGlobal;
            $scope.datos.optionsFrecuencias = frecuenciasGlobal;

            $scope.datos.selectedBanca = $scope.datos.optionsBancas[helperService.retornarIndexPorId($scope.datos.selectedBanca, $scope.datos.optionsBancas)];
            $scope.datos.selectedDia = $scope.datos.optionsDias[helperService.retornarIndexPorId($scope.datos.selectedDia, $scope.datos.optionsDias)];
            $scope.datos.selectedBanco = $scope.datos.optionsBancos[helperService.retornarIndexPorId($scope.datos.selectedBanco, $scope.datos.optionsBancos)];
            $scope.datos.selectedFrecuencia = $scope.datos.optionsFrecuencias[helperService.retornarIndexPorId($scope.datos.selectedFrecuencia, $scope.datos.optionsFrecuencias)];

            console.log('bancos:', $scope.datos.prestamos);
               
            // $http.get(rutaGlobal+"/api/loterias")
            //  .then(function(response){
            //     console.log('Loteria ajav: ', response.data.loterias);

            //     if(todos){
            //         $scope.datos.id = 0;
            //         $scope.datos.descripcion = null,
            //         $scope.datos.abreviatura = null,
            //         $scope.datos.status = true;
            //        // $scope.datos.dias = [];
            //         $scope.datos.sorteos = [];
            //         $scope.datos.horaCierre = moment().format('YYYY/MM/DD');
            //        // $scope.datos.ckbDias = [];
            //         $scope.datos.ckbSorteos = [];


            //         var jsonSorteos = response.data.sorteos;
            //         jsonSorteos.forEach(function(valor, indice, array){
            //             $scope.datos.ckbSorteos.push({'id' :array[indice].id, 'descripcion': array[indice].descripcion, 'existe' : false});
            //         });
                    
            //        }

            //     $scope.datos.loterias= [];
            //     var jsonLoterias = response.data.loterias;
            //     jsonLoterias.forEach(function(valor, indice, array){
            //         array[indice].seleccionado = false;
            //         $scope.datos.loterias.push(array[indice]);
            //     })
            //    // $scope.datos.loterias =response.data.loterias;
            
                
            // });
       
        }
        

        $scope.load = function(codigo_usuario){
            $scope.inicializarDatos(true);    
        }


        


        $scope.editar = function(esNuevo, d){
            
            
            console.log('editar: ', d, ' es nuevo: ', esNuevo);

            if(esNuevo){
                $scope.inicializarDatos(true);
            }
            else{
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;

                $('.form-group').addClass('is-filled');

                $scope.datos.id = d.id;
                $scope.datos.descripcion = d.descripcion;
                $scope.datos.abreviatura = d.abreviatura;
                $scope.datos.status = (d.status == 1) ? true : false;
                //$scope.datos.horaCierre = d.horaCierre;
                //$scope.hora_convertir(false);

                // $scope.datos.ckbDias.forEach(function(valor, indice, array){

                //     array[indice].existe = false;

                //  });

                $scope.datos.ckbSorteos.forEach(function(valor, indice, array){

                    array[indice].existe = false;

                 });

                   
                

                // if(d.dias != undefined){

                //     $scope.datos.dias = d.dias;

                //     $scope.datos.dias.forEach(function(valor, indice, array){

                //         if($scope.datos.ckbDias.find(x => x.id == array[indice].id) != undefined){
                //             let idx = $scope.datos.ckbDias.findIndex(x => x.id == parseInt(array[indice].id));
                //             $scope.datos.ckbDias[idx].existe = true;
                //         }

                //      });
                // }

                if(d.sorteos != undefined){

                    $scope.datos.sorteos = d.sorteos;

                    $scope.datos.sorteos.forEach(function(valor, indice, array){

                        if($scope.datos.ckbSorteos.find(x => x.id == array[indice].id) != undefined){
                            let idx = $scope.datos.ckbSorteos.findIndex(x => x.id == parseInt(array[indice].id));
                            $scope.datos.ckbSorteos[idx].existe = true;
                        }

                     });
                }

                if(d.loteriasRelacionadas != undefined){
                    $scope.datos.loterias.forEach(function(valor, indice, array){
                        console.log('Pivot: ', d.loteriasRelacionadas.find(x => x.pivot.idLoteria == array[indice].id));
                        if(d.loteriasRelacionadas.find(x => x.pivot.idLoteria == array[indice].id)){
                            array[indice].seleccionado = true;
                            $('#btnLoteria'+ indice).addClass('active2');
                        }else{
                            array[indice].seleccionado = false;
                            $('#btnLoteria'+ indice).removeClass('active2');
                        }
    
                     });
                }

                /************* PAGOS COMBINACIONES ********************/

                // if(d.pagosCombinaciones != undefined){
                //     d = d.pagosCombinaciones;
                //     console.log('ediar: ', d);

                //     $scope.datos.primera = d.primera;
                //     $scope.datos.segunda = d.segunda;
                //     $scope.datos.tercera = d.tercera;
                //     $scope.datos.primeraSegunda = d.primeraSegunda;
                //     $scope.datos.primeraTercera = d.primeraTercera;
                //     $scope.datos.segundaTercera = d.segundaTercera;
                //     $scope.datos.tresNumeros = d.tresNumeros;
                //     $scope.datos.dosNumeros = d.dosNumeros;
                // }
                // else{
                //     $scope.datos.primera = null;
                //     $scope.datos.segunda = null;
                //     $scope.datos.tercera = null;
                //     $scope.datos.primeraSegunda = null;
                //     $scope.datos.primeraTercera = null;
                //     $scope.datos.segundaTercera = null;
                //     $scope.datos.tresNumeros = null;
                //     $scope.datos.dosNumeros = null;
                // }
                /************* END PAGOS COMBINACIONES ********************/
                
            }

            $scope.datos.mostrarFormEditar = true;
        }

        $scope.loteria_obtener_por_id = function(){

            $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_loterias_obtener_por_id'})
            .then(function(response){
               console.log('Loteria ajav: ', JSON.parse(response.data[0].dias));

               $scope.datos.loterias =JSON.parse(response.data[0].loterias);
               $scope.datos.ckbDias =JSON.parse(response.data[0].dias);

               console.log('Dentro load: ',moment().fromNow());
           });

        }
        
        $scope.loteriasSeleccionadasCount = function(){
            var contador = 0;
            $scope.datos.loterias.forEach(function(valor, indice, array){
                if(array[indice].seleccionado == true){
                    contador++;
                }
            });

            return contador;
        }

        $scope.superPaleEstaSeleccionado = function(){
            
            var seleccionado = false;
            if(Object.keys($scope.datos.sorteos).length > 0){
                $scope.datos.sorteos.forEach(function(valor, indice, array){
                    if(array[indice].descripcion == "Super pale"){
                        seleccionado = true;
                    }
                });
            }

            console.log("superPaleEstaSeleccionado:", seleccionado);
            return seleccionado;
        }

        $scope.actualizar = function(){
         
            //$scope.datos.horaCierre = moment($scope.datos.horaCierre, ['HH:mm']).format('HH:mm');

            

            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.idEntidadPrestamo = $scope.datos.selectedBanca.id;
            $scope.datos.idEntidadFondo = $scope.datos.selectedBanco.id;
            $scope.datos.idFrecuencia = $scope.datos.selectedFrecuencia.id;

            $scope.datos.status = ($scope.datos.status) ? 1 : 0;
            $scope.datos.horaCierre = $('#horaCierre').val();
            //$scope.hora_convertir(true);


    //   console.log($scope.datos);
    //   return;


          $http.post(rutaGlobal+"/api/prestamos/guardar", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
             .then(function(response){
                 console.log(response.data);
                if(response.data.errores == 0){
                    alert("Se ha guardado correctamente");
                    if($scope.datos.id == 0)
                        $scope.inicializarDatos(true);
                    else{
                        $scope.inicializarDatos(false);
                        $scope.datos.status = ($scope.datos.status == 1) ? true : false;
                    }

                    //$scope.hora_convertir(false);
                }else{
                    alert(response.data.mensaje);
                }
            });
        

        }


        $scope.eliminar = function(d){
            $http.post(rutaGlobal+"/api/loterias/eliminar", {'action':'sp_loterias_elimnar', 'datos': d})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
                
            });
        }
       

        /******************* DIAS *********************/
        // $scope.ckbDias_changed = function(check, d){
        //     console.log('ckbDias changed: ', d);
        //     if(d.existe){
        //         $scope.datos.dias.push(d);
        //     }
        //     else{
        //         if($scope.datos.dias.find(x => x.id == d.id) != undefined){

        //             let idx = $scope.datos.dias.findIndex(x => x.id == d.id);
        //             $scope.datos.dias.splice(idx,1);
        //         }
        //     }
            
        // }
        /******************* END DIAS *********************/


        $scope.ckbSorteos_changed = function(check, d){
            
            if(d.existe){
                if(d.descripcion == "Super pale"){
                   $scope.seleccionarONoTodosLosSorteos(false, "Super pale");
                }
                $scope.datos.sorteos.push(d);
            }
            else{
                if($scope.datos.sorteos.find(x => x.id == d.id) != undefined){

                    let idx = $scope.datos.sorteos.findIndex(x => x.id == d.id);
                    $scope.datos.sorteos.splice(idx,1);
                }
            }
            
            console.log('ckbSorteos_changed: ', $scope.datos.sorteos);
        }

        $scope.seleccionarONoTodosLosSorteos = function(seleccionar, excepto = null){
            //la variable excepto es una variable que va a contener el nombre o descripcion de un sorteo que se quiera o no seleccionar
            if(seleccionar == true){
                $scope.datos.sorteos = [];
                $scope.datos.ckbSorteos.forEach(function(valor, indice, array){
                    // array[indice].existe = true;
                    // $scope.datos.sorteos.push(array[indice]);
                    if(excepto != null){
                        if(array[indice].descripcion != excepto){
                            array[indice].existe = true;
                            $scope.datos.sorteos.push(array[indice]);
                        }
                    }else{
                        array[indice].existe = true;
                        $scope.datos.sorteos.push(array[indice]);
                    }
                });
            }else{
                $scope.datos.sorteos = [];
                $scope.datos.ckbSorteos.forEach(function(valor, indice, array){
                    if(excepto != null){
                        if(array[indice].descripcion != excepto){
                            array[indice].existe = false;
                        }
                    }else{
                        array[indice].existe = false;
                    }
                });
            }
        }


        $scope.hora_convertir = function(_24){
            //Si es verdadero la hora se convertira al formato 24 horas
            if(_24){
                //Si es verdadero eso quiere decir que es PM de lo contrario sera AM
                if($scope.datos.horaCierre.indexOf("PM") != -1){
                    
                    //Aqui se le quitara el PM a la hora
                    var a = $scope.datos.horaCierre.replace(" PM", "");
                    //Aqui la hora se convertira en un arreglo para tener aparte la hora y los minutos
                    a = a.split(':');
                    //La variable hora va a contener el solamente la hora sin minutos ni segundos
                    var hora = parseInt(a[0]);
                    //Aqui se convierte la hora normal en el formato 24 horas
                    $scope.datos.horaCierre = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    $scope.datos.horaCierre = $scope.datos.horaCierre.toString() + ":" + a[1];
                    console.log('actualizar: convertido: ', $scope.datos.horaCierre); 
                }
                else{
                     //Aqui se le quitara el AM a la hora
                     var a = $scope.datos.horaCierre.replace(" AM", "");
                     $scope.datos.horaCierre = a;
                     console.log('actualizar: convertido: ', $scope.datos.horaCierre); 
                }
            }
            else{
                var a = $scope.datos.horaCierre.split(":");
                var hora = parseInt(a[0]);
                if(hora > 12){
                    hora = hora - 12;
                    $scope.datos.horaCierre = hora.toString() + ':' + a[1] + ' PM';
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
                }
                else
                    {
                        $scope.datos.loterias[idx].seleccionado = true;
                        $('#btnLoteria'+ idx).addClass('active2');
                        console.log("rbxLoteriasChanged false, select: ",$scope.datos.loterias[idx].seleccionado , " hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
               
                    }
            }


            

            console.log("rbxLoteriasChanged, hasClass ", $('#optionLabel'+ idx).hasClass('active'), ' index: ', idx);
               
        }


    })
