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
            "idPrestamo":0,
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

            "optionsTiposEntidadesFondo": [],
            "optionsBancas": [],
            "optionsBancasFondos": [],
            "optionsBancosFondos": [],
            "optionsFrecuencias": [],
            "optionsTiposPagos": [],
            "mostrarFormEditar" : false,
            'selectedBanca' : {},
            'selectedBancaFondo' : {},
            'selectedDia' : {},
            'selectedBancoFondo' : {},
            'selectedFrecuencia' : {},
            'selectedTipoEntidadFondo' : {},
            'selectedTiposPagos' : {},
            'selectedBancoCobrar' : {},

            'editar' : false,
            'tipoAmortizacion' : {},
        }

        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }

        $scope.inicializarDatos = function(todos){
            $scope.datos.optionsTiposEntidadesFondo = tiposEntidadesGlobal;
            $scope.datos.prestamos = prestamosGlobal;
            $scope.datos.optionsBancas = bancasGlobal;
            $scope.datos.optionsDias = diasGlobal;
            $scope.datos.optionsBancasFondos = bancasGlobal;
            $scope.datos.optionsBancosFondos = bancosGlobal;
            $scope.datos.optionsFrecuencias = frecuenciasGlobal;
            $scope.datos.optionsTiposPagos = tiposPagosGlobal;
            $scope.datos.optionsBancosCobrar = bancosGlobal;

            
            $scope.datos.selectedTipoEntidadFondo = $scope.datos.optionsTiposEntidadesFondo[helperService.retornarIndexPorId($scope.datos.selectedTipoEntidadFondo, $scope.datos.optionsTiposEntidadesFondo)];
            $scope.datos.selectedBanca = $scope.datos.optionsBancas[helperService.retornarIndexPorId($scope.datos.selectedBanca, $scope.datos.optionsBancas)];
            $scope.datos.selectedDia = $scope.datos.optionsDias[helperService.retornarIndexPorId($scope.datos.selectedDia, $scope.datos.optionsDias)];
            $scope.datos.selectedBancaFondo = $scope.datos.optionsBancasFondos[helperService.retornarIndexPorId($scope.datos.selectedBancaFondo, $scope.datos.optionsBancasFondos)];
            $scope.datos.selectedBancoFondo = $scope.datos.optionsBancosFondos[helperService.retornarIndexPorId($scope.datos.selectedBancoFondo, $scope.datos.optionsBancosFondos)];
            $scope.datos.selectedFrecuencia = $scope.datos.optionsFrecuencias[helperService.retornarIndexPorId($scope.datos.selectedFrecuencia, $scope.datos.optionsFrecuencias)];
            $scope.datos.selectedTiposPagos = $scope.datos.optionsTiposPagos[helperService.retornarIndexPorId($scope.datos.selectedTiposPagos, $scope.datos.optionsTiposPagos)];
            $scope.datos.selectedBancoCobrar = $scope.datos.optionsBancosCobrar[helperService.retornarIndexPorId($scope.datos.selectedBancoCobrar, $scope.datos.optionsBancosCobrar)];

            console.log('bancos:', $scope.datos.tiposPagosRadio);
               
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
                $scope.datos.editar = false;
                $scope.datos.id = 0;
                $scope.datos.status = true;
                $scope.datos.selectedBanca = $scope.datos.optionsBancas[helperService.retornarIndexPorId({}, $scope.datos.optionsBancas)];
                $scope.datos.selectedTipoEntidadFondo = $scope.datos.optionsTiposEntidadesFondo[helperService.retornarIndexPorId({}, $scope.datos.optionsTiposEntidadesFondo)];
                $scope.datos.montoPrestado = null;
                $scope.datos.numeroCuotas = null;
                $scope.datos.tasaInteres = null;
                $scope.datos.montoCuotas = null;
                $scope.datos.selectedFrecuencia = $scope.datos.optionsFrecuencias[helperService.retornarIndexPorId({}, $scope.datos.optionsFrecuencias)];
                $scope.datos.fechaInicio = new Date();
                $scope.datos.selectedBancoFondo = $scope.datos.optionsBancosFondos[0];
                $scope.datos.selectedBancaFondo = $scope.datos.optionsBancasFondos[0];

                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                    
                  })
            }
            else{
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;

                $('.form-group').addClass('is-filled');
                $scope.datos.editar = true;
                // $('#selectPickerBanca').attr('disabled',true);
                $scope.datos.id = d.id;
                $scope.datos.status = (d.status == 1) ? true : false;
                $scope.datos.selectedBanca = $scope.datos.optionsBancas[helperService.retornarIndexPorId({}, $scope.datos.optionsBancas, d.idEntidadPrestamo)];
                $scope.datos.selectedTipoEntidadFondo = $scope.datos.optionsTiposEntidadesFondo[helperService.retornarIndexPorId({}, $scope.datos.optionsTiposEntidadesFondo, d.idTipoEntidadFondo)];
                $scope.datos.montoPrestado = d.montoPrestado;
                $scope.datos.numeroCuotas = d.numeroCuotas;
                $scope.datos.tasaInteres = d.tasaInteres;
                $scope.datos.montoCuotas = d.montoCuotas;
                $scope.datos.tipoAmortizacion = d.tipoAmortizacion;
                $scope.datos.selectedFrecuencia = $scope.datos.optionsFrecuencias[helperService.retornarIndexPorId({}, $scope.datos.optionsFrecuencias, d.idFrecuencia)];
               
                var fechaInicio = d.fechaInicio.split(' ');
                $scope.datos.fechaInicio = new Date(d.fechaInicio);
                
                if($scope.datos.selectedTipoEntidadFondo.descripcion =="Banca")
                    $scope.datos.selectedBancaFondo = $scope.datos.optionsBancasFondos[helperService.retornarIndexPorId({}, $scope.datos.optionsBancasFondos, d.idEntidadFondo)];
                else
                    $scope.datos.selectedBancoFondo = $scope.datos.optionsBancosFondos[helperService.retornarIndexPorId({}, $scope.datos.optionsBancosFondos, d.idEntidadFondo)];
               
               
                console.log('editar index:', d.idEntidadFondo);
                console.log('editar indexRe:', helperService.retornarIndexPorId({}, $scope.datos.optionsBancasFondos, d.idEntidadFondo));
                console.log('editar selected:', $scope.datos.selectedTipoEntidadFondo.descripcion);
                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                    
                  })
                
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
            $scope.datos.idFrecuencia = $scope.datos.selectedFrecuencia.id;
            $scope.datos.idTipoEntidadFondo = $scope.datos.selectedTipoEntidadFondo.id;


            if($scope.datos.selectedTipoEntidadFondo.descripcion == 'Banco'){
                $scope.datos.idEntidadFondo = $scope.datos.selectedBancoFondo.id;
            }else{
                $scope.datos.idEntidadFondo = $scope.datos.selectedBancaFondo.id;
            }
            

            

            $scope.datos.status = ($scope.datos.status) ? 1 : 0;
            $scope.datos.horaCierre = $('#horaCierre').val();
            //$scope.hora_convertir(true);


    //   console.log($scope.datos.selectedTipoEntidadFondo);
    //   return;


          $http.post(rutaGlobal+"/api/prestamos/guardar", {'action':'sp_loterias_actualiza', 'datos': $scope.datos})
             .then(function(response){
                 console.log(response.data);
                if(response.data.errores == 0){
                    $scope.datos.prestamos = response.data.prestamos;
                   console.log('prestamos:', response.data);
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


        $scope.getPrestamo = function(prestamo){
            $scope.datos.montoPagado = null;
            $scope.datos.devuelta = null;

            $scope.datos.idPrestamo = prestamo.id;
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.selectedPrestamoPagar = null;

            $http.post(rutaGlobal+"/api/prestamos/getPrestamo", {'action':'sp_loterias_elimnar', 'datos': $scope.datos})
             .then(function(response){
                console.log(response);
            
                if(response.data.errores == 0)
                {
                    // $scope.inicializarDatos(true);
                    $('#modal-prestamo').modal('show');
                    response.data.prestamo.amortizacion.forEach(function(valor, indice, array){

                        if(indice == 0){
                        array[indice].enable = true;
                        }else{
                            array[indice].enable = false;
                        }
                        array[indice].seleccionado = false;

                        array[indice].interesAPagar = Number(array[indice].montoInteres) - Number(array[indice].montoPagadoInteres);
                        array[indice].capitalAPagar = (Number(array[indice].montoCuota) - Number(array[indice].montoInteres)) - Number(array[indice].montoPagadoCapital);
                        array[indice].cuotaAPagar = Number(array[indice].interesAPagar) + Number(array[indice].capitalAPagar);                        
                        console.log('indice: ',array[indice] );
                    });
                    $scope.datos.selectedPrestamoPagar = response.data.prestamo;
                    calcularTotal();


                }
                
            });
        }

        $scope.seleccionarCuota = function(cuota){
            console.log('dentro', cuota);
            var index = $scope.datos.selectedPrestamoPagar.amortizacion.findIndex(x => x.id == cuota.id);
            if(cuota.enable == false){
                $scope.datos.selectedPrestamoPagar.amortizacion[index].seleccionado = false;
                return;
            }
            
            if(cuota.seleccionado == true){
                $scope.datos.selectedPrestamoPagar.amortizacion[index].seleccionado = false;
                $scope.datos.selectedPrestamoPagar.amortizacion.forEach(function(valor, indice, array){
                    if(indice > index){
                        $scope.datos.selectedPrestamoPagar.amortizacion[indice].enable = false;
                        $scope.datos.selectedPrestamoPagar.amortizacion[indice].seleccionado = false;
                    }
                });
            }else{
                $scope.datos.selectedPrestamoPagar.amortizacion[index].seleccionado = true;
                if(helperService.empty($scope.datos.selectedPrestamoPagar.amortizacion[index + 1], 'object') != true){
                    $scope.datos.selectedPrestamoPagar.amortizacion[index + 1].enable = true;
                }
            }



            
            calcularTotal();
        }


        $scope.pagar = function(){
            console.log('dentro', $scope.datos.selectedPrestamoPagar.amortizacion);
            if(helperService.empty($scope.datos.montoPagado, 'number')){
                alert("El monto debe ser numerico y mayor que cero");
                return;
            }
            if(hayCuotasSeleccionadas() == false){
                alert("Debe seleccionar al menos una cuota");
                return;
            }

            console.log('pagar:', hayCuotasSeleccionadas());


            $scope.datos.cuotas = $scope.datos.selectedPrestamoPagar.amortizacion;
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.idTipoPago = $scope.datos.selectedTiposPagos.id;
            $scope.datos.idBanco = $scope.datos.selectedBancoCobrar.id;

            $http.post(rutaGlobal+"/api/prestamos/cobrar", {'action':'sp_loterias_elimnar', 'datos': $scope.datos})
            .then(function(response){
               console.log(response);
           
               if(response.data.errores == 0)
               {
                   $scope.datos.prestamos = response.data.prestamos;
                    alert("Se ha pagado correctamente");
                    $('#modal-prestamo').modal('hide');
                    return;
               }
               else if(response.data.errores == 1){
                $('#modal-prestamo').modal('hide');
                    alert(response.data.mensaje);
                    return;
               }

               
               
           });
        }

        $scope.txtPagarChanged = function(){
            if(hayCuotasSeleccionadas() == false){
                return;
            }

            if(helperService.empty($scope.datos.montoPagado, 'number') == true){
                    return;
            }

            
            var calculo = $scope.datos.totalAPagar - Number($scope.datos.montoPagado);
            if(calculo < 0){
                $scope.datos.devuelta = Math.abs(calculo);
            }else{
                $scope.datos.devuelta = 0;
            }

            calcularTotal();
            
        }

        function hayCuotasSeleccionadas(){
            var hay = false;
            $scope.datos.selectedPrestamoPagar.amortizacion.forEach(function(valor, indice, array){
                if(array[indice].seleccionado == true){
                    hay = true;
                }
            });

            return hay;
        }
       
        function calcularTotal(){
            var total = 0;
            $scope.datos.selectedPrestamoPagar.amortizacion.forEach(function(valor, indice, array){
                if(array[indice].seleccionado == true)
                    total += array[indice].cuotaAPagar;
                    console.log('calcularTotal:', array[indice].cuotaAPagar);
            });

            $scope.datos.totalAPagar = total;

            console.log(total);
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
