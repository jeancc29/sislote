
// var btnImpresora = document.querySelector("#btnImpresora");
var btnImpresora;
console.log("btnImpresora: ", btnImpresora);

document.addEventListener("DOMContentLoaded", function() { 
    // this function runs when the DOM is ready, i.e. when the document has been parsed
    window.abrirModalImpresora = function(abrirPorqueDebeRegistrarImpresora = false){
        var txtImpresora = document.querySelector("#txtImpresora");
        txtImpresora.value = localStorage.getItem("impresora");
        $('#txtImpresoraGroup').addClass('is-filled');

        if(abrirPorqueDebeRegistrarImpresora){
            alert("Debe registrar una impresora");
            $('#modal-impresora').modal('show');
        }else{
            cerrarMenu();
            $('#modal-impresora').modal('show');
        }
    }
    
    function cerrarMenu(){
        var element = document.querySelector("#toggle-navigation");
        var evObj = document.createEvent('Events');
        evObj.initEvent('click', true, false);
        element.dispatchEvent(evObj);
        console.log("Dentro cerrarMenu");
    
    }

    var guardar = function(){
        localStorage.clear();
        var txtImpresora = document.querySelector("#txtImpresora");
        localStorage.setItem('impresora', txtImpresora.value);
        console.log("guardar: ", localStorage.getItem('impresora'));
        $('#modal-impresora').modal('hide');
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

    var btnImpresoraProbar = document.querySelector("#btnImpresoraProbar");
    btnImpresoraProbar.addEventListener('click', function(e){
        probar();
    });

    
});

// btnImpresora.addEventListener('click', abrirModalImpresora(e))

    
