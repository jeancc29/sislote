myApp
    .controller("myController", function($scope, $http, $timeout, helperService){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];
        var ruta = "";

        function horaToFecha(hora){
            var fecha, time, ano, mes, dia, hh, min, ss;
            fecha = new Date();
            time = hora.split(':');
            ano = fecha.getFullYear();
            mes = fecha.getMonth() + 1;
            dia = fecha.getDate();
            hh = time[0];
            min = time[1];
            ss = time[2];

            var fecha = new Date(ano, mes, dia, hh, min, ss);

           // console.log('horaToFecha, fecha: ', fecha);

            return fecha;
        }

        function hora_convertir(phora, _24 = true){
            //Si es verdadero la hora se convertira al formato 24 horas
            if(_24){
                //Si es verdadero eso quiere decir que es PM de lo contrario sera AM
                if(phora.indexOf("PM") != -1){
                    
                    //Aqui se le quitara el PM a la hora
                    var a = phora.replace(" PM", "");
                    //Aqui la hora se convertira en un arreglo para tener aparte la hora y los minutos
                    a = a.split(':');
                    //La variable hora va a contener el solamente la hora sin minutos ni segundos
                    var hora = parseInt(a[0]);
                    //Aqui se convierte la hora normal en el formato 24 horas
                    if(hora == 12)
                        phora = hora;
                    else
                        phora = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    phora = phora.toString() + ":" + a[1];
                    //console.log('actualizar: convertido: ', phora); 
                }
                else if(phora.indexOf("AM") != -1){
                     //Aqui se le quitara el AM a la hora
                     var a = phora.replace(" AM", "");
                    //  phora = a;
                    //  console.log('actualizar: convertido: ', phora); 
                    var hora = parseInt(a.split(':')[0]);
                     if(hora == 12){
                        phora = hora + 12;
                        phora = phora.toString() + ":" + a.split(':')[1];
                     }else{
                        phora = a;
                     }
                }

            }
            else{
                var a = phora.split(":");
                var hora = parseInt(a[0]);
                if(hora > 12){
                    hora = hora - 12;
                    phora = hora.toString() + ':' + a[1] + ' PM';
                }
            }

            return phora;
        }

        function _convertir_apertura_y_cierre(_24){
            $scope.datos.lunes.apertura = hora_convertir($('#lunesHoraApertura').val(), _24);
            $scope.datos.lunes.cierre = hora_convertir($('#lunesHoraCierre').val(), _24);
            $scope.datos.martes.apertura = hora_convertir($('#martesHoraApertura').val(), _24);
            $scope.datos.martes.cierre = hora_convertir($('#martesHoraCierre').val(), _24);
            $scope.datos.miercoles.apertura = hora_convertir($('#miercolesHoraApertura').val(), _24);
            $scope.datos.miercoles.cierre = hora_convertir($('#miercolesHoraCierre').val(), _24);
            $scope.datos.jueves.apertura = hora_convertir($('#juevesHoraApertura').val(), _24);
            $scope.datos.jueves.cierre = hora_convertir($('#juevesHoraCierre').val(), _24);
            $scope.datos.viernes.apertura = hora_convertir($('#viernesHoraApertura').val(), _24);
            $scope.datos.viernes.cierre = hora_convertir($('#viernesHoraCierre').val(), _24);
            $scope.datos.sabado.apertura = hora_convertir($('#sabadoHoraApertura').val(), _24);
            $scope.datos.sabado.cierre = hora_convertir($('#sabadoHoraCierre').val(), _24);
            $scope.datos.domingo.apertura = hora_convertir($('#domingoHoraApertura').val(), _24);
            $scope.datos.domingo.cierre = hora_convertir($('#domingoHoraCierre').val(), _24);
        }


        function hora_convertir2(phora){
            //Si es verdadero la hora se convertira al formato 24 horas
            //if(_24){
                //Si es verdadero eso quiere decir que es PM de lo contrario sera AM
                if(phora.indexOf("PM") != -1){
                    
                    //Aqui se le quitara el PM a la hora
                    var a = phora.replace(" PM", "");
                    //Aqui la hora se convertira en un arreglo para tener aparte la hora y los minutos
                    a = a.split(':');
                    //La variable hora va a contener el solamente la hora sin minutos ni segundos
                    var hora = parseInt(a[0]);
                    //Aqui se convierte la hora normal en el formato 24 horas
                    phora = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    phora = phora.toString() + ":" + a[1];
                    //console.log('actualizar: convertido: ', phora); 
                }
                else{
                     //Aqui se le quitara el AM a la hora
                     var a = phora.replace(" AM", "");
                     //Si son las 12 AM entonces debo convertir la hora a formato 24
                     var hora = parseInt(a.split(':')[0]);
                     if(hora == 12){
                        phora = hora + 12;
                        phora = phora.toString() + ":" + a.split(':')[1];
                     }else{
                        phora = a;
                     }
                     
                     //console.log('actualizar: convertido: ', phora); 
                }

                return phora;
            //}
            // else{
            //     var a = $scope.datos.horaCierre.split(":");
            //     var hora = parseInt(a[0]);
            //     if(hora > 12){
            //         hora = hora - 12;
            //         $scope.datos.horaCierre = hora.toString() + ':' + a[1] + ' PM';
            //     }
            // }
        }

         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "id":0,
            "descripcion": null,
            "codigo" : null,
            "ip" : null,
            "usuario" : null,
            "clave" : null,
            "idTipoUsuario" : 0,
            "estado":true,
            "permisos": [],
            "minutosCancelarTicket" : null,

            "piepagina1" : null,
            "piepagina2" : null,
            "piepagina3" : null,
            "piepagina4" : null,

            "bancas" : [],

            "ckbPermisosAdicionales": [],
            "mostrarFormEditar" : false,
            "cargando" : false,
            "btnCargando" : false,


            "optionsUsuariosTipos" : [],
            "selectedUsuariosTipos" : {},


            "loterias" : [],
            "ckbLoterias" : [],
            "loteriasSeleccionadas" : [],
            "selectedLoteriaComisiones" : {},
            "selectedLoteriaPagosCombinaciones" : {},


            'lunes' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'martes' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'miercoles' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'jueves' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'viernes' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'sabado' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'domingo' : {
                'apertura': hora_convertir("01:00:00"),
                'cierre' : hora_convertir("23:00:00")
            },
            'comisiones' :{
                'loterias' : [],
                'selectedLoteria' : {}
            },
            'pagosCombinaciones' :{
                'loterias' : [],
                'selectedLoteria' : {}
            },
            'gasto' :{
                'fechaInicio' : new Date(),
                'descripcion' : null,
                'monto' : null,
                'frecuencia' : null,
                'gastos' : [],
                'indexFrecuencia' : 0,
                'index' : null
            },
            'gastos' : [],
            'radioFrecuencias' : []
        }

        
        $scope.inicializarDatos = function(todos, idUsuarioBanca = 0){
            var jwt = helperService.createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario});
            $http.get(rutaGlobal+"/api/bancas?token=" + jwt)
             .then(function(response){
                console.log('Bancas: ', response.data);

                if(todos){
                    $scope.datos.id = 0;
                    $scope.datos.descripcion = null;
                    $scope.datos.codigo = null;
                    $scope.datos.ip = null;
                    $scope.datos.dueno = null;
                    $scope.datos.localidad = null;

                    $scope.datos.porcentajeCaida = null;
                    $scope.datos.balanceDesactivacion = null;
                    $scope.datos.limiteVenta = null;
                    $scope.datos.descontar = null;
                    $scope.datos.deCada = null;
                    $scope.datos.minutosCancelarTicket = null;
                    $scope.datos.piepagina1 = null;
                    $scope.datos.piepagina2 = null;
                    $scope.datos.piepagina3 = null;
                    $scope.datos.piepagina4 = null;

                    $scope.datos.estado = true;
                    $scope.datos.idTipoUsuario = 0;

                    $scope.datos.permisos = [];
                    $scope.datos.ckbPermisosAdicionales = [];
                   

                }

                var copiarATodas = {'id' : 0, 'descripcion' : 'COPIAR A TODAS', "sorteos":[
                    {"id": 5, "descripcion": "Pick 3 Straight"},
                    {"id": 6, "descripcion": "Pick 3 Box"},
                    {"id": 7, "descripcion": "Pick 4 Straight"},
                    {"id": 8, "descripcion": "Pick 4 Box"},
                    {"id": 1, "descripcion": "Directo"},
                    {"id": 2, "descripcion": "Pale"},
                    {"id": 3, "descripcion": "Tripleta"},
                    {"id": 4, "descripcion": "Super pale"},
                   ]}
                

                var jsonLoterias = response.data.loterias;
                response.data.loterias.unshift(copiarATodas);
                $scope.datos.loterias = [];
                jsonLoterias.forEach(function(valor, indice, array){
                    array[indice].comisiones = {};
                    array[indice].comisiones.directo = null;
                    array[indice].comisiones.pale = null;
                    array[indice].comisiones.tripleta = null;
                    array[indice].comisiones.superPale = null;
                    array[indice].comisiones.pick3Straight = null;
                    array[indice].comisiones.pick3Box = null;
                    array[indice].comisiones.pick4Straight = null;
                    array[indice].comisiones.pick4Box = null;
                    
                    array[indice].pagosCombinaciones = {};
                    array[indice].pagosCombinaciones.primera = null;
                    array[indice].pagosCombinaciones.segunda = null;
                    array[indice].pagosCombinaciones.tercera = null;
                    array[indice].pagosCombinaciones.primeraSegunda = null;
                    array[indice].pagosCombinaciones.primeraTercera = null;
                    array[indice].pagosCombinaciones.segundaTercera = null;
                    array[indice].pagosCombinaciones.tresNumeros = null;
                    array[indice].pagosCombinaciones.dosNumeros = null;
                    array[indice].pagosCombinaciones.primerPago = null;
                    array[indice].pagosCombinaciones.pick3TodosEnSecuencia = null;
                    array[indice].pagosCombinaciones.pick33Way = null;
                    array[indice].pagosCombinaciones.pick36Way = null;
                    array[indice].pagosCombinaciones.pick4TodosEnSecuencia = null;
                    array[indice].pagosCombinaciones.pick44Way = null;
                    array[indice].pagosCombinaciones.pick46Way = null;
                    array[indice].pagosCombinaciones.pick412Way = null;
                    array[indice].pagosCombinaciones.pick424Way = null;

                    if(array[indice].id == 0)
                        array[indice].existe = false;
                    else
                        array[indice].existe = true;

                    // $scope.datos.loterias.push(array[indice]);
                    // $scope.datos.ckbLoterias.push(array[indice]);
                    // $scope.datos.loteriasSeleccionadas.push(array[indice]);
                    // $scope.datos.comisiones.loterias.push(array[indice]);
                    // $scope.datos.pagosCombinaciones.loterias.push(array[indice]);
                });
                //$scope.datos.loterias = response.data.loterias;
                //console.log('inicializar: ', jsonLoterias);
                $scope.datos.loterias = jsonLoterias;
                $scope.datos.ckbLoterias = jsonLoterias;
                $scope.datos.ckbLoteriasGuardar = jsonLoterias;
                $scope.datos.loteriasSeleccionadas = jsonLoterias;
                $scope.datos.comisiones.loterias = jsonLoterias;
                $scope.datos.pagosCombinaciones.loterias = jsonLoterias;
                $scope.datos.radioFrecuencias = response.data.frecuencias;
                $scope.datos.gasto.frecuencia = $scope.datos.radioFrecuencias[0];
                $scope.datos.optionsDias = response.data.dias;
                $scope.datos.selectedDia = $scope.datos.optionsDias[0];
                

                // $scope.datos.ckbLoterias = [];
                // jsonLoterias.forEach(function(valor, indice, array){
                //         array[indice].existe = true;
                //         $scope.datos.ckbLoterias.push(array[indice]);
                //     });

                console.log('cbloteias: ', $scope.datos.ckbLoterias);

                


                // $scope.datos.loteriasSeleccionadas = $scope.datos.ckbLoterias;
                // $scope.datos.comisiones.loterias = $scope.datos.loteriasSeleccionadas;
                // $scope.datos.pagosCombinaciones.loterias = $scope.datos.loteriasSeleccionadas;


                $scope.datos.optionsUsuarios = response.data.usuarios;
                let idx = 0;
                if(idUsuarioBanca > 0)
                    idx = $scope.datos.optionsUsuarios.findIndex(x => x.id == idUsuarioBanca);

                $scope.datos.selectedUsuario = $scope.datos.optionsUsuarios[idx];
                $scope.datos.optionsUsuariosTipos = response.data.usuariosTipos;
                $scope.datos.optionsMonedas = response.data.monedas;
                $scope.datos.selectedMoneda = $scope.datos.optionsMonedas[0];

                $scope.datos.bancas =response.data.bancas;
                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    // $('.selectpicker').selectpicker('val', [])
                  })
               
                
                
            }
            // ,
            // function(response) {
            //     // Handle error here
            //     //console.log('Error jean: ', response);
            //     alert("Error");
            // }
            );
       
        }
        

        $scope.load = function(codigo_usuario, ruta){
            $scope.ruta = ruta;
            $scope.inicializarDatos(true);
 
        }


        
        function llenarckbLoterias(loterias){
            console.log('llenarckbLoterias: ', loterias);
            var copiarATodas = {'id' : 0, 'descripcion' : 'COPIAR A TODAS', "sorteos":[
                {"id": 5, "descripcion": "Pick 3 Straight"},
                {"id": 6, "descripcion": "Pick 3 Box"},
                {"id": 7, "descripcion": "Pick 4 Straight"},
                {"id": 8, "descripcion": "Pick 4 Box"},
                {"id": 1, "descripcion": "Directo"},
                {"id": 2, "descripcion": "Pale"},
                {"id": 3, "descripcion": "Tripleta"},
                {"id": 4, "descripcion": "Super pale"},
               ]}
            

           
            loterias.unshift(copiarATodas);
            loterias.forEach(function(valor, indice, array){
                array[indice].comisiones = {};
                array[indice].comisiones.directo = null;
                array[indice].comisiones.pale = null;
                array[indice].comisiones.tripleta = null;
                array[indice].comisiones.superPale = null;
                array[indice].comisiones.pick3Straight = null;
                array[indice].comisiones.pick3Box = null;
                array[indice].comisiones.pick4Straight = null;
                array[indice].comisiones.pick4Box = null;
                
                array[indice].pagosCombinaciones = {};
                array[indice].pagosCombinaciones.primera = null;
                array[indice].pagosCombinaciones.segunda = null;
                array[indice].pagosCombinaciones.tercera = null;
                array[indice].pagosCombinaciones.primeraSegunda = null;
                array[indice].pagosCombinaciones.primeraTercera = null;
                array[indice].pagosCombinaciones.segundaTercera = null;
                array[indice].pagosCombinaciones.tresNumeros = null;
                array[indice].pagosCombinaciones.dosNumeros = null;
                array[indice].pagosCombinaciones.primerPago = null;
                array[indice].pagosCombinaciones.pick3TodosEnSecuencia = null;
                array[indice].pagosCombinaciones.pick33Way = null;
                array[indice].pagosCombinaciones.pick36Way = null;
                array[indice].pagosCombinaciones.pick4TodosEnSecuencia = null;
                array[indice].pagosCombinaciones.pick44Way = null;
                array[indice].pagosCombinaciones.pick46Way = null;
                array[indice].pagosCombinaciones.pick412Way = null;
                array[indice].pagosCombinaciones.pick424Way = null;

                if(array[indice].id == 0)
                    array[indice].existe = false;
                else
                    array[indice].existe = true;

                // $scope.datos.loterias.push(array[indice]);
                // $scope.datos.ckbLoterias.push(array[indice]);
                // $scope.datos.loteriasSeleccionadas.push(array[indice]);
                // $scope.datos.comisiones.loterias.push(array[indice]);
                // $scope.datos.pagosCombinaciones.loterias.push(array[indice]);
            });

            $scope.datos.ckbLoterias = loterias;
        }

        $scope.editar = function(esNuevo, d){
            $('#fechaGasto').addClass('is-filled');
            helperService.destruirScrollBar();
            
            $scope.datos.mostrarFormEditar = true;
            $scope.datos.ckbLoterias = $scope.datos.ckbLoteriasGuardar;
            $scope.datos.gastos = [];
            $scope.datos.imprimirCodigoQr = true;

            if(esNuevo == 1){

                

                $scope.datos.id = 0;
                $scope.datos.descripcion = null;
                $scope.datos.codigo = null;
                $scope.datos.ip = null;
                $scope.datos.dueno = null;
                $scope.datos.localidad = null;

                $scope.datos.porcentajeCaida = null;
                $scope.datos.balanceDesactivacion = null;
                $scope.datos.limiteVenta = null;
                $scope.datos.descontar = null;
                $scope.datos.deCada = null;
                $scope.datos.minutosCancelarTicket = null;
                $scope.datos.piepagina1 = null;
                $scope.datos.piepagina2 = null;
                $scope.datos.piepagina3 = null;
                $scope.datos.piepagina4 = null;

                $scope.datos.estado = true;
                $scope.datos.idTipoUsuario = 0;

                $scope.datos.permisos = [];
                $scope.datos.ckbPermisosAdicionales = [];

                $scope.datos.cargando = true;

                $scope.datos.servidor = servidorGlobal;
                var jwt = helperService.createJWT($scope.datos);
                 $http.post(rutaGlobal+"/api/bancas/getDatos", {'action':'sp_bancas_actualizar', 'datos': jwt})
             .then(function(response){
                // console.log(response.data);
                if(response.data.errores == 0){
                   
                    $scope.datos.cargando = false;
                    llenarckbLoterias(response.data.loterias);
                    $scope.datos.gastos = [];
    
                   
                     $scope.datos.lunes.apertura = hora_convertir("01:00:00");
                     $scope.datos.lunes.cierre = hora_convertir("23:00:00");
                     $scope.datos.martes.apertura = hora_convertir("01:00:00");
                     $scope.datos.martes.cierre = hora_convertir("23:00:00");
                     $scope.datos.miercoles.apertura = hora_convertir("01:00:00");
                     $scope.datos.miercoles.cierre = hora_convertir("23:00:00");
                     $scope.datos.jueves.apertura = hora_convertir("01:00:00");
                     $scope.datos.jueves.cierre = hora_convertir("23:00:00");
                     $scope.datos.viernes.apertura = hora_convertir("01:00:00");
                     $scope.datos.viernes.cierre = hora_convertir("23:00:00");
                     $scope.datos.sabado.apertura = hora_convertir("01:00:00");
                     $scope.datos.sabado.cierre = hora_convertir("23:00:00");
                     $scope.datos.domingo.apertura = hora_convertir("01:00:00");
                     $scope.datos.domingo.cierre = hora_convertir("23:00:00");
    
                     
                    
                    //  $scope.rbxLoteriasComisionesChanged($scope.datos.ckbLoterias[0], false);
                    //  $scope.rbxLoteriasPagosCombinacionesChanged($scope.datos.ckbLoterias[0], false);
                    
                     $scope.rbxLoteriasComisionesChanged($scope.datos.ckbLoterias[0]);
                     $scope.rbxLoteriasPagosCombinacionesChanged($scope.datos.ckbLoterias[0]);
                     
                }else{
                    _convertir_apertura_y_cierre(false);
                    $scope.datos.cargando = false;
                    alert(response.data.mensaje);
                    return;
                }
                
            }
            ,
            function(response) {
                // Handle error here
                $scope.datos.cargando = false;
                console.log('Error jean: ', response);
                alert("Error");
            }
            );


                 //End nuevo
            }
            else if(esNuevo == 2){
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;
                window.scrollTo(0, 0);

                // PerfectScrollBar
                // $("html, body").animate({ scrollTop: 0 }, 600);
                // $(".ps-scrollbar-y").scrollTop = 0;
                $scope.datos.cargando = true;

                d.servidor = servidorGlobal;
                var jwt = helperService.createJWT(d);
                $http.post(rutaGlobal+"/api/bancas/get", {'action':'sp_bancas_actualizar', 'datos': jwt})
                .then(function(response){
                    $scope.datos.cargando = false;
                   console.log(response.data);
                   if(response.data.errores == 0){
                       d = response.data.banca
                    $('.form-group').addClass('is-filled');

                    llenarckbLoterias(response.data.loterias);
                    $scope.datos.id = d.id;
                    $scope.datos.descripcion = d.descripcion;
                    $scope.datos.codigo = d.codigo;
                    $scope.datos.ip = d.ip;
                    $scope.datos.dueno = d.dueno;
                    $scope.datos.localidad = d.localidad;
                    $scope.datos.porcentajeCaida = d.porcentajeCaida;
                    $scope.datos.balanceDesactivacion = d.balanceDesactivacion;
                    $scope.datos.limiteVenta = d.limiteVenta;
                    $scope.datos.descontar = d.descontar;
                    $scope.datos.deCada = d.deCada;
                    $scope.datos.minutosCancelarTicket = d.minutosCancelarTicket;
                    $scope.datos.piepagina1 = d.piepagina1;
                    $scope.datos.piepagina2 = d.piepagina2;
                    $scope.datos.piepagina3 = d.piepagina3;
                    $scope.datos.piepagina4 = d.piepagina4;
                    $scope.datos.estado = (d.status == 1) ? true : false;
                    $scope.datos.imprimirCodigoQr = (d.imprimirCodigoQr == 1) ? true : false;
    
                    
                    
                    $scope.datos.lunes.apertura = d.dias[0].pivot.horaApertura;
                    $scope.datos.lunes.cierre = d.dias[0].pivot.horaCierre;
                    $scope.datos.martes.apertura = d.dias[1].pivot.horaApertura;
                    $scope.datos.martes.cierre = d.dias[1].pivot.horaCierre;
                    $scope.datos.miercoles.apertura = d.dias[2].pivot.horaApertura;
                    $scope.datos.miercoles.cierre = d.dias[2].pivot.horaCierre;
                    $scope.datos.jueves.apertura = d.dias[3].pivot.horaApertura;
                    $scope.datos.jueves.cierre = d.dias[3].pivot.horaCierre;
                    $scope.datos.viernes.apertura = d.dias[4].pivot.horaApertura;
                    $scope.datos.viernes.cierre = d.dias[4].pivot.horaCierre;
                    $scope.datos.sabado.apertura = d.dias[5].pivot.horaApertura;
                    $scope.datos.sabado.cierre = d.dias[5].pivot.horaCierre;
                    $scope.datos.domingo.apertura = d.dias[6].pivot.horaApertura;
                    $scope.datos.domingo.cierre = d.dias[6].pivot.horaCierre;
                    
    
                    $scope.datos.ckbLoterias.forEach(function(valor, indice, array){
    
                        array[indice].existe = false;
    
                     });
    
    
                     $scope.datos.loteriasSeleccionadas = [];
                     $scope.datos.comisiones.loterias = $scope.datos.loteriasSeleccionadas;
                     $scope.datos.pagosCombinaciones.loterias = $scope.datos.loteriasSeleccionadas;
                     if(d.loterias != undefined){
    
                        console.log('editar bancas, loterias: ', d.loterias);
                        
    
                        d.loterias.forEach(function(valor, indice, array){
    
                            if($scope.datos.ckbLoterias.find(x => x.id == array[indice].id) != undefined){
                                let idx = $scope.datos.ckbLoterias.findIndex(x => x.id == parseInt(array[indice].id));
                                $scope.datos.ckbLoterias[idx].existe = true;
                                $scope.datos.loteriasSeleccionadas.push($scope.datos.ckbLoterias[idx]);
                            }else{
                                $scope.datos.ckbLoterias[indice].existe = false;
                            }
    
                         });
                    }
    
                    // $scope.datos.loteriasSeleccionadas.forEach(function(valor, indice, array){
                    //     if($scope.datos.comisiones.loterias.find(x => x.id == array[indice].idLoteria) == undefined){
                    //         $scope.datos.comisiones.loterias.push(array[indice]);
                    //     }
                    //     if($scope.datos.pagosCombinaciones.loterias.find(x => x.id == array[indice].idLoteria) == undefined){
                    //         $scope.datos.pagosCombinaciones.loterias.push(array[indice]);
                    //     }
                    // });
    
                    $scope.datos.comisiones.loterias = [];
                    d.comisiones.forEach(function(valor, indice, array){
                        if($scope.datos.ckbLoterias.find(x => x.id == array[indice].idLoteria) != undefined){
                            let idx = $scope.datos.ckbLoterias.findIndex(x => x.id == parseInt(array[indice].idLoteria));
                            if($scope.datos.ckbLoterias[idx].existe && $scope.datos.ckbLoterias[idx].descripcion != "COPIAR A TODAS"){
                                console.log('comision existe');
                                $scope.datos.ckbLoterias[idx].comisiones.directo = array[indice].directo;
                                $scope.datos.ckbLoterias[idx].comisiones.pale = array[indice].pale;
                                $scope.datos.ckbLoterias[idx].comisiones.tripleta = array[indice].tripleta;
                                $scope.datos.ckbLoterias[idx].comisiones.superPale = array[indice].superPale;
                                $scope.datos.ckbLoterias[idx].comisiones.pick3Straight = array[indice].pick3Straight;
                                $scope.datos.ckbLoterias[idx].comisiones.pick3Box = array[indice].pick3Box;
                                $scope.datos.ckbLoterias[idx].comisiones.pick4Straight = array[indice].pick4Straight;
                                $scope.datos.ckbLoterias[idx].comisiones.pick4Box = array[indice].pick4Box;
    
                                $scope.datos.comisiones.loterias.push($scope.datos.ckbLoterias[idx]);
                            }else{
                                console.log('comision noo existe');
                                $scope.datos.ckbLoterias[idx].comisiones.directo = null;
                                $scope.datos.ckbLoterias[idx].comisiones.pale = null;
                                $scope.datos.ckbLoterias[idx].comisiones.tripleta = null;
                                $scope.datos.ckbLoterias[idx].comisiones.superPale = null;
                                $scope.datos.ckbLoterias[idx].comisiones.pick3Straight = null;
                                $scope.datos.ckbLoterias[idx].comisiones.pick3Box = null;
                                $scope.datos.ckbLoterias[idx].comisiones.pick4Straight = null;
                                $scope.datos.ckbLoterias[idx].comisiones.pick4Box = null;
                            }
                        }
    
                    });
                    $scope.datos.comisiones.selectedLoteria = $scope.datos.comisiones.loterias[0];
    
                    $scope.datos.pagosCombinaciones.loterias = [];
                    d.pagosCombinaciones.forEach(function(valor, indice, array){
    
                        if($scope.datos.ckbLoterias.find(x => x.id == array[indice].idLoteria) != undefined){
                            let idx = $scope.datos.ckbLoterias.findIndex(x => x.id == parseInt(array[indice].idLoteria));
                            if($scope.datos.ckbLoterias[idx].existe && $scope.datos.ckbLoterias[idx].descripcion != "COPIAR A TODAS"){
                                console.log('pagosCombinaciones existe');
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = array[indice].primera;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = array[indice].segunda;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = array[indice].tercera;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = array[indice].primeraSegunda;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = array[indice].primeraTercera;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = array[indice].segundaTercera;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = array[indice].tresNumeros;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = array[indice].dosNumeros;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primerPago = array[indice].primerPago;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick3TodosEnSecuencia = array[indice].pick3TodosEnSecuencia;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick33Way = array[indice].pick33Way;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick36Way = array[indice].pick36Way;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick4TodosEnSecuencia = array[indice].pick4TodosEnSecuencia;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick44Way = array[indice].pick44Way;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick46Way = array[indice].pick46Way;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick412Way = array[indice].pick412Way;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick424Way = array[indice].pick424Way;
    
                                $scope.datos.pagosCombinaciones.loterias.push($scope.datos.ckbLoterias[idx]);
                            }else{
                                console.log('pagosCombinaciones noo existe');
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.primerPago = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick3TodosEnSecuencia = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick33Way = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick36Way = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick4TodosEnSecuencia = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick44Way = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick46Way = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick412Way = null;
                                $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick424Way = null;
                            }
                        }
    
                    });
                    $scope.datos.pagosCombinaciones.selectedLoteria = $scope.datos.pagosCombinaciones.loterias[0];
    
                    //console.log('comisiones: ', $scope.datos.comisiones.loterias, ' pagos: ', $scope.datos.pagosCombinaciones.loterias);
    
                    idx = $scope.datos.optionsUsuarios.findIndex(x => x.id == d.idUsuario);
                    $scope.datos.selectedUsuario = $scope.datos.optionsUsuarios[idx];
    
                    idx = $scope.datos.optionsMonedas.findIndex(x => x.id == d.idMoneda);
                    $scope.datos.selectedMoneda = $scope.datos.optionsMonedas[idx];
                    
                    console.log('monedaa: ' , d.idMoneda);
    
                    $scope.datos.gastos = d.gastos;
    
                    //console.log('editar, idx: ', idx, ' usuario: ', $scope.datos.selectedUsuario);
                    // $scope.rbxLoteriasComisionesChanged($scope.datos.ckbLoterias[0], false);
                    // $scope.rbxLoteriasPagosCombinacionesChanged($scope.datos.ckbLoterias[0], false);
                    $scope.rbxLoteriasComisionesChanged($scope.datos.ckbLoterias[0]);
                    $scope.rbxLoteriasPagosCombinacionesChanged($scope.datos.ckbLoterias[0]);
                    $timeout(function() {
                        // anything you want can go here and will safely be run on the next digest.
                        $('.selectpicker').selectpicker("refresh");
                      });
                      
                   }else{
                       _convertir_apertura_y_cierre(false);
                       $scope.datos.cargando = true;
                       alert(response.data.mensaje);
                       return;
                   }
                   
               }
               ,
               function(response) {
                   // Handle error here
                   $scope.datos.cargando = true;
                   console.log('Error jean: ', response);
                   alert("Error");
               }
               ); // END HTTP

               
            }

            else if(esNuevo == 3){
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;

                $('.form-group').addClass('is-filled');


                //Como estoy clonando el id tiene que ser igual a null
                $scope.datos.id = 0;
                $scope.datos.descripcion = d.descripcion;
                $scope.datos.codigo = d.codigo;
                $scope.datos.ip = d.ip;
                $scope.datos.dueno = d.dueno;
                $scope.datos.localidad = d.localidad;
                $scope.datos.porcentajeCaida = d.porcentajeCaida;
                $scope.datos.balanceDesactivacion = d.balanceDesactivacion;
                $scope.datos.limiteVenta = d.limiteVenta;
                $scope.datos.descontar = d.descontar;
                $scope.datos.deCada = d.deCada;
                $scope.datos.minutosCancelarTicket = d.minutosCancelarTicket;
                $scope.datos.piepagina1 = d.piepagina1;
                $scope.datos.piepagina2 = d.piepagina2;
                $scope.datos.piepagina3 = d.piepagina3;
                $scope.datos.piepagina4 = d.piepagina4;
                $scope.datos.estado = (d.status == 1) ? true : false;
                $scope.datos.imprimirCodigoQr = (d.imprimirCodigoQr == 1) ? true : false;

                
                
                $scope.datos.lunes.apertura = d.dias[0].pivot.horaApertura;
                $scope.datos.lunes.cierre = d.dias[0].pivot.horaCierre;
                $scope.datos.martes.apertura = d.dias[1].pivot.horaApertura;
                $scope.datos.martes.cierre = d.dias[1].pivot.horaCierre;
                $scope.datos.miercoles.apertura = d.dias[2].pivot.horaApertura;
                $scope.datos.miercoles.cierre = d.dias[2].pivot.horaCierre;
                $scope.datos.jueves.apertura = d.dias[3].pivot.horaApertura;
                $scope.datos.jueves.cierre = d.dias[3].pivot.horaCierre;
                $scope.datos.viernes.apertura = d.dias[4].pivot.horaApertura;
                $scope.datos.viernes.cierre = d.dias[4].pivot.horaCierre;
                $scope.datos.sabado.apertura = d.dias[5].pivot.horaApertura;
                $scope.datos.sabado.cierre = d.dias[5].pivot.horaCierre;
                $scope.datos.domingo.apertura = d.dias[6].pivot.horaApertura;
                $scope.datos.domingo.cierre = d.dias[6].pivot.horaCierre;
                

                $scope.datos.ckbLoterias.forEach(function(valor, indice, array){

                    array[indice].existe = false;

                 });


                 $scope.datos.loteriasSeleccionadas = [];
                 $scope.datos.comisiones.loterias = $scope.datos.loteriasSeleccionadas;
                 $scope.datos.pagosCombinaciones.loterias = $scope.datos.loteriasSeleccionadas;
                 if(d.loterias != undefined){

                    console.log('editar bancas, loterias: ', d.loterias);
                    

                    d.loterias.forEach(function(valor, indice, array){

                        if($scope.datos.ckbLoterias.find(x => x.id == array[indice].id) != undefined){
                            let idx = $scope.datos.ckbLoterias.findIndex(x => x.id == parseInt(array[indice].id));
                            $scope.datos.ckbLoterias[idx].existe = true;
                            $scope.datos.loteriasSeleccionadas.push($scope.datos.ckbLoterias[idx]);
                        }else{
                            $scope.datos.ckbLoterias[idx].existe = false;
                        }

                     });
                }

                // $scope.datos.loteriasSeleccionadas.forEach(function(valor, indice, array){
                //     if($scope.datos.comisiones.loterias.find(x => x.id == array[indice].idLoteria) == undefined){
                //         $scope.datos.comisiones.loterias.push(array[indice]);
                //     }
                //     if($scope.datos.pagosCombinaciones.loterias.find(x => x.id == array[indice].idLoteria) == undefined){
                //         $scope.datos.pagosCombinaciones.loterias.push(array[indice]);
                //     }
                // });

                $scope.datos.comisiones.loterias = [];
                d.comisiones.forEach(function(valor, indice, array){
                    if($scope.datos.ckbLoterias.find(x => x.id == array[indice].idLoteria) != undefined){
                        let idx = $scope.datos.ckbLoterias.findIndex(x => x.id == parseInt(array[indice].idLoteria));
                        if($scope.datos.ckbLoterias[idx].existe && $scope.datos.ckbLoterias[idx].descripcion != "COPIAR A TODAS"){
                            console.log('comision existe');
                            $scope.datos.ckbLoterias[idx].comisiones.directo = array[indice].directo;
                            $scope.datos.ckbLoterias[idx].comisiones.pale = array[indice].pale;
                            $scope.datos.ckbLoterias[idx].comisiones.tripleta = array[indice].tripleta;
                            $scope.datos.ckbLoterias[idx].comisiones.superPale = array[indice].superPale;
                            $scope.datos.ckbLoterias[idx].comisiones.pick3Straight = array[indice].pick3Straight;
                            $scope.datos.ckbLoterias[idx].comisiones.pick3Box = array[indice].pick3Box;
                            $scope.datos.ckbLoterias[idx].comisiones.pick4Straight = array[indice].pick4Straight;
                            $scope.datos.ckbLoterias[idx].comisiones.pick4Box = array[indice].pick4Box;

                            $scope.datos.comisiones.loterias.push($scope.datos.ckbLoterias[idx]);
                        }else{
                            console.log('comision noo existe');
                            $scope.datos.ckbLoterias[idx].comisiones.directo = null;
                            $scope.datos.ckbLoterias[idx].comisiones.pale = null;
                            $scope.datos.ckbLoterias[idx].comisiones.tripleta = null;
                            $scope.datos.ckbLoterias[idx].comisiones.superPale = null;
                            $scope.datos.ckbLoterias[idx].comisiones.pick3Straight = null;
                            $scope.datos.ckbLoterias[idx].comisiones.pick3Box = null;
                            $scope.datos.ckbLoterias[idx].comisiones.pick4Straight = null;
                            $scope.datos.ckbLoterias[idx].comisiones.pick4Box = null;
                        }
                    }

                });
                $scope.datos.comisiones.selectedLoteria = $scope.datos.comisiones.loterias[0];

                $scope.datos.pagosCombinaciones.loterias = [];
                d.pagosCombinaciones.forEach(function(valor, indice, array){

                    if($scope.datos.ckbLoterias.find(x => x.id == array[indice].idLoteria) != undefined){
                        let idx = $scope.datos.ckbLoterias.findIndex(x => x.id == parseInt(array[indice].idLoteria));
                        if($scope.datos.ckbLoterias[idx].existe && $scope.datos.ckbLoterias[idx].descripcion != "COPIAR A TODAS"){
                            console.log('pagosCombinaciones existe');
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = array[indice].primera;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = array[indice].segunda;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = array[indice].tercera;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = array[indice].primeraSegunda;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = array[indice].primeraTercera;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = array[indice].segundaTercera;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = array[indice].tresNumeros;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = array[indice].dosNumeros;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primerPago = array[indice].primerPago;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick3TodosEnSecuencia = array[indice].pick3TodosEnSecuencia;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick33Way = array[indice].pick33Way;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick36Way = array[indice].pick36Way;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick4TodosEnSecuencia = array[indice].pick4TodosEnSecuencia;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick44Way = array[indice].pick44Way;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick46Way = array[indice].pick46Way;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick412Way = array[indice].pick412Way;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick424Way = array[indice].pick424Way;

                            $scope.datos.pagosCombinaciones.loterias.push($scope.datos.ckbLoterias[idx]);
                        }else{
                            console.log('pagosCombinaciones noo existe');
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.primerPago = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick3TodosEnSecuencia = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick33Way = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick36Way = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick4TodosEnSecuencia = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick44Way = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick46Way = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick412Way = null;
                            $scope.datos.ckbLoterias[idx].pagosCombinaciones.pick424Way = null;
                        }
                    }

                });
                $scope.datos.pagosCombinaciones.selectedLoteria = $scope.datos.pagosCombinaciones.loterias[0];

                //console.log('comisiones: ', $scope.datos.comisiones.loterias, ' pagos: ', $scope.datos.pagosCombinaciones.loterias);

                idx = $scope.datos.optionsUsuarios.findIndex(x => x.id == d.idUsuario);
                $scope.datos.selectedUsuario = $scope.datos.optionsUsuarios[idx];



                $scope.datos.gastos = d.gastos;

                //console.log('editar, idx: ', idx, ' usuario: ', $scope.datos.selectedUsuario);
                // $scope.rbxLoteriasComisionesChanged($scope.datos.ckbLoterias[0], false);
                // $scope.rbxLoteriasPagosCombinacionesChanged($scope.datos.ckbLoterias[0], false);
                $scope.rbxLoteriasComisionesChanged($scope.datos.ckbLoterias[0]);
                $scope.rbxLoteriasPagosCombinacionesChanged($scope.datos.ckbLoterias[0]);
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                  });
            }
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

        $scope.actualizar = function(){
         
            //$scope.datos.horaCierre = moment($scope.datos.horaCierre, ['HH:mm']).format('HH:mm');
            
            console.log('actualizar: ', $scope.datos);
           
           

            if($scope.datos.descripcion == undefined || $scope.datos.descripcion == ""){
                alert("La descripcion no debe estar vacio");
                return;
            }
            if($scope.datos.codigo == undefined || $scope.datos.codigo == ""){
                alert("El codigo no debe estar vacio");
                return;
            }

            if($scope.datos.dueno == undefined || $scope.datos.dueno == ""){
                alert("El dueno no debe estar vacio");
                return;
            }

            if($scope.datos.localidad == undefined || $scope.datos.localidad == ""){
                alert("La localidad no debe estar vacio");
                return;
            }

            if(Number($scope.datos.balanceDesactivacion) != $scope.datos.balanceDesactivacion)
            {
                alert("El balanceDesactivacion pale debe ser numerico");
                return;
            }

            if(Number($scope.datos.limiteVenta) != $scope.datos.limiteVenta)
            {
                alert("El limiteVenta pale debe ser numerico");
                return;
            }

            if(Number($scope.datos.descontar) != $scope.datos.descontar)
            {
                alert("El descontar pale debe ser numerico");
                return;
            }

            if(Number($scope.datos.deCada) != $scope.datos.deCada)
            {
                alert("El deCada pale debe ser numerico");
                return;
            }

            if(Number($scope.datos.minutosCancelarTicket) != $scope.datos.minutosCancelarTicket)
            {
                alert("El minutoscancelarticket pale debe ser numerico");
                return;
            }


            if(Object.keys($scope.datos.ckbLoterias).length == 0){
                alert("Debe seleccionar al menos una loteria");
                return;
            }
            if(Object.keys($scope.datos.loterias).length == 0){
                alert("No hay loterias existentes");
                return;
            }

            if($scope.datos.optionsMonedas.length == 0){
                alert('No hay monedas, vuelva a actualizar la pagina');
                return;
            }

            var errores = false;

            $scope.datos.ckbLoterias.forEach(function(valor, indice, array){
                if(array[indice].existe == true){
                    // if(array[indice].sorteos.find(x => x.descripcion == "Directo") != undefined){
                    //     if($scope.empty(array[indice].pagosCombinaciones.primera, 'number') == true ||
                    //     $scope.empty(array[indice].pagosCombinaciones.primera, 'number') == true ||
                    //     $scope.empty(array[indice].pagosCombinaciones.primera, 'number') == true
                    //     ){
                    //         errores = true;
                    //         alert('Hay campos de premios vacios en la loteria ',  array[indice].descripcion, ' ', array[indice]);
                    //     }
                    // }
                    // if(array[indice].sorteos.find(x => x.descripcion == "Pale") != undefined){
                    //     if($scope.empty(array[indice].pagosCombinaciones.primeraSegunda, 'number') == true ||
                    //     $scope.empty(array[indice].pagosCombinaciones.primeraTercera, 'number') == true ||
                    //     $scope.empty(array[indice].pagosCombinaciones.segundaTercera, 'number') == true
                    //     ){
                    //         errores = true;
                    //         alert('Hay campos de premios vacios en la loteria ',  array[indice].descripcion, ' ', array[indice]);
                    //     }
                    // }
                    // if(array[indice].sorteos.find(x => x.descripcion == "Tripleta") != undefined){
                    //     if($scope.empty(array[indice].pagosCombinaciones.tresNumeros, 'number') == true ||
                    //     $scope.empty(array[indice].pagosCombinaciones.dosNumeros, 'number') == true
                    //     ){
                    //         errores = true;
                    //         alert('Hay campos de premios vacios en la loteria ',  array[indice].descripcion, ' ', array[indice]);
                    //     }
                    // }
                    // if(array[indice].sorteos.find(x => x.descripcion == "Super pale") != undefined){
                    //     if($scope.empty(array[indice].pagosCombinaciones.primerPago, 'number') == true
                    //     ){
                    //         errores = true;
                    //         alert('Hay campos de premios vacios en la loteria ',  array[indice].descripcion, ' ', array[indice]);
                    //     }
                    // }

                    if(array[indice].descripcion != "COPIAR A TODAS"){
                        if(pagosCombinacionesDatosValidos(array[indice]) == false){
                            errores = true;
                            alert('Hay campos de premios vacios en la loteria ',  array[indice].descripcion, ' ', array[indice]);
                        }
                    }
                    
                }//Primer if
            });


            if(errores)
                return;


            // if($scope.datos.piepagina1 == undefined || $scope.datos.piepagina1 == ""){
            //     alert("La piepagina1 no debe estar vacio");
            //     return;
            // }

            // if($scope.datos.piepagina2 == undefined || $scope.datos.piepagina2 == ""){
            //     alert("La piepagina2 no debe estar vacio");
            //     return;
            // }
            // if($scope.datos.piepagina3 == undefined || $scope.datos.piepagina3 == ""){
            //     alert("La piepagina3 no debe estar vacio");
            //     return;
            // }

            // if($scope.datos.piepagina4 == undefined || $scope.datos.piepagina4 == ""){
            //     alert("La piepagina4 no debe estar vacio");
            //     return;
            // }
            
            //El IP lo validaremos despues
            // if($scope.datos.ip == undefined || $scope.datos.ip == ""){
            //     alert("El abreviatura no debe estar vacio");
            //     return;
            // }

            if($scope.datos.btnCargando == true){
                return;
            }
            
            $scope.datos.status = ($scope.datos.estado) ? 1 : 0;
            $scope.datos.idUsuario = idUsuario;
            $scope.datos.idUsuarioBanca = $scope.datos.selectedUsuario.id;
            $scope.datos.idMoneda = $scope.datos.selectedMoneda.id;
            $scope.datos.loteriasSeleccionadas = $scope.datos.ckbLoterias;

            var datos = $scope.datos;
            delete datos['monedas'];
            delete datos['frecuencias'];
            delete datos['dias'];

            _convertir_apertura_y_cierre(true);
            $scope.datos.btnCargando = true;
            datos.servidor = servidorGlobal;
            var jwt = helperService.createJWT(datos);
          $http.post(rutaGlobal+"/api/bancas/guardar", {'action':'sp_bancas_actualizar', 'datos': jwt})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0){
                            $scope.inicializarDatos(true);
                            alert("Se ha guardado correctamente");
                            $scope.datos.mostrarFormEditar = false;
                            $scope.datos.btnCargando = false;
                            

                    // if($scope.datos.id == 0)
                    //     {
                    //         $scope.inicializarDatos(true);
                    //         $scope.datos.mostrarFormEditar = false;
                    //         _convertir_apertura_y_cierre(false);
                    //         alert("Se ha guardado correctamente");
                    //     }
                    // else{
                    //     //$scope.inicializarDatos(true);
                    //     $scope.datos.status = ($scope.datos.status == 1) ? true : false;
                    //     _convertir_apertura_y_cierre(false);
                    //     $scope.datos.bancas = response.data.bancas;
                    //     $scope.editar(2, response.data.banca[0]);
                    //     alert("Se ha guardado correctamente");
                    // }
                }else{
                    _convertir_apertura_y_cierre(false);
                    $scope.datos.btnCargando = false;
                    alert(response.data.mensaje);
                    return;
                }
                
            }
            ,
            function(response) {
                // Handle error here
                $scope.datos.btnCargando = false;
                console.log('Error jean: ', response);
                alert("Error");
            }
            );
        

        }

        function pagosCombinacionesDatosValidos(loteria){
            var validos = true;
            if(loteria.sorteos.find(x => x.descripcion == "Directo") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.primera, 'number') == true ||
                $scope.empty(loteria.pagosCombinaciones.primera, 'number') == true ||
                $scope.empty(loteria.pagosCombinaciones.primera, 'number') == true
                ){
                    validos = false;
                }
            }
            if(loteria.sorteos.find(x => x.descripcion == "Pale") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.primeraSegunda, 'number') == true ||
                $scope.empty(loteria.pagosCombinaciones.primeraTercera, 'number') == true ||
                $scope.empty(loteria.pagosCombinaciones.segundaTercera, 'number') == true
                ){
                    validos = false;
                }
            }
            if(loteria.sorteos.find(x => x.descripcion == "Tripleta") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.tresNumeros, 'number') == true ||
                $scope.empty(loteria.pagosCombinaciones.dosNumeros, 'number') == true
                ){
                    validos = false;
                }
            }
            if(loteria.sorteos.find(x => x.descripcion == "Super pale") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.primerPago, 'number') == true
                ){
                    validos = false;
                }
            }

            if(loteria.sorteos.find(x => x.descripcion == "Pick 3 Straight") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.pick3TodosEnSecuencia, 'number') == true
                ){
                    validos = false;
                }
            }

            if(loteria.sorteos.find(x => x.descripcion == "Pick 3 Box") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.pick33Way, 'number') == true
                ){
                    validos = false;
                }

                if($scope.empty(loteria.pagosCombinaciones.pick36Way, 'number') == true
                ){
                    validos = false;
                }
            }

            if(loteria.sorteos.find(x => x.descripcion == "Pick 4 Straight") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.pick4TodosEnSecuencia, 'number') == true
                ){
                    validos = false;
                }
            }

            if(loteria.sorteos.find(x => x.descripcion == "Pick 4 Box") != undefined){
                if($scope.empty(loteria.pagosCombinaciones.pick44Way, 'number') == true
                ){
                    validos = false;
                }

                if($scope.empty(loteria.pagosCombinaciones.pick46Way, 'number') == true
                ){
                    validos = false;
                }

                if($scope.empty(loteria.pagosCombinaciones.pick412Way, 'number') == true
                ){
                    validos = false;
                }
                if($scope.empty(loteria.pagosCombinaciones.pick424Way, 'number') == true
                ){
                    validos = false;
                }
            }

            return validos;
        }


        $scope.eliminar = function(d){
            console.log('bancas eliminar: ',d);
            $http.post(rutaGlobal+"/api/bancas/eliminar", {'action':'sp_bancas_elimnar', 'datos': d})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0)
                {
                    $scope.inicializarDatos(true);
                    alert(response.data.mensaje);
                }
                
            },
            function(response) {
                // Handle error here
                //console.log('Error jean: ', response);
                alert("Error");
            });
        }
       

        $scope.verificarLoteriasSeleccionadas = function(){
            $scope.datos.loteriasSeleccionadas.forEach(function(valor, indice, array){
                if($scope.datos.comisiones.loterias.find(x => x.id == array[indice].id) == undefined){

                    let idx = $scope.datos.comisiones.loterias.findIndex(x => x.id == array[indice].id);
                    $scope.datos.comisiones.loterias.push(array[indice]);
                    // $scope.datos.loteriasSeleccionadas.splice(idx,1);
                }
                if($scope.datos.pagosCombinaciones.loterias.find(x => x.id == array[indice].id) == undefined){

                    let idx = $scope.datos.pagosCombinaciones.loterias.findIndex(x => x.id == array[indice].id);
                    $scope.datos.pagosCombinaciones.loterias.push(array[indice]);
                    // $scope.datos.loteriasSeleccionadas.splice(idx,1);
                }
            });

            $scope.datos.comisiones.loterias.forEach(function(valor, indice, array){
                if($scope.datos.loteriasSeleccionadas.find(x => x.id == array[indice].id) == undefined){
                    let idx = $scope.datos.comisiones.loterias.findIndex(x => x.id == array[indice].id);
                    $scope.datos.comisiones.loterias.splice(idx,1);
                }
            });
            $scope.datos.pagosCombinaciones.loterias.forEach(function(valor, indice, array){
                if($scope.datos.loteriasSeleccionadas.find(x => x.id == array[indice].id) == undefined){
                    let idx = $scope.datos.pagosCombinaciones.loterias.findIndex(x => x.id == array[indice].id);
                    $scope.datos.pagosCombinaciones.loterias.splice(idx,1);
                }
            });
        }

        $scope.ckbLoterias_changed = function(check, d){
           // console.log('ckbSorteos_changed: ', d);
            if(d.existe){
                $scope.datos.loteriasSeleccionadas.push(d);
            }
            else{
                if($scope.datos.loteriasSeleccionadas.find(x => x.id == d.id) != undefined){

                    let idx = $scope.datos.loteriasSeleccionadas.findIndex(x => x.id == d.id);
                    $scope.datos.loteriasSeleccionadas.splice(idx,1);
                }
            }

            $scope.verificarLoteriasSeleccionadas();
            
        }


        $scope.ckbPermisosAdicionales_changed = function(check, d){
            //console.log('ckbPermisosAdicionales changed: ', check);
            if(d.existe){
                $scope.datos.dias.push(d);
            }
            else{
                if($scope.datos.dias.find(x => x.idDia == d.idDia) != undefined){

                    let idx = $scope.datos.dias.findIndex(x => x.idDia == d.idDia);
                    $scope.datos.dias.splice(idx,1);
                }
            }
            
        }

        $scope.rbxLoteriasComisionesChanged = function(loteria, first = null){
            $scope.datos.selectedLoteriaComisiones = loteria;
            $scope.datos.indexLoteriaComisiones = $scope.datos.ckbLoterias.findIndex( x => x.id == loteria.id);
        }

        $scope.rbxLoteriasPagosCombinacionesChanged = function(loteria, first = null){
            /* Si el parametro opcionar first es igual a "true" estonces eso quiere decir que es el primer elemento del ngRepeat loteria entonces la loteria dada se seleccionadara
               si el parametro opcionar first es igual a "null" eso quiere decir que el parametro no se ha usado entonces la loteria dada se seleccionadara
               de lo contrario la loteria no podra seleccionarse */
               
         
                $scope.datos.selectedLoteriaPagosCombinaciones = loteria;
                $scope.datos.indexLoteriaPagosCombinaciones = $scope.datos.ckbLoterias.findIndex( x => x.id == loteria.id);
                   
            
        }

        // $scope.rbxLoteriasComisionesChanged = function(loteria, first = null){
        //     /* Si el parametro opcionar first es igual a "true" estonces eso quiere decir que es el primer elemento del ngRepeat loteria entonces la loteria dada se seleccionadara
        //        si el parametro opcionar first es igual a "null" eso quiere decir que el parametro no se ha usado entonces la loteria dada se seleccionadara
        //        de lo contrario la loteria no podra seleccionarse */
               
        //       if($scope.datos.id > 0){
        //             if(first != null)
        //             {
        //                 $scope.datos.selectedLoteriaComisiones = loteria;
        //             }else{
        //                 let idx = $scope.datos.ckbLoterias.findIndex( x => x.id == $scope.datos.selectedLoteriaComisiones.id);

        //                 $scope.datos.ckbLoterias[idx].comisiones.directo = $scope.datos.selectedLoteriaComisiones.directo;
        //                 $scope.datos.ckbLoterias[idx].comisiones.pale = $scope.datos.selectedLoteriaComisiones.pale;
        //                 $scope.datos.ckbLoterias[idx].comisiones.tripleta = $scope.datos.selectedLoteriaComisiones.tripleta;

        //                 console.log('antes: ', $scope.datos.selectedLoteriaComisiones);
        //                 $scope.datos.selectedLoteriaComisiones = loteria;
        //                 console.log('despues: ', $scope.datos.selectedLoteriaComisiones);
        //             }
        //       }else{
        //         if(first ==true)
        //         {
        //             $scope.datos.ckbLoterias.forEach(function(valor, indice, array){
        //                 let idx = $scope.datos.ckbLoterias.findIndex( x => x.id == array[indice].id);
        //                 $scope.datos.ckbLoterias[idx].comisiones.directo = 0;
        //                 $scope.datos.ckbLoterias[idx].comisiones.pale = 0;
        //                 $scope.datos.ckbLoterias[idx].comisiones.tripleta = 0;
        //             });
        //             $scope.datos.selectedLoteriaComisiones = loteria;
        //         }else{
        //             let idx = $scope.datos.ckbLoterias.findIndex( x => x.id == $scope.datos.selectedLoteriaComisiones.id);

        //             $scope.datos.ckbLoterias[idx].comisiones.directo = $scope.datos.selectedLoteriaComisiones.directo;
        //             $scope.datos.ckbLoterias[idx].comisiones.pale = $scope.datos.selectedLoteriaComisiones.pale;
        //             $scope.datos.ckbLoterias[idx].comisiones.tripleta = $scope.datos.selectedLoteriaComisiones.tripleta;


        //             $scope.datos.selectedLoteriaComisiones = loteria;
        //         }
        //       }
    
        //     //console.log('radiobotton: ', loteria.sorteos);
        // }

        // $scope.rbxLoteriasPagosCombinacionesChanged = function(loteria, first = null){
        //     /* Si el parametro opcionar first es igual a "true" estonces eso quiere decir que es el primer elemento del ngRepeat loteria entonces la loteria dada se seleccionadara
        //        si el parametro opcionar first es igual a "null" eso quiere decir que el parametro no se ha usado entonces la loteria dada se seleccionadara
        //        de lo contrario la loteria no podra seleccionarse */
               
        //     if($scope.datos.id > 0){
        //         if(first != null)
        //         {
        //             $scope.datos.selectedLoteriaPagosCombinaciones = loteria;
        //         }else{
        //             let idx = $scope.datos.ckbLoterias.findIndex( x => x.id == $scope.datos.selectedLoteriaPagosCombinaciones.id);
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = $scope.datos.selectedLoteriaPagosCombinaciones.primera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = $scope.datos.selectedLoteriaPagosCombinaciones.segunda;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = $scope.datos.selectedLoteriaPagosCombinaciones.tercera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = $scope.datos.selectedLoteriaPagosCombinaciones.primeraSegunda;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = $scope.datos.selectedLoteriaPagosCombinaciones.primeraTercera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = $scope.datos.selectedLoteriaPagosCombinaciones.segundaTercera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = $scope.datos.selectedLoteriaPagosCombinaciones.tresNumeros;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = $scope.datos.selectedLoteriaPagosCombinaciones.dosNumeros;

        //             $scope.datos.selectedLoteriaPagosCombinaciones = loteria;
        //         }
        //     }else{
        //         if(first != null)
        //         {
        //             $scope.datos.ckbLoterias.forEach(function(valor, indice, array){
        //                 let idx = $scope.datos.ckbLoterias.findIndex( x => x.id == array[indice].id);
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = 0;
        //                 $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = 0;
        //             });
        //             $scope.datos.selectedLoteriaPagosCombinaciones = $scope.datos.ckbLoterias[0];
        //         }else{
        //             let idx = $scope.datos.ckbLoterias.findIndex( x => x.id == $scope.datos.selectedLoteriaPagosCombinaciones.id);
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.primera = $scope.datos.selectedLoteriaPagosCombinaciones.primera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.segunda = $scope.datos.selectedLoteriaPagosCombinaciones.segunda;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.tercera = $scope.datos.selectedLoteriaPagosCombinaciones.tercera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraSegunda = $scope.datos.selectedLoteriaPagosCombinaciones.primeraSegunda;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.primeraTercera = $scope.datos.selectedLoteriaPagosCombinaciones.primeraTercera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.segundaTercera = $scope.datos.selectedLoteriaPagosCombinaciones.segundaTercera;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.tresNumeros = $scope.datos.selectedLoteriaPagosCombinaciones.tresNumeros;
        //             $scope.datos.ckbLoterias[idx].pagosCombinaciones.dosNumeros = $scope.datos.selectedLoteriaPagosCombinaciones.dosNumeros;

        //             $scope.datos.selectedLoteriaPagosCombinaciones = loteria;
        //         }
        //     }
    
        //     //console.log('radiobotton: ', loteria.sorteos);
        // }

        $scope.comisionSorteo = function(monto, idLoteria, idSorteo){
            let idx = $scope.datos.comisiones.selectedLoteria.sorteos.findIndex(x => x.id == idSorteo && x.pivot.idLoteria == idLoteria);
            $scope.datos.comisiones.selectedLoteria.sorteos[idx].monto = monto;
            //console.log($scope.datos.comisiones.selectedLoteria.sorteos[idx]);
        }


        $scope.existeSorteo = function(sorteo, es_comisiones = true){
            if(es_comisiones == false)
            
            var existe = false;
            if(es_comisiones){
                if($scope.datos.selectedLoteriaComisiones.sorteos == undefined)
                return false;
            
                $scope.datos.selectedLoteriaComisiones.sorteos.forEach(function(valor, indice, array){
                    //console.log('existesorteo: parametro: ', sorteo, ' varia: ', array[indice].descripcion);
                    if(sorteo == array[indice].descripcion)
                        existe = true;
                });
            }else{
                if($scope.datos.selectedLoteriaPagosCombinaciones.sorteos == undefined)
                return false;
            
                $scope.datos.selectedLoteriaPagosCombinaciones.sorteos.forEach(function(valor, indice, array){
                    //console.log('existesorteo: parametro: ', sorteo, ' varia: ', array[indice].descripcion);
                    if(sorteo == array[indice].descripcion)
                        existe = true;
                });
            }

            //console.log('sorteos: ',$scope.datos.selectedLoteriaPagosCombinaciones.sorteos,' sorteo: ', sorteo, ' ,pagos: ', $scope.datos.selectedLoteriaPagosCombinaciones.sorteos.find(x => x.descripcion == sorteo))
            // console.log('existesorteo: ', $scope.datos.selectedLoteriaPagosCombinaciones);
            return existe;
        }
       

        $scope.gastosAdd = function(){
            $scope.datos.gasto.fechaInicio = $('#gastosFecha').val();
            console.log('gastoAdd: ', $scope.datos.gasto.index);
            //console.log('gastoAdd find: ', $scope.datos.gastos.find(x => x.descripcion == $scope.datos.gasto.descripcion));

            if($scope.empty($scope.datos.gasto.descripcion,'string')){
                alert('La descripcion esta vacia');
                    return;
            }
            if($scope.empty($scope.datos.gasto.monto,'number')){
                alert('El monto es incorrecto');
                    return;
            }
            if($scope.datos.gasto.frecuencia.descripcion.toLowerCase() == "semanal"){
               if($scope.empty($scope.datos.selectedDia, 'string')){
                    alert('Debe seleccionar un dia');
                    return;
               }
            }

            if($scope.datos.gastos.find(x => x.descripcion == $scope.datos.gasto.descripcion) != undefined){
                let idx = $scope.datos.gastos.findIndex(x => x.descripcion == $scope.datos.gasto.descripcion);
                if(idx != $scope.datos.gasto.index){
                    alert('La descripcion ya existe debe elegir una diferente idx:', idx, ' index:', $scope.datos.gasto.index);
                    return;
                }
            }

            
            if($scope.datos.gasto.index != null){
                
                

                $scope.datos.gastos[$scope.datos.gasto.index].descripcion = $scope.datos.gasto.descripcion;
                $scope.datos.gastos[$scope.datos.gasto.index].fechaInicio = $scope.datos.gasto.fechaInicio;
                $scope.datos.gastos[$scope.datos.gasto.index].monto = $scope.datos.gasto.monto;
                $scope.datos.gastos[$scope.datos.gasto.index].frecuencia = $scope.datos.gasto.frecuencia;
                if($scope.datos.gasto.frecuencia.descripcion.toLowerCase() == "semanal"){
                    $scope.datos.gastos[$scope.datos.gasto.index].idDia = $scope.datos.selectedDia.id;
                    $('#btnCloseModalGasto').click();
                    // alert('Dentro:' + $scope.datos.selectedDia.id);
                }else{
                    $scope.datos.gastos[$scope.datos.gasto.index].idDia = null;
                }
                
            }else{
                var idDia = null;
                if($scope.datos.gasto.frecuencia.descripcion.toLowerCase() == "semanal"){
                    idDia = $scope.datos.selectedDia.id;
                }else{
                    idDia = null;
                }
                $scope.datos.gastos.push(
                    {
                        'id' : 0,
                        'descripcion' : $scope.datos.gasto.descripcion,
                        'fecha' : $scope.datos.gasto.fecha,
                        'monto' : $scope.datos.gasto.monto,
                        'frecuencia' : $scope.datos.gasto.frecuencia,
                        'fechaInicio' : $scope.datos.gasto.fechaInicio,
                        'fechaProximoGasto' : $scope.datos.gasto.fechaProximoGasto,
                        'idDia' : idDia,
                    });

                    $scope.datos.gasto.index = null;
                    $scope.datos.gasto.descripcion = null;
                    $scope.datos.gasto.fecha = moment().format('YYYY/MM/DD');
                    $('#btnCloseModalGasto').click();
            }
            
           
            //console.log('gastos: ', $scope.datos.gasto);
        }

        $scope.toFecha = function(fecha){
            if(fecha != undefined && fecha != null )
                return new Date(fecha);
            else
                return '-';
        }



        $scope.gastoEditar = function(esNuevo, d){
            $('#fechaGasto').addClass('is-filled');
            
            $('#modal-gasto').modal('show');

            if(esNuevo){
                $scope.rbxFrecuenciasChanged($scope.datos.radioFrecuencias[0]);
                $scope.datos.gasto.index = null;
                $scope.datos.gasto.descripcion = null;
                $scope.datos.gasto.monto = null;
                $scope.datos.gasto.fechaInicio = moment().format('YYYY/MM/DD');
            }
            else{
                //$scope.inicializarDatos();
                //$scope.datos.mostrarFormEditar = true;

                $('.form-group').addClass('is-filled');
                $('#modal-gasto').modal('toggle');

                $scope.rbxFrecuenciasChanged(d.frecuencia);
                $scope.datos.gasto.index = $scope.datos.gastos.findIndex( x => x.descripcion == d.descripcion);
                $scope.datos.gasto.monto = Number(d.monto);
                $scope.datos.gasto.descripcion = d.descripcion;
                // $scope.datos.gasto.fechaInicio = d.fechaInicio;
                if(d.frecuencia.descripcion.toLowerCase() == "semanal"){
                    $scope.datos.selectedDia = $scope.datos.optionsDias[$scope.datos.optionsDias.findIndex(x => x.id == d.idDia)];
                    console.log("Semaanal: " + d);
                }


                $('#modal-gasto').modal('toggle');

                
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    $('.selectpicker').selectpicker("refresh");
                  });
            }
        }

        function seleccionarFrecuencia(){

        }


        $scope.rbxFrecuenciasChanged = function(frecuencia, first = null){
            if(typeof frecuencia === 'undefined')
                return;

            //Verificamos si es un objeto
            if(typeof frecuencia === 'object')
                $scope.datos.gasto.frecuencia = frecuencia;
            //Verificamos si es un numero, si es asi entonces eso quiere decir que ese numero es el id de la frecuencia por lo tanto buscamos la frecuencia por dicho id
            else if(typeof frecuencia === 'number'){
                if($scope.datos.radioFrecuencias.find( x => x.id == frecuencia) )
                    $scope.datos.gasto.frecuencia = $scope.datos.radioFrecuencias.find( x => x.id == frecuencia);
            }
                
            $scope.datos.indexFrecuencia = $scope.datos.radioFrecuencias.findIndex( x => x.id == $scope.datos.gasto.frecuencia.id); 
            $scope.datos.radioFrecuencias.forEach(function(valor, indice, array){
                if($scope.datos.gasto.frecuencia.id == array[indice].id){
                    $('#labelFrecuencia'+array[indice].descripcion).addClass('active');
                }else{
                    $('#labelFrecuencia'+array[indice].descripcion).removeClass('active');
                }
            });

            console.log('indexFrecuencia: ', $scope.datos.indexFrecuencia);
        }

        $scope.gastoEliminar = function(gasto){
            if($scope.datos.gastos.find(x => x.descripcion == gasto.descripcion) != undefined){
    
                let idx = $scope.datos.gastos.findIndex(x => x.descripcion == gasto.descripcion);
                $scope.datos.gastos.splice(idx,1);
            }
        }


        $scope.copiarATodasPremios = function(){
            var idxCopiarATodas = $scope.datos.ckbLoterias.findIndex(x => x.descripcion == "COPIAR A TODAS");
            
            for (let index = 0; index < Object.keys($scope.datos.ckbLoterias).length; index++) {
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primera) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.primera = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primera;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.segunda) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.segunda = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.segunda;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.tercera) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.tercera = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.tercera;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primeraSegunda) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.primeraSegunda = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primeraSegunda;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primeraTercera) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.primeraTercera = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primeraTercera;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.segundaTercera) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.segundaTercera = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.segundaTercera;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.tresNumeros) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.tresNumeros = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.tresNumeros;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.dosNumeros) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.dosNumeros = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.dosNumeros;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primerPago) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.primerPago = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.primerPago;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick3TodosEnSecuencia) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick3TodosEnSecuencia = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick3TodosEnSecuencia;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick33Way) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick33Way = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick33Way;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick36Way) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick36Way = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick36Way;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick4TodosEnSecuencia) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick4TodosEnSecuencia = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick4TodosEnSecuencia;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick44Way) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick44Way = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick44Way;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick46Way) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick46Way = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick46Way;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick412Way) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick412Way = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick412Way;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick424Way) == false)
                    $scope.datos.ckbLoterias[index].pagosCombinaciones.pick424Way = $scope.datos.ckbLoterias[idxCopiarATodas].pagosCombinaciones.pick424Way;
                
            }
        }


        $scope.copiarATodasComisiones = function(){
            var idxCopiarATodas = $scope.datos.ckbLoterias.findIndex(x => x.descripcion == "COPIAR A TODAS");
            
            for (let index = 0; index < Object.keys($scope.datos.ckbLoterias).length; index++) {
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.directo) == false)
                    $scope.datos.ckbLoterias[index].comisiones.directo = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.directo;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pale) == false)
                    $scope.datos.ckbLoterias[index].comisiones.pale = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pale;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.tripleta) == false)
                    $scope.datos.ckbLoterias[index].comisiones.tripleta = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.tripleta;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.superPale) == false)
                    $scope.datos.ckbLoterias[index].comisiones.superPale = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.superPale;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick3Straight) == false)
                    $scope.datos.ckbLoterias[index].comisiones.pick3Straight = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick3Straight;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick3Box) == false)
                    $scope.datos.ckbLoterias[index].comisiones.pick3Box = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick3Box;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick4Straight) == false)
                    $scope.datos.ckbLoterias[index].comisiones.pick4Straight = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick4Straight;
                if(helperService.empty($scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick4Box) == false)
                    $scope.datos.ckbLoterias[index].comisiones.pick4Box = $scope.datos.ckbLoterias[idxCopiarATodas].comisiones.pick4Box;
               
            }
        }

    })



  

