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

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "fechaFiltro" : null,
            "accionBusqueda" : "todos",
            "bancas" : null,
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

            
        //    //console.log('bancasGlobal', bancasGlobal);
            
        //     $scope.datos.objetoBancas = bancasGlobal;
        //     // $scope.datos.optionsBancas = helperService.copiarObjecto(bancasGlobal);
        //     $scope.datos.optionsSorteos = sorteosGlobal;
            
        //     $scope.datos.ventas = vGlobal;


        //     $scope.optionsMonedas = monedasGlobal;
        //     $scope.selectedMoneda = $scope.optionsMonedas[0];
        //     $scope.monedaAbreviatura = $scope.selectedMoneda.abreviatura;
        //     $scope.cbxMonedasChanged();

        //     // $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];
            
        //     $timeout(function() {
        //         // anything you want can go here and will safely be run on the next digest.
        //         //$('#multiselect').selectpicker('val', []);
        //         $('#multiselect').selectpicker("refresh");
        //         $('.selectpicker').selectpicker("refresh");
        //         //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
        //       })

            $scope.datos.bancas = null;
            $scope.datos.idMoneda = null;
            getVentas(true);
       
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




        

        

      
        $scope.seleccionarTicket = function(ticket){
            $scope.mostrarVentanaTicket = true;
            $scope.datos.selectedTicket = ticket;
        }

       
        $scope.buscar = function(){

           
            
            $scope.datos.idUsuario = idUsuarioGlobal;
            $scope.datos.layout = 'Principal';
            $scope.datos.idMoneda = $scope.selectedMoneda.id;

            if(Object.keys($scope.datos.selectedBancas).length > 0){
                $scope.datos.bancas = null;
                $scope.datos.selectedBancas.forEach(function(valor, indice, array){

                    if(array[indice].descripcion != "N/A"){
                        if($scope.datos.bancas == null){
                            $scope.datos.bancas = [];
                        }
                        $scope.datos.bancas.push(array[indice]);
                    }
    
                });
            }else{
                $scope.datos.bancas = null;
            }

            // console.log($scope.datos.bancas);
            // return;
            getVentas();
            

        }

        

        function getVentas(onCreate = false){
            $scope.datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT($scope.datos);
            $scope.cargando = true;
            $http.post(rutaGlobal+"/api/reportes/ventasporfecha", {'action':'sp_ventas_buscar', 'datos': jwt})
             .then(function(response){
                console.log('ventasPorFecha ',response);
                // if(response.data.errores == 0){

                    $scope.datos.ventas = response.data.ventas;
                    $scope.cargando = false;

                   if(onCreate)
                   {
                    $scope.datos.objetoBancas = response.data.bancas;
                    // $scope.datos.optionsBancas = helperService.copiarObjecto(bancasGlobal);
                    $scope.datos.optionsSorteos = response.data.sorteos;
                    
                    $scope.optionsMonedas = response.data.monedas;
                    $scope.selectedMoneda = $scope.optionsMonedas[0];
                    $scope.monedaAbreviatura = $scope.selectedMoneda.abreviatura;
                    $scope.cbxMonedasChanged();

                    // $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];
                    
                    $timeout(function() {
                        // anything you want can go here and will safely be run on the next digest.
                        //$('#multiselect').selectpicker('val', []);
                        $('#multiselect').selectpicker("refresh");
                        $('.selectpicker').selectpicker("refresh");
                        //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                    });
                   }

                // }else{
                //     alert(response.data.mensaje);
                //     return;
                // }

            }
            // ,
            // function(){
            //     $scope.cargando = false;
            //     alert("Error");
            // }
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

        $scope.cbxMonedasChanged = function(){
            $scope.datos.bancas = [];
            $scope.datos.selectedBancas = [];
            $scope.datos.optionsBancas = [];
            // $scope.datos.optionsBancas.unshift({'id' : 0, 'descripcion' : 'N/A'});
            $scope.datos.objetoBancas.forEach(function(valor, indice, array){
                if(array[indice].idMoneda == $scope.selectedMoneda.id)
                    $scope.datos.optionsBancas.push(array[indice]);
            });

            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                $('#multiselect').selectpicker("refresh");
                $('.selectpicker').selectpicker("refresh");
              })
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

      

       

        $scope.cambiarAccionBusqueda = function(accionBusqueda){
            $scope.datos.accionBusqueda = accionBusqueda;
        }

        $scope.greaterThan = function(prop, val){
            
            $scope.totalVentas = 0;
            $scope.totalPremios = 0;
            $scope.totalComisiones = 0;
            $scope.totalDescuentos = 0;
            $scope.totalNeto = 0;
            
            return function(item){
                
                var mostrar = false;;
                if(helperService.empty($scope.datos.fechaFiltro, 'string') == true){
                    mostrar = true;

                    if(mostrar == true){
                        $scope.totalVentas += Number(item.ventas);
                        $scope.totalPremios += Number(item.premios);
                        $scope.totalComisiones += Number(item.comisiones);
                        $scope.totalDescuentos += Number(item.descuentos);
                        $scope.totalNeto += Number(item.totalNeto);
                    }
                    console.log('greaterThan:', $scope.totalVentas);
                    return mostrar;
                }else{
                    mostrar = item['fecha'].indexOf($scope.datos.fechaFiltro.toUpperCase()) != -1;

                    if(mostrar == true){
                        $scope.totalVentas += Number(item.ventas);
                        $scope.totalPremios += Number(item.premios);
                        $scope.totalComisiones += Number(item.comisiones);
                        $scope.totalDescuentos += Number(item.descuentos);
                        $scope.totalNeto += Number(item.totalNeto);
                    }
                    return mostrar;
                }
                
            }
        }


        $scope.seleccionarTodasLasBancas = function(){
            $scope.datos.selectedBancas = [];
            if($('#labelSeleccionarTodas').hasClass('active2')){
                $('#labelSeleccionarTodas').removeClass('active2');
                $timeout(function() {
                    $('#multiselect').selectpicker('val', [])
                  });
            }
            else{
                $('#labelSeleccionarTodas').addClass('active2');
                var arregloIdBancas = [];
                $scope.datos.bancas = [];
                
                $scope.datos.optionsBancas.forEach(function(valor, indice, array){

                    if(array[indice].descripcion != "N/A"){
                        $scope.datos.bancas.push(array[indice]);
                        arregloIdBancas.push(array[indice].id);
                    }
                    
                  
                });


                console.log("seleccionarToas: ", arregloIdBancas);
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    ////Aqui se seleccionan las loterias
                    // $('#multiselect').selectpicker('val', arregloIdBancas);
                    // $('#multiselect').selectpicker("refresh");
                    $('#multiselect').selectpicker('val', arregloIdBancas)
                  })
            }
        }








     

      

        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }

       

       
    


       


    })
