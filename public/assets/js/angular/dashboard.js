myApp
    .controller("myController", function($scope, $http, $timeout, helperService){
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

        

       


        $scope.cargando = false;
         $scope.optionsTipoCliente = [];
        $scope.selectedTipoCliente = {};
        $scope.es_cliente = false;
        $scope.datos =  {
            "loteriasJugadasDashboard":[],
            "selectedLoteria" : {},
            'fecha' : new Date()
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
            var fecha = $scope.datos.fecha.getFullYear() + '-' + helperService.to2Digitos($scope.datos.fecha.getMonth() + 1) + '-' + helperService.to2Digitos($scope.datos.fecha.getDate());
            // console.log('Bancas: ', $scope.datos.fecha.getDate(), ' completa:', $scope.datos.fecha);
            $scope.cargando = true;
            $http.get(rutaGlobal+"/api/dashboard?fecha=" + fecha + "&idUsuario=" + idUsuarioGlobal)
             .then(function(response){
                 $scope.ventasGrafica = response.data.ventasGrafica;
                 

                 $scope.loterias = response.data.loterias;
                 $scope.sorteos = response.data.sorteos;
                 $scope.bancasConVentas = response.data.bancasConVentas;
                 $scope.bancasSinVentas = response.data.bancasSinVentas;
                 $scope.totalVentasLoterias = response.data.totalVentasLoterias;
                 $scope.totalPremiosLoterias = response.data.totalPremiosLoterias;
                 $scope.loteriasJugadasDashboard = response.data.loteriasJugadasDashboard;
                 
                console.log('Bancas: ', $scope.ventasGrafica);
                $scope.cargando = false;
                $timeout(function() {
                    // anything you want can go here and will safely be run on the next digest.
                    //$('#multiselect').selectpicker('val', []);
                    crearGrafica();
                  })
                
                
                // $("#modal-cargando").modal("hide");
            });
        }


        

        

        $scope.cambiarLoteria = function(loteria, first = null){
            if(first != null && first == true){
                $scope.datos.selectedLoteria = loteria;
            }
            else if(first == null){
                $scope.datos.selectedLoteria = loteria;
            }
        }
       
        
        $scope.onFechaChanged = function(){
            var fecha = $scope.datos.fecha.getFullYear() + '-' + helperService.to2Digitos($scope.datos.fecha.getMonth() + 1) + '-' + helperService.to2Digitos($scope.datos.fecha.getDate());
            // console.log('Bancas: ', $scope.datos.fecha.getDate(), ' completa:', $scope.datos.fecha);
            $("#modal-cargando").modal("toggle");
            $http.get(rutaGlobal+"/api/dashboard?fecha=" + fecha + "&idUsuario=" + idUsuarioGlobal)
             .then(function(response){
                 $scope.ventasGrafica = response.data.ventasGrafica;
                 crearGrafica();

                 $scope.loterias = response.data.loterias;
                 $scope.sorteos = response.data.sorteos;
                 $scope.bancasConVentas = response.data.bancasConVentas;
                 $scope.bancasSinVentas = response.data.bancasSinVentas;
                 $scope.totalVentasLoterias = response.data.totalVentasLoterias;
                 $scope.totalPremiosLoterias = response.data.totalPremiosLoterias;
                 $scope.loteriasJugadasDashboard = response.data.loteriasJugadasDashboard;

                console.log('Bancas: ', $scope.ventasGrafica);
                $("#modal-cargando").modal("toggle");
            });
        }
       

        function crearGrafica()
        {
            $('#myfirstchart').empty();
            new Morris.Bar({
                // ID of the element in which to draw the chart.
                element: 'myfirstchart',
                // Chart data records -- each entry in this array corresponds to a point on
                // the chart.
              
                // data: [
                //   { year: '2008', sales: 200, value: 20 },
                //   { year: '2009', sales: 100, value: 10 },
                //   { year: '2010', sales: 70, value: 5 },
                //   { year: '2011', sales: 50, value: 5 },
                //   { year: '2012', sales: 200, value: 20 }
                // ],
                data: $scope.ventasGrafica,
                // The name of the data record attribute that contains x-values.
                xkey: 'dia',
                // A list of names of data record attributes that contain y-values.
                ykeys: ['total', 'neto'],
                // Labels for the ykeys -- will be displayed when you hover over the
                // chart.
                labels: ['total', 'neto'],
                // stacked: true,
                barSize: 30,
                resize: true,
                fillOpacity: 0.1,
                hideHover: true,
                behaveLikeLine: true,
              
                pointFillColors: ['#ffffff'],
                pointStrokeColors: ['black'],
                lineColors: ['red', 'blue'],
              
                barColors: function (row, series, type) {
                  console.log('row: ', row.y);
                  if(series.key == "neto" && row.y > 0)
                      return "#75b281";
                  if(series.key == "neto" && row.y < 0)
                    return "#dc2365";
                  else
                    // return "#c2c2d6";
                    return "#95999e";
                  }
              });
        }


    })



  

