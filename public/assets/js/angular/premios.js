var myApp = angular
    .module("myModule", [])
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        $scope.datos =  {
            "idUsuario":0,
            "idBanca":0,
            "idLoteria":0,
            "idSorteo": 0,
            "fecha" : null,
            "numerosGanadores" : 0,
            "horaCierre": moment().format('YYYY/MM/DD'),


            "loteria" : null,
            "desde" : new Date(),
            "hasta" : new Date(),
            "premios" : [],

            
            "mostrarFormEditar" : false,

            "optionsLoterias" : [],
            "selectedLoteria" : {},
            "optionsSorteos" : [],
            "selectedSorteo" : {},
            "loterias" : [],
            "existeSorteo" : false
        }


        $scope.inicializarDatos = function(idLoteria, idSorteo){
            

            $http.get(rutaGlobal+"/api/premios", {'action':'sp_datosgenerales_obtener_todos'})
             .then(function(response){
                console.log('Loteria ajav: ', response.data);

             


                
                $scope.datos.optionsLoterias = response.data.loterias;
                $scope.datos.loterias = response.data.loterias;
                //$scope.datos.optionsSorteos = response.data.sorteos;
                
                
                let idx = 0;
                if(idLoteria > 0)
                    idx = $scope.datos.optionsLoterias.findIndex(x => x.id == idLoteria);
                 $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[idx];
                 $scope.cbxLoteriasChanged();
    

                 $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                  })
            

               
            },
            function(response) {
                // Handle error here
                //console.log('Error jean: ', response);
                alert("Error");
            });
       
        }
        

        $scope.load = function(idUsuario, idBanca = 0){
            $scope.datos.idUsuario = idUsuario;
            $scope.datos.idBanca = idBanca;
            console.log('idUsuario', $scope.datos.idUsuario);
            $scope.inicializarDatos(0, 0);
        }

      
        

        $scope.actualizar = function(vistaSencilla = false){
            
            console.log($scope.datos.loterias);
            var errores = false, mensaje = "";
            

            if(vistaSencilla == true){
                if($scope.datos.optionsLoterias.find(x => x.id == $scope.datos.selectedLoteria.id) == undefined){
                    alert("Debe seleccionar una loteria");
                    return;
                }

                if($scope.existeSorteo('Super pale', $scope.datos.selectedLoteria) == false){
                    if($scope.empty($scope.datos.primera, 'number') == true || $scope.empty($scope.datos.segunda, 'number') == true || $scope.empty($scope.datos.tercera, 'number') == true){
                        alert("Hay campos vacios");
                        return;
                    }
                }else if($scope.existeSorteo('Super pale', $scope.datos.selectedLoteria) == true){
                    if($scope.empty($scope.datos.primera, 'number') == true || $scope.empty($scope.datos.segunda, 'number') == true){
                        alert("Hay campos vacios");
                        return;
                    }
                }
                
                let idx = $scope.datos.loterias.findIndex(x => x.id == $scope.datos.selectedLoteria.id);
                $scope.datos.loterias[idx].primera = $scope.datos.primera;
                $scope.datos.loterias[idx].segunda = $scope.datos.segunda;
                $scope.datos.loterias[idx].tercera = $scope.datos.tercera;

                // console.log('actualizar: ', idx);
                // return;
            }
            
            $scope.datos.loterias.forEach(function(valor, indice, array){
                
              

                //Verificamos que todos los datos no esten vacios y que el sorteo 'Super pale' no exista
                if($scope.empty(array[indice].primera, 'number') == false && $scope.empty(array[indice].segunda, 'number') == false && $scope.empty(array[indice].tercera, 'number') == false && $scope.existeSorteo('Super pale', array[indice]) == false){
                    if(array[indice].primera.length != 2){
                        alert('El valor del compa 1era de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                        errores = true;
                    }
                    if(array[indice].segunda.length != 2){
                        alert('El valor del compa 2da de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                        errores = true;
                    }
                    if(array[indice].tercera.length != 2){
                        alert('El valor del compa 3era de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                        errores = true;
                    }
                }
                //Verificamos que todos los datos no esten vacios, excepto la tripleta y que el sorteo 'Super pale' si exista
                else if($scope.empty(array[indice].primera, 'number') == false && $scope.empty(array[indice].segunda, 'number') == false && $scope.empty(array[indice].tercera, 'number') == true && $scope.existeSorteo('Super pale', array[indice]) == true){
                    if(array[indice].primera.length != 2){
                        alert('El valor del compa 1era de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                        errores = true;
                    }
                    if(array[indice].segunda.length != 2){
                        alert('El valor del compa 2da de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                        errores = true;
                    }
                }

               

             });

             if(errores){
                 alert(mensaje);
                 return;
             }

           
   
            console.log('actualizar: ', $scope.datos);
          
          $http.post(rutaGlobal+"/api/premios/guardar", {'action':'sp_premios_actualiza', 'datos': $scope.datos})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                    alert("Se ha guardado correctamente");
                }else if(response.data.errores == 1){
                    alert(response.data.mensaje);
                }

            },
            function(response) {
                // Handle error here
                console.log('Error jean: ', response);
                alert("Error");
            });

        }

        $scope.borrar = function(id){

            if($scope.datos.optionsLoterias.find(x => x.id == id) == undefined){
                return;
            }

            $scope.datos.idLoteria = id;
            $http.post(rutaGlobal+"/api/premios/erase", {'action':'sp_premios_actualiza', 'datos': $scope.datos})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                    alert("Se ha borrado correctamente");
                }else if(response.data.errores == 1){
                    alert(response.data.mensaje);
                }

            },
            function(response) {
                // Handle error here
                //console.log('Error jean: ', response);
                alert("Error");
            });
        }

        $scope.buscar = function(){

            console.log('buscar: ', $scope.datos);
            
          
          $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_premios_buscar', 'datos': $scope.datos})
             .then(function(response){
                console.log(response.data[0]);
                $scope.datos.premios = response.data;

                // if(response.data[0].errores == 0){
                //     $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                //     alert("Se ha guardado correctamente");
                // }
            },
            function(response) {
                // Handle error here
                //console.log('Error jean: ', response);
                alert("Error");
            });

        }


        $scope.eliminar = function(d){
            $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_premios_elimina', 'datos': d})
             .then(function(response){
                console.log(response.data[0][0]);
                var json = JSON.parse(response.data[0][0]);
                console.log(json);
                if(response.data[0].errores == 0)
                {
                    $scope.inicializarDatos(0);
                    alert(response.data[0].mensaje);
                }
                
            },
            function(response) {
                // Handle error here
                //console.log('Error jean: ', response);
                alert("Error");
            });
        }




       


       

        $scope.cbxLoteriasChanged = function(){
           $scope.datos.primera =  $scope.datos.selectedLoteria.primera;
           $scope.datos.segunda =  $scope.datos.selectedLoteria.segunda;
           $scope.datos.tercera =  $scope.datos.selectedLoteria.tercera;
           $scope.datos.existeSorteo = $scope.existeSorteo('Super pale', $scope.datos.selectedLoteria);
           console.log("Changed sorteo", $scope.datos.selectedLoteria);

           if($scope.empty($scope.datos.selectedLoteria.primera, "number") == false)
                $('#primeraVentanaSencilla').addClass('is-filled');
           if($scope.empty($scope.datos.selectedLoteria.segunda, "number") == false)
                $('#segundaVentanaSencilla').addClass('is-filled');
           if($scope.empty($scope.datos.selectedLoteria.tercera, "number") == false)
                $('#terceraVentanaSencilla').addClass('is-filled');
        }

        $scope.editarPremio = function(id){
            if($scope.datos.optionsLoterias.find(x => x.id == id) != undefined){
                $scope.datos.selectedLoteria = $scope.datos.optionsLoterias.find(x => x.id == id);
                $scope.cbxLoteriasChanged();
                $timeout(function() {
                    $('.selectpicker').selectpicker("refresh");
                  })
            }
        }

        $scope.cbxLoteriasChanged2 = function(){
          
            if($scope.datos.bloqueoJugada.selectedLoteria.bloqueosJugadas != undefined){

                $scope.datos.bloqueoJugada.bloqueosJugadas = JSON.parse($scope.datos.bloqueoJugada.selectedLoteria.bloqueosJugadas);

                

                // a.forEach(function(valor, indice, array){

                //     if($scope.datos.ckbDias.find(x => x.idDia == array[indice].idDia) != undefined){
                //         let idx = $scope.datos.ckbDias.findIndex(x => x.idDia == parseInt(array[indice].idDia));
                //         $scope.datos.ckbDias[idx].existe = true;
                //     }

                //  });
            }
            else
                $scope.datos.bloqueoJugada.bloqueosJugadas = [];


                
        }


        $scope.agregar_guion = function(cadena){
            if(cadena.length == 4){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3];
            }
            if(cadena.length == 6){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3] + '-' + cadena[4] + cadena[5];
            }
           return cadena;
        }


        $scope.p = function(){
            console.log("p: ", Object.keys($scope.datos.premios).length);

            return Object.keys($scope.datos.premios).length;
            // if(last){

            //     (function($){
            //         $.fn.hasScrollBar = function(){
            //           return this.get(0).scrollHeight > this.height();
            //         }
            //       })(jQuery);
    
    
            //     //console.log('hasScrollBar function: ', $('#table_body').hasScrollBar());

            // }

            
        }

        $scope.empty = function(valor, tipo){
            if(tipo === 'number'){
                if(Number(valor) == undefined || valor == '' || valor == null || Number(valor) <= 0)
                    return true;
            }

            return false;
        }


        $scope.existeSorteo = function(sorteo, loteria){
            //console.log('existesorteo: ', $scope.datos.comisiones.selectedLoteria);
            var existe = false;

            
            
            
            if(loteria.sorteos == undefined)
            return false;

        
            loteria.sorteos.forEach(function(valor, indice, array){
                //console.log('existesorteo: parametro: ', sorteo, ' varia: ', array[indice].descripcion);
                if(sorteo == array[indice].descripcion)
                    existe = true;
            });
            

            //console.log('sorteos: ',$scope.datos.selectedLoteriaPagosCombinaciones.sorteos,' sorteo: ', sorteo, ' ,pagos: ', $scope.datos.selectedLoteriaPagosCombinaciones.sorteos.find(x => x.descripcion == sorteo))

            return existe;
        }



    })
