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
            "loterias" : []
        }


        $scope.inicializarDatos = function(idLoteria, idSorteo){
            

            $http.get("/api/premios", {'action':'sp_datosgenerales_obtener_todos'})
             .then(function(response){
                console.log('Loteria ajav: ', response.data);

             


                
                $scope.datos.optionsLoterias = response.data.loterias;
                $scope.datos.loterias = response.data.loterias;
                //$scope.datos.optionsSorteos = response.data.sorteos;
                
                
                let idx = 0;
                if(idLoteria > 0)
                    idx = $scope.datos.optionsLoterias.findIndex(x => x.id == idLoteria);

                // let idx2 = 0;
                // if(idSorteo > 0)
                //     idx2 = $scope.datos.optionsSorteos.findIndex(x => x.idSorteo == idSorteo);


                 $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[idx];
                 //$scope.datos.selectedSorteo = $scope.datos.optionsSorteos[idx2];
                

                 $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                  })
            

                // $scope.datos.quiniela = $scope.datos.selectedLoteria.quinielaBloqueoLoteria;
                // $scope.datos.pale = $scope.datos.selectedLoteria.paleBloqueoLoteria;
                // $scope.datos.tripleta = $scope.datos.selectedLoteria.tripletaBloqueoLoteria;

                // $scope.datos.optionsSorteos =JSON.parse(response.data[0].sorteos);
                // $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];
                
            });
       
        }
        

        $scope.load = function(idUsuario, idBanca = 0){
            $scope.datos.idUsuario = idUsuario;
            $scope.datos.idBanca = idBanca;
            console.log('idUsuario', $scope.datos.idUsuario);
            $scope.inicializarDatos(0, 0);
           
        }

      
        

        $scope.actualizar = function(){
            
            console.log($scope.datos.loterias);
            var errores = false, mensaje = "";
            


            $scope.datos.loterias.forEach(function(valor, indice, array){
                
              

                if((array[indice].primera != undefined 
                    && array[indice].primera != null
                    ) && (array[indice].segunda != undefined && array[indice].segunda != null) && (array[indice].tercera != undefined && array[indice].tercera != null)){
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

               

             });

             if(errores){
                 alert(mensaje);
                 return;
             }

            // if(Number($scope.datos.numerosGanadores) != $scope.datos.numerosGanadores)
            // {
            //     alert("El monto quiniela debe ser numerico");
            //     return;
            // }

            /*Si la hora actual del sistema es menor que la hora de cierre de la loteria seleccionada 
                entonces eso quiere decir que los numeros no han salido por lo tanto es un error */
            // if((new Date() < new Date($scope.datos.selectedLoteria.fecha_actual_horaCierre)))
            // {
            //     alert("Error, la loteria aun no ha cerrado");
            //     return;
            // }

            // if($scope.datos.numerosGanadores.length != 6)
            // {
            //     alert("Debe registrar 6 numeros ganadores");
            //     return;
            // }
           

            //$scope.datos.idLoteria = $scope.datos.selectedLoteria.id;
            //$scope.datos.idSorteo = $scope.datos.selectedSorteo.idSorteo;
   
            console.log('actualizar: ', $scope.datos);
          
          $http.post("/api/premios/guardar", {'action':'sp_premios_actualiza', 'datos': $scope.datos})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                    alert("Se ha guardado correctamente");
                }else if(response.data.errores == 1){
                    alert(response.data.mensaje);
                }

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
                
            });
        }




       


       

        $scope.cbxLoteriasChanged = function(){
            $scope.datos.numerosGanadores = $scope.datos.selectedLoteria.numerosGanadoresHoy;
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



    })
