var myApp = angular
    .module("myModule", [])
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "idVenta":0,
        "idUsuario": 0,
        "idBanca" : 0,

    'optionsLoterias':[],

    'estadisticas_ventas' : {
        'total' : 0,
        'total_jugadas' : 0
    },

    'fecha': moment().format('D MMM, YYYY'),

        'monitoreo' : {
            'ventas' : [],
            'fecha' : new Date(),
            'idTicketSecuencia' : '',
            'datosBusqueda' : {},
            'estado' : 5,
            'total_todos' : 0,
            'total_ganadores' : 0,
            'total_perdedores' : 0,
            'total_pendientes' : 0,
            'total_cancelados' : 0
        },
        'duplicar' : {
            'numeroticket' : null
        },
        'jugadasReporte' : {
            'optionsLoterias':[],
            'selectedLoteria' : {},
            'fecha' : new Date(),
            'jugadas' : [],

            'total_directo' : 0,
            'total_palet' : 0,
            'total_tripleta' : 0,
            'monto_total' : 0
        },
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


            $scope.datos.jugadasReporte.jugadas = [];
            $scope.datos.jugadasReporte.optionsLoterias = [];
            $scope.datos.jugadasReporte.selectedLoteria = {};
            

            $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_datosgenerales_obtener_todos'})
             .then(function(response){
                console.log('principal ajav: ', JSON.parse(response.data[0].loteriasActivas));

                $scope.datos.optionsLoterias =JSON.parse(response.data[0].loteriasActivas);
                $scope.datos.jugadasReporte.optionsLoterias =JSON.parse(response.data[0].loteriasActivas);
                $scope.datos.jugadasReporte.selectedLoteria = $scope.datos.jugadasReporte.optionsLoterias[0];
                // $scope.datos.loterias.push($scope.datos.optionsLoterias[0]);
                // $scope.datos.loterias.push($scope.datos.optionsLoterias[1]);
                //$scope.datos.loterias = [$scope.datos.optionsLoterias[0], $scope.datos.optionsLoterias[1]];
                
                $scope.datos.caracteristicasGenerales =JSON.parse(response.data[0].caracteristicasGenerales);
                var estadisticas_ventas =JSON.parse(response.data[0].estadisticas_ventas);
                $scope.datos.estadisticas_ventas.total = (estadisticas_ventas[0].total != undefined) ? estadisticas_ventas[0].total : 0;
                $scope.datos.estadisticas_ventas.total_jugadas = (estadisticas_ventas[0].total_jugadas != undefined) ? estadisticas_ventas[0].total_jugadas : 0;
                console.log('Dentor load: ',moment().format('D MMM, YYYY'));

                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('#multiselect').selectpicker('val', []);
                  })
            });
       
        }
        

        $scope.load = function(codigo_usuario){
            $scope.inicializarDatos();

          $scope.datos.idUsuario = parseInt(codigo_usuario);
          $scope.datos.idBanca = parseInt(codigo_usuario);
             
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




        $scope.buscar = function(){

            $('#fechaBusqueda').addClass('is-filled');

            console.log('buscar: ', $scope.datos.monitoreo);
            
          
          $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_ventas_buscar', 'datos': $scope.datos.monitoreo})
             .then(function(response){
                console.log(response.data[0]);
                $scope.datos.monitoreo.ventas = response.data;

                $scope.datos.monitoreo.total_todos = Object.keys($scope.datos.monitoreo.ventas).length;
                $scope.datos.monitoreo.total_pendientes = 0;
                $scope.datos.monitoreo.total_ganadores = 0;
                $scope.datos.monitoreo.total_perdedores = 0;
                $scope.datos.monitoreo.total_cancelados = 0;

                $scope.datos.monitoreo.ventas.forEach(function(valor, indice, array){

                    if(array[indice].estado2 == 2) $scope.datos.monitoreo.total_pendientes ++;
                    if(array[indice].estado2 == 3) $scope.datos.monitoreo.total_ganadores ++;
                    if(array[indice].estado2 == 4) $scope.datos.monitoreo.total_perdedores ++;
                    if(array[indice].estado2 == 0) $scope.datos.monitoreo.total_cancelados ++;
    
                 });

                // if(response.data[0].errores == 0){
                //     $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                //     alert("Se ha guardado correctamente");
                // }
            });

        }


       

     


        $scope.buscar_jugadas = function(){

            $('#fechaBusqueda').addClass('is-filled');

            

            $scope.datos.jugadasReporte.idLoteria = $scope.datos.jugadasReporte.selectedLoteria.idLoteria;
            console.log('buscar_jugadas: ', $scope.datos.jugadasReporte);
          
          $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_jugadas_buscar', 'datos': $scope.datos.jugadasReporte})
             .then(function(response){
                console.log(response.data);
                $scope.datos.jugadasReporte.jugadas = [];
                $scope.datos.jugadasReporte.total_directo = 0;
                $scope.datos.jugadasReporte.total_palet = 0;
                $scope.datos.jugadasReporte.total_tripleta = 0;
                $scope.datos.jugadasReporte.monto_total = 0;

                //$scope.datos.jugadasReporte.jugadas = response.data;
                if(response.data != undefined){
                    var jsonJugadas = response.data;
                    jsonJugadas.forEach(function(valor, indice, array){
                        $scope.datos.jugadasReporte.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                    });
                }

                $scope.calcularTotal();

            });

        }





       




        $scope.ventasReporte_buscar = function(){

            $('#fechaVentasReporte').addClass('is-filled');

            

            console.log('ventasReporte_buscar: ', $scope.datos.ventasReporte);
          
          $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_reporteVentas_buscar', 'datos': $scope.datos.ventasReporte})
             .then(function(response){
                console.log(response.data);
                $scope.datos.ventasReporte.loterias =JSON.parse(response.data[0].loterias);
                $scope.datos.ventasReporte.ticketsGanadoresSinPagar =JSON.parse(response.data[0].ticketsGanadoresSinPagar);

                var jsonVentas =JSON.parse(response.data[0].ventas);
                $scope.datos.ventasReporte.ventas.pendientes = jsonVentas[0].pendientes;
                $scope.datos.ventasReporte.ventas.ganadores = jsonVentas[0].ganadores;
                $scope.datos.ventasReporte.ventas.perdedores = jsonVentas[0].perdedores;
                $scope.datos.ventasReporte.ventas.total = jsonVentas[0].total;
                $scope.datos.ventasReporte.ventas.ventas = jsonVentas[0].ventas;
                $scope.datos.ventasReporte.ventas.comisiones = jsonVentas[0].comisiones;
                $scope.datos.ventasReporte.ventas.descuentos = jsonVentas[0].descuentos;
                $scope.datos.ventasReporte.ventas.premios = jsonVentas[0].premios;
                $scope.datos.ventasReporte.ventas.neto = jsonVentas[0].neto;
                $scope.datos.ventasReporte.ventas.balance = jsonVentas[0].balance;

                //$scope.datos.jugadasReporte.jugadas = response.data;
                // if(response.data != undefined){
                //     var jsonJugadas = response.data;
                //     jsonJugadas.forEach(function(valor, indice, array){
                //         $scope.datos.jugadasReporte.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                //     });
                // }

            });

        }


        $scope.calcularTotal = function(){
            var monto_a_pagar = 0, total_palet_tripleta = 0, total_directo = 0, total_pale = 0, total_tripleta = 0, jugdada_total_palet = 0, jugada_total_directo = 0, jugada_total_tripleta = 0, jugada_monto_total = 0;
           


         

             //Calcular total jugdasReporte
             $scope.datos.jugadasReporte.jugadas.forEach(function(valor, indice, array){

                if(array[indice].jugada.length == 2) jugada_total_directo += parseFloat(array[indice].monto);
                if(array[indice].tam == 4) jugdada_total_palet += parseFloat(array[indice].monto);
                if(array[indice].tam == 6) jugada_total_tripleta += parseFloat(array[indice].monto);

                jugada_monto_total +=  parseFloat(array[indice].monto);
             });

             $scope.datos.jugadasReporte.total_directo = jugada_total_directo;
             $scope.datos.jugadasReporte.total_palet = jugdada_total_palet;
             $scope.datos.jugadasReporte.total_tripleta = jugada_total_tripleta;
             $scope.datos.jugadasReporte.monto_total = jugada_monto_total;
        
        }


    })

  