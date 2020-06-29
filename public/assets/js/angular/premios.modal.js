


function retornarAngularModulo(){
    try{
        return angular.module("myModule");
    }catch(e){
        // console.log(e);
        return angular.module("myModule", [])
    }
}

var myApp = retornarAngularModulo();
myApp.controller("controllerPremiosModal", function($scope, $http, $timeout, helperService){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        $scope.indexLoteriaPremiosModal = 0;
        $scope.hola = function(){
            // console.log("hola: ", $scope.mostrarModalPremios);
            if($scope.mostrarModalPremios == true)
                $scope.mostrarModalPremios = false;
            else
                $scope.mostrarModalPremios = true;
        }
        $scope.mostrarModalPremios = false;
        $scope.datosPremiosModal =  {
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

            "optionsLoteriasPremiosModal" : [],
            "selectedLoteriaPremiosModal" : {},
            "optionsSorteos" : [],
            "selectedSorteo" : {},
            "loterias" : [],
            "existeSorteoPremiosModal" : false
        }


        $scope.inicializarDatosPremiosModal = function(idLoteria, idSorteo){
            
            var jwt = helperService.createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario, "layout" : "vistaPremiosModal"});
            $http.get(rutaGlobal+"/api/premios?layout=vistaPremiosModal&&token=" + jwt, {'action':'sp_datosgenerales_obtener_todos'})
             .then(function(response){
                console.log('Loteria ajav: ', response.data);

             


                
                $scope.datosPremiosModal.optionsLoteriasPremiosModal = response.data.loterias;
                $scope.datosPremiosModal.loterias = response.data.loterias;
                //$scope.datosPremiosModal.optionsSorteos = response.data.sorteos;
                
                
                let idx = 0;
                if(idLoteria > 0)
                    idx = $scope.datosPremiosModal.optionsLoteriasPremiosModal.findIndex(x => x.id == idLoteria);
                 $scope.datosPremiosModal.selectedLoteriaPremiosModal = $scope.datosPremiosModal.optionsLoteriasPremiosModal[idx];
                //  console.log('premiosModal selectedLoteria:',$scope.datosPremiosModal.selectedLoteriaPremiosModal);
                 $scope.cbxLoteriasPremiosModalChanged();
    

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
                console.log('Error jean: ', response);
                alert("Error");
            });
       
        }
        

        $scope.loadPremiosModal = function(idUsuario, idBanca = 0){
            $scope.datosPremiosModal.idUsuario = idUsuario;
            $scope.datosPremiosModal.idBanca = idBanca;
            //console.log('idUsuario', $scope.datosPremiosModal.idUsuario);
            $scope.inicializarDatosPremiosModal(0, 0);
        }

      
        

        $scope.actualizarPremiosModal = function(vistaSencilla = false){
            
           // console.log($scope.datosPremiosModal.loterias);
            var errores = false, mensaje = "";
            

            if(vistaSencilla == true){
                if($scope.datosPremiosModal.optionsLoteriasPremiosModal.find(x => x.id == $scope.datosPremiosModal.selectedLoteriaPremiosModal.id) == undefined){
                    alert("Debe seleccionar una loteria");
                    return;
                }

                if(
                    $scope.existeSorteoPremiosModal('Directo', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true 
                    || $scope.existeSorteoPremiosModal('Pale', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true
                    || $scope.existeSorteoPremiosModal('Tripleta', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true){
                    if(helperService.empty($scope.datosPremiosModal.primera, 'number', false) == true || helperService.empty($scope.datosPremiosModal.segunda, 'number', false) == true || helperService.empty($scope.datosPremiosModal.tercera, 'number', false) == true){
                        alert("Hay campos vacios otros: ");
                        return;
                    }
                }
                else if($scope.existeSorteoPremiosModal('Super pale', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true){
                    if(helperService.empty($scope.datosPremiosModal.primera, 'number', false) == true || helperService.empty($scope.datosPremiosModal.segunda, 'number', false) == true){
                        alert("Hay campos vacios super pale");
                        return;
                    }
                }
                else if($scope.existeSorteoPremiosModal('Pick 3 Straight', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true || $scope.existeSorteoPremiosModal('Pick 3 Box', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true){
                    if(helperService.empty($scope.datosPremiosModal.pick3, 'number', false) == true){
                        alert("Hay campos vacios pick3");
                        return;
                    }
                }
                else if($scope.existeSorteoPremiosModal('Pick 4 Straight', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true || $scope.existeSorteoPremiosModal('Pick 4 Box', $scope.datosPremiosModal.selectedLoteriaPremiosModal) == true){
                    if(helperService.empty($scope.datosPremiosModal.pick4, 'number', false) == true){
                        alert("Hay campos vacios pick4");
                        return;
                    }
                }
                
                let idx = $scope.datosPremiosModal.loterias.findIndex(x => x.id == $scope.datosPremiosModal.selectedLoteriaPremiosModal.id);
                $scope.datosPremiosModal.loterias[idx].primera = $scope.datosPremiosModal.primera;
                $scope.datosPremiosModal.loterias[idx].segunda = $scope.datosPremiosModal.segunda;
                $scope.datosPremiosModal.loterias[idx].tercera = $scope.datosPremiosModal.tercera;
                $scope.datosPremiosModal.loterias[idx].pick3 = $scope.datosPremiosModal.pick3;
                $scope.datosPremiosModal.loterias[idx].pick4 = $scope.datosPremiosModal.pick4;

                // console.log('actualizar: ', idx);
                // return;
            }else{
                $scope.datosPremiosModal.loterias.forEach(function(valor, indice, array){
                
                
                    if(
                        $scope.existeSorteoPremiosModal('Directo', array[indice]) == true 
                        || $scope.existeSorteoPremiosModal('Pale', array[indice]) == true
                        || $scope.existeSorteoPremiosModal('Tripleta', array[indice]) == true){
                        if(helperService.empty(array[indice].primera, 'number', false) == true || helperService.empty(array[indice].segunda, 'number', false) == true || helperService.empty(array[indice].tercera, 'number', false) == true){
                            alert("Hay campos vacios otros");
                            errores = true;
                        }
                    }
                    if($scope.existeSorteoPremiosModal('Super pale', array[indice]) == true){
                        if(helperService.empty(array[indice].primera, 'number', false) == true || helperService.empty(array[indice].segunda, 'number', false) == true){
                            alert("Hay campos vacios super pale");
                            errores = true;
                        }
                    }
                    if($scope.existeSorteoPremiosModal('Pick 3 Straight', array[indice]) == true || $scope.existeSorteoPremiosModal('Pick 3 Box', array[indice]) == true){
                        if(helperService.empty(array[indice].pick3, 'number', false) == true){
                            alert("Hay campos vacios pick3");
                            errores = true;
                        }
                    }
                    if($scope.existeSorteoPremiosModal('Pick 4 Straight', array[indice]) == true || $scope.existeSorteoPremiosModal('Pick 4 Box', array[indice]) == true){
                        // console.log('existe pick4 hola: ', $scope.existeSorteoPremiosModal('Pick 4 Box', array[indice]));
                        if(helperService.empty(array[indice].pick4, 'number', false) == true){
                            alert("Hay campos vacios pick4");
                            errores = true;
                        }
                    }
                  
    
                    // //Verificamos que todos los datos no esten vacios y que el sorteo 'Super pale' no exista
                    // if(helperService.empty(array[indice].primera, 'number') == false && helperService.empty(array[indice].segunda, 'number') == false && helperService.empty(array[indice].tercera, 'number') == false && $scope.existeSorteoPremiosModal('Super pale', array[indice]) == false){
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
                    // else if(helperService.empty(array[indice].primera, 'number') == false && helperService.empty(array[indice].segunda, 'number') == false && helperService.empty(array[indice].tercera, 'number') == true && $scope.existeSorteoPremiosModal('Super pale', array[indice]) == true){
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

           
   
            // console.log('actualizar: ', $scope.datos);

            if(vistaSencilla == true){

                $scope.datosPremiosModal.layout = 'vistaSencilla';
            }else{
                $scope.datosPremiosModal.layout = 'vistaNormal';
            }

            $scope.datosPremiosModal.idUsuario = idUsuarioGlobal;
            $scope.datosPremiosModal.servidor = servidorGlobal;
          var jwt = helperService.createJWT($scope.datosPremiosModal);
          $http.post(rutaGlobal+"/api/premios/guardar", {'action':'sp_premios_actualiza', 'datos': jwt})
             .then(function(response){
                // console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatosPremiosModal($scope.datosPremiosModal.idLoteria, $scope.datosPremiosModal.idSorteo, response);
                    alert(response.data.mensaje);
                }else if(response.data.errores == 1){
                    $scope.inicializarDatosPremiosModal($scope.datosPremiosModal.idLoteria, $scope.datosPremiosModal.idSorteo);
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

    
    

       

        $scope.cbxLoteriasPremiosModalChanged = function(){
            if(helperService.empty($scope.datosPremiosModal.selectedLoteriaPremiosModal, 'object') == true)
                return;

            
           $scope.datosPremiosModal.primera =  $scope.datosPremiosModal.selectedLoteriaPremiosModal.primera;
           $scope.datosPremiosModal.segunda =  $scope.datosPremiosModal.selectedLoteriaPremiosModal.segunda;
           $scope.datosPremiosModal.tercera =  $scope.datosPremiosModal.selectedLoteriaPremiosModal.tercera;
           $scope.datosPremiosModal.pick3 =  $scope.datosPremiosModal.selectedLoteriaPremiosModal.pick3;
           $scope.datosPremiosModal.pick4 =  $scope.datosPremiosModal.selectedLoteriaPremiosModal.pick4;
           $scope.datosPremiosModal.existeSorteoPremiosModal = $scope.existeSorteoPremiosModal('Super pale', $scope.datosPremiosModal.selectedLoteriaPremiosModal);
        //    console.log("Changed sorteo", $scope.datosPremiosModal.selectedLoteriaPremiosModal);

           if(helperService.empty($scope.datosPremiosModal.selectedLoteriaPremiosModal.primera, "number", false) == false)
                $('#primeraPremiosModal').addClass('is-filled');
           if(helperService.empty($scope.datosPremiosModal.selectedLoteriaPremiosModal.segunda, "number", false) == false)
                $('#segundaPremiosModal').addClass('is-filled');
           if(helperService.empty($scope.datosPremiosModal.selectedLoteriaPremiosModal.tercera, "number", false) == false)
                $('#terceraPremiosModal').addClass('is-filled');
           if(helperService.empty($scope.datosPremiosModal.selectedLoteriaPremiosModal.pick3, "number", false) == false)
                $('#pick3PremiosModal').addClass('is-filled');
           if(helperService.empty($scope.datosPremiosModal.selectedLoteriaPremiosModal.pick4, "number", false) == false)
                $('#pick4PremiosModal').addClass('is-filled');
        }

        $scope.changeFocusPremiosModal = function(event, elementIdToSetFocus,  lengthOfStringToChangeFocus, string, element = undefined, index = -1){
            if(string != undefined){

                if(element != undefined && index == -1){
                    
                    if(element == 'datosPick3'){
                        
                        // if(string.length == 2){
                        //     $scope.datosPremiosModal.primera = string.substr(1, string.length - 1);
                        // }
                        // else if(string.length == 3){
                        //     $scope.datosPremiosModal.primera = string.substr(1, 2);
                        // }

                        if(string == undefined || string == '' || string == null){
                            $scope.datosPremiosModal.primera = undefined;
                        }
                        else if(string.length == 1)
                        {
                            $scope.datosPremiosModal.primera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datosPremiosModal.primera = string.substr(1, string.length - 1);
                        }
                        else if(string.length == 3){
                            $scope.datosPremiosModal.primera = string.substr(1, 2);
                        }
                    }
                    

                    if(element == 'datosPick4'){
                        if(string == undefined || string == '' || string == null){
                            
                            $scope.datosPremiosModal.segunda = undefined;
                            $scope.datosPremiosModal.tercera = undefined;
                        }
                        else if(string.length == 1){
                            $scope.datosPremiosModal.segunda = string;
                            $scope.datosPremiosModal.tercera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datosPremiosModal.segunda = string;
                            $scope.datosPremiosModal.tercera = undefined;
                        }
                        else if(string.length == 3){
                            $scope.datosPremiosModal.tercera = string.substr(2, string.length - 1);
                        }
                        else if(string.length == 4){
                            if($scope.datosPremiosModal.tercera != undefined && $scope.datosPremiosModal.tercera != '' && $scope.datosPremiosModal.tercera != null){
                                if($scope.datosPremiosModal.tercera.length == 1)
                                    $scope.datosPremiosModal.tercera += string.substr(3, string.length - 1);
                                else if($scope.datosPremiosModal.tercera.length == 2){
                                    $scope.datosPremiosModal.tercera = string.substr(2, 2);
                                }

                                $scope.datosPremiosModal.segunda = string.substr(0, 2);
                            }else{
                                $scope.datosPremiosModal.tercera = string.substr(2, 2);
                                $scope.datosPremiosModal.segunda = string.substr(0, 2);
                            }
                            //$scope.datosPremiosModal.tercera += string.substr(3, string.length - 1);
                        }
                    }

                    
                }
                else{
                    if(element == 'ngRepeatPick3'){
                        if(string == undefined || string == '' || string == null){
                            $scope.datosPremiosModal.loterias[index].primera = undefined;
                        }
                        else if(string.length == 1)
                        {
                            $scope.datosPremiosModal.loterias[index].primera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datosPremiosModal.loterias[index].primera = string.substr(1, string.length - 1);
                        }
                        else if(string.length == 3){
                            $scope.datosPremiosModal.loterias[index].primera = string.substr(1, 2);
                        }
                    }
                    

                    if(element == 'ngRepeatPick4'){
                        // console.log('changeFocus length: ', string == '');
                        if(string == undefined || string == '' || string == null){
                            
                            $scope.datosPremiosModal.loterias[index].segunda = undefined;
                            $scope.datosPremiosModal.loterias[index].tercera = undefined;
                        }
                        else if(string.length == 1){
                            $scope.datosPremiosModal.loterias[index].segunda = string;
                            $scope.datosPremiosModal.loterias[index].tercera = undefined;
                        }
                        else if(string.length == 2){
                            $scope.datosPremiosModal.loterias[index].segunda = string;
                            $scope.datosPremiosModal.loterias[index].tercera = undefined;
                        }
                        else if(string.length == 3){
                            $scope.datosPremiosModal.loterias[index].tercera = string.substr(2, string.length - 1);
                        }
                        else if(string.length == 4){
                            if($scope.datosPremiosModal.loterias[index].tercera != undefined && $scope.datosPremiosModal.loterias[index].tercera != '' && $scope.datosPremiosModal.loterias[index].tercera != null){
                                if($scope.datosPremiosModal.loterias[index].tercera.length == 1)
                                    $scope.datosPremiosModal.loterias[index].tercera += string.substr(3, string.length - 1);
                                else if($scope.datosPremiosModal.loterias[index].tercera.length == 2){
                                    $scope.datosPremiosModal.loterias[index].tercera = string.substr(2, 2);
                                }
                                $scope.datosPremiosModal.loterias[index].segunda = string.substr(0, 2);
                            }else{
                                $scope.datosPremiosModal.loterias[index].tercera = string.substr(2, 2);
                                $scope.datosPremiosModal.loterias[index].segunda = string.substr(0, 2);
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

       




        $scope.existeSorteoPremiosModal = function(sorteo, loteria){
            //console.log('existeSorteoPremiosModal: ', $scope.datosPremiosModal.comisiones.selectedLoteriaPremiosModal);
            var existe = false;

            // console.log('existeSorteoPremiosModal:', loteria);
            
            
            if(helperService.empty(loteria, 'object') == true )
            return false;
            if(helperService.empty(loteria.sorteos, 'object') == true )
            return false;

        
            loteria.sorteos.forEach(function(valor, indice, array){
                //console.log('existeSorteoPremiosModal: parametro: ', sorteo, ' varia: ', array[indice].descripcion);
                if(sorteo == array[indice].descripcion)
                    existe = true;
            });
            

            //console.log('sorteos: ',$scope.datosPremiosModal.selectedLoteriaPremiosModalPagosCombinaciones.sorteos,' sorteo: ', sorteo, ' ,pagos: ', $scope.datosPremiosModal.selectedLoteriaPremiosModalPagosCombinaciones.sorteos.find(x => x.descripcion == sorteo))

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