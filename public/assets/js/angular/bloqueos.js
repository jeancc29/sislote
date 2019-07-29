myApp
    .controller("myController", function($scope, $http, $timeout){
        $scope.busqueda = "";
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

        $scope.datos =  {
            "idUsuario":0,
            "idLoteria":0,
            "quiniela": 0,
            "pale" : 0,
            "tripleta" : 0,
            "estado":true,
            "dias": [],
            "horaCierre": moment().format('YYYY/MM/DD'),

            "ckbDias": [],
            "mostrarFormEditar" : false,

            "optionsLoterias" : [],
            "selectedLoteria" : {},
            "optionsSorteos" : [],
            "selectedSorteo" : {},


            "jugada" : null,
            "monto" : null,
            "fechaDesde" : new Date(),
            "fechaHasta" : new Date(),
            "bloqueosJugadas" : [],
            "bloqueoJugada" : {
                "idLoteria" : 0,
                "idUsuario" : 0,
                "jugada" : null,
                "monto" : null,
                "fechaDesde" : new Date(),
                "fechaHasta" : new Date(),

                "optionsLoterias" : [],
                "selectedLoteria" : {},
            }
        }


        $scope.inicializarDatos = function(idLoteria){
            

            $http.get(rutaGlobal+"/api/bloqueos")
             .then(function(response){
                console.log('Loteria ajav: ', response.data);

                //Datos bloqueosJugadas
                $scope.datos.bloqueoJugada.jugada = null;
                $scope.datos.bloqueoJugada.monto = null;
                $scope.datos.bloqueoJugada.fechaDesde = new Date();
                $scope.datos.bloqueoJugada.fechaHasta = new Date();

                // if(todos){
                //     $scope.datos.idLoteria = 0;
                //     $scope.datos.descripcion = null,
                //     $scope.datos.abreviatura = null,
                //     $scope.datos.estado = true;
                //     $scope.datos.dias = [];
                //     $scope.datos.horaCierre = moment().format('YYYY/MM/DD');
                //     $scope.datos.ckbDias = [];
                   

                //     var jsonDias = JSON.parse(response.data[0].dias);
                //     jsonDias.forEach(function(valor, indice, array){
                //         $scope.datos.ckbDias.push({'idDia' :array[indice].idDia, 'descripcion': array[indice].descripcion, 'existe' : false});
                //     });
                    
                //    }



                $scope.datos.loterias = response.data.loterias;
                $scope.datos.optionsLoterias = response.data.loterias;
                $scope.datos.bloqueoJugada.optionsLoterias = response.data.loterias;
                
                let idx = 0;
                if(idLoteria > 0)
                    idx = $scope.datos.optionsLoterias.findIndex(x => x.id == idLoteria);



                $scope.datos.selectedLoteria = $scope.datos.optionsLoterias[idx];
                $scope.datos.bloqueoJugada.selectedLoteria = $scope.datos.bloqueoJugada.optionsLoterias[idx];


                if($scope.datos.bloqueoJugada.selectedLoteria.bloqueosjugadas != undefined){
                    $scope.datos.bloqueoJugada.bloqueosJugadas = $scope.datos.bloqueoJugada.selectedLoteria.bloqueosjugadas;
                }else{
                    $scope.datos.bloqueoJugada.bloqueosJugadas = [];
                }

                

                $scope.datos.quiniela = $scope.datos.selectedLoteria.quiniela;
                $scope.datos.pale = $scope.datos.selectedLoteria.pale;
                $scope.datos.tripleta = $scope.datos.selectedLoteria.tripleta;

                $scope.datos.optionsSorteos = response.data.sorteos;
                $scope.datos.selectedSorteo = $scope.datos.optionsSorteos[0];



                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    $('#multiselect').selectpicker("refresh");
                    $('.selectpicker').selectpicker("refresh");
                    //$('#cbxLoteriasBuscarJugada').selectpicker('val', [])
                  })
                
            });
       
        }
        

        $scope.load = function(idUsuario){
            $scope.datos.idUsuario = idUsuario;
            $scope.datos.bloqueoJugada.idUsuario = idUsuario;
            $scope.inicializarDatos(0);
           
        }

      


        

        $scope.loteria_obtener_por_id = function(){

            $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_loterias_obtener_por_id'})
            .then(function(response){
               console.log('Loteria ajav: ', JSON.parse(response.data[0].dias));

               $scope.datos.loterias =JSON.parse(response.data[0].loteriasActivas);
               $scope.datos.ckbDias =JSON.parse(response.data[0].dias);

               console.log('Dentro load: ',moment().fromNow());
           });

        }
        

        $scope.actualizar = function(){
            //$("nav").find(".navbar-form").get(0).outerHTML

            console.log($("nav").find(".navbar-form").get(0));
         
            //$scope.datos.horaCierre = moment($scope.datos.horaCierre, ['HH:mm']).format('HH:mm');
            

            if(Number($scope.datos.quiniela) != $scope.datos.quiniela)
            {
                alert("El monto quiniela debe ser numerico");
                return;
            }

            if(Number($scope.datos.pale) != $scope.datos.pale)
            {
                alert("El monto pale debe ser numerico");
                return;
            }
           
            if(Number($scope.datos.tripleta) != $scope.datos.tripleta)
            {
                alert("El monto tripleta debe ser numerico");
                return;
            }

            $scope.datos.idLoteria = $scope.datos.selectedLoteria.id;
   
          
          $http.post(rutaGlobal+"/api/bloqueos/loterias/guardar", {'action':'sp_bloqueosLoterias_actualiza', 'datos': $scope.datos})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.idLoteria);
                    alert("Se ha guardado correctamente");
                }
            });

        }


        $scope.eliminar = function(d){
            $http.post($scope.ROOT_PATH +"clases/consultaajax.php", {'action':'sp_loterias_elimnar', 'datos': d})
             .then(function(response){
                console.log(response.data[0][0]);
                var json = JSON.parse(response.data[0][0]);
                console.log(json);
                if(json[0].errores == 0)
                {
                    $scope.inicializarDatos(0);
                    alert(json[0].mensaje);
                }
                
            });
        }




        $scope.actualizar_bloqueo_jugada = function(){
         
            //$scope.datos.horaCierre = moment($scope.datos.horaCierre, ['HH:mm']).format('HH:mm');
            

            if(Number($scope.datos.bloqueoJugada.jugada) != $scope.datos.bloqueoJugada.jugada)
            {
                alert("La jugada debe ser numerica");
                return;
            }

            if(Number($scope.datos.bloqueoJugada.monto) != $scope.datos.bloqueoJugada.monto)
            {
                alert("El monto debe ser numerico");
                return;
            }
           

           

            $scope.datos.bloqueoJugada.idLoteria = $scope.datos.bloqueoJugada.selectedLoteria.id;
            // $scope.datos.fechaDesde = moment($('#fechaDesde').val(), ['YYYY-MM-DD']).format('YYYY-MM-DD');
            // $scope.datos.fechaHasta = moment($('#fechaHasta').val(), ['YYYY-MM-DD']).format('YYYY-MM-DD');

            
   
          
          $http.post(rutaGlobal+"/api/bloqueos/jugadas/guardar", {'action':'sp_bloqueosJugadas_actualiza', 'datos': $scope.datos.bloqueoJugada})
             .then(function(response){
                console.log(response);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.idLoteria);
                    alert("Se ha guardado correctamente");
                }
            });

        }



        $scope.eliminar_bloqueo_jugada = function(d){
            console.log('eliminar_jugada: ', d);
            
            $http.post(rutaGlobal+"/api/bloqueos/jugadas/eliminar", {'action':'sp_bloqueosJugadas_eliminar', 'datos': d})
             .then(function(response){
                console.log(response.data);
                if(response.data.errores == 0){
                    $scope.inicializarDatos($scope.datos.bloqueoJugada.selectedLoteria.idLoteria);
                    alert("Se ha eliminado correctamente");
                }
                
            });
        }
       

        $scope.hora_convertir = function(_24){
            //Si es verdadero la hora se convertira al formato 24 horas
            if(_24){
                //Si es verdadero eso quiere decir que es PM de lo contrario sera AM
                if($scope.datos.horaCierre.indexOf("PM") != -1){
                    
                    //Aqui se le quitara el PM a la hora
                    var a = $scope.datos.horaCierre.replace(" PM", "");
                    //Aqui la hora se convertira en un arreglo para tener aparte la hora y los minutos
                    a = a.split(':');
                    //La variable hora va a contener el solamente la hora sin minutos ni segundos
                    var hora = parseInt(a[0]);
                    //Aqui se convierte la hora normal en el formato 24 horas
                    $scope.datos.horaCierre = hora + 12;
                    //Aqui se concatena la hora en formato 24 con los minutos
                    $scope.datos.horaCierre = $scope.datos.horaCierre.toString() + ":" + a[1];
                    console.log('actualizar: convertido: ', $scope.datos.horaCierre); 
                }
                else{
                     //Aqui se le quitara el AM a la hora
                     var a = $scope.datos.horaCierre.replace(" AM", "");
                     $scope.datos.horaCierre = a;
                     console.log('actualizar: convertido: ', $scope.datos.horaCierre); 
                }
            }
            else{
                var a = $scope.datos.horaCierre.split(":");
                var hora = parseInt(a[0]);
                if(hora > 12){
                    hora = hora - 12;
                    $scope.datos.horaCierre = hora.toString() + ':' + a[1] + ' PM';
                }
            }
        }


        $scope.cbxLoteriasChanged = function(){
            $scope.datos.quiniela = $scope.datos.selectedLoteria.quiniela;
            $scope.datos.pale = $scope.datos.selectedLoteria.pale;
            $scope.datos.tripleta = $scope.datos.selectedLoteria.tripleta;
            $scope.datos.bloqueosJugadas = $scope.datos.selectedLoteria.bloqueosjugadas;


            if($scope.datos.selectedLoteria.bloqueosjugadas != undefined){

                $scope.datos.bloqueosJugadas = $scope.datos.selectedLoteria.bloqueosjugadas;

                

                // a.forEach(function(valor, indice, array){

                //     if($scope.datos.ckbDias.find(x => x.idDia == array[indice].idDia) != undefined){
                //         let idx = $scope.datos.ckbDias.findIndex(x => x.idDia == parseInt(array[indice].idDia));
                //         $scope.datos.ckbDias[idx].existe = true;
                //     }

                //  });
            }
            else
                $scope.datos.bloqueosJugadas = [];


                
        }

        $scope.cbxLoteriasChanged2 = function(){
          
            if($scope.datos.bloqueoJugada.selectedLoteria.bloqueosjugadas != undefined){

                $scope.datos.bloqueoJugada.bloqueosJugadas = $scope.datos.bloqueoJugada.selectedLoteria.bloqueosjugadas;

                

                // a.forEach(function(valor, indice, array){

                //     if($scope.datos.ckbDias.find(x => x.idDia == array[indice].idDia) != undefined){
                //         let idx = $scope.datos.ckbDias.findIndex(x => x.idDia == parseInt(array[indice].idDia));
                //         $scope.datos.ckbDias[idx].existe = true;
                //     }

                //  });
            }
            else
                $scope.datos.bloqueoJugada.bloqueosJugadas = [];


                
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


        $scope.p = function(){
            console.log("p: ", Object.keys($scope.datos.bloqueosJugadas).length);

            return Object.keys($scope.datos.bloqueosJugadas).length;
            // if(last){

            //     (function($){
            //         $.fn.hasScrollBar = function(){
            //           return this.get(0).scrollHeight > this.height();
            //         }
            //       })(jQuery);
    
    
            //     //console.log('hasScrollBar function: ', $('#table_body').hasScrollBar());

            // }

            
        }


    })
