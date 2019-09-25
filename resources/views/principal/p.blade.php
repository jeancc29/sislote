@extends('header')

@section('content')

<div class="main-panel" ng-init="load('{{session('idUsuario')}}', '{{url('principal/ticket')}}')">






              

<div class="content">
                      <!-- <div class="container-fluid"> -->
<div class="">
    <div class="row d-none d-md-flex">
        <div class="col-md-12">
        <div class="card mt-0 mb-2 p-0">
            <div class="card-body">

              <div  class="col-12 m-0 p-0">
                    <!-- o.descripcion disable when validarHora(o.horaCierre, o.descripcion) for o in datos.optionsLoterias track by o.id -->
                            <select 
                            id="multiselect2"
                                ng-model="datos.selectedBancas"
                                ng-options="o.descripcion for o in bancasGlobal track by o.id"
                                class="selectpicker w-100" 
                                data-style="select-with-transition" 
                                title="Seleccionar loteria"
                                data-size="7" aria-setsize="2">
                            </select>
                    </div>
            
               

                <div class="row">
                       

                    <div class="col-sm-1 col-2 text-center">
                                <div class="form-check mt-3">
                                <label class="form-check-label">
                                    <input ng-model="datos.hayDescuento" ng-change="calcularTotal()" class="form-check-input" type="checkbox" value=""> Desc
                                    <span class="form-check-sign">
                                    <span class="check"></span>
                                    </span>
                                </label>
                                </div>
                    </div>

                    <div  class="col-lg-4 col-md-4 col-sm-4 col-10">
                    <!-- o.descripcion disable when validarHora(o.horaCierre, o.descripcion) for o in datos.optionsLoterias track by o.id -->
                            <select 
                            id="multiselect"
                                ng-change="calcularTotal()"
                                ng-model="datos.loterias"
                                ng-options="o.descripcion for o in datos.optionsLoterias track by o.id"
                                class="selectpicker col-12" 
                                data-style="select-with-transition" 
                                multiple title="Seleccionar loteria"
                                data-size="7" aria-setsize="2">
                            </select>
                    </div>

                    
                    <!-- ng-blur="monto_disponible(true)" -->
                            <div class="col-sm-2 col-4">
                                <form>
                                    <div id="divInputJugada" class="form-group">
                                        <label  for="jugada" class="bmd-label-floating">Jugada</label>
                                        <input 

                                            
                                           
                                            ng-model="datos.jugada"
                                            ng-keyup="inputJugadaKeyup($event)"
                                            
                                            class="form-control h4" 
                                            id="inputJugada" 
                                            type="text" name="text" 
                                            minLength="2" maxLength="6"  required="true" />
                                    </div>
                                </form>
                            </div>

                            <!-- <div class="form-group"> -->
                                
                                <div style="font-size: 16px" class="col-3 col-sm-2 mt-2">
                                <input disabled ng-model="datos.montoExistente" type="text" class="form-control" id="inputPassword" placeholder="0.00">
                                </div>
                            <!-- </div> -->

                        
                            <div class="col-sm-2 col-3">    
                                <div class="form-group">
                                    <label for="monto" class="bmd-label-floating">Monto</label>
                                    <input
                                        ng-model="datos.monto"
                                        ng-keyup="jugada_insertar($event)"
                                        id="inputMonto"
                                        class="form-control h4" id="monto" 
                                        type="number" name="number" number="true" minLength="1" maxLength="4" required="true" />
                                </div>
                            </div>
                    

                </div>


                <div class="row justify-content-md-center" >
                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Monto: <span class="bg-info p-1 text-white rounded">@{{datos.monto_jugado | currency}}</span>
                        </h4>
                    </div>
                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Descuento: <span ng-click="hola()" class="bg-info p-1 text-white rounded">@{{datos.descuentoMonto | currency}}</span>
                        </h4>
                    </div>

                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Total a pagar: <span class="bg-info p-1 text-white rounded">@{{datos.monto_a_pagar | currency}}</span>
                        </h4>
                    </div>
                    
                    <div class="col-4 col-md-3">
                        <h4 class="font-weight-bold">
                        Jugadas: <span class="bg-info p-1 text-white rounded">@{{datos.total_jugadas | currency}}</span>
                        </h4>
                    </div>
                   
                </div>

            </div>
        </div>
        </div>
        
    </div>


    
    


      




    

    <!-- FIN BOTONES REPORTES -->



    <!-- MODAL MONITOREO -->
    <style>
                .modal-lg {
            max-width: 90% !important;
        }

        .modal-lg-normal {
            max-width: 60% !important;
        }

        
    </style>

    






    
