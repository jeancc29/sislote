var myApp = angular
    .module("myModule", [])
    .controller("myController", function($scope, $http, $timeout, $window, $document){
        $scope.busqueda = "";
        var ruta = '';
        // $scope.optionsTipoUsuario = [{name:"Cliente", id:1}, {name:"Garante", id:2}, {name:"Usuario", id:3}];
        // $scope.selectedTipoUsuario = $scope.optionsTipoUsuario[0];

       

        $scope.datos = {
            'mensaje' : 'Hola mi amor'
        }
      

        $http.get("/api/loterias", {'datos' : $scope.datos})
        .then(function(response){

           console.log(response)


       });


       $scope.obtener = function(){
        $http.get("/api/loterias", {'datos' : $scope.datos})
        .then(function(response){

           console.log(response)


       });
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