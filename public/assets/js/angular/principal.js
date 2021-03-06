// var myApp = angular
//     .module("myModule", [])
myApp
    .controller("myController", function($scope, helperService, printerService, $http, $timeout, $window, $document){
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
            "print" : true,
            "idVenta":0,
            "compartido":0,
        "idUsuario": 0,
        "idBanca" : 0,
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
    'monto_jugado': 0,
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

    $scope.bancasChanged = function(klk){
        // console.log('bancasChanged:', $scope.datos.optionsBancas);
        $scope.datos.selectedBancas = $scope.datos.optionsBancas.find(x => x.id == klk.id);
        // console.log('Bancas changed:', $scope.datos.selectedBancas);
        $scope.datos.idBanca = $scope.datos.selectedBancas.id;


        var jwt = helperService.createJWT($scope.datos);
        $http.post(rutaGlobal+"/api/principal/indexPost", {'datos':jwt, 'action':'sp_jugadas_obtener_montoDisponible'})
             .then(function(response){
                 
                console.log("indexPostBanca: ", response.data.loterias);
                

                if(response.data.errores == 1){
                    alert('Error: ' + response.data.mensaje);
                    return;
                }
                
                
                $scope.datos.idVenta = response.data.idVenta;
                $scope.datos.optionsBancas = response.data.bancas;
                let idx = 0;
                
                if($scope.datos.optionsBancas.find(x => x.id == response.data.idBanca) != undefined)
                    idx = $scope.datos.optionsBancas.findIndex(x => x.id == response.data.idBanca);
                // $scope.datos.selectedBancas = $scope.datos.optionsBancas[idx];
                $scope.datos.selectedBancas = $scope.datos.optionsBancas[helperService.retornarIndexPorId($scope.datos.selectedBancas, $scope.datos.optionsBancas, response.data.idBanca)];
                $scope.datos.idBanca = response.data.idBanca;

                // console.log('idBanca:', response.data );

                $scope.datos.optionsVentas = (response.data.ventas != undefined) ? response.data.ventas : [{'id': 1, 'codigoBarra' : 'No hay ventas'}];
                $scope.datos.selectedVentas = $scope.datos.optionsVentas[0];
                 $scope.datos.optionsLoterias =response.data.loterias;
                //  console.log('select: ',$scope.datos.selectedVentas);
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
        $scope.inicializarDatos = function(response = null){

            // console.log('helper: ', helperService.empty($scope.datos.selectedBancas, 'string'));
            // console.log('helper: ', helperService.empty($scope.datos.selectedBancas));
            // console.log('helper1: ', $scope.datos.selectedBancas);
           
            connectSocket();
            $scope.datos.idVenta = 0;
            $scope.datos.total = 0;
            $scope.datos.subTotal = 0;
            $scope.datos.descuentoPorcentaje = 0;
            $scope.datos.descuentoMonto = 0;
            $scope.datos.loterias = [];
            
            $scope.datos.jugadas = [];
            

            $scope.datos.jugada = null;
            $scope.datos.monto_a_pagar = 0;
            $scope.datos.total_jugadas = 0;
            $scope.datos.total_directo = 0;
            $scope.datos.total_palet_tripleta = 0;

            
            
            if(response != null){
                //console.log('principal:',response.data.bancas)
                $scope.datos.optionsBancas = response.data.bancas;
                $scope.datos.idVenta = response.data.idVenta;
                let idx = 0;


                
                // if($scope.datos.optionsBancas.find(x => x.id == response.data.idBanca) != undefined)
                //     idx = $scope.datos.optionsBancas.findIndex(x => x.id == response.data.idBanca);
                // $scope.datos.selectedBancas = $scope.datos.optionsBancas[idx];
                $scope.datos.selectedBancas = $scope.datos.optionsBancas[helperService.retornarIndexPorId($scope.datos.selectedBancas, $scope.datos.optionsBancas)];                
                $scope.datos.idBanca = response.data.idBanca;

                $scope.seleccionarPrimeraLoteria();

                // $scope.datos.optionsVentas = (response.data.ventas != undefined) ? response.data.ventas : [{'id': 1, 'codigoBarra' : 'No hay ventas'}];
                // $scope.datos.selectedVentas = $scope.datos.optionsVentas[0];
                // $scope.datos.optionsLoterias =response.data.loterias;
                // $scope.datos.jugadasReporte.optionsLoterias = response.data.loterias;
                // $scope.datos.jugadasReporte.selectedLoteria = $scope.datos.jugadasReporte.optionsLoterias[0];

                // $scope.datos.estadisticas_ventas.total_jugadas = response.data.total_jugadas;
                // $scope.datos.estadisticas_ventas.total = response.data.total_ventas;
                
                
                $timeout(function() {
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                  })

                  return;
            }
            else{
                //Limpiamos algunos campos
                $scope.datos.jugadasReporte.jugadas = [];
                $scope.datos.jugadasReporte.optionsLoterias = [];
                $scope.datos.jugadasReporte.selectedLoteria = {};
                $scope.datos.optionsLoterias = [];
         
                var jwt = helperService.createJWT($scope.datos);
                $http.post(rutaGlobal+"/api/principal/indexPost", {'datos':jwt, 'action':'sp_jugadas_obtener_montoDisponible'})
             .then(function(response){
                
                // console.log(response.data);
                
                if(response.data.errores == 1){
                    alert('Error: ' + response.data.mensaje);
                    return;
                }
                
                
                $scope.datos.idVenta = response.data.idVenta;
                $scope.datos.optionsBancas = response.data.bancas;
                $scope.datos.jugadasReporte.optionsBancas = response.data.bancas;
                $scope.datos.jugadasReporte.optionsBancas.unshift({id: 0, descripcion: "Todas"});
                $scope.datos.jugadasReporte.selectedBanca = $scope.datos.jugadasReporte.optionsBancas[0];
                let idx = 0;
                
                if($scope.datos.optionsBancas.find(x => x.id == response.data.idBanca) != undefined)
                    idx = $scope.datos.optionsBancas.findIndex(x => x.id == response.data.idBanca);
                // $scope.datos.selectedBancas = $scope.datos.optionsBancas[idx];
                $scope.datos.selectedBancas = $scope.datos.optionsBancas[helperService.retornarIndexPorId($scope.datos.selectedBancas, $scope.datos.optionsBancas, response.data.idBanca)];
                $scope.datos.idBanca = response.data.idBanca;

                // console.log('idBanca:', response.data );

                $scope.datos.optionsVentas = (response.data.ventas != undefined) ? response.data.ventas : [{'id': 1, 'codigoBarra' : 'No hay ventas'}];
                $scope.datos.selectedVentas = $scope.datos.optionsVentas[0];
                 $scope.datos.optionsLoterias =response.data.loterias;
                //  console.log('select: ',$scope.datos.selectedVentas);
                //  console.log($scope.datos.optionsLoterias);
                $scope.datos.jugadasReporte.optionsLoterias = response.data.loteriasTodas;
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

                $scope.seleccionarPrimeraLoteria();

                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                  })
            });
            }
            
       
        }
        

        $scope.load = function(codigo_usuario, ROOT_PATH, idBanca = 1){
            
            ruta = ROOT_PATH;
            // console.log('ROOT_PATH: ', ruta);
            $scope.datos.idUsuario = idUsuario;
            $scope.datos.servidor = servidorGlobal;
            $scope.inicializarDatos();
            $scope.datos.idUsuario = idUsuario; //parseInt(codigo_usuario);
            window.setInterval(quitarLoteriasCerradas, 1000);

        //   $scope.datos.idUsuario = idUsuario; //parseInt(codigo_usuario);
          //$scope.datos.idBanca = idBanca; //parseInt(codigo_usuario);
          startTime();

          var a = new Hola("Jean", "Contreras");
        //   console.log('clase: ', ruta);
        }

        function eliminarUltimoCaracter(d){
            if (d == undefined)
                return;
                
            var da = '';
            for(var i=0; i < d.length - 1; i++){
                da += d[i];          
            }

            return da;
        }

        $scope.quitarOPonerClaseActive = function(){
            // $('#menu').
            if($('#nav-pills-menu').hasClass('active')){
                $('#nav-pills-jugar').addClass('active')
            }
        }

      
        $scope.tecladoClick = function(d){
            // console.log(d);

            if(d.toLowerCase() === 'enter'){
                if($scope.txtActive == 1){
                    //Android.showToast("Desde la web");
                    $scope.monto_disponible();
                    $scope.txtActive = 2;
                }else if($scope.txtActive == 2){
                    $scope.jugada_insertar(1, true);
                    $scope.txtActive = 1;
                }

                return;
            }

            if(d.toLowerCase() === 'backspace'){
                if($scope.txtActive == 1){
                    if($scope.datos.jugada != undefined){
                        $scope.datos.jugada = eliminarUltimoCaracter($scope.datos.jugada);
                    }
                        
                }else if($scope.txtActive == 2){
                    if($scope.datos.monto != undefined)
                        $scope.datos.monto = eliminarUltimoCaracter($scope.datos.monto);
                }

                
                return;
            }

            if(d.toLowerCase() !== 'enter'){
                if($scope.txtActive == 1){
                    if(Number(d) == d){
                        if($scope.datos.jugada == undefined || $scope.datos.jugada == null)
                            $scope.datos.jugada =d; 
                        else
                            $scope.datos.jugada +=d; 
                    }
                }else if($scope.txtActive == 2){
                    if(Number(d) == d){
                        if($scope.datos.monto == undefined || $scope.datos.monto == null)
                            $scope.datos.monto =d; 
                        else
                            $scope.datos.monto +=d; 
                    }
                }
            }
        }


        $scope.actualizar = function(){
          $scope.persona.idSector = $scope.selectedTipoSector.tipo_registro;
          $scope.persona.tipo_usuario = $scope.selectedTipoUsuario.tipo_registro;
          $scope.persona.tipo_cliente = $scope.selectedTipoCliente.tipo_registro;
          $scope.persona.sexo = $scope.selectedTipoCliente.id;

          

          $http.post("/prestamoGitHub/clases/consultaajax.php",{'data':$scope.persona, 'action':'persona_actualizar'})
                    .then(function(response){

                        if(response.data[0].errores == 0)
                            {
                              alert(response.data[0].mensaje);
                            }
                    })
        }

        function indexOfLoteriaYJugadaActual(elemento){
            return elemento.jugada == helperService.ordenarMenorAMayor($scope.datos.jugada) && elemento.idLoteria == $scope.datos.loterias[0].id;
        }

        $scope.monto_disponible = function(esBlur = false){
            
            
            
        
            // if(Number($scope.datos.jugada) != $scope.datos.jugada)
            //     {
            //         //$('#inputJugada').focus(); Este focus bloquea toda la pagina asi que dicidi comentarlo
            //         $scope.datos.monto = null;
            //         $scope.datos.jugada = null;
            //         $scope.jugada = null;

            //         return;
            //     }

           
            if($scope.datos.jugada != undefined){
                
                
                
                
         
                $scope.datos.jugada = ($scope.datos.jugada.length >= 2 &&  $scope.datos.jugada.length <= 6) ? $scope.datos.jugada : null;
                //jugada = parseInt(jugada);
                
                if($scope.datos.jugada != null){
                    if(Object.keys($scope.datos.loterias).length <= 0){
                        if(esBlur == false)
                            alert("Debes seleccionar una loteria");
                        return;
                    }
                    
                    if(Object.keys($scope.datos.loterias).length > 1 && helperService.esSuperpale($scope.datos.jugada) == false){
                        $scope.datos.montoExistente = 'X';
                        $('#inputMonto').focus();
                        $('#inputMonto').select();
                        return;
                    }

                    
                    
                    
                    $scope.datos.jugada = $scope.datos.jugada;
                    $scope.datos.idLoteria = $scope.datos.loterias[0].id;
                    $scope.datos.idBanca = $scope.datos.selectedBancas.id;
                    
                    $('#inputMonto').focus();
                    $('#inputMonto').select();

                    //Esto lo hago para que el valor de $scope.datos.jugada no cambie al momento de ordenarMenorAMayor los pale.
                    /* 
                        Solucione el problema de que duraba mucho al hacer la peticion al servidor para obtener el monto 
                        disponible, gracias a las 4 lineas de codigo de abajo.

                        el tiempo de renderizado de angularjs disminuyo por lo tanto haciendo que la peticion al servidor se haga mas rapida
                         ya que la variable $scope.datos.montoDisponible tiene pocos datos a diferencia de la variable $scope.datos que tiene muchos
                         datos por renderizar haciendo un trabajo mas pesado para angularjs y tambien para el servidor

                         asi pude darme cuenta que trabajando los datos con variables diferentes angularjs funciona mejor
                    */
                    $scope.datos.montoDisponible = {};
                    $scope.datos.montoDisponible.jugada = helperService.ordenarMenorAMayor($scope.datos.jugada);
                    $scope.datos.montoDisponible.idLoteria = $scope.datos.idLoteria;
                    $scope.datos.montoDisponible.idBanca = $scope.datos.idBanca;
                    $scope.datos.montoDisponible.servidor = $scope.datos.servidor;

                    if(helperService.esSuperpale($scope.datos.jugada)){
                        if($scope.datos.loterias.length != 2){
                            alert("Debes seleccionar dos loterias");
                            return;
                        }

                        //Ordeno de menor a mayor las variables idLoteria y idLoteriaSuperpale
                        //La variable idLoteria tendra el valor menor y la variable idLoteriaSuperpale el valor mayor
                        if($scope.datos.loterias[0].id > $scope.datos.loterias[1].id){
                            $scope.datos.montoDisponible.idLoteria = $scope.datos.loterias[1].id;
                            $scope.datos.montoDisponible.idLoteriaSuperpale = $scope.datos.loterias[0].id;
                        }else{
                            $scope.datos.montoDisponible.idLoteriaSuperpale = $scope.datos.loterias[1].id;
                        }
                    }else{
                        $scope.datos.montoDisponible.idLoteriaSuperpale = null;
                    }
                   
                    // $http.post(rutaGlobal+"/api/principal/montodisponible",{'datos':$scope.datos, 'action':'sp_jugadas_obtener_montoDisponible'})
                    //   .then(function(response){
                    //         console.log(response);
                    //      $scope.datos.montoExistente = response.data.monto;
                    //       $('#inputMonto').focus();
                    //       return;
                    //   })

                    var jwt = helperService.createJWT($scope.datos.montoDisponible);
                    $http.post(rutaGlobal+"/api/principal/montodisponible",{'datos':jwt, 'action':'sp_jugadas_obtener_montoDisponible'})
                    .then(function(response){
                        //   console.log(response);
                        
                        var indexJugada = $scope.datos.jugadas.findIndex(indexOfLoteriaYJugadaActual);
                        if(indexJugada != -1){
                            var montoDisponible = helperService.redondear(response.data.monto) - $scope.datos.jugadas[indexJugada].monto;
                            if(montoDisponible < 0)
                                $scope.datos.montoExistente = 0;
                            else
                                $scope.datos.montoExistente = montoDisponible;
                        }else{
                            $scope.datos.montoExistente = response.data.monto;
                        }
                        $('#inputMonto').focus();
                        
                    })

                }
            }

            $scope.datos.montoExistente = 0;
          }
  
          
          $scope.jugadaCorrecta = function(){
              if($scope.datos.jugada == undefined || $scope.datos.jugada == null)
                return false;
              var jugada = '';
              for (let index = 0; index < $scope.datos.jugada.length; index++) {
                  if($scope.datos.jugada[index] == '+' || $scope.datos.jugada[index] == '-' || $scope.datos.jugada[index] == 's'){
                    //LOS CARACTERES ESPECIALES QUE ESTAN ARRIBA DEBEN ESTAR EN LA ULTIMA POSICION DE LA JUGADA, DE LO CONTRARIO ES UNA JUGADA INCORRECTA
                    if((index + 1) != $scope.datos.jugada.length)
                        return false;
                  }
                  if($scope.datos.jugada[index] != '+' && $scope.datos.jugada[index] != '-' && $scope.datos.jugada[index] != 's')
                   jugada += $scope.datos.jugada[index];
                  
              }

            console.log("jugadaCorrecta: ", Number(jugada) == jugada);
            console.log("jugadaCorrecta Number(jugada): ", Number(jugada));
              if(Number(jugada) == jugada)
                  return true;
              else
                return false;
          }

          $scope.esPick3Pick4UOtro = function(jugada){
            if(jugada.length == 3){
                return 'pick3Straight'
            }
            else if(jugada.length == 4 && jugada.indexOf('+') != -1)
                return 'pick3Box'
            else if(jugada.length == 5 && jugada.indexOf('+') != -1)
                return 'pick4Box'
            else if(jugada.length == 5 && jugada.indexOf('-') != -1)
                return 'pick4Straight'
            else
                return 'otro';
          }

          $scope.agregarGuionPorSorteo = function(jugada, sorteo){
            var cadena = jugada;
    
             if(sorteo == "Pick 3 Box"){
                cadena +='+';
            }
            else if(sorteo == "Pick 4 Straight"){
                cadena +='-';
            }
            else if(sorteo == "Pick 4 Box"){
                cadena +='+';
            }
    
    
    
    
            return cadena;
        }
  
          $scope.jugada_insertar = function(evento, sinevento = false){
           
            $scope.jugadaCorrecta();
           
                if(sinevento){
                    evento = {};
                    evento.keyCode = 13;
                }

                if($scope.datos.jugada != null && evento.keyCode == 13){
                    if(helperService.esSuperpale($scope.datos.jugada)){
                        if($scope.datos.loterias.length != 2){
                            alert("Debe seleccionar dos loterias para super pale");
                            return;
                        }

                        if($scope.datos.loterias[0].id > $scope.datos.loterias[1].id)
                            jugadaAdd($scope.datos.loterias[1], $scope.datos.loterias[0]);
                        else
                            jugadaAdd($scope.datos.loterias[0], $scope.datos.loterias[1]);
                    }else{
                        $scope.datos.loterias.forEach(function(valor, indice, array){
                            jugadaAdd(array[indice]);
                         }); //END FOREACH
                    }

                //$('#inputJugada').focus();
                // $scope.datos.monto = null;
                
                    $scope.datos.jugada = null;
                    $scope.jugada = null;
                    $scope.calcularTotal();
                } // END IF PRINCIPAL
            
            
        }

        var jugadaAdd = function(loteria, loteriaSuperpale = null){
            
            if($scope.datos.monto > 0){
                        
                //Verificamos que se haya seleccionado una loteria
                if(Object.keys($scope.datos.loterias).length > 0){
                    //Verificamos que la jugada sea numerica
                    
                    if($scope.jugadaCorrecta() == false){
                        alert("Jugada incorrecta");
                        return;
                    }
                    //if(Number($scope.datos.jugada) == $scope.datos.jugada){
                        /*Verificamos que el monto sea menor que el montoExistentte cuando las loterias seleccionadas sea una
                        o verificamos que las loterias seleccionadas sean mas de una y el monto existente sea igual a X,
                        Si se cumple unas de estas dos condiciones entonces se insertara o actualizara la jugada, de lo contrario
                        Se le indicara al usuario que no hay existencia suficiente */
                        
                         if(Number($scope.datos.montoExistente) < $scope.datos.monto && Object.keys($scope.datos.loterias).length == 1){
                            //  console.log('existente: ', $scope.datos.montoExistente, ' montojugar: ', $scope.datos.monto);
                            alert("No hay existencia suficiente para ese monto");
                            $('#inputJugada').focus();
                            $scope.monto = 0;
                            return;
                        }
                        else{
                            if(helperService.esSuperpale($scope.datos.jugada)){
                                $scope.datos.jugada =helperService.ordenarMenorAMayor($scope.datos.jugada);
                                if($scope.datos.jugadas.find(x => (x.jugada == $scope.datos.jugada && x.idLoteria == loteria.id && x.idLoteriaSuperpale == loteriaSuperpale.id)) != undefined){
                                    
                                    let idx = $scope.datos.jugadas.findIndex(x => (x.jugada == $scope.datos.jugada && x.idLoteria == loteria.id && x.idLoteriaSuperpale == loteriaSuperpale.id));
                                    $scope.datos.jugadas[idx].monto = parseFloat($scope.datos.jugadas[idx].monto)+ parseFloat($scope.datos.monto);
                                    $('#inputJugada').focus();
                                    //$scope.monto = 0;
                                }
                                else{
                                //    console.log('insertar, foreach: ', loteria);
                                    $scope.datos.jugadas.push(
                                        {'jugada':$scope.datos.jugada, 
                                        'monto':$scope.datos.monto, 
                                        'tam': $scope.datos.jugada.length, 
                                        'idLoteria': loteria.id,
                                        'descripcion':loteria.descripcion, 
                                        'abreviatura' : loteria.abreviatura,
                                        'idLoteriaSuperpale': loteriaSuperpale.id,
                                        'descripcionSuperpale':loteriaSuperpale.descripcion, 
                                        'abreviaturaSuperpale' : loteriaSuperpale.abreviatura,
                                    });
                                    $('#inputJugada').focus();
                                    //$scope.datos.monto = 0;
                                }
                            }else{
                                $scope.datos.jugada =helperService.ordenarMenorAMayor($scope.datos.jugada);
                                if($scope.datos.jugadas.find(x => (x.jugada == $scope.datos.jugada && x.idLoteria == loteria.id)) != undefined){
                                    
                                    let idx = $scope.datos.jugadas.findIndex(x => (x.jugada == $scope.datos.jugada && x.idLoteria == loteria.id));
                                    $scope.datos.jugadas[idx].monto = parseFloat($scope.datos.jugadas[idx].monto)+ parseFloat($scope.datos.monto);
                                    $('#inputJugada').focus();
                                    //$scope.monto = 0;
                                }
                                else{
                                //    console.log('insertar, foreach: ', loteria);
                                    $scope.datos.jugadas.push(
                                        {'jugada':$scope.datos.jugada, 
                                        'monto':$scope.datos.monto, 
                                        'tam': $scope.datos.jugada.length, 
                                        'idLoteria': loteria.id,
                                        'descripcion':loteria.descripcion, 
                                        'abreviatura' : loteria.abreviatura
                                    });
                                    $('#inputJugada').focus();
                                    //$scope.datos.monto = 0;
                                }
                            }
                            
                        }
                        // if(( Object.keys($scope.datos.loterias).length == 1 && $scope.datos.montoExistente > $scope.datos.monto) || (Object.keys($scope.datos.loterias).length > 1 && $scope.datos.montoExistente == 'X')){
                            
                        // }
                        // else{
                        //     alert("No hay existencia suficiente para ese monto");
                        // }
                    // }
                    // else{
                    //     alert("La jugada debe ser numerica");
                    // }
                }
                else{
                    alert("Debes seleccionar una loteria");
                }
            }
           

        }

        $scope.esSuperpale = function(jugada){
            return helperService.esSuperpale(jugada);
        }

        $scope.getAbreviaturaSuperpale = function(jugada){
            var abreviatura = "";
            if(helperService.esSuperpale(jugada.jugada))
                abreviatura += jugada.abreviatura.substring(0, 2) + "/" + jugada.abreviaturaSuperpale.substring(0, 2);
            else
                abreviatura += jugada.abreviatura;
            
            return abreviatura;
        }

        $scope.jugada_eliminar = function(jugada, idLoteria){
            if($scope.datos.jugadas.find(x => x.jugada == jugada && x.idLoteria == idLoteria) != undefined){
    
                let idx = $scope.datos.jugadas.findIndex(x => x.jugada == jugada && x.idLoteria == idLoteria);
                $scope.datos.jugadas.splice(idx,1);
                $scope.calcularTotal();
            }
        }

        $scope.prueba = function(){
            label = document.getElementById('divInputJugada');
            //if(label.classList.contains("is-focused") == true && $scope.myStyle.length)
            $scope.myStyle = {'font-size': '20px'};
        }

        $scope.calcularTotal = function(){
            var monto_jugado = 0, monto_a_pagar = 0, total_palet_tripleta = 0, total_directo = 0, total_pale = 0, total_tripleta = 0, total_pick3 = 0, total_pick4 = 0, jugdada_total_palet = 0, jugada_total_directo = 0, jugada_total_tripleta = 0, jugada_total_superPale = 0, jugada_total_pick4 = 0, jugada_monto_total = 0;
             $scope.datos.jugadas.forEach(function(valor, indice, array){

                if(array[indice].tam == 2) total_directo += parseFloat(array[indice].monto);
                if(array[indice].tam == 4) total_pale += parseFloat(array[indice].monto);
                if(array[indice].tam == 6) total_tripleta += parseFloat(array[indice].monto);
                if($scope.esPick3Pick4UOtro(array[indice].jugada).indexOf('pick3') != -1) total_pick3 += parseFloat(array[indice].monto);
                if($scope.esPick3Pick4UOtro(array[indice].jugada).indexOf('pick4') != -1) total_pick4 += parseFloat(array[indice].monto);
                if(array[indice].tam == 4 || array[indice].tam == 6) total_palet_tripleta += parseFloat(array[indice].monto);

                monto_a_pagar +=  parseFloat(array[indice].monto);
                monto_jugado +=  parseFloat(array[indice].monto);
             });


            //  $scope.datos.monto_a_pagar = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * monto_a_pagar : monto_a_pagar;
            //  $scope.datos.total_directo = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_directo : total_directo;
            //  $scope.datos.total_pale = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_pale : total_pale;
            //  $scope.datos.total_tripleta = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_tripleta : total_tripleta;
            //  $scope.datos.total_palet_tripleta = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * total_palet_tripleta : total_palet_tripleta;
            //  $scope.datos.total_jugadas = (Object.keys($scope.datos.loterias).length > 1) ? Object.keys($scope.datos.loterias).length * Object.keys($scope.datos.jugadas).length : Object.keys($scope.datos.jugadas).length;
            //  $scope.datos.descuentoMonto = ($scope.datos.hayDescuento) ? parseInt(parseFloat($scope.datos.monto_a_pagar) / parseFloat($scope.datos.caracteristicasGenerales[0].cantidadAplicar)) * parseFloat($scope.datos.caracteristicasGenerales[0].descuentoValor) : 0;
             
             
            //  $scope.datos.monto_a_pagar =  monto_a_pagar;
             $scope.datos.monto_jugado =  monto_jugado;
             $scope.datos.total_directo =  total_directo;
             $scope.datos.total_pale =  total_pale;
             $scope.datos.total_tripleta = total_tripleta;
             $scope.datos.total_pick3 = total_pick3;
             $scope.datos.total_pick4 = total_pick4;
             $scope.datos.total_palet_tripleta =  total_palet_tripleta;
             $scope.datos.total_jugadas =  Object.keys($scope.datos.jugadas).length;
             $scope.datos.descuentoMonto = ($scope.datos.hayDescuento) ? parseInt(parseFloat($scope.datos.monto_jugado) / parseFloat($scope.datos.selectedBancas.deCada)) * parseFloat($scope.datos.selectedBancas.descontar)  : 0;
             $scope.datos.monto_a_pagar = Number(monto_a_pagar) - Number($scope.datos.descuentoMonto);

             //Calcular total jugdasReporte
             $scope.datos.jugadasReporte.jugadas.forEach(function(valor, indice, array){

                if(array[indice].sorteo == "Directo") jugada_total_directo += parseFloat(array[indice].monto);
                if(array[indice].sorteo == "Pale") jugdada_total_palet += parseFloat(array[indice].monto);
                if(array[indice].sorteo == "Tripleta") jugada_total_tripleta += parseFloat(array[indice].monto);
                if(array[indice].sorteo == "Super pale") jugada_total_superPale += parseFloat(array[indice].monto);
                if(array[indice].sorteo.indexOf("Pick 3") != -1) jugada_total_pick3 += parseFloat(array[indice].monto);
                if(array[indice].sorteo.indexOf("Pick 4") != -1) jugada_total_pick4 += parseFloat(array[indice].monto);

                jugada_monto_total +=  parseFloat(array[indice].monto);
             });

             $scope.datos.jugadasReporte.total_directo = jugada_total_directo;
             $scope.datos.jugadasReporte.total_palet = jugdada_total_palet;
             $scope.datos.jugadasReporte.total_tripleta = jugada_total_tripleta;
             $scope.datos.jugadasReporte.total_superPale = jugada_total_superPale;
             $scope.datos.jugadasReporte.total_pick4 = jugada_total_pick4;
             $scope.datos.jugadasReporte.monto_total = jugada_monto_total;
        
        }

        $scope.inputJugadaKeyup = function(evento){
            // console.log('inputJugadaKeyup: ', evento.key);

            //si es un asterisco pues entonces lo quito
            if(evento.keyCode == 42){
                if(helperService.empty($scope.datos.jugada, "string") == false){
                    if($scope.datos.jugada.indexOf("*") != -1){
                        $scope.datos.jugada.replace("*", "");
                    }
                }
                return;
            }

            if($scope.datos.jugada != undefined){
                if(evento.key == '+'){
                    if($scope.datos.jugada.length != 4 && $scope.datos.jugada.length != 5){
                        $scope.datos.jugada = $scope.datos.jugada.substring(0, $scope.datos.jugada.length - 1);
                        return;
                    }
                }
                if(evento.key == '-'){
                    if($scope.datos.jugada.length != 5){
                        $scope.datos.jugada = $scope.datos.jugada.substring(0, $scope.datos.jugada.length - 1);
                        return;
                    }
                }
            }

            
            
            if(($scope.datos.jugada != null && evento.keyCode == 13) || ($scope.datos.jugada != null && evento.key == '+') || ($scope.datos.jugada != null && evento.key == '-') || ($scope.datos.jugada != null && evento.key == 's')){
                // console.log('inputJugadaKeyup: ', $scope.datos.jugada.length);
                $scope.monto_disponible();
            }
        }
        // $scope.inputJugadaKeypress = function(evento){
        //     // if(evento.key == '-'){
        //     //     if($scope.datos.jugada == undefined)
        //     //     if($scope.datos.jugada.length == 3)
        //     // }
        //     if($scope.datos.jugada != undefined)
                // console.log('inputJugadaKeypress: ', $scope.datos.jugada.length);
        //     //if($scope.datos.jugada != null && evento.keyCode == 13) $scope.monto_disponible();
        // }


        $scope.agregar_guion = function(cadena){
            if(cadena.length == 4 && $scope.esPick3Pick4UOtro(cadena) == "otro"){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3];
            }
            else if(cadena.length == 6){
                cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3] + '-' + cadena[4] + cadena[5];
            }
            else if($scope.esPick3Pick4UOtro(cadena) == "pick3Box"){
                cadena = cadena.substring(0, cadena.length - 1)
            }
            else if($scope.esPick3Pick4UOtro(cadena) == "pick4Box"){
                cadena = cadena.substring(0, cadena.length - 1)
            }
            else if($scope.esPick3Pick4UOtro(cadena) == "pick4Straight"){
                cadena = cadena.substring(0, cadena.length - 1)
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


             return loterias;
        }



        $scope.venta_guardar = function(e){
            //e.preventDefault();
          


                if(Object.keys($scope.datos.jugadas).length ==0)
                {
                    /*La variable map es la variable que uso en la directiva onShitfPress,
                        anteriormente esta variable era una variable local en la directiva onShiftPress
                        pero tube que convertirla en una variable global para asi poder reestablecer sus valores a 'false' 
                        ya que al momento de llamar la funcion alert() esta detiene la ejecucion de la pagina y esto provocaba
                        que la variable map se quedara con todos sus valores en 'true' y perdia su funcionamiento y al momento
                        de presionar la tecla Shift la directiva se activaba creyendo que se habian presionado las 2 teclas cuando
                        solamente habia sido una, asi que por esta razon tube que reestablecer sus valores a 'false' */
                    map = {9: false, 16: false};
                    // alert('No hay jugadas realizadas');
                    demo.showSwal('auto-close', "No hay jugadas", "Debe registrar jugadas para crear ticket");
                    return;
                }
                // if(Object.keys($scope.datos.loterias).length ==0)
                // {
                //     map = {9: false, 16: false};
                //     alert('Debe seleccionar una loteria');
                //     return;
                // }

                $scope.datos.total = $scope.datos.monto_jugado;
                $scope.datos.idBanca = $scope.datos.selectedBancas.id;

            
                
                
                $scope.datos.subTotal = 0;
                if($scope.datos.print){
                    if(helperService.empty(localStorage.getItem("impresora"), 'string') == true){
                        window.abrirModalImpresora(true);
                        return;
                    }
                }
                
                var jwt = helperService.createJWT($scope.datos);
                $http.post(rutaGlobal+"/api/principal/guardar",{'datos':jwt, 'action':'sp_ventas_actualiza'})
                .then(function(response){

                    console.log('Principal.js venta_guardar : ', response.data);
 
                    if(response.data.errores == 0)
                        {
                            // console.log(response);
                          //alert(response.data.mensaje);
                          $scope.inicializarDatos(response);
                        //   $scope.imprimirTicket(response.data.venta, (e == 1) ? true : false);
                        //   $scope.datos.enviarSMS.codigoBarra = response.data.venta.codigoBarra;
                        //   $scope.print();
                          //$scope.abrirVentanaSms();
                          if($scope.datos.print)
                            printerService.printTicket(response.data.venta);
                        }
                    else{
                        alert(response.data.mensaje);
                        return;
                    }
                   
                })








            
            // if(e.keyCode == 107){
            //     if(Object.keys($scope.datos.jugadas).length ==0)
            //     {
            //         alert('No hay jugadas realizadas');
            //         return;
            //     }
            //     if(Object.keys($scope.datos.loterias).length ==0)
            //     {
            //         alert('Debe seleccionar una loteria');
            //         return;
            //     }

            //     $scope.datos.total = $scope.datos.monto_a_pagar;
                
                // console.log('venta guardar: ',$scope.datos );

            //     $http.post($scope.ROOT_PATH +"clases/consultaajax.php",{'datos':$scope.datos, 'action':'sp_ventas_actualiza'})
            //     .then(function(response){
                    // console.log(response);
            //         if(response.data[0].errores == 0)
            //             {
            //               alert(response.data[0].mensaje);
            //               $scope.inicializarDatos();
            //             }
            //         else{
            //             alert(response.data[0].mensaje);
            //         }
                   
            //     })
            // }
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

        $scope.recargar = function(){
            // location.reload(); //triggering unload (e.g. reloading the page) makes the print dialog appear
            //     window.stop();
            // if (window.stop) {
            //     location.reload(); //triggering unload (e.g. reloading the page) makes the print dialog appear
            //     window.stop(); //immediately stop reloading
            // }
            // console.log('recargar: ', window.stop);
        }

        $scope.imprimirTicket = function(ticket, es_movil){
            printerService.printTicket(ticket, CMD.TICKET_COPIA);
        }

        $scope.buscar = function(){

            // console.log('monitoreo before addClass',$scope.datos.monitoreo);
            $('#fechaBusqueda').addClass('is-filled');
            
            // console.log('monitoreo after addClass',$scope.datos.monitoreo);
            
            $scope.datos.monitoreo.idUsuario = $scope.datos.idUsuario;
            $scope.datos.monitoreo.layout = 'Principal';
            $scope.datos.monitoreo.idBanca = $scope.datos.selectedBancas.id;
            $scope.datos.monitoreo.servidor = $scope.datos.servidor;
            
          var jwt = helperService.createJWT($scope.datos.monitoreo);
          $http.post(rutaGlobal+"/api/monitoreo/tickets", {'action':'sp_ventas_buscar', 'datos': jwt})
             .then(function(response){
                // console.log('monitoreo ',response);
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

            // console.log('validarHora: ', loteria, new Date(fecha_actual_horaCierre), ' hora: ', new Date(), ' comparacion: ', (new Date(fecha_actual_horaCierre) >= new Date()));

            // console.log('Validar hora: ',new Date() >= fecha_actual_horaCierre, ' fechaCierre: ', fecha_actual_horaCierre, ' fechaActual: ', new Date());
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

        $scope.seleccionarPrimeraLoteria = function(){
            
            if($scope.datos.optionsLoterias.length == 0){
                return;
            }

            $scope.datos.loterias.push($scope.datos.optionsLoterias[0]);
            var arregloNombreLoteria = [];
            arregloNombreLoteria.push($scope.datos.optionsLoterias[0].id);

            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                ////Aqui se seleccionan las loterias
                $('#multiselect').selectpicker('val', arregloNombreLoteria);
                $('#multiselect').selectpicker("refresh");
              })
        }


        $scope.duplicarViejo = function(){

            if($scope.datos.duplicar.numeroticket == null || $scope.datos.duplicar.numeroticket == undefined)
            {
                alert('El numero de ticket no debe estar vacio');
                return;
            }

            if(Number($scope.datos.duplicar.numeroticket) != $scope.datos.duplicar.numeroticket)
            {
                alert('El numero de ticket debe ser numerico');
                return;
            }

            $scope.datos.duplicar.codigoBarra = $scope.datos.duplicar.numeroticket;
            $scope.datos.duplicar.servidor = $scope.datos.servidor;
            var jwt = helperService.createJWT($scope.datos.duplicar);
            $http.post(rutaGlobal+"/api/principal/duplicar", {'action':'sp_ventas_obtenerpor_numeroticket', 'datos': jwt})
             .then(function(response){
                // console.log(response.data);
               
                if(response.data.errores == 1){
                    alert(response.data.mensaje);
                    return;
                }

                
                $scope.datos.idVenta = 0;
                $scope.datos.loterias = [];
                $scope.datos.jugadas = [];
                $scope.datos.duplicar.numeroticket = null;
               
                
                var jsonLoterias = response.data.loterias;
                var arregloNombreLoteria = [];
                jsonLoterias.forEach(function(valor, indice, array){

                    // console.log('duplicar, optionloteries: ', $scope.datos.optionsLoterias);
                    
                    if($scope.datos.optionsLoterias.find(x => x.id == array[indice].id) != undefined){
                        let idx = $scope.datos.optionsLoterias.findIndex(x => x.id == array[indice].id);
                        $scope.datos.loterias.push($scope.datos.optionsLoterias[idx]);
                        arregloNombreLoteria.push($scope.datos.optionsLoterias[idx].id);
                    }{
                        // console.log('duplicar, no existe: ');
                    }

                });

                
                /*Esta linea de codigo 
                    $('#multiselect').selectpicker('val', arregloNombreLoteria);
                 da un error $digest, asi que para evitar el error $digest lo que hice
                 fue meter la linea de codigo dentro de la funcion $timeout de angular */
                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    ////Aqui se seleccionan las loterias
                    $('#multiselect').selectpicker('val', arregloNombreLoteria);
                    $('#multiselect').selectpicker("refresh");
                  })

            
                if(response.data.jugadas != undefined){
                    var jsonJugadas = response.data.jugadas;
                    jsonJugadas.forEach(function(valor, indice, array){
                        //Si la jugada ya existe dentro de la variable $scope.datos.jugadas entonces vamos continuar con la siguiente jugada
                        if($scope.datos.jugadas.find(x => x.jugada == array[indice].jugada && x.idLoteria == array[indice].idLoteria) != undefined)
                        {

                        }else{
                            var idx = $scope.datos.optionsLoterias.findIndex(x => x.id == array[indice].idLoteria);
                            $scope.datos.jugadas.push({
                                    'jugada':array[indice].jugada, 'monto':array[indice].monto, 
                                    'tam': array[indice].jugada.length, 
                                    'idLoteria': array[indice].idLoteria,
                                    'descripcion':$scope.datos.optionsLoterias[idx].descripcion, 
                                    'abreviatura' : $scope.datos.optionsLoterias[idx].abreviatura
                                });
                            //$scope.datos.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                        }

                        //$scope.datos.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                    });
                }
                $scope.calcularTotal();
                $('#modal-duplicar').modal('hide');
               

            });
        }



        $scope.duplicar = function(){

            if($scope.datos.duplicar.numeroticket == null || $scope.datos.duplicar.numeroticket == undefined)
            {
                alert('El numero de ticket no debe estar vacio');
                return;
            }

            if(Number($scope.datos.duplicar.numeroticket) != $scope.datos.duplicar.numeroticket)
            {
                alert('El numero de ticket debe ser numerico');
                return;
            }

            $scope.datos.duplicar.codigoBarra = $scope.datos.duplicar.numeroticket;
            $scope.datos.duplicar.servidor = $scope.datos.servidor;
            var jwt = helperService.createJWT($scope.datos.duplicar);
            $http.post(rutaGlobal+"/api/principal/duplicar", {'action':'sp_ventas_obtenerpor_numeroticket', 'datos': jwt})
             .then(function(response){

            //    console.log(response.data);
               
                if(response.data.errores == 0){
                    $('#modal-duplicar').modal('toggle');
                    $('#modal-duplicar-avanzado').modal('toggle');


                    $scope.datos.duplicar.numeroticket = null;
                    $scope.datos.duplicar.optionsLoterias = helperService.copiarObjecto($scope.datos.optionsLoterias);
                    $scope.datos.duplicar.loterias = response.data.loterias;
                    $scope.datos.duplicar.jugadas = response.data.jugadas;
                    
                    $scope.datos.duplicar.optionsLoterias.unshift({'id' : 0, 'descripcion' : "No copiar"});
                    $scope.datos.duplicar.optionsLoterias.unshift({'id' : -1, 'descripcion' : "No mover"});
                    $scope.datos.duplicar.selectedLoteria = $scope.datos.duplicar.optionsLoterias[0];
                    
    
                    
                    $scope.datos.duplicar.loterias.forEach(function(valor, indice, array){
                        array[indice].selectedComboLoteria = $scope.datos.duplicar.optionsLoterias[0];
                    });
    
                    

                    $timeout(function() {
                        // anything you want can go here and will safely be run on the next digest.
                        ////Aqui se seleccionan las loterias
                        
                        $('.selectpicker').selectpicker("refresh");
                      })
                    
                }

                
               

            
                
               

            });
        }

        $scope.duplicarInsertar = function(){
            $scope.datos.duplicar.loterias.forEach(function(valor, indice, array){
                $scope.datos.duplicar.jugadas.forEach(function(valorJugada, indiceJugada, arrayJugada){
                    if(array[indice].id == arrayJugada[indiceJugada].idLoteria){
                        if(array[indice].selectedComboLoteria.descripcion != "No copiar")
                       {
                        arrayJugada[indiceJugada].jugada = $scope.agregarGuionPorSorteo(arrayJugada[indiceJugada].jugada, arrayJugada[indiceJugada].sorteo);
                           if(array[indice].selectedComboLoteria.descripcion == "No mover")
                            {
                                if($scope.datos.jugadas.find(x => (x.jugada == arrayJugada[indiceJugada].jugada && x.idLoteria == array[indice].id)) != undefined){        
                                    if (confirm('La jugada ' + arrayJugada[indiceJugada].jugada +' existe en la loteria ' + array[indice].descripcion + ', Desea agregar?')) {
                                        let idx = $scope.datos.jugadas.findIndex(x => (x.jugada == arrayJugada[indiceJugada].jugada && x.idLoteria == array[indice].id));
                                        $scope.datos.jugadas[idx].monto = parseFloat($scope.datos.jugadas[idx].monto)+ parseFloat(arrayJugada[indiceJugada].monto);
                                    }
                                    
                                }
                                else{
                                    $scope.datos.jugadas.push({'jugada':arrayJugada[indiceJugada].jugada, 'monto':arrayJugada[indiceJugada].monto, 
                                                                'tam': arrayJugada[indiceJugada].jugada.length, 'idLoteria': array[indice].id,
                                                                'descripcion':array[indice].descripcion, 'abreviatura' : array[indice].abreviatura});
                                }
                            }else
                            {
                                if($scope.datos.jugadas.find(x => (x.jugada == arrayJugada[indiceJugada].jugada && x.idLoteria == array[indice].selectedComboLoteria.id)) != undefined){        
                                    if (confirm('La jugada ' + arrayJugada[indiceJugada].jugada +' existe en la loteria ' + array[indice].selectedComboLoteria.descripcion + ', Desea agregar?')) {
                                        let idx = $scope.datos.jugadas.findIndex(x => (x.jugada == arrayJugada[indiceJugada].jugada && x.idLoteria == array[indice].selectedComboLoteria.id));
                                        $scope.datos.jugadas[idx].monto = parseFloat($scope.datos.jugadas[idx].monto)+ parseFloat(arrayJugada[indiceJugada].monto);
                                    }
                                }
                                else{
                                    $scope.datos.jugadas.push({'jugada':arrayJugada[indiceJugada].jugada, 'monto':arrayJugada[indiceJugada].monto, 
                                                                'tam': arrayJugada[indiceJugada].jugada.length, 'idLoteria': array[indice].selectedComboLoteria.id,
                                                                'descripcion':array[indice].selectedComboLoteria.descripcion, 'abreviatura' : array[indice].selectedComboLoteria.abreviatura});
                                }
                            }
                       }
                    }
                });
            });

            $scope.datos.duplicar.numeroticket = null;
            $scope.datos.duplicar.optionsLoterias = [];
            $scope.datos.duplicar.loterias = [];
            $scope.datos.duplicar.jugadas = [];
            $scope.calcularTotal();
            $('#modal-duplicar-avanzado').modal('toggle');
            // console.log('duplicarInsertar:', $scope.datos.jugadas);
        }

        $scope.buscar_jugadas = function(){

            $('#fechaBusqueda').addClass('is-filled');

            

            $scope.datos.jugadasReporte.idLoteria = $scope.datos.jugadasReporte.selectedLoteria.id;
            $scope.datos.jugadasReporte.bancas = [];
            
            
            if($scope.datos.jugadasReporte.selectedBanca.id == 0){
                $scope.datos.jugadasReporte.optionsBancas.forEach(function(item){
                    if(item.id != 0)
                    $scope.datos.jugadasReporte.bancas.push(item);
                })
                // $scope.datos.jugadasReporte.bancas = $scope.datos.jugadasReporte.optionsBancas;
            }
                
            else
                $scope.datos.jugadasReporte.bancas.push($scope.datos.jugadasReporte.selectedBanca);
            
                console.log("reportes/jugadas bancas: ", $scope.datos.jugadasReporte.bancas);
                $scope.datos.jugadasReporte.servidor = $scope.datos.servidor;
            var jwt = helperService.createJWT($scope.datos.jugadasReporte);
          $http.post(rutaGlobal+"/api/reportes/jugadas", {'action':'sp_jugadas_buscar', 'datos': jwt})
             .then(function(response){

                console.log("reportes/jugadas: ", response);                
                $scope.datos.jugadasReporte.jugadas = [];
                $scope.datos.jugadasReporte.total_directo = 0;
                $scope.datos.jugadasReporte.total_palet = 0;
                $scope.datos.jugadasReporte.total_tripleta = 0;
                $scope.datos.jugadasReporte.monto_total = 0;

                //$scope.datos.jugadasReporte.jugadas = response.data;
                if(response.data != undefined){
                    var jsonJugadas = response.data.jugadas;
                    jsonJugadas.forEach(function(valor, indice, array){
                        $scope.datos.jugadasReporte.jugadas.push(array[indice]);
                    });
                }

                $scope.calcularTotal();

            });

        }





        $scope.pagar = function(){

            if($scope.datos.pagar.codigoBarra == null || $scope.datos.pagar.codigoBarra == undefined)
            {
                alert('El codigo del ticket no debe estar vacio');
                return;
            }

            if(Number($scope.datos.pagar.codigoBarra) != $scope.datos.pagar.codigoBarra)
            {
                alert('El codigo del ticket debe ser numerico');
                return;
            }


            $scope.datos.pagar.idUsuario = $scope.datos.idUsuario;
            $scope.datos.pagar.servidor = $scope.datos.servidor;

            // console.log($scope.datos.pagar, ' Pagar idUsuario');
            var jwt = helperService.createJWT($scope.datos.pagar);
            $http.post(rutaGlobal+"/api/principal/pagar", {'action':'sp_pagar_buscar', 'datos': jwt})
             .then(function(response){


                if(response.data.errores == 1){
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.pagar.codigoBarra = null;
                    // console.log("pagar ticket: ", response.data);
                    if(tieneJugadasPendientes(response.data.venta))
                        printerService.printTicket(response.data.venta, CMD.TICKET_PAGADO);
                    alert(response.data.mensaje);
                }

            });
        }


        var tieneJugadasPendientes = function(venta){
            var tienePendientes = false;
            for(var c=0; c < venta.jugadas.length; c++){
                // console.log("tieneJugadasPendientes j: ", venta.jugadas[c]);
                // console.log("tieneJugadasPendientes js: ", venta.jugadas[c].status);
                
                if(venta.jugadas[c].status == 0){
                    tienePendientes = true;
                    break;
                }
            }
            // console.log("tieneJugadasPendientes: ", tienePendientes);
            
            return tienePendientes;
        }

        $scope.imprimirCuadre = function(){
            if(helperService.empty($scope.datos.ventasReporte.ventas.banca, "string") == false)
                printerService.printCuadre($scope.datos.ventasReporte.ventas);
        }


        $scope.ventasReporte_buscar = function(){

            $('#fechaVentasReporte').addClass('is-filled');

            $scope.datos.ventasReporte.idUsuario = idUsuario;
            $scope.datos.ventasReporte.layout = 'Principal';
          
            $scope.datos.ventasReporte.servidor = $scope.datos.servidor;
            $scope.datos.ventasReporte.idBanca = $scope.datos.selectedBancas.id;

          var jwt = helperService.createJWT($scope.datos.ventasReporte);
          $http.post(rutaGlobal+"/api/reportes/ventas", {'action':'sp_reporteVentas_buscar', 'datos': jwt})
             .then(function(response){

                // console.log('ventasReporte_buscar: ', response.datos);

                if(response.data.errores == 0){
                    $scope.datos.ventasReporte.loterias =response.data.loterias;
                $scope.datos.ventasReporte.ticketsGanadores =response.data.ticketsGanadores;

                var jsonVentas =response.data;
                $scope.datos.ventasReporte.ventas.banca = jsonVentas.banca;
                $scope.datos.ventasReporte.ventas.balanceHastaLaFecha = jsonVentas.balanceHastaLaFecha;
                $scope.datos.ventasReporte.ventas.pendientes = jsonVentas.pendientes;
                $scope.datos.ventasReporte.ventas.ganadores = jsonVentas.ganadores;
                $scope.datos.ventasReporte.ventas.perdedores = jsonVentas.perdedores;
                $scope.datos.ventasReporte.ventas.total = jsonVentas.total;
                $scope.datos.ventasReporte.ventas.ventas = jsonVentas.ventas;
                $scope.datos.ventasReporte.ventas.comisiones = jsonVentas.comisiones;
                $scope.datos.ventasReporte.ventas.descuentos = jsonVentas.descuentos;
                $scope.datos.ventasReporte.ventas.premios = jsonVentas.premios;
                $scope.datos.ventasReporte.ventas.neto = jsonVentas.neto;
                $scope.datos.ventasReporte.ventas.balanceActual = jsonVentas.balanceActual;
                }else if(response.data.errores == 1){
                    alert(response.data.mensaje);
                }
                //$scope.datos.ventasReporte.ventas.balance = jsonVentas.balance;


// console.log('ventasReporte_buscar2: ', $scope.datos.ventasReporte.ventas.balanceHastaLaFecha);

       
                // $scope.datos.ventasReporte.loterias =JSON.parse(response.data[0].loterias);
                // $scope.datos.ventasReporte.ticketsGanadoresSinPagar =JSON.parse(response.data[0].ticketsGanadoresSinPagar);

                // var jsonVentas =JSON.parse(response.data[0].ventas);
                // $scope.datos.ventasReporte.ventas.pendientes = jsonVentas[0].pendientes;
                // $scope.datos.ventasReporte.ventas.ganadores = jsonVentas[0].ganadores;
                // $scope.datos.ventasReporte.ventas.perdedores = jsonVentas[0].perdedores;
                // $scope.datos.ventasReporte.ventas.total = jsonVentas[0].total;
                // $scope.datos.ventasReporte.ventas.ventas = jsonVentas[0].ventas;
                // $scope.datos.ventasReporte.ventas.comisiones = jsonVentas[0].comisiones;
                // $scope.datos.ventasReporte.ventas.descuentos = jsonVentas[0].descuentos;
                // $scope.datos.ventasReporte.ventas.premios = jsonVentas[0].premios;
                // $scope.datos.ventasReporte.ventas.neto = jsonVentas[0].neto;
                // $scope.datos.ventasReporte.ventas.balance = jsonVentas[0].balance;

                //$scope.datos.jugadasReporte.jugadas = response.data;
                // if(response.data != undefined){
                //     var jsonJugadas = response.data;
                //     jsonJugadas.forEach(function(valor, indice, array){
                //         $scope.datos.jugadasReporte.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                //     });
                // }

            });

        }


        $scope.cancelarDesdeMovil = function(){
            
            if($scope.datos.selectedVentas.codigoBarra != undefined && $scope.datos.selectedVentas.codigoBarra != 'NO HAY VENTAS'){
                $scope.es_movil = true;
                $scope.datos.cancelar.codigoBarra = $scope.datos.selectedVentas.codigoBarra;
                $('#modal-cancelar').modal('toggle');
            }
            else{
                    $scope.es_movil = false;
                    // console.log('else escondar');
            }
        }

        $scope.cancelar = function(){
            // $scope.datos.cancelar.codigoBarra = codigoBarra;
            console.log("cancelar: ", $scope.datos.cancelar.codigoBarra);
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


            // $scope.datos.cancelar.razon = "";

            $scope.datos.cancelar.servidor = $scope.datos.servidor;
            $scope.datos.cancelar.idUsuario = $scope.datos.idUsuario;
            $scope.datos.cancelar.idBanca = $scope.datos.selectedBancas.id;
           // $scope.datos.cancelar.codigoBarra = $scope.datos.idUsuario;
            var jwt = helperService.createJWT($scope.datos.cancelar);
            $http.post(rutaGlobal+"/api/principal/cancelar", {'action':'sp_ventas_cancelar', 'datos': jwt})
             .then(function(response){
                // console.log(response.data);

                if(response.data.errores == 1){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.cancelar.codigoBarra = null;
                    // $scope.inicializarDatos(response);
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

        $scope.print = function(){
            

            // if($scope.empty($scope.datos.numSms, 'number' == true) && $scope.datos.sms == true){
            //     alert("El numero sms no es valido");
            //     return
            // }
            // if($scope.empty($scope.datos.numWhatsapp, 'number' == true) && $scope.datos.whatsapp == true){
            //     alert("El numero whatsapp no es valido");
            //     return
            // }

            // console.log('print function llamada desde ticket: ');
            return;
            

            $scope.datos.enviarSMS.sms = $scope.datos.sms;
            $scope.datos.enviarSMS.whatsapp = $scope.datos.whatsapp;
            $scope.datos.enviarSMS.numSms = $scope.datos.numSms;
            $scope.datos.enviarSMS.numWhatsapp = $scope.datos.numWhatsapp;
            $scope.datos.enviarSMS.idUsuario = idUsuario;
            $http.post(rutaGlobal+"/api/principal/sms", {'action':'sp_ventas_cancelar', 'datos': $scope.datos.enviarSMS})
             .then(function(response){
                // console.log(response.data);
            });

        }

        $scope.getBancaMoneda = function(banca){
           return helperService.getBancaMoneda(banca);
        }

        $scope.keyPressGuardarVenta = function(event){
            // console.log("keyPressGuardarVenta evento: ", event.keyCode);
            if(helperService.empty(String(event.keyCode), "string") == false){
                if(event.keyCode == 42){
                    quitarAsteriscoInputJugada();
                    $scope.venta_guardar(2);
                }
            }
        }

        function quitarAsteriscoInputJugada(){
            if(helperService.empty($scope.datos.jugada, "string") == false){
                if($scope.datos.jugada.indexOf("*") != -1){
                    $scope.datos.jugada.replace("*", "");
                }
            }
        }

        function startTime() {
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();
            m = checkTime(m);
            s = checkTime(s);
            document.getElementById('txtTime').innerHTML =
            h + ":" + m + ":" + s;
            var t = setTimeout(startTime, 500);
          }

          function checkTime(i) {
            if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
            return i;
          }

          function connectSocket()
          {
              var signedToken = helperService.createJWT({}, socketKeyGlobal);
            //   var socket = io.connect('ws://pruebass.ml:3000', {query: 'auth_token='+signedToken +'&room=' + servidorGlobal});
              var socket = io.connect('ws://loteriasdo.gq:3000', {query: 'auth_token='+signedToken +'&room=' + servidorGlobal});
                socket.on('connect', function(data) {
                    // console.log('se conecto: ' + data);
                    socket.on("test-channel:App\\Events\\EventName", function(data) {
                        // console.log('seMessage: ' + data);
                        // socket.emit('join', 'Hello World from client');
                    });
                    socket.on("realtime-stock:App\\Events\\RealtimeStockEvent", function(data) {
                        // console.log('seMessage: ' + data);
                        // socket.emit('join', 'Hello World from client');
                    });
                    socket.on("lotteries:App\\Events\\LotteriesEvent", function(data) {
                        // console.log('seMessage: ' + data);
                        // console.log('seMessage: ' + JSON.stringify(data));
                        if(!helperService.empty(data, "object")){
                            if(!helperService.empty(data.lotteries, "object")){
                                $scope.datos.optionsLoterias = data.lotteries;
                                $timeout(function() {
                                    // anything you want can go here and will safely be run on the next digest.
                                    //$('#multiselect').selectpicker('val', []);
                                    $scope.seleccionarPrimeraLoteria();
                                    $('#multiselect').selectpicker("refresh");
                                    $('.selectpicker').selectpicker("refresh");
                                    //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                                })
                            }
                        }
                        // console.log('seMessage: ' + data.lotteries[0].descripcion);
                        // socket.emit('join', 'Hello World from client');
                    });
                    socket.emit('join', 'Hello World from client');
                });

                // Connection failed
                socket.on('error', function(err) {
                    throw new Error(err);
                });

                // Connection succeeded
                socket.on('success', function(data) {
                    // console.log(data.message);
                    // console.log('user info: ' + data.user);
                    // console.log('logged in: ' + data.user.logged_in)
                })
          }

          function quitarLoteriasCerradas()
          {
            //   console.log("Dentro quitarLoterias");
              
              $scope.datos.optionsLoterias.forEach(function(value, index, array){
                
                var stringFechaActualRd = new Date().toLocaleString("es-ES", {timeZone: 'America/Santo_Domingo'});
                var stringFechaActualRdArray = stringFechaActualRd.split(" ");
                var fecha = stringFechaActualRdArray[0].split("/");
                var fechaLoteria = new Date(fecha[2] + "/" + fecha[1] + "/" + fecha[0] + " " + array[index].horaCierre); 
                var fechaFinalRd = new Date(new Date().toLocaleString("en-US", {timeZone: 'America/Santo_Domingo'}));
                var milliSeconds = fechaFinalRd.getTime() - fechaLoteria.getTime(); //Resta de milisegundos
                // console.log("fechaFinal: ", fechaLoteria);
                if(milliSeconds >= 0){
                   if(!helperService.tienePermiso("Jugar fuera de horario")) 
                   {
                        if(helperService.tienePermiso("Jugar minutos extras")) 
                        {
                            var minutosExtras = (!helperService.empty(array[index].minutosExtras, "string")) ? parseInt(array[index].minutosExtras) : 0;
                            var fechaLoteriaMasMinutosExtras = helperService.addMinutes(fechaLoteria, minutosExtras);
                            milliSeconds = fechaFinalRd.getTime() - fechaLoteriaMasMinutosExtras.getTime(); //Resta de milisegundos
                            if(milliSeconds >= 0)
                                cerrarLoteria(index);

                        }else{
                            cerrarLoteria(index);
                        }
                        // console.log("milliSeconds: ", milliSeconds, " hour: ", horaCierre, " fechaLoteria:", fechaLoteria);
                   }
                }
            });
          }

          function quitarLoteriasCerradasViejo()
          {
            //   console.log("Dentro quitarLoterias");
              
              $scope.datos.optionsLoterias.forEach(function(value, index, array){
                var horaCierre = array[index].horaCierre.split(":");
                var fechaActual = new Date();
                var fechaLoteria = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), fechaActual.getDate(), horaCierre[0], horaCierre[1], 0); 
                var fechaFinal = new Date();
                var milliSeconds = fechaFinal.getTime() - fechaLoteria.getTime(); //Resta de milisegundos
                
                if(milliSeconds >= 0){
                   if(!helperService.tienePermiso("Jugar fuera de horario")) 
                   {
                        if(helperService.tienePermiso("Jugar minutos extras")) 
                        {
                            var minutosExtras = (helperService.empty(array[index].minutosExtras, "string")) ? 0 : parseInt(array[index].minutosExtras);
                            var fechaLoteriaMasMinutosExtras = helperService.addMinutes(fechaLoteria, minutosExtras);
                            milliSeconds = fechaFinal.getTime() - fechaLoteriaMasMinutosExtras.getTime(); //Resta de milisegundos
                            if(milliSeconds >= 0)
                                cerrarLoteria(index);

                        }else{
                            cerrarLoteria(index);
                        }
                        // console.log("milliSeconds: ", milliSeconds, " hour: ", horaCierre, " fechaLoteria:", fechaLoteria);
                   }
                }
            });
          }

          function cerrarLoteria(index){
            $scope.datos.optionsLoterias.splice(index, 1);
            $timeout(function() {
                // anything you want can go here and will safely be run on the next digest.
                //$('#multiselect').selectpicker('val', []);
                $scope.seleccionarPrimeraLoteria();
                $('#multiselect').selectpicker("refresh");
                $('.selectpicker').selectpicker("refresh");
                //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
            })
          }

    })

    var map = {9: false, 16: false};
    myApp.directive('onShiftTab', function() {
        return function(scope, element, attrs) {
             map = {9: false, 16: false};
        
            element.on("keydown", function(event) {
                if (event.which in map) {
                    map[event.which] = true;
                    if (map[9] && map[16]) {
                        scope.$apply(function(){
                            scope.$eval(attrs.onShiftTab, {'$event': event});
                        });
                        event.preventDefault();
                    }
                }
            });
            element.on("keyup", function(event) {
                if (event.which in map) {
                    map[event.keyCode] = false;

                }else{
                    map = {9: false, 16: false};
                }
            });
        };
        });

        myApp.directive('customValidation', function(){
            return {
              require: 'ngModel',
              link: function(scope, element, attrs, modelCtrl) {
         
                modelCtrl.$parsers.push(function (inputValue) {
         
                  var transformedInput = inputValue.toLowerCase().replace(/ /g, ''); 
                  var transformedInput = inputValue.toLowerCase().replace("*", ''); 
         
                  if (transformedInput!=inputValue) {
                    modelCtrl.$setViewValue(transformedInput);
                    modelCtrl.$render();
                  }         
         
                  return transformedInput;         
                });
              }
            };
         });