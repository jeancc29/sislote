myApp.service('helperService', function(){
	this.print = function(nombre){
		alert('Hola ' + nombre + ' soy el servicio palomo');
    }
    
    
    this.empty = function(valor, tipo = undefined, validarMayorQueCero = true){
        // console.log("Empty dentro helper:", typeof valor);
   
        if(tipo === 'number' || typeof valor == 'number'){
            if(validarMayorQueCero){
                if(Number(valor) <= 0){
                    return true;
                }
            }
            
            if(Number(valor) == undefined || valor == '' || valor == null || isNaN(valor) == true)
                return true;
        }
        if(tipo === 'string' || typeof valor == 'string'){
            if(valor == undefined || valor == '' || valor == null)
                return true;
        }
        if(tipo === 'object' || typeof valor == 'object'){
            if(valor == undefined || valor == '' || valor == null || valor == {})
                return true;
            if(Object.keys(valor).length == 0)
                return true;
        }

        return false;
    }

    this.Number

    this.retornarObjectoPorId = function(objeto, arregloDeObjetos, idPorDefectoSiObjetoNulo = undefined){
        var objectoRetornar = {};
        if(this.empty(objeto) == false){
            if(this.empty(arregloDeObjetos) == false){
                objectoRetornar = arregloDeObjetos.find(x => x.id == objeto.id);
            }
        }
        else if(this.empty(arregloDeObjetos) == false){
            if(idPorDefectoSiObjetoNulo != undefined){
                objectoRetornar = arregloDeObjetos.find(x => x.id == idPorDefectoSiObjetoNulo);
            }
            else
                objectoRetornar = arregloDeObjetos[0];
        }

        return objectoRetornar;
    }

    this.retornarIndexPorId = function(objeto, arregloDeObjetos, idPorDefectoSiObjetoNulo = undefined){
        var indexRetornar = 0;
        if(this.empty(objeto) == false){
            if(this.empty(arregloDeObjetos) == false){
                indexRetornar = arregloDeObjetos.findIndex(x => x.id == objeto.id);
            }
        }
        else if(this.empty(arregloDeObjetos) == false){
            if(idPorDefectoSiObjetoNulo != undefined){
                indexRetornar = arregloDeObjetos.findIndex(x => x.id == idPorDefectoSiObjetoNulo);
            }
            else
                indexRetornar = 0;
        }

        return indexRetornar;
    }

    this.redondear = function(numero, decimales = 2, usarComa = false){
        var opciones = {
            maximumFractionDigits: decimales,
            useGrouping: false
        };

        try{
            numero = Intl.NumberFormat(usarComa, opciones).format(numero);
            numero = parseFloat(numero);
        }catch(e){
            console.log(e);
            numero = 0;
        }

        return numero;
    }

    this.copiarObjecto = function(objeto){
        //JSON.parse(JSON.stringify(objeto)) esto retorna una copia exacta de un objeto
        return JSON.parse(JSON.stringify(objeto));
    }
});