<!DOCTYPE html>
<html lang="en" ng-app="myApp">
<head>
  <meta charset="UTF-8">
  <title>Document</title>

  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
 <script src="script.js"></script>
 <script src="angular.min.js"></script> -->

 <link href="<?php echo assets('assets/css/material-dashboard.css') ?>?v=2.0.2" rel="stylesheet" />
<script src="<?php echo assets('assets/bootstrap/dist/css/bootstrap.min.css') ?>" type="text/javascript"></script>

<script src="<?php echo assets('assets/js/angular.min.js') ?>" ></script>


</head>
<body ng-controller="myController" ng-init="load('<?php echo url('principal');?>')">


  <div class="row">
    <div class="col-4 bg-primary">
        <h5 class="text-center my-0">**ORIGINAL**</h5>
        <h5 class="text-center my-0">{{datos[0].usuario}}</h5>
        <p class="text-center my-0">{{datos[0].codigo + '-' + datos[0].idTicketSecuencia}}</p>
        <p class="text-center my-0">Fecha: {{toFecha(datos[0].fecha) | date:"dd/MM/yyyy hh:mm a"}}</p>
        <h5 class="text-center my-0">{{datos[0].codigoBarra}}</h5>
        <div ng-repeat="l in loterias">
            <div class="row justify-content-center">
                <div class="col-11">
                    <p style="border-top-style: dashed; border-bottom-style: dashed;" class="text-center font-weight-bold py-1 mt-2 mb-0">{{l.descripcion}}: {{l.monto | number:2}}</p>
                </div>
            </div>
            <table class="table table-sm table-borderless">
                <thead>
                <tr>
                    <th class="text-center" scope="col">Jugada</th>
                    <th class="text-center" scope="col">Monto</th>
                    <th class="text-center" scope="col">Jugada</th>
                    <th class="text-center" scope="col">Monto</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="c in jugadas | filter:{'idLoteria': l.idLoteria}">
                    <td class="text-center" style="font-size: 14px">{{c.jugada}}</td>
                    <td class="text-center" style="font-size: 14px">{{c.monto}}</td>
                    <td class="text-center" style="font-size: 14px">{{c.jugada}}</td>
                    <td class="text-center" style="font-size: 14px">{{c.monto}}</td>
                </tr> 
                </tbody>
            </table> <!-- TABLA -->
        </div> <!-- DIV DATOS LOTERIAS Y JUGADAS -->
    </div> <!-- COL PRINCIPAL -->
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="<?php echo ROOT_PATH ?>assets/js/core/jquery.min.js"></script>

<script>
  $(document).ready(function(){
   
    window.print();
  });
</script>
<script>
  var myApp = angular
    .module("myApp", [])
    .controller("myController", function($scope,$http, $window, $document){
      
      var datos = [
        {"jugada" : "02-99-05", "monto" : "20.00"},
        {"jugada" : "99-01-05", "monto" : "10.00"},
        {"jugada" : "02-01-88", "monto" : "5.00"},
        {"jugada" : "02-70-05", "monto" : "20.00"}
      ];
      

      var validarExistencia = function(ruta){
        if(getValue() == 0){
            location.href = ruta;
            //console.log('validar: ', ruta);
        }
    }

      $scope.load = function(ROOT_PATH){
        ROOT_PATH;
        validarExistencia(ROOT_PATH);
      }

      var getValue = function(){
        return $window.sessionStorage.length;
    }
      
    var getData = function(){
      var json = [];
      var contador = 0;
      $.each($window.sessionStorage, function(i, v){
        json.push(angular.fromJson(v));
      });
      return json;
    }
      


    $scope.addItem = function(data){
        //var image = document.getElementById('img'+id);
        // json = {
        //   id: id,
        //   img: image.src
        // }
        $window.sessionStorage.setItem('datos', JSON.stringify(data));
        $scope.count = getValue();
        $scope.datos = getData();
    }
    
    $scope.removeItem = function(id){
      $window.sessionStorage.removeItem(id);
      $document.
      $scope.count = getValue();
      $scope.datos = getData();
     
      alert('Removed with Success!');
    }


    $scope.imprime = function(){
      $scope.addItem(datos);
     // a=window.frames['iframeOculto'].src='index.php';
    }

    $scope.toFecha = function(fecha){
        return new Date(fecha);
    }
    

    
    
     $scope.datos = getData();
    $scope.count = getValue();
    $scope.loterias = JSON.parse($scope.datos[0].loterias);
    $scope.jugadas = JSON.parse($scope.datos[0].jugadas);
  


    console.log($scope.datos);

    });
</script>

</body>
</html>