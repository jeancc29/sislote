myApp
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        // function horaToFecha(hora){
        //     var fecha, time, ano, mes, dia, hh, min, ss;
        //     fecha = new Date();
        //     time = hora.split(':');
        //     ano = fecha.getFullYear();
        //     mes = fecha.getMonth() + 1;
        //     dia = fecha.getDate();
        //     hh = time[0];
        //     min = time[1];
        //     ss = time[2];

        //     var fecha = new Date(ano, mes, dia, hh, min, ss);

        //    // console.log('horaToFecha, fecha: ', fecha);

        //     return fecha;
        // }

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
                    phora = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    phora = phora.toString() + ":" + a[1];
                    //console.log('actualizar: convertido: ', phora); 
                }
                else{
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


         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "loteriasJugadasDashboard":[],
            "selectedLoteria" : {}
        }

        
        $scope.inicializarDatos = function(todos, idUsuario = 0){
               
            $http.get(rutaGlobal+"/api/bancas")
             .then(function(response){
                //console.log('Bancas: ', response.data);

               


               
                

                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    // $('.selectpicker').selectpicker("refresh");
                    // $('.selectpicker').selectpicker('val', [])
                  })
               
                
                
            });

           
           
       
        }
        

        $scope.load = function(codigo_usuario){
            $scope.datos.loteriasJugadasDashboard = loteriasJugadasDashboardGlobal;
            
        }


        

        $scope.cambiarLoteria = function(loteria, first = null){
            if(first != null && first == true){
                $scope.datos.selectedLoteria = loteria;
            }
            else if(first == null){
                $scope.datos.selectedLoteria = loteria;
            }
        }
       
        

       


        
       


    })



  

