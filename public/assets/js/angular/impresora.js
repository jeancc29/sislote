
// var btnImpresora = document.querySelector("#btnImpresora");
var btnImpresora;
console.log("btnImpresora: ", btnImpresora);

document.addEventListener("DOMContentLoaded", function() { 
    // this function runs when the DOM is ready, i.e. when the document has been parsed

   

    

    window.abrirModalImpresora = function(abrirPorqueDebeRegistrarImpresora = false){
        var txtImpresora = document.querySelector("#txtImpresora");
        var radioPapelPequeno = document.querySelector("#radioPapelPequeno");
        var radioPapelGrande = document.querySelector("#radioPapelGrande");

        txtImpresora.value = localStorage.getItem("impresora");
        $('#txtImpresoraGroup').addClass('is-filled');

        var papel = window.getPapel();
        if(papel == "grande"){
            radioPapelGrande.checked = true;
            radioPapelPequeno.checked = false;
        }
        else{
            radioPapelPequeno.checked = true;
            radioPapelGrande.checked = false;
        }
        
        if(abrirPorqueDebeRegistrarImpresora){
            alert("Debe registrar una impresora");
            $('#modal-impresora').modal('show');
        }else{
            cerrarMenu();
            $('#modal-impresora').modal('show');
        }
    }

    window.getPapel = function(){
        var papel = localStorage.getItem("papel");
        if(papel == null){
            return "grande";
        }
        return papel;
    }
    
    function cerrarMenu(){
        if($('#toggle-navigation').length){
            var element = document.querySelector("#toggle-navigation");
            var evObj = document.createEvent('Events');
            evObj.initEvent('click', true, false);
            element.dispatchEvent(evObj);
            console.log("Dentro cerrarMenu");
        }
    }

    var guardar = function(){
        localStorage.clear();
        var txtImpresora = document.querySelector("#txtImpresora");
        localStorage.setItem('impresora', txtImpresora.value);

        var radioPapelGrande = document.querySelector("#radioPapelGrande");
        if(radioPapelGrande.checked)
            localStorage.setItem('papel', "grande");
        else
            localStorage.setItem('papel', "pequeno");

        console.log("guardar: ", localStorage.getItem('impresora'));
        $('#modal-impresora').modal('hide');
    }

    var descargarAppImprimir = function(){
        console.log("descargarAppImprimir");
        $.fileDownload(rutaProgramaJavaParaImprimir, {
            successCallback: function (url) {
         
                alert('You just got a file download dialog or ribbon for this URL :' + url);
            },
            failCallback: function (html, url) {
         
                alert('Your file download just failed for this URL:' + url + '\r\n' +
                        'Here was the resulting error HTML: \r\n' + html
                        );
            }
        });
    }
    
    var detenerAppImprimir = function(){
        console.log("descargarAppImprimir");
        var data = [];
        data.push("CLOSE_APP");
        // Crea una nueva conexión.
        const socket = new WebSocket('ws://localhost:8999');

        // Abre la conexión
        socket.addEventListener('open', function (event) {
            console.log("Conectado");
            data.unshift(localStorage.getItem("impresora"));
            socket.send(data);
        });

        // Escucha por mensajes
        socket.addEventListener('message', function (event) {
            console.log('Message from server', event.data);
            if(event.data == true)
                socket.close();
        });

        socket.addEventListener('onerror', function (event) {
            console.log('Error from server', event.data);
        });

        socket.addEventListener('onclose', function (event) {
            console.log('Close from server', event.data);
        });

    }

    var probar = function(){
        var txtImpresora = document.querySelector("#txtImpresora");
        localStorage.setItem('impresora', txtImpresora.value);
        console.log("guardar: ", localStorage.getItem('impresora'));
        $('#modal-impresora').modal('hide');
    }

    btnImpresora = document.querySelector("#btnImpresora");
    btnImpresora.addEventListener('click', function(e){
        window.abrirModalImpresora();
    });

    var btnImpresoraGuardar = document.querySelector("#btnImpresoraGuardar");
    btnImpresoraGuardar.addEventListener('click', function(e){
        guardar();
    });

    var btnImpresoraDescargar = document.querySelector("#btnImpresoraDescargar");
    btnImpresoraDescargar.addEventListener('click', function(e){
        descargarAppImprimir();
    });

    var btnImpresoraDetener = document.querySelector("#btnImpresoraDetener");
    btnImpresoraDetener.addEventListener('click', function(e){
        detenerAppImprimir();
    });

    // var btnImpresoraProbar = document.querySelector("#btnImpresoraProbar");
    // btnImpresoraProbar.addEventListener('click', function(e){
    //     probar();
    // });




    
    
});

// btnImpresora.addEventListener('click', abrirModalImpresora(e))

    
