var myApp = angular
    .module("myModule", [])
    .controller("myController", function($scope, $http, $timeout, $window, $document){
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

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "idVenta":0,
        "idUsuario": 0,
        "idBanca" : 1,
        "codigoBarra":"barra29",
        "total": 0,
        "subTotal":0,
        "descuentoPorcentaje":0,
        "descuentoMonto":0,
        "hayDescuento":0,
        "sms":true,
        "whatsapp":0,
        "estado":0,
        "loterias": [],
        "jugadas":[],

        'optionsBancas' : [],
        'selectedBancas' : {},

    'optionsLoterias':[],
    'loterias':[],
    'jugada':null,

    'estadisticas_ventas' : {
        'total' : 0,
        'total_jugadas' : 0
    },

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
        'cancelar' : {
            'codigoBarra' : null,
            'razon' : null,
        },
        'enviarSMS' : {}
        
    }
        $scope.inicializarDatos = function(response = null){

            
           
            
            $scope.datos.idVenta = 0;
            $scope.datos.total = 0;
            $scope.datos.subTotal = 0;
            $scope.datos.descuentoPorcentaje = 0;
            $scope.datos.descuentoMonto = 0;
            $scope.datos.loterias = [];
            
            $scope.datos.jugadas = [];
            $scope.datos.optionsLoterias = [];

            $scope.datos.jugada = null;
            $scope.datos.monto_a_pagar = 0;
            $scope.datos.total_jugadas = 0;
            $scope.datos.total_directo = 0;
            $scope.datos.total_palet_tripleta = 0;

            $scope.datos.jugadasReporte.jugadas = [];
            $scope.datos.jugadasReporte.optionsLoterias = [];
            $scope.datos.jugadasReporte.selectedLoteria = {};
            
            if(response != null){
                $scope.datos.optionsBancas = response.data.bancas;
                let idx = 0;
                if($scope.datos.optionsBancas.find(x => x.id == response.data.idBanca) != undefined)
                    idx = $scope.datos.optionsBancas.findIndex(x => x.id == response.data.idBanca);
                $scope.datos.selectedBancas = $scope.datos.optionsBancas[idx];
                $scope.datos.idBanca = response.data.idBanca;


                $scope.datos.optionsVentas = (response.data.ventas != undefined) ? response.data.ventas : [{'id': 1, 'codigoBarra' : 'No hay ventas'}];
                $scope.datos.selectedVentas = $scope.datos.optionsVentas[0];
                $scope.datos.optionsLoterias =response.data.loterias;
                $scope.datos.jugadasReporte.optionsLoterias = response.data.loterias;
                $scope.datos.jugadasReporte.selectedLoteria = $scope.datos.jugadasReporte.optionsLoterias[0];

                $scope.datos.estadisticas_ventas.total_jugadas = response.data.total_jugadas;
                $scope.datos.estadisticas_ventas.total = response.data.total_ventas;
                
                
                $timeout(function() {
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                  })

                  return;
            }

            $http.post(rutaGlobal+"/api/principal/indexPost", {'datos':$scope.datos, 'action':'sp_jugadas_obtener_montoDisponible'})
             .then(function(response){

                console.log(response)

                $scope.datos.optionsBancas = response.data.bancas;
                let idx = 0;
                if($scope.datos.optionsBancas.find(x => x.id == response.data.idBanca) != undefined)
                    idx = $scope.datos.optionsBancas.findIndex(x => x.id == response.data.idBanca);
                $scope.datos.selectedBancas = $scope.datos.optionsBancas[idx];
                $scope.datos.idBanca = response.data.idBanca;

                $scope.datos.optionsVentas = (response.data.ventas != undefined) ? response.data.ventas : [{'id': 1, 'codigoBarra' : 'No hay ventas'}];
                $scope.datos.selectedVentas = $scope.datos.optionsVentas[0];
                 $scope.datos.optionsLoterias =response.data.loterias;
                 console.log('select: ',$scope.datos.selectedVentas);
                //  console.log($scope.datos.optionsLoterias);
                $scope.datos.jugadasReporte.optionsLoterias = response.data.loterias;
                $scope.datos.jugadasReporte.selectedLoteria = $scope.datos.jugadasReporte.optionsLoterias[0];

                $scope.datos.estadisticas_ventas.total_jugadas = response.data.total_jugadas;
                $scope.datos.estadisticas_ventas.total = response.data.total_ventas;
                // // $scope.datos.loterias.push($scope.datos.optionsLoterias[0]);
                // // $scope.datos.loterias.push($scope.datos.optionsLoterias[1]);
                // //$scope.datos.loterias = [$scope.datos.optionsLoterias[0], $scope.datos.optionsLoterias[1]];
                
                // $scope.datos.caracteristicasGenerales =JSON.parse(response.data[0].caracteristicasGenerales);
                // var estadisticas_ventas =JSON.parse(response.data[0].estadisticas_ventas);
                // $scope.datos.estadisticas_ventas.total = (estadisticas_ventas[0].total != undefined) ? estadisticas_ventas[0].total : 0;
                // $scope.datos.estadisticas_ventas.total_jugadas = (estadisticas_ventas[0].total_jugadas != undefined) ? estadisticas_ventas[0].total_jugadas : 0;


                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                  })
            });
       
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



      
     


  
  
     

      
        $scope.calcularTotal = function(){
            var monto_a_pagar = 0, total_palet_tripleta = 0, total_directo = 0, total_pale = 0, total_tripleta = 0, jugdada_total_palet = 0, jugada_total_directo = 0, jugada_total_tripleta = 0, jugada_monto_total = 0;
             $scope.datos.jugadas.forEach(function(valor, indice, array){

                if(array[indice].tam == 2) total_directo += parseFloat(array[indice].monto);
                if(array[indice].tam == 4) total_pale += parseFloat(array[indice].monto);
                if(array[indice].tam == 6) total_tripleta += parseFloat(array[indice].monto);
                if(array[indice].tam == 4 || array[indice].tam == 6) total_palet_tripleta += parseFloat(array[indice].monto);

                monto_a_pagar +=  parseFloat(array[indice].monto);
             });


            //  $scope.datos.monto_a_pagar = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * monto_a_pagar : monto_a_pagar;
            //  $scope.datos.total_directo = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_directo : total_directo;
            //  $scope.datos.total_pale = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_pale : total_pale;
            //  $scope.datos.total_tripleta = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_tripleta : total_tripleta;
            //  $scope.datos.total_palet_tripleta = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_palet_tripleta : total_palet_tripleta;
            //  $scope.datos.total_jugadas = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * Object.keys($scope.datos.jugadas).length : Object.keys($scope.datos.jugadas).length;
            //  $scope.datos.descuentoMonto = ($scope.datos.hayDescuento) ? parseInt(parseFloat($scope.datos.monto_a_pagar) / parseFloat($scope.datos.caracteristicasGenerales[0].cantidadAplicar)) * parseFloat($scope.datos.caracteristicasGenerales[0].descuentoValor) : 0;
             
             $scope.datos.monto_a_pagar =  monto_a_pagar;
             $scope.datos.total_directo =  total_directo;
             $scope.datos.total_pale =  total_pale;
             $scope.datos.total_tripleta = total_tripleta;
             $scope.datos.total_palet_tripleta =  total_palet_tripleta;
             $scope.datos.total_jugadas =  Object.keys($scope.datos.jugadas).length;
             $scope.datos.descuentoMonto = ($scope.datos.hayDescuento) ? parseInt(parseFloat($scope.datos.monto_a_pagar) / parseFloat($scope.datos.selectedBancas.deCada)) * parseFloat($scope.datos.selectedBancas.descontar)  : 0;
             

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



        $scope.agregar_guion = function(cadena){
            if(cadena.length == 4){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3];
            }
            if(cadena.length == 6){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3] + '-' + cadena[4] + cadena[5];
            }
           return cadena;
        }




        

        $scope.abrirVentanaSms = function(){
            if($scope.datos.sms == true || $scope.datos.whatsapp == true){
                if($scope.datos.sms != true)
                    $scope.datos.numSms = null;
                if($scope.datos.whatsapp != true)
                    $scope.datos.numWhatsapp = null;

                $('#modal-sms').modal('show');
            }
        }

      

        $scope.buscar = function(){

            console.log('monitoreo before addClass',$scope.datos.monitoreo);
            $('#fechaBusqueda').addClass('is-filled');
            
            console.log('monitoreo after addClass',$scope.datos.monitoreo);
            
            $scope.datos.monitoreo.idUsuario = $scope.datos.idUsuario;
            $scope.datos.monitoreo.layout = 'Principal';
          
          $http.post(rutaGlobal+"/api/reportes/monitoreo", {'action':'sp_ventas_buscar', 'datos': $scope.datos.monitoreo})
             .then(function(response){
                console.log('monitoreo ',response);
                if(response.data.errores == 0){
                    $scope.datos.monitoreo.ventas = response.data.monitoreo;

                    $scope.datos.monitoreo.total_todos = Object.keys($scope.datos.monitoreo.ventas).length;
                    $scope.datos.monitoreo.total_pendientes = 0;
                    $scope.datos.monitoreo.total_ganadores = 0;
                    $scope.datos.monitoreo.total_perdedores = 0;
                    $scope.datos.monitoreo.total_cancelados = 0;

                    $scope.datos.monitoreo.ventas.forEach(function(valor, indice, array){

                        if(array[indice].status == 1) $scope.datos.monitoreo.total_pendientes ++;
                        if(array[indice].status == 2) $scope.datos.monitoreo.total_ganadores ++;
                        if(array[indice].status == 3) $scope.datos.monitoreo.total_perdedores ++;
                        if(array[indice].status == 0) $scope.datos.monitoreo.total_cancelados ++;
        
                    });
                }else{
                    alert(response.data.mensaje);
                    return;
                }

                // if(response.data[0].errores == 0){
                //     $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                //     alert("Se ha guardado correctamente");
                // }
            });

        }


        $scope.validarHora = function(horaCierre, loteria){
            var fecha, time, ano, mes, dia, hh, min, ss, fecha_actual_horaCierre;
            fecha = new Date();
            time = horaCierre.split(':');
            ano = fecha.getFullYear();
            mes = fecha.getMonth() + 1;
            dia = fecha.getDate();
            hh = time[0];
            min = time[1];
            ss = time[2];

            fecha_actual_horaCierre = new Date(ano, mes, dia, hh, min, ss);

            //console.log('validarHora: ', loteria, new Date(fecha_actual_horaCierre), ' hora: ', new Date(), ' comparacion: ', (new Date(fecha_actual_horaCierre) >= new Date()));

            //console.log('Validar hora: ',new Date() >= fecha_actual_horaCierre, ' fechaCierre: ', fecha_actual_horaCierre, ' fechaActual: ', new Date());
            return (new Date() >= fecha_actual_horaCierre);
        }

        $scope.buscarpor_ticket_estado = function(estado){
            // console.log('buscarpor ticket, estado: ', estado);
            if(estado == null){
                if($scope.datos.monitoreo.idTicket == undefined){
                    delete $scope.datos.monitoreo.datosBusqueda['idTicket'];
                }else{
                    $scope.datos.monitoreo.datosBusqueda.idTicket = $scope.datos.monitoreo.idTicket;
                }
            }
            else{
                if(estado == 5){
                    delete $scope.datos.monitoreo.datosBusqueda['status'];
                }
                else{
                    $scope.datos.monitoreo.datosBusqueda.status = estado;
                }
            }
           


        }










        $scope.cancelar = function(){

            if($scope.datos.cancelar.codigoBarra == null || $scope.datos.cancelar.codigoBarra == undefined)
            {
                alert('El codigo del ticket no debe estar vacio');
                return;
            }

            if(Number($scope.datos.cancelar.codigoBarra) != $scope.datos.cancelar.codigoBarra)
            {
                alert('El codigo del ticket debe ser numerico');
                return;
            }

            if($scope.datos.cancelar.razon == null || $scope.datos.cancelar.razon == undefined)
            {
                alert('La razon no debe estar vacia');
                return;
            }

            $scope.datos.cancelar.idUsuario = $scope.datos.idUsuario;
            $scope.datos.cancelar.idBanca = $scope.datos.selectedBancas.id;
           // $scope.datos.cancelar.codigoBarra = $scope.datos.idUsuario;

            $http.post(rutaGlobal+"/api/principal/cancelar", {'action':'sp_ventas_cancelar', 'datos': $scope.datos.cancelar})
             .then(function(response){
                console.log(response.data);

                if(response.data.errores == 1){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.inicializarDatos(response);
                    alert(response.data.mensaje);
                }

            });
        }

        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }

        // $scope.toSecuencia = function(idTicket){
        //     var str = "" + idTicket;
        //     var pad = "000000000";
        //     var ans = pad.substring(0, pad.length - str.length) + str;
        //     return ans;
        // }

        $scope.estado = function(status){
            if(status == 1)
                return 'Pendientes';
            else if(status == 2)
                return 'Ganador';
            else if(status == 3)
                return 'Perdedor';
            else
                return 'Cancelado';
        }

        $scope.empty = function(valor, tipo){
            if(tipo === 'number'){
                if(Number(valor) == undefined || valor == '' || valor == null || Number(valor) <= 0)
                    return true;
            }
            if(tipo === 'string'){
                if(valor == undefined || valor == '' || valor == null)
                    return true;
            }

            return false;
        }

        var getData = function(){
            var json = [];
            // var contador = 0;
            // $.each($window.sessionStorage, function(i, v){
            //   json.push(angular.fromJson(v));
            // });
            return json;
          }

       


    })
