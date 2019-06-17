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
        "estado":0,
        "loterias": [],
        "jugadas":[],

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
    }
        $scope.inicializarDatos = function(){

           
            
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
            

            $http.get("/api/principal")
             .then(function(response){

                console.log(response)

                $scope.datos.optionsVentas = (response.ventas != undefined) ? response.ventas : [{'id': 1, 'ticket' : 'No hay ventas'}];
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
            // console.log('ROOT_PATH: ', ruta);
            $scope.inicializarDatos();

          $scope.datos.idUsuario = codigo_usuario; //parseInt(codigo_usuario);
          $scope.datos.idBanca = idBanca; //parseInt(codigo_usuario);

          var a = new Hola("Jean", "Contreras");
          console.log('clase: ', a.nombre);
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
            console.log(d);

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


        $scope.monto_disponible = function(blur = false){
            
            
            
        
            if(Number($scope.datos.jugada) != $scope.datos.jugada)
                {
                    //$('#inputJugada').focus(); Este focus bloquea toda la pagina asi que dicidi comentarlo
                    $scope.datos.monto = null;
                    $scope.datos.jugada = null;
                    $scope.jugada = null;

                    return;
                }

            if($scope.datos.jugada != undefined && $scope.datos.jugada > 0){
                
                
                
                
         
                $scope.datos.jugada = ($scope.datos.jugada.length >= 2 &&  $scope.datos.jugada.length <= 6 && $scope.datos.jugada.length != 3 && $scope.datos.jugada.length != 5) ? $scope.datos.jugada : null;
                //jugada = parseInt(jugada);
                
                if($scope.datos.jugada != null){
                    if(Object.keys($scope.datos.loterias).length <= 0){
                        if(blur == false)
                            alert("Debes seleccionar una loteria");
                        return;
                    }
                    
                    if(Object.keys($scope.datos.loterias).length > 1){
                        $scope.datos.montoExistente = 'X';
                        $('#inputMonto').focus();
                        return;
                    }
                    

                    $scope.datos.idLoteria = $scope.datos.loterias[0].id;
                   
                    $http.post("api/principal/montodisponible",{'datos':$scope.datos, 'action':'sp_jugadas_obtener_montoDisponible'})
                      .then(function(response){
                            // console.log(response);
                         $scope.datos.montoExistente = response.data.monto;
                          $('#inputMonto').focus();
                          return;
                      })

                }
            }

            $scope.datos.montoExistente = 0;
          }
  
  
          $scope.jugada_insertar = function(evento, sinevento = false){
           
           
                if(sinevento){
                    evento = {};
                    evento.keyCode = 13;
                }

                if($scope.datos.jugada != null && evento.keyCode == 13){
                    $scope.datos.loterias.forEach(function(valor, indice, array){
                        
                    if(Number($scope.datos.jugada) != $scope.datos.jugada)
                    {
                        $('#inputJugada').focus();
                        $scope.datos.monto = null;
                        $scope.datos.jugada = null;
                        $scope.jugada = null;
                        
    
                        return;
                    }
                    // console.log('insertar, foreach: ', array[indice], ' monto: ', $scope.datos.monto, ' jugada: ', $scope.datos.jugada);
                    
                    if($scope.datos.monto > 0){
                        
                        //Verificamos que se haya seleccionado una loteria
                        if(Object.keys($scope.datos.loterias).length > 0){
                            //Verificamos que la jugada sea numerica
                            
                            if(Number($scope.datos.jugada) == $scope.datos.jugada){
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
                                    if($scope.datos.jugadas.find(x => (x.jugada == $scope.datos.jugada && x.idLoteria == array[indice].id)) != undefined){
                                        
                                        let idx = $scope.datos.jugadas.findIndex(x => (x.jugada == $scope.datos.jugada && x.idLoteria == array[indice].id));
                                        $scope.datos.jugadas[idx].monto = parseFloat($scope.datos.jugadas[idx].monto)+ parseFloat($scope.datos.monto);
                                        $('#inputJugada').focus();
                                        //$scope.monto = 0;
                                    }
                                    else{
                                       // console.log('insertar, foreach: ', array[indice]);
                                        $scope.datos.jugadas.push({'jugada':$scope.datos.jugada, 'monto':$scope.datos.monto, 
                                                                    'tam': $scope.datos.jugada.length, 'idLoteria': array[indice].id,
                                                                    'descripcion':array[indice].descripcion, 'abreviatura' : array[indice].abreviatura});
                                        $('#inputJugada').focus();
                                        //$scope.datos.monto = 0;
                                    }
                                }
                                // if(( Object.keys($scope.datos.loterias).length == 1 && $scope.datos.montoExistente > $scope.datos.monto) || (Object.keys($scope.datos.loterias).length > 1 && $scope.datos.montoExistente == 'X')){
                                    
                                // }
                                // else{
                                //     alert("No hay existencia suficiente para ese monto");
                                // }
                            }
                            else{
                                alert("La jugada debe ser numerica");
                            }
                        }
                        else{
                            alert("Debes seleccionar una loteria");
                        }
                    }
                   
                    
                    
                    
                    
                    
                    //Verificamos si la jugada existe, si es asi entonces se le sumara el monto nuevo
                   
                }); //END FOREACH

                //$('#inputJugada').focus();
                $scope.datos.monto = null;
                $scope.datos.jugada = null;
                $scope.jugada = null;

                $scope.calcularTotal();
                    
                } // END IF PRINCIPAL
            
            
             return;
            if($scope.datos.jugada != null && evento.keyCode == 13){
                if(Number($scope.datos.jugada) != $scope.datos.jugada)
                {
                    $('#inputJugada').focus();
                    $scope.datos.monto = null;
                    $scope.datos.jugada = null;
                    $scope.jugada = null;

                    return;
                }
                // if(Object.keys($scope.datos.loterias).length <= 0){
                //     alert("Debes seleccionar una loteria");
                //     $('#inputJugada').focus();
                //     $scope.monto = 0;
                //     return;
                // }
                // if(Number($scope.datos.jugada) === NaN){
                //     alert("La jugada debe ser numerica");
                //     $('#inputJugada').focus();
                //     $scope.monto = 0;
                //     return;
                // }
                // if($scope.montoExistente < monto && Object.keys($scope.datos.loterias).length == 1){
                //     alert("La jugada debe ser numerica");
                //     $('#inputJugada').focus();
                //     $scope.monto = 0;
                //     return;
                // }

                // console.log('jugada insertar monto: ',
                //                 $scope.datos.monto, 
                //                 ' lengh: ', Object.keys($scope.datos.loterias).length,
                //                  ' Existente: ', $scope.datos.montoExistente,
                //                 'condicion: ', (Number($scope.datos.montoExistente) > $scope.datos.monto),
                //             ' juagada: ', Number($scope.datos.jugada) );
                //Verificamos que el monto a jugar sea mayor que cero
                if($scope.datos.monto > 0){
                    //Verificamos que se haya seleccionado una loteria
                    if(Object.keys($scope.datos.loterias).length > 0){
                        //Verificamos que la jugada sea numerica
                        if(Number($scope.datos.jugada) == $scope.datos.jugada){
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
                                if($scope.datos.jugadas.find(x => x.jugada == $scope.datos.jugada) != undefined){
    
                                    let idx = $scope.datos.jugadas.findIndex(x => x.jugada == $scope.datos.jugada);
                                    $scope.datos.jugadas[idx].monto = parseFloat($scope.datos.jugadas[idx].monto)+ parseFloat($scope.datos.monto);
                                    $('#inputJugada').focus();
                                    $scope.monto = 0;
                                }
                                else{
                                    $scope.datos.jugadas.push({'jugada':$scope.datos.jugada, 'monto':$scope.datos.monto, 'tam': $scope.datos.jugada.length});
                                    $('#inputJugada').focus();
                                    $scope.datos.monto = 0;
                                }
                            }
                            // if(( Object.keys($scope.datos.loterias).length == 1 && $scope.datos.montoExistente > $scope.datos.monto) || (Object.keys($scope.datos.loterias).length > 1 && $scope.datos.montoExistente == 'X')){
                                
                            // }
                            // else{
                            //     alert("No hay existencia suficiente para ese monto");
                            // }
                        }
                        else{
                            alert("La jugada debe ser numerica");
                        }
                    }
                    else{
                        alert("Debes seleccionar una loteria");
                    }
                }
               
                //$('#inputJugada').focus();
                $scope.datos.monto = null;
                $scope.datos.jugada = null;
                $scope.jugada = null;

                $scope.calcularTotal();
                
                    
                
                //Verificamos si la jugada existe, si es asi entonces se le sumara el monto nuevo
               
                
            }
                   

            //$scope.calcularTotal();
        }

        $scope.jugada_eliminar = function(jugada){
            if($scope.datos.jugadas.find(x => x.jugada == jugada) != undefined){
    
                let idx = $scope.datos.jugadas.findIndex(x => x.jugada == jugada);
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
             $scope.datos.descuentoMonto = ($scope.datos.hayDescuento) ? parseInt(parseFloat($scope.datos.monto_a_pagar) / parseFloat($scope.datos.caracteristicasGenerales[0].cantidadAplicar)) * parseFloat($scope.datos.caracteristicasGenerales[0].descuentoValor) : 0;
             

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

        $scope.inputJugadaKeyup = function(evento){
            if($scope.datos.jugada != null && evento.keyCode == 13) $scope.monto_disponible();
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
                    alert('No hay jugadas realizadas');
                    return;
                }
                if(Object.keys($scope.datos.loterias).length ==0)
                {
                    map = {9: false, 16: false};
                    alert('Debe seleccionar una loteria');
                    return;
                }

                $scope.datos.total = $scope.datos.monto_a_pagar;
                

                $http.post("/api/principal/guardar",{'datos':$scope.datos, 'action':'sp_ventas_actualiza'})
                .then(function(response){

                    console.log(response);
 
                    if(response.data.errores == 0)
                        {
                            // console.log(response);
                          alert(response.data.mensaje);
                          $scope.inicializarDatos();
                          $scope.imprimirTicket(response.data.venta, (e == 1) ? true : false);
                        }
                    else{
                        alert(response.data.mensaje);
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
                
            //     console.log('venta guardar: ',$scope.datos );

            //     $http.post($scope.ROOT_PATH +"clases/consultaajax.php",{'datos':$scope.datos, 'action':'sp_ventas_actualiza'})
            //     .then(function(response){
            //         console.log(response);
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
            // window.print();
            $window.sessionStorage.removeItem('ticket');
            $window.sessionStorage.setItem('ticket', JSON.stringify(ticket));
            // console.log(ruta);
            
            //a=window.frames['iframeOculto'].src= ruta;

            if(!es_movil)
                $('#iframeOculto').attr('src',ruta);
            else
                $('#iframeOcultoMovil').attr('src',ruta);

            console.log('iframe: ', $('#iframeOculto'));


            var json = [];
            var contador = 0;
            $.each($window.sessionStorage, function(i, v){
                json.push(angular.fromJson(v));
            });
            // console.log('iimprimirTicket: ', json);
            

            //a=window.frames['iframeOculto'].src= '';
            // setTimeout(() => { $(".printFrame").remove(); }, 1000);
            //setTimeout(() => {  window.frames['iframeOculto'].src= ''; }, 1000);




        }

        $scope.buscar = function(){

            console.log('monitoreo before addClass',$scope.datos.monitoreo);
            $('#fechaBusqueda').addClass('is-filled');
            
            console.log('monitoreo after addClass',$scope.datos.monitoreo);
            
            $scope.datos.monitoreo.idUsuario = $scope.datos.idUsuario;
          
          $http.post("api/reportes/monitoreo", {'action':'sp_ventas_buscar', 'datos': $scope.datos.monitoreo})
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

            $http.post("api/principal/duplicar", {'action':'sp_ventas_obtenerpor_numeroticket', 'datos': $scope.datos.duplicar})
             .then(function(response){

               
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
                        if(Object.keys($scope.datos.loterias).length > 1 && $scope.datos.jugadas.find(x => x.jugada == array[indice].jugada) != undefined)
                        {

                        }else{
                            $scope.datos.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                        }

                        //$scope.datos.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
                    });
                }
                $scope.calcularTotal();
                $('#modal-duplicar').modal('hide');
               

            });
        }


        $scope.buscar_jugadas = function(){

            $('#fechaBusqueda').addClass('is-filled');

            

            $scope.datos.jugadasReporte.idLoteria = $scope.datos.jugadasReporte.selectedLoteria.id;
          
          $http.post("api/reportes/jugadas", {'action':'sp_jugadas_buscar', 'datos': $scope.datos.jugadasReporte})
             .then(function(response){

                
                $scope.datos.jugadasReporte.jugadas = [];
                $scope.datos.jugadasReporte.total_directo = 0;
                $scope.datos.jugadasReporte.total_palet = 0;
                $scope.datos.jugadasReporte.total_tripleta = 0;
                $scope.datos.jugadasReporte.monto_total = 0;

                //$scope.datos.jugadasReporte.jugadas = response.data;
                if(response.data != undefined){
                    var jsonJugadas = response.data.jugadas;
                    jsonJugadas.forEach(function(valor, indice, array){
                        $scope.datos.jugadasReporte.jugadas.push({'jugada':array[indice].jugada, 'monto':array[indice].monto, 'tam': array[indice].jugada.length});
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

            // console.log($scope.datos.pagar, ' Pagar idUsuario');

            $http.post("/api/principal/pagar", {'action':'sp_pagar_buscar', 'datos': $scope.datos.pagar})
             .then(function(response){


                if(response.data.errores == 1){
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.pagar.codigoBarra = null;
                    alert(response.data.mensaje);
                }

            });
        }





        $scope.ventasReporte_buscar = function(){

            $('#fechaVentasReporte').addClass('is-filled');

            

          
          $http.post("api/reportes/ventas", {'action':'sp_reporteVentas_buscar', 'datos': $scope.datos.ventasReporte})
             .then(function(response){

                // console.log('ventasReporte_buscar: ', response);

                $scope.datos.ventasReporte.loterias =response.data.loterias;
                $scope.datos.ventasReporte.ticketsGanadores =response.data.ticketsGanadores;

                var jsonVentas =response.data;
                $scope.datos.ventasReporte.ventas.pendientes = jsonVentas.pendientes;
                $scope.datos.ventasReporte.ventas.ganadores = jsonVentas.ganadores;
                $scope.datos.ventasReporte.ventas.perdedores = jsonVentas.perdedores;
                $scope.datos.ventasReporte.ventas.total = jsonVentas.total;
                $scope.datos.ventasReporte.ventas.ventas = jsonVentas.ventas;
                $scope.datos.ventasReporte.ventas.comisiones = jsonVentas.comisiones;
                $scope.datos.ventasReporte.ventas.descuentos = jsonVentas.descuentos;
                $scope.datos.ventasReporte.ventas.premios = jsonVentas.premios;
                $scope.datos.ventasReporte.ventas.neto = jsonVentas.neto_final;
                //$scope.datos.ventasReporte.ventas.balance = jsonVentas.balance;


       
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
                    console.log('else escondar');
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
           // $scope.datos.cancelar.codigoBarra = $scope.datos.idUsuario;

            $http.post("api/principal/cancelar", {'action':'sp_ventas_cancelar', 'datos': $scope.datos.cancelar})
             .then(function(response){
                // console.log(response.data);

                if(response.data.errores == 1){
                    $scope.datos.cancelar.codigoBarra = null;
                    $scope.datos.cancelar.razon = null;
                    alert(response.data.mensaje);
                    return;
                }else if(response.data.errores == 0){
                    $scope.datos.cancelar.codigoBarra = null;
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
        })