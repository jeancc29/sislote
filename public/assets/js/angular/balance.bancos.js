myApp
    .controller("myController", function($scope, $http, $timeout, $window, $document, helperService){
        $scope.busqueda = "";
        var ruta = '';
        $scope.txtActive = 0;
        $scope.es_movil = false;
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        class Hola{
             constructor(nombre, apellido){
                 this.nombre = nombre;
                 this.apellido = apellido;
             }
        }

        $scope.cargando = false;
        $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "nombreBancoFiltro" : null,
            "accionBusqueda" : "todos",
            "bancas" : null,
            "idVenta":0,
        "idBanca" : null,
        "idLoteria" : null,
        "idSorteo" : null,
        "jugada" : null,
        'fechaHasta' : new Date(),
        
        'optionsBancas' : [],
        'selectedBancas' : {},

    'optionsLoterias':[],
    


    'monto_a_pagar': 0,
    'total_jugadas': 0,
    'total_directo': 0,
    'total_pale': 0,
    'total_tripleta': 0,
    'total_palet_tripleta': 0,

    'fecha': moment().format('D MMM, YYYY'),

        'monitoreo' : {
            'ventas' : [],
            'fecha' : new Date(),
            'idTicket' : '',
            'datosBusqueda' : {},
            'estado' : 5,
            'total_todos' : 0,
            'total_ganadores' : 0,
            'total_perdedores' : 0,
            'total_pendientes' : 0,
            'total_cancelados' : 0
        },
        'cancelar' : {
            'codigoBarra' : null,
            'razon' : null,
        },
        'pagar' : {
            'codigoBarra' : null
        }
        
    }
        $scope.inicializarDatos = function(response = null){

            
           //console.log('bancasGlobal', bancasGlobal);
            
            // $scope.datos.objetoBancas = bancasGlobal;
            // $scope.datos.optionsBancas = helperService.copiarObjecto(bancasGlobal);
            // $scope.datos.optionsSorteos = sorteosGlobal;
            
            // $scope.datos.ventas = vGlobal;

            // $scope.datos.optionsBancas.unshift({'id' : 0, 'descripcion' : 'N/A'});

            // // $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];

            $scope.buscar();
            
            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                //$('#multiselect').selectpicker('val', []);
                $('#multiselect').selectpicker("refresh");
                $('.selectpicker').selectpicker("refresh");
                //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
              })

           
       
        }
        

        $scope.load = function(codigo_usuario, ROOT_PATH, idBanca = 1){
            
            ruta = ROOT_PATH;
            console.log('ROOT_PATH: ', ruta);
            $scope.inicializarDatos();

          $scope.datos.idUsuario = idUsuario; //parseInt(codigo_usuario);
          $scope.datos.idBanca = idBanca; //parseInt(codigo_usuario);

          var a = new Hola("Jean", "Contreras");
          console.log('clase: ', ruta);
        }



      
     


  
  
     

   


        $scope.agregar_guion = function(cadena, sorteo = undefined){
            if(cadena.length == 4 && sorteo == 'Pale'){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3];
            }
            if(cadena.length == 6){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3] + '-' + cadena[4] + cadena[5];
            }
           return cadena;
        }




        


  

       
        $scope.buscar = function(){

           
            
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.layout = 'Principal';
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $scope.cargando = true;
          $http.post(rutaGlobal+"/api/balance/bancos", {'action':'sp_ventas_buscar', 'datos': jwt})
             .then(function(response){
                console.log('balance ',response);
                if(response.data.errores == 0){
                    $scope.cargando = false;
                    $scope.datos.bancas = response.data.bancas;
                }else{
                    $scope.cargando = false;
                    alert(response.data.mensaje);
                    return;
                }

            }
            ,
            function(){
                $scope.cargando = false;
                alert("Error");
            }
            );

        }

        $scope.totalesParaFiltros = function(){
            $scope.datos.objetoBancas.forEach(function(valor, indice, array){

                $scope.datos.total_todos ++;
                if(array[indice].ventas > 0) $scope.datos.total_con_ventas ++;
                if(array[indice].premios > 0) $scope.datos.total_con_premios ++;
                if(array[indice].ticketsPendientes > 0) $scope.datos.total_cont_tickets_pendientes ++;

            });
        }



      

       

        $scope.cambiarAccionBusqueda = function(accionBusqueda){
            $scope.datos.accionBusqueda = accionBusqueda;
        }

        $scope.greaterThan = function(prop, val){
            
            
            return function(item){
                console.log('greaterThan:');
                if(helperService.empty($scope.datos.nombreBancoFiltro, 'string') == true){
                        return true;
                }else{
                        return item['nombre'].toUpperCase().indexOf($scope.datos.nombreBancoFiltro.toUpperCase()) != -1;
                }
                
            }
        }









     

      

        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }

       

       
    


       


    })
