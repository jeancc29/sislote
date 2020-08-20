myApp
    .controller("myController", function($scope, $http, $timeout, $window, $document, helperService){
        $scope.busqueda = "";
        var ruta = '';
        var bancasBusqueda = [];
        $scope.txtActive = 0;
        $scope.es_movil = false;
        $scope.cargando = false;
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
            "descripcionBanca" : null,
            "accionBusqueda" : "con ventas",
            "idVenta":0,
        "idBanca" : null,
        "idLoteria" : null,
        "idSorteo" : null,
        "jugada" : null,
        'fechaDesde' : new Date(),
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
            
            $scope.datos.bancas = bancasGlobal;
            $scope.datos.optionsLoterias = loteriasGlobal;
            $scope.datos.optionsSorteos = sorteosGlobal;
            
            $scope.datos.selectedBanca = $scope.datos.optionsBancas[0];
            $scope.optionsMonedas = monedasGlobal;
            console.log("monedas: ", $scope.optionsMonedas);
            
            $scope.selectedMoneda = $scope.optionsMonedas[0];
            // $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[0];
            // $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];
            
            console.log("Dentro inicializar datos");
            
            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                //$('#multiselect').selectpicker('val', []);
                $('#multiselect').selectpicker("refresh");
                $('.selectpicker').selectpicker("refresh");
                helperService.actualizarScrollBar();
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
            console.log('clase: ', monedasGlobal);
        }

      
        $scope.calcularTotal = function(){
            var totalTickets = 0, totalVentas = 0, totalComisiones = 0, totalDescuentos = 0, totalPremios = 0, totalTotalNeto = 0, totalBalance = 0, totalBalanceActual = 0, totalBalanceActual = 0, totalcaidaAcumulada = 0;
             $scope.datos.bancas.forEach(function(valor, indice, array){

                totalTickets += parseFloat(array[indice].tickets);
                totalVentas += parseFloat(array[indice].ventas);
                totalComisiones += parseFloat(array[indice].comisiones);
                totalDescuentos += parseFloat(array[indice].descuentos);
                totalPremios += parseFloat(array[indice].premios);
                totalTotalNeto += parseFloat(array[indice].totalNeto);
                totalBalance += parseFloat(array[indice].balance);
                totalBalanceActual += parseFloat(array[indice].balanceActual);
                totalcaidaAcumulada += parseFloat(array[indice].caidaAcumulada);
               
             });

            $scope.datos.totalTickets = totalTickets;
            $scope.datos.totalVentas = totalVentas;
            $scope.datos.totalComisiones = totalComisiones;
            $scope.datos.totalDescuentos = totalDescuentos;
            $scope.datos.totalPremios = totalPremios;
            $scope.datos.totalTotalNeto = totalTotalNeto;
            $scope.datos.totalBalance = totalBalance;
            $scope.datos.totalBalanceActual = totalBalanceActual;
            $scope.datos.totalcaidaAcumulada = totalcaidaAcumulada;
        
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
            $scope.datos.selectedTicket = ticket;
        }

       
        $scope.buscar = function(onCreate = false){
            
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.layout = 'Principal';
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $scope.cargando = true;
            $http.post(rutaGlobal+"/api/reportes/historico", {'action':'sp_ventas_buscar', 'datos': jwt})
                .then(function(response){
                    console.log('monitoreo ',response);
                    // if(response.data.errores == 0){

                        $scope.datos.bancas = response.data.bancas;
                        if(onCreate){
                            $scope.optionsMonedas = response.data.monedas;
                            $scope.selectedMoneda = $scope.optionsMonedas[0];
                        }
                        $scope.calcularTotal();
                        $scope.cargando = false;
                        $timeout(function() {
                            // anything you want can go here and will safely be run on the next digest.
                            //$('#multiselect').selectpicker('val', []);
                            $('#multiselect').selectpicker("refresh");
                            $('.selectpicker').selectpicker("refresh");
                            helperService.actualizarScrollBar();
                            //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                          })

                          

                        
                    // }else{
                    //     alert(response.data.mensaje);
                    //     return;
                    // }

                }
                ,
                function(){
                    $scope.cargando = false;
                    alert("Error");
                }
                );

        }

        $scope.totalesParaFiltros = function(){
            $scope.datos.bancas.forEach(function(valor, indice, array){

                $scope.datos.total_todos ++;
                if(array[indice].ventas > 0) $scope.datos.total_con_ventas ++;
                if(array[indice].premios > 0) $scope.datos.total_con_premios ++;
                if(array[indice].ticketsPendientes > 0) $scope.datos.total_cont_tickets_pendientes ++;

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

        // $scope.buscarTicket = function(opcion){
        //     // console.log('buscarpor ticket, estado: ', estado);
        //     if(estado == null){
        //         if($scope.datos.monitoreo.descripcion == undefined){
        //             delete $scope.datos.monitoreo.datosBusqueda['descripcion'];
        //         }else{
        //             $scope.datos.monitoreo.datosBusqueda.descripcion = $scope.datos.descripcion;
        //         }
        //     }
        //     else{
        //         if(estado == 5){
        //             delete $scope.datos.monitoreo.datosBusqueda['status'];
        //         }
        //         else{
        //             $scope.datos.monitoreo.datosBusqueda.status = estado;
        //         }
        //     }
           


        // }

       

        $scope.cambiarAccionBusqueda = function(accionBusqueda){
            $scope.datos.accionBusqueda = accionBusqueda;
        }

        $scope.greaterThan = function(prop, val){

         
            
            bancasBusqueda = [];
            $scope.totalGanadores = 0;
            $scope.totalPendientes = 0;
            $scope.totalPerdedores = 0;
            $scope.totalVentas = 0;
            $scope.totalTickets = 0;
            $scope.totalComisiones = 0;
            $scope.totalDescuentos = 0;
            $scope.totalPremios = 0;
            $scope.totalTotalNeto = 0;
            $scope.totalBalance = 0;
            $scope.totalBalanceActual = 0;
            $scope.totalcaidaAcumulada = 0;
            
            return function(item){
                
                if(helperService.empty($scope.datos.descripcionBanca, 'string') == true){
                    var mostrar = false;
                    if($scope.datos.accionBusqueda == "con premios"){
                        mostrar = item['premios'] > 0 && item['idMoneda'] == $scope.selectedMoneda.id;
                    }
                    else if($scope.datos.accionBusqueda == "con ventas"){
                        mostrar = item['ventas'] > 0 && item['idMoneda'] == $scope.selectedMoneda.id;
                    }
                    else if($scope.datos.accionBusqueda == "con tickets pendientes"){
                        mostrar = item['ticketsPendientes'] > 0 && item['idMoneda'] == $scope.selectedMoneda.id;
                    }
                    else{
                        mostrar = item['idMoneda'] == $scope.selectedMoneda.id;
                    }

                    if(mostrar == true){
                        $scope.totalGanadores += Number(item.ganadores);
                        $scope.totalPendientes += Number(item.pendientes);
                        $scope.totalPerdedores += Number(item.perdedores);
                        $scope.totalVentas += Number(item.ventas);
                        $scope.totalTickets += Number(item.tickets);
                        $scope.totalComisiones += Number(item.comisiones);
                        $scope.totalDescuentos += Number(item.descuentos);
                        $scope.totalPremios += Number(item.premios);
                        $scope.totalTotalNeto += Number(item.totalNeto);
                        $scope.totalBalance += Number(item.balance);
                        $scope.totalBalanceActual += Number(item.balanceActual);
                        $scope.totalcaidaAcumulada += Number(item.caidaAcumulada);
                        bancasBusqueda.push(item);
                    }
                    console.log('greaterThan: ',bancasBusqueda);
                    return mostrar;
                }else{
                    var mostrar = false;

                    if($scope.datos.accionBusqueda == "con premios")
                        mostrar = item['premios'] > 0 && item['descripcion'].indexOf($scope.datos.descripcionBanca.toUpperCase()) != -1 && item['idMoneda'] == $scope.selectedMoneda.id;
                    else if($scope.datos.accionBusqueda == "con ventas" )
                        mostrar = item['ventas'] > 0 && item['descripcion'].indexOf($scope.datos.descripcionBanca.toUpperCase()) != -1 && item['idMoneda'] == $scope.selectedMoneda.id;
                    else if($scope.datos.accionBusqueda == "con tickets pendientes")
                        mostrar = item['ticketsPendientes'] > 0 && item['descripcion'].indexOf($scope.datos.descripcionBanca.toUpperCase()) != -1 && item['idMoneda'] == $scope.selectedMoneda.id;
                    else
                        mostrar = item['descripcion'].indexOf($scope.datos.descripcionBanca.toUpperCase()) != -1 && item['idMoneda'] == $scope.selectedMoneda.id;
                    
                        if(mostrar == true){
                            $scope.totalGanadores += Number(item.ganadores);
                            $scope.totalPendientes += Number(item.pendientes);
                            $scope.totalPerdedores += Number(item.perdedores);
                            $scope.totalVentas += Number(item.ventas);
                            $scope.totalTickets += Number(item.tickets);
                            $scope.totalComisiones += Number(item.comisiones);
                            $scope.totalDescuentos += Number(item.descuentos);
                            $scope.totalPremios += Number(item.premios);
                            $scope.totalTotalNeto += Number(item.totalNeto);
                            $scope.totalBalance += Number(item.balance);
                            $scope.totalBalanceActual += Number(item.balanceActual);
                            $scope.totalcaidaAcumulada += Number(item.caidaAcumulada);
                            bancasBusqueda.push(item);
                        }
                    return mostrar;
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
            if(agregarRazon == true){
                $scope.datos.cancelar.razon = ".";
                $scope.datos.cancelar.codigoBarra = ticket.codigoBarra;
            }
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
            if(agregarRazon == true){
                $scope.datos.cancelar.razon = ".";
                $scope.datos.cancelar.codigoBarra = ticket.codigoBarra;
            }


            
           // $scope.datos.cancelar.codigoBarra = $scope.datos.idUsuario;

            $http.post(rutaGlobal+"/api/principal/eliminar", {'action':'sp_ventas_cancelar', 'datos': $scope.datos.cancelar})
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

            // console.log($scope.datos.pagar, ' Pagar idUsuario');

            $http.post(rutaGlobal+"/api/principal/pagar", {'action':'sp_pagar_buscar', 'datos': $scope.datos.pagar})
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
