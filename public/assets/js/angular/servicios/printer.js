myApp.service('printerService', function(helperService){
    var intentosDeConexion = 0;
    var maximoIntentosDeConexion = 10

    this.connectToQzServer = function(){
        var self = this;
        if(!qz.websocket.isActive()){
            qz.websocket.connect().then(function() {
            
            }).catch(function(err) { 
                intentosDeConexion++;
                if(intentosDeConexion <= maximoIntentosDeConexion)
                    self.connectToQzServer();
                console.error(err); 
            });
        }
    }
    
    this.printTicket = async function(venta, typeTicket = CMD.TICKET_ORIGINAL){
        if(helperService.empty(localStorage.getItem("impresora"), 'string') == true){
            window.abrirModalImpresora(true);
            return;
        }
		// var data = [
        //     CMD.TEXT_FORMAT.TXT_NORMAL,
        //     'Raw Data\n',
        //     CMD.TEXT_FORMAT.TXT_2HEIGHT,
        //     'More Raw Data\n',
        //     CMD.TEXT_FORMAT.TXT_2WIDTH,
        //     'More Raw Data\n',
        //     CMD.TEXT_FORMAT.TXT_4SQUARE,
        //     CMD.TEXT_FORMAT.TXT_ALIGN_CT,
        //     'FINAL\n\n\n\n',
        //     CMD.PAPER.PAPER_FULL_CUT,
        //     ];
        // var data = this.generateTicket(venta, typeTicket);
        var data = this.generateTicketGrande(venta, typeTicket);

            // qz.print(config, data).catch(function(e) { console.error(e); });
            if(!qz.websocket.isActive())
            {
                qz.websocket.connect().then(function() {
                var config = qz.configs.create(localStorage.getItem("impresora"));
                return qz.print(config, data);
                }).catch(function(err) { console.error(err); });
            }else{
                // await qz.websocket.disconnect()
                var config = qz.configs.create(localStorage.getItem("impresora"));
                // await qz.websocket.connect(config)
                return qz.print(config, data);
            }
    }

    this.printCuadre = async function(datos){
        if(helperService.empty(localStorage.getItem("impresora"), 'string') == true){
            window.abrirModalImpresora(true);
            return;
        }
		
        var data = this.generateCuadre(datos);

            // qz.print(config, data).catch(function(e) { console.error(e); });
            if(!qz.websocket.isActive())
            {
                qz.websocket.connect().then(function() {
                var config = qz.configs.create(localStorage.getItem("impresora"));
                return qz.print(config, data);
                }).catch(function(err) { console.error(err); });
            }else{
                // await qz.websocket.disconnect()
                var config = qz.configs.create(localStorage.getItem("impresora"));
                // await qz.websocket.connect(config)
                return qz.print(config, data);
            }
    }

    

    this.probarImpresora = async function(){
        if(helperService.empty(localStorage.getItem("impresora"), 'string') == true){
            window.abrirModalImpresora(true);
            return;
        }
		
        var data = [];
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "**PRUEBA EXITOSA**", 3);

            // qz.print(config, data).catch(function(e) { console.error(e); });
            if(!qz.websocket.isActive())
            {
                qz.websocket.connect().then(function() {
                var config = qz.configs.create("POS58 Printer");
                return qz.print(config, data);
                }).catch(function(err) { console.error(err); });
            }else{
                // await qz.websocket.disconnect()
                var config = qz.configs.create("POS58 Printer");
                // await qz.websocket.connect(config)
                return qz.print(config, data);
            }
    }

    this.generateTicket = function(venta, typeTicket){
        var self = this;
        var data = [];
        data.push(CMD.TEXT_FORMAT.TXT_ALIGN_CT);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, venta.banca.descripcion);
        data = this.printTicketHeader(data, typeTicket);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, venta.fecha);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL,"Ticket: " + helperService.toSecuencia(venta.idTicket, venta.banca.codigo));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, "Fecha: " + venta.fecha);
        
        if(typeTicket == CMD.TICKET_ORIGINAL || typeTicket == CMD.TICKET_PAGADO)
            data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, venta.codigoBarra);
        
        var total = 0;
        venta.loterias.forEach(function(valor, indice, arrayLoterias){
            var primerCicloJugadas = true;
            var contadorCicleJugadas = 0;
            var totalPorLoteria = 0;
            var jugadas = self.getJugadasPertenecienteALoteria(arrayLoterias[indice].id, venta.jugadas, typeTicket);

            console.log("Juagads: ", jugadas);
            
            if(jugadas.length > 0){
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "---------------");
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, arrayLoterias[indice].descripcion);
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "---------------");
            
                jugadas.forEach(function(valor, indiceJugadas, arrayJugadas){
                    var jugada = arrayJugadas[indiceJugadas];
                    var espaciosPrimerMonto = "         ";
                    var espaciosSegundaJugada = "       ";
                    var espaciosSegundoMonto = "         ";
                    total += helperService.redondear(jugada.monto);
                    totalPorLoteria += helperService.redondear(jugada.monto);

                    // map[map.length] = _getMapNuevo(cmd: CMD.left);
                    if(primerCicloJugadas){
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "JUGADA   MONTO  JUGADA   MONTO");
                        primerCicloJugadas = false;
                    }

                    if(((indiceJugadas + 1) % 2) == 0){ //PAR
                        var jugadaAnterior = helperService.agregarSignoYletrasParaImprimir(jugadas[indiceJugadas - 1].jugada, jugadas[indiceJugadas - 1].sorteo);
                        var montoAnterior = jugadas[indiceJugadas - 1].monto;
                        // espaciosPrimerMonto = self.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado(espaciosPrimerMonto, jugadaAnterior) + montoAnterior;
                        
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, self.quitarOPonerEspaciosJugada(false, montoAnterior, espaciosSegundaJugada) + helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), 0);
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, self.quitarOPonerEspaciosMonto(false, helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), espaciosSegundoMonto) + jugada.monto + self.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado("     ", jugada.monto));
                      }else{
                        // data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, "", 0);
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), 0);
                        
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_OFF, "", 0);
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, self.quitarOPonerEspaciosMonto(true, helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), espaciosPrimerMonto) + jugada.monto + self.siEsUltimaJugadaDarSaltoDeLinea(indiceJugadas, jugadas.length, jugada.monto), 0);
                      }
                });

                var loteriasLength = (typeTicket == CMD.TICKET_PAGADO) ? venta.loterias.length - 1 : venta.loterias.length;
                if(loteriasLength > 1){
                    data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "total: " + String(totalPorLoteria), 2);
                }
                
            }

            
        });

        if(venta.hayDescuento == 1){
            data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "\nsubTotal: " + venta.total)
            data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "descuento: "+ venta.descuentoMonto);
            total -= helperService.redondear(venta.descuentoMonto);
        }

        var saltoLineaTotal = "\n";
        if((typeTicket != CMD.TICKET_ORIGINAL && typeTicket != CMD.TICKET_PAGADO) || venta.banca.imprimirCodigoQr == 0)
            saltoLineaTotal += "\n\n";
        
        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "TOTAL: " + String(helperService.redondear(total)) + saltoLineaTotal )
        
        if(typeTicket == CMD.TICKET_CANCELADO)
            data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "** CANCELADO **\n\n\n");
        
        if(typeTicket == CMD.TICKET_ORIGINAL){
            var banca = venta.banca;

            if(banca.piepagina1 != null){
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, banca.piepagina1);
            }
            if(banca.piepagina2 != null)
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, banca.piepagina2);
            if(banca.piepagina3 != null)
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, banca.piepagina3);
            if(banca.piepagina4 != null)
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, banca.piepagina4);
            if(banca.imprimirCodigoQr == 1)
                data = CMD.QR(data, venta.codigoQr);
            
            data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "\n\n");
            }else{
            data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "\n\n");
            }

            data.push(CMD.PAPER.PAPER_FULL_CUT);
            data.push("\x1b\x69");

        return data;
    }

    this.generateTicketGrande = function(venta, typeTicket){
        var self = this;
        var data = [];
        data.push(CMD.TEXT_FORMAT.TXT_ALIGN_CT);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, venta.banca.descripcion);
        data = this.printTicketHeader(data, typeTicket);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, venta.fecha);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL,"Ticket: " + helperService.toSecuencia(venta.idTicket, venta.banca.codigo));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, "Fecha: " + venta.fecha);
        
        if(typeTicket == CMD.TICKET_ORIGINAL || typeTicket == CMD.TICKET_PAGADO)
            data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, venta.codigoBarra);
        
        var total = 0;
        venta.loterias.forEach(function(valor, indice, arrayLoterias){
            var primerCicloJugadas = true;
            var contadorCicleJugadas = 0;
            var totalPorLoteria = 0;
            var jugadas = self.getJugadasPertenecienteALoteria(arrayLoterias[indice].id, venta.jugadas, typeTicket);

            console.log("Juagads: ", jugadas);
            
            if(jugadas.length > 0){
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "---------------");
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, arrayLoterias[indice].descripcion);
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "---------------");
            
                jugadas.forEach(function(valor, indiceJugadas, arrayJugadas){
                    var jugada = arrayJugadas[indiceJugadas];
                    var espaciosPrimerMonto = "            ";
                    var espaciosSegundaJugada = "             ";
                    var espaciosSegundoMonto = "            ";
                    total += helperService.redondear(jugada.monto);
                    totalPorLoteria += helperService.redondear(jugada.monto);

                    // map[map.length] = _getMapNuevo(cmd: CMD.left);
                    if(primerCicloJugadas){
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "JUGADA      MONTO        JUGADA      MONTO");
                        primerCicloJugadas = false;
                    }

                    if(((indiceJugadas + 1) % 2) == 0){ //PAR
                        var jugadaAnterior = helperService.agregarSignoYletrasParaImprimir(jugadas[indiceJugadas - 1].jugada, jugadas[indiceJugadas - 1].sorteo);
                        var montoAnterior = jugadas[indiceJugadas - 1].monto;
                        // espaciosPrimerMonto = self.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado(espaciosPrimerMonto, jugadaAnterior) + montoAnterior;
                        
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, self.quitarOPonerEspaciosJugada(false, montoAnterior, espaciosSegundaJugada) + helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), 0);
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, self.quitarOPonerEspaciosMonto(false, helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), espaciosSegundoMonto) + jugada.monto + self.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado("     ", jugada.monto));
                      }else{
                        // data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, "", 0);
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_ON, helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), 0);
                        
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_BOLD_OFF, "", 0);
                        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, self.quitarOPonerEspaciosMonto(true, helperService.agregarSignoYletrasParaImprimir(jugada.jugada, jugada.sorteo), espaciosPrimerMonto) + jugada.monto + self.siEsUltimaJugadaDarSaltoDeLinea(indiceJugadas, jugadas.length, jugada.monto), 0);
                      }
                });

                var loteriasLength = (typeTicket == CMD.TICKET_PAGADO) ? venta.loterias.length - 1 : venta.loterias.length;
                if(loteriasLength > 1){
                    data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "total: " + String(totalPorLoteria));
                }
                
            }

            
        });

        if(venta.hayDescuento == 1){
            data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "\nsubTotal: " + venta.total)
            data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "descuento: "+ venta.descuentoMonto);
            total -= helperService.redondear(venta.descuentoMonto);
        }

        var saltoLineaTotal = "\n";
        if((typeTicket != CMD.TICKET_ORIGINAL && typeTicket != CMD.TICKET_PAGADO) || venta.banca.imprimirCodigoQr == 0)
            saltoLineaTotal += "\n\n";
        
        data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "TOTAL: " + String(helperService.redondear(total)))
        
        if(typeTicket == CMD.TICKET_CANCELADO)
            data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "** CANCELADO **\n\n\n");
        
        if(typeTicket == CMD.TICKET_ORIGINAL){
            var banca = venta.banca;

            if(banca.piepagina1 != null){
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, banca.piepagina1);
            }
            if(banca.piepagina2 != null)
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, banca.piepagina2);
            if(banca.piepagina3 != null)
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, banca.piepagina3);
            if(banca.piepagina4 != null)
                data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_NORMAL, banca.piepagina4);
            if(banca.imprimirCodigoQr == 1)
                data = CMD.QR(data, venta.codigoQr);
            
            data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "\n\n");
            }else{
            data = self.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "\n\n");
            }

            data.push(CMD.PAPER.PAPER_FULL_CUT);
            data.push("\x1b\x69");

        return data;
    }

    this.printTicketHeader = function(data, typeTicket){
        switch (typeTicket) {
            case CMD.TICKET_ORIGINAL:
                return this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, CMD.TICKET_ORIGINAL);
                break;
            case CMD.TICKET_PAGADO:
                return this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, CMD.TICKET_ORIGINAL);
                break;
            case CMD.TICKET_CANCELADO:
                return this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, CMD.TICKET_CANCELADO);
                break;
            default:
                return this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, CMD.TICKET_COPIA);
                break;
        }
    }

     this.generateCuadre = function(datos){
        var data = [];
        data.push(CMD.TEXT_FORMAT.TXT_ALIGN_CT);
        console.log("printerService generateCUadre: ", datos);
        
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, "Cuadre");
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_4SQUARE, datos.banca.descripcion);
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Balance hasta la fecha: " + String(datos.balanceHastaLaFecha));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Tickets pendientes: " + String(datos.pendientes));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Tickets perdedores: " + String(datos.perdedores));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Tickets ganadores:  " + String(datos.ganadores));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Total:              " + String(datos.total));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Ventas:             " + String(datos.ventas));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Comisiones:         " + String(datos.comisiones));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "descuentos:         " + String(datos.descuentos));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "premios:            " + String(datos.premios));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "neto:               " + String(datos.neto));
        data = this.addCommandAndTextToData(data, CMD.TEXT_FORMAT.TXT_2HEIGHT, "Balance mas ventas: " + String(datos.balanceActual));
        return data;
      }

    this.addCommandAndTextToData = function(data, cmd, text, lineSpace = 1){
        var lineSpaceString = '';
        for (let contador = 0; contador < lineSpace; contador++) {
            lineSpaceString += '\n';
        }

        data.push(cmd);
        if(lineSpace == 0)
            data.push(text);
        else
            data.push(text + lineSpaceString);
        return data;
    }

    this.getJugadasPertenecienteALoteria = function(idLoteria, jugadas, typeTicket = CMD.TICKET_ORIGINAL){
        console.log("Dentro jugadas idLoteria: ", idLoteria);
        console.log("Dentro jugadas: ", jugadas);
        
        if(typeTicket == CMD.TICKET_PAGADO)
            return jugadas.filter(j => j.idLoteria == idLoteria && j.status == 0);
        else
            return jugadas.filter(j => j.idLoteria == idLoteria);
    }
    
    
     this.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado = function(tamano, jugadaOMonto){
        // print("tamano: $tamano - ${tamano.length} | jugadaOMonto: $jugadaOMonto - ${jugadaOMonto.length}");
        // print("tamanoFinal: ${tamano.substring(0, tamano.length - jugadaOMonto.length)} - ${tamano.substring(0, tamano.length - jugadaOMonto.length).length}");
        return tamano.substring(0, tamano.length - String(jugadaOMonto).length);
      }

      this.siEsUltimaJugadaDarSaltoDeLinea = function(contadorCicloJugadas, cantidadJugadas, monto){
         var saltoLinea = "";
        if((contadorCicloJugadas + 1) == cantidadJugadas)
            // saltoLinea = "                     \n";
            // saltoLinea = this.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado("                     ", monto) + "\n";
            saltoLinea = this.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado("                              ", monto) + "\n";

        return saltoLinea;
      }

      this.quitarOPonerEspaciosJugada = function(primeraJugadaEnLaFila, montoAnterior = '', espaciosDePrimerMontoA2daJugada){
        
            console.log("Dentro else quitarEspacioJugada");
            
            return this.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado(espaciosDePrimerMontoA2daJugada, montoAnterior);
        

        // return '';
      }

      this.quitarOPonerEspaciosMonto = function(primerMontoEnLaFila, jugadaAnterior = '', espaciosDeJugadaAMonto = ''){
        var espaciosUtlimaJugada = "                     "; //21 espacios
        if(primerMontoEnLaFila){
            return this.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado(espaciosDeJugadaAMonto, jugadaAnterior);
        }else{
            return this.quitarEspaciosDeAcuerdoAlTamanoDeLaJugadaOMontoDado(espaciosDeJugadaAMonto, jugadaAnterior);
        }
            
        return '';
      }

      

});