<!DOCTYPE html>
<html lang="en" ng-app="myModule">
<head>
  <meta charset="UTF-8">
  <title>Document</title>

  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
 <script src="script.js"></script>
 <script src="angular.min.js"></script> -->

 <link href="<?php echo asset('assets/css/material-dashboard.css') ?>?v=2.0.2" rel="stylesheet" />
<script src="<?php echo asset('assets/bootstrap/dist/css/bootstrap.min.css') ?>" type="text/javascript"></script>

<script src="<?php echo asset('assets/js/angular.min.js') ?>" ></script>


</head>
<body ng-controller="myController" ng-init="load('<?php echo url('principal');?>')">
<!-- <img src="{{datos.imagen}}" alt=""> -->
<div id="hola" class="row">
  <div class="col-4 bg-primary">
    <h1>Holaa</h1>
  </div>
</div>
  <div class="row">
    <div id="imprimir" class="col-12 col-sm-4 bg-primary text-center" style="min-width: 300px; max-width: 302px;">
        <h5 class="text-center my-0">**ORIGINAL**</h5>
        <h5 class="text-center my-0">{{datos[1].usuario}}</h5>
        <p class="text-center my-0">{{datos[1].codigo + '-' + toSecuencia(datos[1].idTicket)}}</p>
        <p class="text-center my-0">Fecha: {{toFecha(datos[1].created_at.date) | date:"dd/MM/yyyy hh:mm a"}}</p>
        <h5 class="text-center my-0 font-weight-bold">{{datos[1].codigoBarra}}</h5>
        <div class="row justify-content-center"  ng-repeat="l in loterias">
          <div class="col-12 text-center">
            <p style="border-top-style: dashed; border-bottom-style: dashed;" class="text-center font-weight-bold py-1 mt-2 mb-0">{{l.descripcion}}: {{ l.total | number:2}}</p>
          </div>
          <div class="col-6">
              <table class="table table-sm table-borderless">
                  <thead>
                  <tr>
                      <th class="text-center" scope="col">Jugada</th>
                      <th class="text-center" scope="col">Monto</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr ng-repeat="c in l.jugadas1 | filter:{'idLoteria': l.id}">
                      <td class="text-center" style="font-size: 14px">{{c.jugada}}</td>
                      <td class="text-center" style="font-size: 14px">{{c.monto}}</td>
                  </tr> 
                  </tbody>
              </table> <!-- TABLA -->
            </div> <!-- END COL 6 -->
            <div class="col-6">
              <table class="table table-sm table-borderless">
                  <thead>
                  <tr>
                      <th class="text-center" scope="col">Jugada</th>
                      <th class="text-center" scope="col">Monto</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr ng-repeat="c in l.jugadas2 | filter:{'idLoteria': l.id}">
                      <td class="text-center" style="font-size: 14px">{{c.jugada}}</td>
                      <td class="text-center" style="font-size: 14px">{{c.monto}}</td>
                  </tr> 
                  </tbody>
              </table> <!-- TABLA -->
            </div> <!-- END COL 6 -->

        </div> <!-- END ROW JUGADAS -->
    </div> <!-- COL PRINCIPAL -->
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<script src="<?php echo asset('assets/js/core/jquery.min.js') ?>"></script>
<script src="<?php echo asset('assets/js/html2canvas.js') ?>"></script>

<script>
  $(document).ready(function(){
   
    //window.print();
    
    //https://www.indalcasa.com/programacion/javascript/llamar-a-una-funcion-de-un-padre-desde-iframe/
    //https://stackoverflow.com/questions/23648458/call-angularjs-function-using-jquery-javascript/23648641
  });