</div>
</div>


               
</div>
          
</div>

<script src="{{asset('assets/js/core/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/core/popper.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/js/core/bootstrap-material-design.min.js')}}" type="text/javascript"></script>

<script src="{{asset('assets/js/plugins/perfect-scrollbar.jquery.min.js')}}" ></script>


<!-- Plugin for the momentJs  -->
<script src="{{asset('assets/js/plugins/moment.min.js')}}"></script>

<!--  Plugin for Sweet Alert -->
<script src="{{asset('assets/js/plugins/sweetalert2.js')}}"></script>

<!-- Forms Validations Plugin -->
<script src="{{asset('assets/js/plugins/jquery.validate.min.js')}}"></script>

<!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
<script src="{{asset('assets/js/plugins/jquery.bootstrap-wizard.js')}}"></script>

<!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
<script src="{{asset('assets/js/plugins/bootstrap-selectpicker.js')}}" ></script>

<!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
<script src="{{asset('assets/js/plugins/bootstrap-datetimepicker.min.js')}}"></script>

<!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
<script src="{{asset('assets/js/plugins/jquery.dataTables.min.js')}}"></script>

<!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
<script src="{{asset('assets/js/plugins/bootstrap-tagsinput.js')}}"></script>

<!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
<script src="{{asset('assets/js/plugins/jasny-bootstrap.min.js')}}"></script>

<!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
<script src="{{asset('assets/js/plugins/fullcalendar.min.js')}}"></script>

<!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
<script src="{{asset('assets/js/plugins/jquery-jvectormap.js')}}"></script>

<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="{{asset('assets/js/plugins/nouislider.min.js')}}" ></script>

<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>

<!-- Library for adding dinamically elements -->
<script src="{{asset('assets/js/plugins/arrive.min.js')}}"></script>


<!--  Google Maps Plugin    -->

<script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB2Yno10-YTnLjjn_Vtk0V8cdcY5lC4plU"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>


<!-- Chartist JS -->
<script src="{{asset('assets/js/plugins/chartist.min.js')}}"></script>

<!--  Notifications Plugin    -->
<script src="{{asset('assets/js/plugins/bootstrap-notify.js')}}"></script>





<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{asset('assets/js/material-dashboard.js')}}" type="text/javascript"></script>
<!-- <script src="{{asset('assets/js/prueba_jquery.js')}}" type="text/javascript"></script> -->
<!-- Material Dashboard DEMO methods, don't include it in your project! -->
<script src="{{asset('assets/demo/demo.js')}}"></script>




  
  <script>
  $(document).ready(function(){

    // Initialise the wizard
    demo.initMaterialWizard();
    setTimeout(function() {
      $('.card.card-wizard').addClass('active');
    }, 600);


     // initialise Datetimepicker and Sliders
     md.initFormExtendedDatetimepickers();
    if($('.slider').length != 0){
      md.initSliders();
    }

   

    
    
    $('#facebook').sharrre({
  share: {
    facebook: true
  },
  enableHover: false,
  enableTracking: false,
  enableCounter: false,
  click: function(api, options){
    api.simulateClick();
    api.openPopup('facebook');
  },
  template: '<i class="fab fa-facebook-f"></i> Facebook',
  url: 'https://demos.creative-tim.com/material-dashboard-pro/examples/dashboard.html'
});

    $('#google').sharrre({
  share: {
    googlePlus: true
  },
  enableCounter: false,
  enableHover: false,
  enableTracking: true,
  click: function(api, options){
    api.simulateClick();
    api.openPopup('googlePlus');
  },
  template: '<i class="fab fa-google-plus"></i> Google',
  url: 'https://demos.creative-tim.com/material-dashboard-pro/examples/dashboard.html'
});

    $('#twitter').sharrre({
  share: {
    twitter: true
  },
  enableHover: false,
  enableTracking: false,
  enableCounter: false,
  buttons: { twitter: {via: 'CreativeTim'}},
  click: function(api, options){
    api.simulateClick();
    api.openPopup('twitter');
  },
  template: '<i class="fab fa-twitter"></i> Twitter',
  url: 'https://demos.creative-tim.com/material-dashboard-pro/examples/dashboard.html'
});

    
    var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-46172202-1']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script');
    ga.type = 'text/javascript';
    ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(ga, s);
})();

    // Facebook Pixel Code Don't Delete
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');

try{
  fbq('init', '111649226022273');
  fbq('track', "PageView");

}catch(err) {
  console.log('Facebook Track Error:', err);
}

  });
</script>
<noscript>
  <img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=111649226022273&ev=PageView&noscript=1"
/>

</noscript>











    </body>

</html>



@endsection