myApp
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        $scope.datos =  {
            "idUsuario":0,
            "idBanca":0,
            "idLoteria":0,
            "idSorteo": 0,
            "numerosGanadores" : 0,
            "horaCierre": moment().format('YYYY/MM/DD'),


            "loteria" : null,
            "fecha" : new Date(),
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
            

               
            }
            // ,
            // function(response) {
            //     // Handle error here
            //     //console.log('Error jean: ', response);
            //     alert("Error");
            // }
            );
       
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

                if(
                    $scope.existeSorteo('Directo', $scope.datos.selectedLoteria) == true 
                    || $scope.existeSorteo('Pale', $scope.datos.selectedLoteria) == true
                    || $scope.existeSorteo('Tripleta', $scope.datos.selectedLoteria) == true){
                    if($scope.empty($scope.datos.primera, 'number') == true || $scope.empty($scope.datos.segunda, 'number') == true || $scope.empty($scope.datos.tercera, 'number') == true){
                        alert("Hay campos vacios otros: ");
                        return;
                    }
                }
                else if($scope.existeSorteo('Super pale', $scope.datos.selectedLoteria) == true){
                    if($scope.empty($scope.datos.primera, 'number') == true || $scope.empty($scope.datos.segunda, 'number') == true){
                        alert("Hay campos vacios super pale");
                        return;
                    }
                }
                else if($scope.existeSorteo('Pick 3 Straight', $scope.datos.selectedLoteria) == true || $scope.existeSorteo('Pick 3 Box', $scope.datos.selectedLoteria) == true){
                    if($scope.empty($scope.datos.pick3, 'number') == true){
                        alert("Hay campos vacios pick3");
                        return;
                    }
                }
                else if($scope.existeSorteo('Pick 4 Straight', $scope.datos.selectedLoteria) == true || $scope.existeSorteo('Pick 4 Box', $scope.datos.selectedLoteria) == true){
                    if($scope.empty($scope.datos.pick4, 'number') == true){
                        alert("Hay campos vacios pick4");
                        return;
                    }
                }
                
                let idx = $scope.datos.loterias.findIndex(x => x.id == $scope.datos.selectedLoteria.id);
                $scope.datos.loterias[idx].primera = $scope.datos.primera;
                $scope.datos.loterias[idx].segunda = $scope.datos.segunda;
                $scope.datos.loterias[idx].tercera = $scope.datos.tercera;
                $scope.datos.loterias[idx].pick3 = $scope.datos.pick3;
                $scope.datos.loterias[idx].pick4 = $scope.datos.pick4;

                // console.log('actualizar: ', idx);
                // return;
            }else{
                $scope.datos.loterias.forEach(function(valor, indice, array){
                
                
                    if(
                        $scope.existeSorteo('Directo', array[indice]) == true 
                        || $scope.existeSorteo('Pale', array[indice]) == true
                        || $scope.existeSorteo('Tripleta', array[indice]) == true){
                        if($scope.empty(array[indice].primera, 'number') == true || $scope.empty(array[indice].segunda, 'number') == true || $scope.empty(array[indice].tercera, 'number') == true){
                            alert("Hay campos vacios otros");
                            errores = true;
                        }
                    }
                    if($scope.existeSorteo('Super pale', array[indice]) == true){
                        if($scope.empty(array[indice].primera, 'number') == true || $scope.empty(array[indice].segunda, 'number') == true){
                            alert("Hay campos vacios super pale");
                            errores = true;
                        }
                    }
                    if($scope.existeSorteo('Pick 3 Straight', array[indice]) == true || $scope.existeSorteo('Pick 3 Box', array[indice]) == true){
                        if($scope.empty(array[indice].pick3, 'number') == true){
                            alert("Hay campos vacios pick3");
                            errores = true;
                        }
                    }
                    if($scope.existeSorteo('Pick 4 Straight', array[indice]) == true || $scope.existeSorteo('Pick 4 Box', array[indice]) == true){
                        console.log('existe pick4 hola: ', $scope.existeSorteo('Pick 4 Box', array[indice]));
                        if($scope.empty(array[indice].pick4, 'number') == true){
                            alert("Hay campos vacios pick4");
                            errores = true;
                        }
                    }
                  
    
                    // //Verificamos que todos los datos no esten vacios y que el sorteo 'Super pale' no exista
                    // if($scope.empty(array[indice].primera, 'number') == false && $scope.empty(array[indice].segunda, 'number') == false && $scope.empty(array[indice].tercera, 'number') == false && $scope.existeSorteo('Super pale', array[indice]) == false){
                    //     if(array[indice].primera.length != 2){
                    //         alert('El valor del campo 1era de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                    //         errores = true;
                    //     }
                    //     if(array[indice].segunda.length != 2){
                    //         alert('El valor del campo 2da de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                    //         errores = true;
                    //     }
                    //     if(array[indice].tercera.length != 2){
                    //         alert('El valor del campo 3era de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                    //         errores = true;
                    //     }
                    // }
                    // //Verificamos que todos los datos no esten vacios, excepto la tripleta y que el sorteo 'Super pale' si exista
                    // else if($scope.empty(array[indice].primera, 'number') == false && $scope.empty(array[indice].segunda, 'number') == false && $scope.empty(array[indice].tercera, 'number') == true && $scope.existeSorteo('Super pale', array[indice]) == true){
                    //     if(array[indice].primera.length != 2){
                    //         alert('El valor del campo 1era de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                    //         errores = true;
                    //     }
                    //     if(array[indice].segunda.length != 2){
                    //         alert('El valor del campo 2da de la loteria ', array[indice].descripcion, ' debe ser numerico de dos digitos');
                    //         errores = true;
                    //     }
                    // }
    
                   
    
                 });
            }

             if(errores){
                 return;
             }

           
   
            console.log('actualizar: ', $scope.datos);

            if(vistaSencilla == true){
                $scope.datos.layout = 'vistaSencilla';
            }else{
                $scope.datos.layout = 'vistaNormal';
            }


          
          $http.post(rutaGlobal+"/api/premios/guardar", {'action':'sp_premios_actualiza', 'datos': $scope.datos})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                    alert(response.data.mensaje);
                }else if(response.data.errores == 1){
                    $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                    alert(response.data.mensaje);
                }

            }
            // ,
            // function(response) {
            //     // Handle error here
            //     console.log('Error jean: ', response);
            //     alert("Error");
            // }
            );

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
           $scope.datos.pick3 =  $scope.datos.selectedLoteria.pick3;
           $scope.datos.pick4 =  $scope.datos.selectedLoteria.pick4;
           $scope.datos.existeSorteo = $scope.existeSorteo('Super pale', $scope.datos.selectedLoteria);
           console.log("Changed sorteo", $scope.datos.selectedLoteria);

           if($scope.empty($scope.datos.selectedLoteria.primera, "number") == false)
                $('#primeraVentanaSencilla').addClass('is-filled');
           if($scope.empty($scope.datos.selectedLoteria.segunda, "number") == false)
                $('#segundaVentanaSencilla').addClass('is-filled');
           if($scope.empty($scope.datos.selectedLoteria.tercera, "number") == false)
                $('#terceraVentanaSencilla').addClass('is-filled');
           if($scope.empty($scope.datos.selectedLoteria.pick3, "number") == false)
                $('#pick3VentanaSencilla').addClass('is-filled');
           if($scope.empty($scope.datos.selectedLoteria.pick4, "number") == false)
                $('#pick4VentanaSencilla').addClass('is-filled');
        }

        $scope.changeFocus = function(event, elementIdToSetFocus,  lengthOfStringToChangeFocus, string, element = undefined, index = -1){
            if(string != undefined){

                if(element != undefined && index == -1){
                    
                    if(element == 'datosPick3'){
                        
                        // if(string.length == 2){
                        //     $scope.datos.primera = string.substr(1, string.length - 1);
                        // }
                        // else if(string.length == 3){
                        //     $scope.datos.primera = string.substr(1, 2);
                        // }

                        if(string == undefined || string == '' || string == null){
                            $scope.datos.primera = undefined;
                        }
                        else if(string.length == 1)
                        {
                            $scope.datos.primera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datos.primera = string.substr(1, string.length - 1);
                        }
                        else if(string.length == 3){
                            $scope.datos.primera = string.substr(1, 2);
                        }
                    }
                    

                    if(element == 'datosPick4'){
                        if(string == undefined || string == '' || string == null){
                            
                            $scope.datos.segunda = undefined;
                            $scope.datos.tercera = undefined;
                        }
                        else if(string.length == 1){
                            $scope.datos.segunda = string;
                            $scope.datos.tercera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datos.segunda = string;
                            $scope.datos.tercera = undefined;
                        }
                        else if(string.length == 3){
                            $scope.datos.tercera = string.substr(2, string.length - 1);
                        }
                        else if(string.length == 4){
                            if($scope.datos.tercera != undefined && $scope.datos.tercera != '' && $scope.datos.tercera != null){
                                if($scope.datos.tercera.length == 1)
                                    $scope.datos.tercera += string.substr(3, string.length - 1);
                                else if($scope.datos.tercera.length == 2){
                                    $scope.datos.tercera = string.substr(2, 2);
                                }

                                $scope.datos.segunda = string.substr(0, 2);
                            }else{
                                $scope.datos.tercera = string.substr(2, 2);
                                $scope.datos.segunda = string.substr(0, 2);
                            }
                            //$scope.datos.tercera += string.substr(3, string.length - 1);
                        }
                    }

                    
                }
                else{
                    if(element == 'ngRepeatPick3'){
                        if(string == undefined || string == '' || string == null){
                            $scope.datos.loterias[index].primera = undefined;
                        }
                        else if(string.length == 1)
                        {
                            $scope.datos.loterias[index].primera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datos.loterias[index].primera = string.substr(1, string.length - 1);
                        }
                        else if(string.length == 3){
                            $scope.datos.loterias[index].primera = string.substr(1, 2);
                        }
                    }
                    

                    if(element == 'ngRepeatPick4'){
                        console.log('changeFocus length: ', string == '');
                        if(string == undefined || string == '' || string == null){
                            
                            $scope.datos.loterias[index].segunda = undefined;
                            $scope.datos.loterias[index].tercera = undefined;
                        }
                        else if(string.length == 1){
                            $scope.datos.loterias[index].segunda = string;
                            $scope.datos.loterias[index].tercera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datos.loterias[index].segunda = string;
                            $scope.datos.loterias[index].tercera = undefined;
                        }
                        else if(string.length == 3){
                            $scope.datos.loterias[index].tercera = string.substr(2, string.length - 1);
                        }
                        else if(string.length == 4){
                            if($scope.datos.loterias[index].tercera != undefined && $scope.datos.loterias[index].tercera != '' && $scope.datos.loterias[index].tercera != null){
                                if($scope.datos.loterias[index].tercera.length == 1)
                                    $scope.datos.loterias[index].tercera += string.substr(3, string.length - 1);
                                else if($scope.datos.loterias[index].tercera.length == 2){
                                    $scope.datos.loterias[index].tercera = string.substr(2, 2);
                                }
                                $scope.datos.loterias[index].segunda = string.substr(0, 2);
                            }else{
                                $scope.datos.loterias[index].tercera = string.substr(2, 2);
                                $scope.datos.loterias[index].segunda = string.substr(0, 2);
                            }
                        }
                    }
                }
                if(string.length == lengthOfStringToChangeFocus && elementIdToSetFocus != 'no'){
                    $('#'+elementIdToSetFocus).focus();
                    $('#'+elementIdToSetFocus).focus();
                }
            }
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


        $scope.agregar_guion = function(cadena, sorteo = undefined){
            console.log('agregar_guion:', sorteo);
            if(cadena.length == 4 && sorteo == 'Pale'){
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
            console.log('empty:', valor);
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



    });


    myApp.directive('selectAllOnClick', [function() {
        return {
          restrict: 'A',
          link: function(scope, element, attrs) {
            var hasSelectedAll = false;
            element.on('click', function($event) {
              if (!hasSelectedAll) {
                try {
                  //IOs, Safari, thows exception on Chrome etc
                  this.selectionStart = 0;
                  this.selectionEnd = this.value.length + 1;
                  hasSelectedAll = true;
                } catch (err) {
                  //Non IOs option if not supported, e.g. Chrome
                  this.select();
                  hasSelectedAll = true;
                }
              }
            });
            //On blur reset hasSelectedAll to allow full select
            element.on('blur', function($event) {
              hasSelectedAll = false;
            });
          }
        };
      }]);

      myApp.run(function($rootScope) {
        $rootScope.typeOf = function(value) {
          return typeof value;
        };
      })

      myApp.directive('stringToNumber', function() {
        return {
          require: 'ngModel',
          link: function(scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function(value) {
              return '' + value;
            });
            ngModel.$formatters.push(function(value) {
              return parseFloat(value);
            });
          }
        };
      });