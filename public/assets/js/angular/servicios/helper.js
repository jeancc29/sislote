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
        else if(tipo === 'string' || typeof valor == 'string'){
            if(valor == undefined || valor == '' || valor == null)
                return true;
        }
        else if(tipo === 'date' || typeof valor == 'date'){
            console.log('dentro date service:', valor);

            if(valor == undefined || valor == null)
                return true;
        }
        else if(tipo === 'object' || typeof valor == 'object'){
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

    this.ordenarMenorAMayor = function(jugada){
        if(jugada.length == 4 && isNaN(Number(jugada)) != true){
            
            var primerParNumeros = jugada.substr(0, 2);
            var segundoParNumeros = jugada.substr(2, 2);
            if(Number(primerParNumeros) < Number(segundoParNumeros)){
                return jugada;
            }else{
                jugada = segundoParNumeros + primerParNumeros;
                return jugada;
            }
        }
        // console.log("Dentro super pale");

        if(jugada.length == 5 && isNaN(Number(jugada.substr(0, jugada.length - 1))) != true && jugada.substr(jugada.length - 1) == "s"){
            var ultimoCaracter = jugada.substr(jugada.length - 1);
            var primerParNumeros = jugada.substr(0, 2);
            var segundoParNumeros = jugada.substr(2, 2);
            if(Number(primerParNumeros) < Number(segundoParNumeros)){
                return jugada;
            }else{
                jugada = segundoParNumeros + primerParNumeros + ultimoCaracter;
                return jugada;
            }
        }

        return jugada;
    }

    this.agregar_guion_y_letra = function(cadena){
        if(cadena.length == 4 && this.esPick3Pick4UOtro(cadena) == "otro"){
            cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3];
        }
        else if(cadena.length == 6){
            cadena = cadena[0] + cadena[1] + '-' + cadena[2] + cadena[3] + '-' + cadena[4] + cadena[5];
        }
        else if(this.esPick3Pick4UOtro(cadena) == "pick3Straight"){
            cadena = cadena;
        }
        else if(this.esPick3Pick4UOtro(cadena) == "pick3Box"){
            cadena = cadena.substring(0, cadena.length - 1);
        }
        else if(this.esPick3Pick4UOtro(cadena) == "pick4Box"){
            cadena = cadena.substring(0, cadena.length - 1);
        }
        else if(this.esPick3Pick4UOtro(cadena) == "pick4Straight"){
            cadena = cadena.substring(0, cadena.length - 1);
        }
       return cadena;
    }

    this.esPick3Pick4UOtro = function(jugada){
        if(jugada.length == 3){
            return 'pick3Straight'
        }
        else if(jugada.length == 4 && jugada.indexOf('+') != -1)
            return 'pick3Box'
        else if(jugada.length == 5 && jugada.indexOf('+') != -1)
            return 'pick4Box'
        else if(jugada.length == 5 && jugada.indexOf('-') != -1)
            return 'pick4Straight'
        else
            return 'otro';
      }


      this.jugadaEsCorrecta = function(jugada){
        if(jugada == undefined || jugada == null)
          return false;
        var jugadaTmp = '';

        //Quitamos los caracteres especiales como (-, +) 
        if(jugada.length == 4 && jugada.indexOf('+') != -1){
            jugada = jugada.substring(0, jugada.length - 1);
        }
        if(jugada.length == 5 && (jugada.indexOf('+') != -1 || jugada.indexOf('-') != -1)){
            jugada = jugada.substring(0, jugada.length - 1);
        }

        for (let index = 0; index < jugada.length; index++) {
            if(jugada[index] != '+' && jugada[index] != '-'){
                jugadaTmp += jugada[index];
            }
        }


        if(Number(jugadaTmp) == jugada)
            return true;
        else
          return false;
    }


    this.actualizarScrollBar = function(){
        /* javascript for updating the Perfect Scrollbar when the content of the page is changing */
        $('.main-panel').perfectScrollbar('update');
    }

    this.destruirScrollBar = function(){
        /* javascript for detroying the Perfect Scrollbar */
        $('.main-panel').perfectScrollbar('destroy');
    }

    this.to2Digitos = function(digito){
        var str = "" + digito;
        var pad = "00";
        var ans = pad.substring(0, pad.length - str.length) + str;
        return ans;
    }

    this.getBancaMoneda = function(banca){
        return banca.descripcion + ' (' + banca.monedaAbreviatura + ')';
    }

    this.toSecuencia = function(idTicket, codigo = null){
        var str = "" + idTicket;
        var pad = "000000000";
        if(codigo == null)
            return pad.substring(0, pad.length - str.length) + str;
        else
            return codigo + "-" + pad.substring(0, pad.length - str.length) + str;
    }

    this.agregarSignoYletrasParaImprimir = function(jugada, sorteo){
        switch(sorteo){
          case "Pale":
            return jugada[0] + jugada[1] + '-' + jugada[2] + jugada[3];
            break;
          case "Pick 3 Box":
            return jugada + "B";
            break;
          case "Pick 4 Box":
            return jugada + "B";
            break;
          case "Pick 3 Straight":
            return jugada + "S";
            break;
          case "Pick 4 Straight":
            return jugada + "S";
            break;
          case "Tripleta":
            return jugada[0] + jugada[1] + '-' + jugada[2] + jugada[3] + '-' + jugada[4] + jugada[5];
            break;
          default:
            return jugada;
            break;
        }
      }

      // Here's how to do basic integer<->byte (string/char) operations in JavaScript. 
      // Took me a good while to dig this up.

        function intToChar(integer) {
            return String.fromCharCode(integer)
        }
    
        function charToInt(char) {
            return char.charCodeAt(0)
        }
    this.base64url = function(source) {
        // Encode in classical base64
        encodedSource = CryptoJS.enc.Base64.stringify(source);
      
        // Remove padding equal characters
        encodedSource = encodedSource.replace(/=+$/, '');
      
        // Replace characters according to base64url specifications
        encodedSource = encodedSource.replace(/\+/g, '-');
        encodedSource = encodedSource.replace(/\//g, '_');
      
        return encodedSource;
      }

    this.createJWT = function(data, key = apiKeyGlobal){
        var header = {
        "alg": "HS256",
        "typ": "JWT"
        };

        var stringifiedHeader = CryptoJS.enc.Utf8.parse(JSON.stringify(header));
        var encodedHeader = this.base64url(stringifiedHeader);

        // data = {
        // "id": 1337,
        // "username": "john.doe"
        // };

        var stringifiedData = CryptoJS.enc.Utf8.parse(JSON.stringify(data));
        var encodedData = this.base64url(stringifiedData);

        var token = encodedHeader + "." + encodedData;

        var signature = CryptoJS.HmacSHA256(token, key);
        signature = this.base64url(signature);

        var signedToken = token + "." + signature;
        return signedToken;
    }

   this.tienePermiso = function(permiso){
    //    console.log("helper.js tienePermiso: ", permisosGlobal.find(c => c.descripcion == permiso));
       
       return permisosGlobal.findIndex(c => c.descripcion == permiso) != -1;
   } 

   this.millisToSeconds = function(millis){
    return ((millis % 60000) / 1000).toFixed(0);
   }

    this.millisToMinutesAndSeconds = function(millis) {
    var minutes = Math.floor(millis / 60000);
    var seconds = ((millis % 60000) / 1000).toFixed(0);
    return minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
  }

  this.addMinutes = function(date, minutes) {
    return new Date(date.getTime() + minutes*60000);
}

this.esSuperpale = function(jugada) {
    return (jugada.length == 5 && jugada.substr(jugada.length - 1) == "s");
}

});