</script>
<script>
  var myApp = angular
    .module("myModule", [])
    .controller("myController", function($scope,$http, $window, $document){

      $scope.toSecuencia = function(idTicket){
            var str = "" + idTicket;
            var pad = "000000000";
            var ans = pad.substring(0, pad.length - str.length) + str;
            return ans;
        }
      
      var datos = [
        {"jugada" : "02-99-05", "monto" : "20.00"},
        {"jugada" : "99-01-05", "monto" : "10.00"},
        {"jugada" : "02-01-88", "monto" : "5.00"},
        {"jugada" : "02-70-05", "monto" : "20.00"}
      ];
      
      console.log('Dentro ticket');

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


    // $scope.imprime = function(){
    //   $scope.addItem(datos);
    //  // a=window.frames['iframeOculto'].src='index.php';
    // }

    $scope.toFecha = function(fecha){
        return new Date(fecha);
    }
    

    $scope.loteriaTotal = function(idLoteria){
      var total = 0;
      $scope.jugadas.forEach(function(valor, indice, array){
        if(array[indice].idLoteria == idLoteria)
            total += Number(array[indice].monto);
      });

      return total;
    }

    
     function convertHmtl2Canvas(){
      console.log('Conver dentro parte de afuera');
      var elm = $('#imprimir').get(0);
      var lebar = "600";
      var tinggi = "400";
      var type = "jpeg";
      var filename = "htmltoimage";
      var blob = '';
      $scope.datosImagen = {
        "imagen" : null,
        "nombre" : null
      };
        html2canvas(elm).then(function(canvas){

          canvas = scaleCanvas(canvas, canvas.width, canvas.height);
        var dataUrl = canvas.toDataURL('image/png');
        $scope.datosImagen.imagen = dataUrl;
        $scope.datosImagen.nombre = $scope.datos[1].codigoBarra;
        console.log('response imagen: ' ,$scope.datosImagen);
        
        $http.post("/api/imagen/guardar",{'datos':$scope.datosImagen, 'action':'sp_ventas_actualiza'})
          .then(function(response){
              
              console.log('response imagen: ' , response);
              //El metodo send que esta en el archivo header.blade.php se encarga de enviar la foto del ticket a traves del printer, sms o whatsapp
              window.parent.send(response.data.nombre, response.data.imagenBase64, false);
              //$scope.addImageBase64(response.data.imagenBase64)
              
            },
            function(response) {
                alert('Error imagen ticket');
            });
      
     
        
      //   console.log('Conver dentro parte de adentro');
      //   $scope.datos.imagen = canvas2image(canvas);
      //   $scope.datos.type = $scope.datos.imagen.type;
      //   $scope.datos.size = $scope.datos.imagen.size;
      //   $scope.datos.name = $scope.datos.imagen.name;
      //   $scope.datos.lastModifiedDate = $scope.datos.imagen.lastModifiedDate;
      //   console.log('imgÑ ', $scope.datos);

      //   var fd = new FormData();
      // fd.append('fname', 'test4.png');
      // fd.append('archivo', $scope.datos.imagen);
      // console.log('form: ', fd);
      // $scope.datos.form = fd;
      //   $http.post("/api/imagen/guardar",{'datos':$scope.datos, 'action':'sp_ventas_actualiza'})
      //     .then(function(response){

      //         console.log('response imagen: ' , response);
              
      //     })

      });

      console.log('blob: ', blob);

    }

function scaleCanvas (canvas, width, height) {
		var w = canvas.width,
			h = canvas.height;
		if (width == undefined) {
			width = w;
		}
		if (height == undefined) {
			height = h;
		}

		var retCanvas = document.createElement('canvas');
		var retCtx = retCanvas.getContext('2d');
		retCanvas.width = width;
		retCanvas.height = height;
		retCtx.drawImage(canvas, 0, 0, w, h, 0, 0, width, height);
		return retCanvas;
  }
  
    function canvas2image(canvas){
      canvas = scaleCanvas(canvas, canvas.width, canvas.height);
		var dataUrl = canvas.toDataURL('image/png');
		var data = atob(dataUrl.substring("data:image/png;base64,".length)),
			asArray = new Uint8Array(data.length);

		for(var i = 0; i < data.length; ++i){
			asArray[i] = data.charCodeAt(i);
		}

		var blob = new Blob([asArray.buffer], {type: "image/png"});
		blob.lastModifiedDate = new Date();
		blob.name = "foto";
    console.log('blob: ', blob);
    return blob;

      //   var jpegFile = canvas.toDataURL('image/jpeg');
      // var jpegFile64 = jpegFile.replace(/^data:image\/(png|jpeg);base64,/, "");
      // var jpegBlob = base64ToBlob(jpegFile64, 'image/jpeg');
      // console.log('blob: ', jpegBlob);
      // return jpegBlob;

  }
  
  function base64ToBlob(base64, mime){
    mime = mime || '';
    var sliceSize = 1024;
    var byteChars = window.atob(base64);
    var byteArrays = [];

    for(var offset = 0, len = byteChars.length; offset < len; offset += sliceSize){
      var slice = byteChars.slice(offset, offset + sliceSize);

      var byteNumbers = new Array(slice.length);
      for(var i = 0; i < slice.length; i++){
        byteNumbers[i] = slice.charCodeAt(i);
      }
      var byteArray = new Uint8Array(byteNumbers);
      byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, {type: mime});
  }

 

    
    
     $scope.datos = getData();
    $scope.count = getValue();
    $scope.loterias = $scope.datos[1].loterias;
    $scope.jugadas = [];
    $scope.jugadas2 = [];

    

      $scope.loterias.forEach(function(v, i, a){

        let idx = $scope.loterias.findIndex(x => x.id == a[i].id);
        //Creamos el arreglo jugadasTodas que va a contener todas las jugadas de cada loteria perteneciente a este ticket
        $scope.loterias[idx].jugadasTodas = [];
        $scope.loterias[idx].total = 0;
        var total = 0;
        
        $scope.datos[1].jugadas.forEach(function(valor, indice, array){
            if(a[i].id == array[indice].idLoteria){
              total += Number(array[indice].monto);
              $scope.loterias[idx].jugadasTodas.push(array[indice]);
            }
        });

        //  $scope.loterias[idx].total = total;

      });


    $scope.loterias.forEach(function(v, i, a){
        let idx = $scope.loterias.findIndex(x => x.id == a[i].id);
        $scope.loterias[idx].jugadas1 = [];
        $scope.loterias[idx].jugadas2 = [];
        $scope.loterias[idx].total = 0;
        var total = 0;

        

        //console.log(a[i].descripcion, ' ',a[i].jugadasTodas);
        
        if(Object.keys(a[i].jugadasTodas).length > 1){
            var mitad = Math.round(Object.keys(a[i].jugadasTodas).length / 2);
            $scope.loterias[idx].tamJugadas = Object.keys(a[i].jugadasTodas).length;
            a[i].jugadasTodas.forEach(function(valor, indice, array){
              total += Number(array[indice].monto);
               //console.log(a[i].descripcion, ': ', array[indice].monto);
                if((parseInt(indice) + 1) <= mitad){
                  $scope.loterias[idx].jugadas1.push(array[indice]);
                }else{
                  $scope.loterias[idx].jugadas2.push(array[indice]);
                }
            });
          }else{
            $scope.loterias[idx].jugadas1 = a[i].jugadasTodas;
            $scope.loterias[idx].tamJugadas = Object.keys(a[i].jugadasTodas).length;
            // console.log('JugadasTodas: ', a[i].jugadasTodas[1]);
            total += Number(a[i].jugadasTodas[0].monto);
          }

        $scope.loterias[idx].total = total;          

      });

      console.log('Antes de convertHmtl2Canvas()');
      convertHmtl2Canvas();
    

    // if(Object.keys($scope.datos[1].jugadas).length > 1){
    //   var mitad = Object.keys($scope.datos[1].jugadas).length / 2;
    //   $scope.datos[1].jugadas.forEach(function(valor, indice, array){
    //       if((indice + 1) <= mitad){
    //         $scope.jugadas.push(array[indice]);
    //       }else{
    //         $scope.jugadas2.push(array[indice]);
    //       }
    //   });
    // }else{
    //   $scope.jugadas = $scope.datos[1].jugadas;
    // }

      

      console.log('datos ticket: ', $scope.datos);
  

    });
</script>

</body>
</html>