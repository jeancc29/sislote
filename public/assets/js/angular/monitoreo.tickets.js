myApp
    .controller("myController", function($scope, $http, $timeout, $window, $document, helperService, printerService){
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
        "idBanca" : null,
        "idLoteria" : null,
        "idSorteo" : null,
        "jugada" : null,
        'fecha' : new Date(),
        
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
            
            // $scope.datos.optionsBancas = bancasGlobal;
            // $scope.datos.optionsLoterias = loteriasGlobal;
            // $scope.datos.optionsSorteos = sorteosGlobal;
            // $scope.datos.optionsBancas.unshift({'id' : 0, 'descripcion' : 'N/A'});
            // $scope.datos.optionsLoterias.unshift({'id' : 0, 'descripcion' : 'N/A'});
            // $scope.datos.optionsSorteos.unshift({'id' : 0, 'descripcion' : 'N/A'});

            // $scope.datos.selectedBanca = $scope.datos.optionsBancas[0];
            // $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[0];
            // $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];
            
            // $timeout(function() {
            //     // anything you want can go here and will safely be run on the next digest.
            //     //$('#multiselect').selectpicker('val', []);
            //     $('#multiselect').selectpicker("refresh");
            //     $('.selectpicker').selectpicker("refresh");
            //     //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
            //   })

            getMonitoreo(true);
           
       
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



        $scope.agregar_guion = function(cadena, sorteo = undefined){
            if(cadena.length == 4 && sorteo == 'Pale'){
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

      
        $scope.seleccionarTicket = function(ticket){
            $scope.mostrarVentanaTicket = true;
            getTicketVerTicket(ticket.codigoBarra);
        }

        getTicketVerTicket = function(codigoBarra){
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.codigoBarra = codigoBarra;
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $scope.cargandoVentanaTicket = true;
          $http.post(rutaGlobal+"/api/principal/buscarTicket", {'action':'sp_ventas_buscar', 'datos': jwt})
             .then(function(response){
                console.log('monitoreo ',response);
                if(response.data.errores == 0){
                    $scope.datos.selectedTicket = response.data.venta;
                    $scope.cargandoVentanaTicket = false;

                }else{
                    $scope.cargandoVentanaTicket = false;
                    alert(response.data.mensaje);
                    return;
                }

                // if(response.data[0].errores == 0){
                //     $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                //     alert("Se ha guardado correctamente");
                // }
            }
            ,
            function(){
                $scope.cargandoVentanaTicket = false;
                alert("error");
            }
            );
        }

        getTicketImprimir = function(codigoBarra){
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.codigoBarra = codigoBarra;
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $scope.cargando = true;
          $http.post(rutaGlobal+"/api/principal/buscarTicket", {'action':'sp_ventas_buscar', 'datos': jwt})
             .then(function(response){
                console.log('monitoreo ',response);
                if(response.data.errores == 0){
                    
                    $scope.cargando = false;
                    printerService.printTicket(response.data.venta, CMD.TICKET_COPIA);
                }else{
                    $scope.cargando = false;
                    alert(response.data.mensaje);
                    return;
                }

                // if(response.data[0].errores == 0){
                //     $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                //     alert("Se ha guardado correctamente");
                // }
            }
            ,
            // function(){
            //     $scope.cargando = false;
            //     alert("error");
            // }
            );
        }

        $scope.getJugadasPertenecienteALoteria = function(idLoteria, jugadas){
            // console.log("Dentro jugadas idLoteria: ", idLoteria);
            // console.log("Dentro jugadas: ", jugadas);
            
            var a = jugadas.filter(j => j.idLoteria == idLoteria && j.sorteo != "Super pale");
            console.log("jugadas: ", a);
            return a;
        }

        $scope.getJugadasPertenecienteALoteriaLength = function(idLoteria, jugadas){
            // console.log("Dentro jugadas idLoteria: ", idLoteria);
            // console.log("Dentro jugadas: ", jugadas);
            
            var a = jugadas.filter(j => j.idLoteria == idLoteria && j.sorteo != "Super pale");
            console.log("jugadas: ", a);
            return a.length;
        }

        $scope.getJugadasSuperpalePertenecienteALoteria = function(idLoteria, idLoteriaSuperpale, jugadas){
            console.log("Dentro jugadas idLoteria: ", idLoteria);
            console.log("Dentro jugadas: ", jugadas);
            
            var a = jugadas.filter(j => j.idLoteria == idLoteria && j.idLoteriaSuperpale == idLoteriaSuperpale && j.sorteo == "Super pale");
            console.log("jugadasSuper: ", a);
            return a;
        }

        $scope.getJugadasSuperpalePertenecienteALoteriaLength = function(idLoteria, idLoteriaSuperpale, jugadas){
            console.log("Dentro jugadas idLoteria: ", idLoteria);
            console.log("Dentro jugadas: ", jugadas);
            
            var a = jugadas.filter(j => j.idLoteria == idLoteria && j.idLoteriaSuperpale == idLoteriaSuperpale && j.sorteo == "Super pale");
            console.log("jugadasSuper: ", a);
            return a.length;
        }


       
        $scope.buscar = function(){

            console.log('monitoreo before addClass',$scope.datos.monitoreo);
            $('#fechaBusqueda').addClass('is-filled');
            
            console.log('monitoreo after addClass',$scope.datos.monitoreo);
            
            $scope.datos.monitoreo.idUsuario = $scope.datos.idUsuario;
            $scope.datos.monitoreo.layout = 'Principal';

            if($scope.datos.selectedBanca.id == 0){
                $scope.datos.idBanca = null;
            }else
                $scope.datos.idBanca = $scope.datos.selectedBanca.id;
            if($scope.datos.selectedLoteria.id == 0){
                $scope.datos.idLoteria = null;
            }else
                $scope.datos.idLoteria = $scope.datos.selectedLoteria.id;
            if($scope.datos.selectedSorteo.id == 0){
                $scope.datos.idSorteo = null;
            }else
                $scope.datos.idSorteo = $scope.datos.selectedSorteo.id;
            
            getMonitoreo();
        }

        function getMonitoreo(onCreate = false)
        {
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.fecha = $scope.datos.monitoreo.fecha;
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $scope.cargando = true;
          $http.post(rutaGlobal+"/api/monitoreo/tickets", {'action':'sp_ventas_buscar', 'datos': jwt})
             .then(function(response){
                console.log('monitoreo ',response);
                if(response.data.errores == 0){
                    if(onCreate)
                        llenarCombos(response.data);

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
                    $scope.cargando = false;

                }else{
                    $scope.cargando = false;
                    alert(response.data.mensaje);
                    return;
                }

                // if(response.data[0].errores == 0){
                //     $scope.inicializarDatos($scope.datos.idLoteria, $scope.datos.idSorteo);
                //     alert("Se ha guardado correctamente");
                // }
            }
            ,
            // function(){
            //     $scope.cargando = false;
            //     alert("error");
            // }
            );
        }

        function llenarCombos(data)
        {
            $scope.datos.optionsBancas = data.bancas;
            $scope.datos.optionsLoterias = data.loterias;
            $scope.datos.optionsSorteos = data.sorteos;
            $scope.datos.optionsBancas.unshift({'id' : 0, 'descripcion' : 'N/A'});
            $scope.datos.optionsLoterias.unshift({'id' : 0, 'descripcion' : 'N/A'});
            $scope.datos.optionsSorteos.unshift({'id' : 0, 'descripcion' : 'N/A'});

            $scope.datos.selectedBanca = $scope.datos.optionsBancas[0];
            $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[0];
            $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];
            
            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                //$('#multiselect').selectpicker('val', []);
                $('#multiselect').selectpicker("refresh");
                $('.selectpicker').selectpicker("refresh");
                //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
              })
        }

        $scope.imprimirTicket = function(ticket, es_movil){
            // printerService.printTicket(ticket, CMD.TICKET_COPIA);
            getTicketImprimir(ticket.codigoBarra);
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










        $scope.cancelar = function(ticket, agregarRazon = true){

            if(ticket.codigoBarra == null || ticket.codigoBarra == undefined)
            {
                alert('El codigo del ticket no debe estar vacio');
                return;
            }

            if(Number(ticket.codigoBarra) != ticket.codigoBarra)
            {
                alert('El codigo del ticket debe ser numerico');
                return;
            }

            

            $scope.datos.cancelar.idUsuario = $scope.datos.idUsuario;
            $scope.datos.cancelar.idBanca = idBancaGlobal;
            $scope.datos.cancelar.servidor = servidorGlobal;
            if(agregarRazon == true){
                $scope.datos.cancelar.razon = ".";
                $scope.datos.cancelar.codigoBarra = ticket.codigoBarra;
            }
           // $scope.datos.cancelar.codigoBarra = $scope.datos.idUsuario;

           var jwt = helperService.createJWT($scope.datos.cancelar);
            $http.post(rutaGlobal+"/api/principal/cancelar", {'action':'sp_ventas_cancelar', 'datos': jwt})
             .then(function(response){
                console.log(response.data);

                if(response.data.errores == 1){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    $scope.buscar();
                    $scope.inicializarDatos(response);
                    alert(response.data.mensaje);
                }

            });
        }

        $scope.eliminar = function(ticket, agregarRazon = true){

            if(ticket.codigoBarra == null || ticket.codigoBarra == undefined)
            {
                alert('El codigo del ticket no debe estar vacio');
                return;
            }

            if(Number(ticket.codigoBarra) != ticket.codigoBarra)
            {
                alert('El codigo del ticket debe ser numerico');
                return;
            }

            

            $scope.datos.cancelar.idUsuario = $scope.datos.idUsuario;
            $scope.datos.cancelar.idBanca = idBancaGlobal;
            $scope.datos.cancelar.servidor = servidorGlobal;
            if(agregarRazon == true){
                $scope.datos.cancelar.razon = ".";
                $scope.datos.cancelar.codigoBarra = ticket.codigoBarra;
            }


            
           // $scope.datos.cancelar.codigoBarra = $scope.datos.idUsuario;

           var jwt = helperService.createJWT($scope.datos.cancelar);
            $http.post(rutaGlobal+"/api/principal/eliminar", {'action':'sp_ventas_cancelar', 'datos': jwt})
             .then(function(response){
                console.log(response.data);

                if(response.data.errores == 1){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    $scope.buscar();
                    $scope.inicializarDatos(response);
                    alert(response.data.mensaje);
                }

            });
        }

        $scope.pagar = function(ticket){

            if(ticket.codigoBarra == null || ticket.codigoBarra == undefined)
            {
                alert('El codigo del ticket no debe estar vacio');
                return;
            }

            if(Number(ticket.codigoBarra) != ticket.codigoBarra)
            {
                alert('El codigo del ticket debe ser numerico');
                return;
            }


            $scope.datos.pagar.idUsuario = idUsuarioGlobal;
            $scope.datos.pagar.codigoBarra = ticket.codigoBarra;
            $scope.datos.pagar.servidor = servidorGlobal;

            // console.log($scope.datos.pagar, ' Pagar idUsuario');

            var jwt = helperService.createJWT($scope.datos.pagar);
            $http.post(rutaGlobal+"/api/principal/pagar", {'action':'sp_pagar_buscar', 'datos': jwt})
             .then(function(response){


                if(response.data.errores == 1){
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.buscar();
                    $scope.datos.pagar.codigoBarra = null;
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

        $scope.abrirModalRazon = function(ticket, esCancelar){
            $scope.datos.cancelar.codigoBarra = ticket.codigoBarra;

            $scope.esCancelar = esCancelar;
            $('#inputCodigoBarra').addClass('is-filled');
            $('#modal-cancelar-eliminar').modal('show');
        }

        $scope.cancelarEliminarDesdeModalRazon = function(){

            console.log('cancelarEliminarDesdeModalRazon:', $scope.datos.cancelar.razon);
            
            if($scope.esCancelar == true){
                if($scope.empty($scope.datos.cancelar.razon, "string") == true){
                    alert('Debe espeficicar una razon');
                    return;
                }
    
                $('#modal-cancelar-eliminar').modal('hide');
               $scope.cancelar($scope.datos.cancelar, false);
            }
            else if($scope.esCancelar == false){
                if($scope.empty($scope.datos.cancelar.razon, "string") == true){
                    alert('Debe espeficicar una razon');
                    return;
                }
    
                $('#modal-cancelar-eliminar').modal('hide');
                $scope.eliminar($scope.datos.cancelar, false);
            }
        }

        $scope.toSecuencia = function(idTicket){
            var str = "" + idTicket;
            var pad = "000000000";
            var ans = pad.substring(0, pad.length - str.length) + str;
            return ans;
        }

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
