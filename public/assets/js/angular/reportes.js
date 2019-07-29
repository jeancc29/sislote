myApp
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
          
            'bancas' : [],
            'idLoteria' : null,
            'optionsLoterias':[],
            'selectedLoteria' : {},
            'fecha' : new Date(),
            'jugadas' : [],

            'total_directo' : 0,
            'total_palet' : 0,
            'total_tripleta' : 0,
            'monto_total' : 0,
        'pagar' : {
            'codigoBarra' : null
        },
        'ventasReporte' : {
            'ventas' : {
                'pendientes' : 0,
                'ganadores' : 0,
                'perdedores' : 0,
                'total' : 0,
                'ventas' : 0,
                'comisiones' : 0,
                'descuentos' : 0,   
                'premios' : 0,
                'neto' : 0,
                'balance' : 0,
            },
            'loterias' : [],
            'ticketsGanadoresSinPagar' : [],
            'fecha' : new Date()
        },
    }
        $scope.inicializarDatos = function(){
            
            $scope.datos.idVenta = 0;
            
            $scope.datos.optionsLoterias = [];


            $scope.datos.jugadas = [];
            $scope.datos.optionsLoterias = [];
            $scope.datos.selectedLoteria = {};
            

            $http.post(rutaGlobal+"/api/reportes/jugadas", {'datos':$scope.datos})
             .then(function(response){
              
                $scope.datos.optionsBancas = response.data.bancas;
               

                
                $scope.datos.optionsLoterias =response.data.loterias;
                $scope.datos.optionsLoterias =response.data.loterias;
                $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[0];
                console.log(response);
               
                
               
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker('refresh');
                  })
            });
       
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

        $scope.loterias_concatenar = function(abreviatura_o_descripcion){
            var loterias = '';
            if(abreviatura_o_descripcion)
            {
                if(Object.keys($scope.datos.loterias).length > 1)
                    return loterias = Object.keys($scope.datos.loterias).length + 'x';
                else
                    return loterias = $scope.datos.loterias[0].abreviatura;
            }
            else{
                if(Object.keys($scope.datos.loterias).length == 1)
                    return loterias = $scope.datos.loterias[0].descripcion;
            }
                
            
            $scope.datos.loterias.forEach(function(valor, indice, array){
               
                //Si entra a este bucle es porque los datos se mostraran en el Grid Grande
                if(Object.keys($scope.datos.loterias).length > 1)
                {
                    loterias += array[indice].abreviatura;
                }
                

                if(array[indice + 1] != undefined)
                    loterias += ', ';
             });

             console.log('dentro loterias concatenar');

             return loterias;
        }






       
        $scope.buscar_jugadas = function(){

            $('#fechaBusqueda').addClass('is-filled');

            
            if(Object.keys($scope.datos.bancas).length <= 0){
                alert("Debes seleccionar una banca");
                return;
            }
            $scope.datos.idLoteria = $scope.datos.selectedLoteria.id;

          
          $http.post(rutaGlobal+"/api/reportes/jugadas", {'action':'sp_jugadas_buscar', 'datos': $scope.datos})
             .then(function(response){

                
                $scope.datos.jugadas = [];
                $scope.datos.total_directo = 0;
                $scope.datos.total_palet = 0;
                $scope.datos.total_tripleta = 0;
                $scope.datos.monto_total = 0;

                //$scope.datos.jugadas = response.data;
                if(response.data != undefined){
                    var jsonJugadas = response.data.jugadas;
                    jsonJugadas.forEach(function(valor, indice, array){
                        $scope.datos.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                    });
                }

                $scope.calcularTotal();

            });

        }
     







        $scope.calcularTotal = function(){
            var monto_a_pagar = 0, total_palet_tripleta = 0, total_directo = 0, total_pale = 0, total_tripleta = 0, jugdada_total_palet = 0, jugada_total_directo = 0, jugada_total_tripleta = 0, jugada_monto_total = 0;
            

             $scope.datos.jugadas.forEach(function(valor, indice, array){

                if(array[indice].jugada.length == 2) jugada_total_directo += parseFloat(array[indice].monto);
                if(array[indice].tam == 4) jugdada_total_palet += parseFloat(array[indice].monto);
                if(array[indice].tam == 6) jugada_total_tripleta += parseFloat(array[indice].monto);

                jugada_monto_total +=  parseFloat(array[indice].monto);
             });

             $scope.datos.total_directo = jugada_total_directo;
             $scope.datos.total_palet = jugdada_total_palet;
             $scope.datos.total_tripleta = jugada_total_tripleta;
             $scope.datos.monto_total = jugada_monto_total;
        
        }


    })

  