
-- quitar COLLATE utf8mb4_unicode_ci del mysql del servidor linux de digitalocean porque eso no funciona ahi

use loterias;
DROP PROCEDURE IF EXISTS `guardarVenta`;
delimiter ;;
CREATE PROCEDURE `guardarVenta` (IN pidUsuario varchar(30), idBanca int, idVentaHash varchar(200), compartido int, descuentoMonto int, hayDescuento boolean, total decimal(20, 2), jugadas json)
BEGIN
-- , OUT psalida nvarchar(1000)
	declare idVenta bigint; 
    declare idTicket bigint; 
    declare codigoBarraCorrecto boolean; 
    declare codigoBarra varchar(11);
    declare loteriasJugadas json;
    
    
	declare errores int; declare total_ventas int; declare total_jugadas int; declare wday int;
    declare mensaje varchar(100);
    declare ventas LONGTEXT;
    declare venta LONGTEXT;
    declare bancas LONGTEXT;
    declare loterias LONGTEXT;
    declare caracteristicasGenerales varchar(200);
    declare siguienteIdVenta bigInt;
    declare idBancaIdVentaTemporal int;
	DROP TEMPORARY TABLE IF EXISTS `TempTable`;
    CREATE TEMPORARY TABLE TempTable (id int primary key auto_increment, loterias json not null); 
	set @errores = 0;
     set sql_safe_updates = 0;
     
    -- getIdVentaTemporal
    -- select idVenta from idventatemporals where idventatemporals.idVentaHash = idVentaHash COLLATE utf8mb4_unicode_ci and idventatemporals.idBanca = idBanca into idVenta;
   set idVenta = (select idventatemporals.idVenta from idventatemporals where idventatemporals.idVentaHash = idVentaHash COLLATE utf8mb4_unicode_ci and idventatemporals.idBanca = idBanca);
   
   
   if idVenta is null 
		then
			set @errores = 1;
			select 1 as errores, 'Error de seguridad: idVenta incorrecto' as mensaje, idVenta as idVenta, idVentaHash, idBanca;
        end if;
    -- END getIdVentaTemporal
    
    -- EN CASO DE QUE LA VENTA EXISTA LA VAMOS A BORRAR PORQUE ESO QUIERE DECIR QUE FUE UNA VENTA QUE SE REALIZO PERO POR UN PROBLEMA DE CONEXION EL SERVIDOR NO LA RETORNO
    -- BORRAR VENTA ERRONEA
    select id from sales where id = idVenta and sales.idBanca = idBanca into idTicket;
    if idTicket is not null
	then
		delete from salesdetails where salesdetails.idVenta = idVenta;
        delete from sales where sales.id = idVenta;
		delete from tickets where id = idTicket;
    end if;
    -- END BORRAR VENTA ERRONEA
    
    
    if not exists(select id from users where id = pidUsuario and status = 1)
		then
			set @errores = 1;
			select 1 as errores, 'Error: El usuario no existe' as mensaje;
        end if;
       
	if (select count(p.descripcion) from users u inner join permission_user pu on u.id = pu.idUsuario inner join permissions p on p.id = pu.idPermiso where p.descripcion in('Vender tickets', 'Acceso al sistema') and u.id = pidUsuario) != 2
    then
		set @errores = 1;
		select 1 as errores, 'Error: No tiene permiso para realizar esta accion' as mensaje;
    end if;
    
    if (select id from branches where branches.idUsuario = pidUsuario) != idBanca
    then
		if not exists(select count(p.descripcion) from users u inner join permission_user pu on u.id = pu.idUsuario inner join permissions p on p.id = pu.idPermiso where p.descripcion = 'Jugar como cualquier banca' and u.id = pidUsuario)
        then
			set @errores = 1;
			select 1 as errores, 'Error: No tiene permiso para realizar esta accion' as mensaje;
        end if;
    end if;
    
    
    	-- Convert wday to wday laravel
           set wday =  weekday(now());
           -- le sumamos uno porque en mysql el wday del lunes comienza en el cero pero en php el wday del lunes empieza desde el 1
           set wday = wday + 1;
           -- si el wday es igual a 7 entonces lo hacemos igual a cero ya que el wday en php solo llega hasta el 6
           if wday = 7 then
				set wday = 0;
            end if;
            
    -- DATE_FORMAT(now(),'%H:%i:%s') < DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') 
    
    if DATE_FORMAT(now(),'%H:%i:%s') < (select DATE_FORMAT(concat(date(now()), ' ', bd.horaApertura),'%H:%i:%s') from branches b inner join branches_days bd on b.id = bd.idBanca inner join days d on d.id = bd.idDia where d.wday = wday and b.id = idBanca)
    then
		set @errores = 1;
		select 1 as errores, 'Error: la banca aun no ha abierto' as mensaje;
    end if;
    
    if DATE_FORMAT(now(),'%H:%i:%s') > (select DATE_FORMAT(concat(date(now()), ' ', bd.horaCierre),'%H:%i:%s') from branches b inner join branches_days bd on b.id = bd.idBanca inner join days d on d.id = bd.idDia where d.wday = wday and b.id = idBanca)
    then
		set @errores = 1;
		select 1 as errores, 'Error: la banca ha cerrado' as mensaje;
    end if;
    
    
    if total > (select limiteVenta from branches where id = idBanca)
    then
		set @errores = 1;
		select 1 as errores, 'Error: A excedido el limite de ventas de la banca' as mensaje;
    end if;
    
    
    -- Generamos y guardamos codigo de barra
    set codigoBarraCorrecto = false;
		while codigoBarraCorrecto != true do
			-- select ROUND((RAND() * (max-min))+min)
			select ROUND((RAND() * (9999999999-1111111111))+1111111111) into codigoBarra;
            if not exists(select id from tickets where tickets.codigoBarra = codigoBarra COLLATE utf8mb4_unicode_ci)
            then
				insert into tickets(tickets.idBanca, tickets.codigoBarra) values(idBanca, codigoBarra);
                select id from tickets where tickets.codigoBarra = codigoBarra COLLATE utf8mb4_unicode_ci into idTicket;
                set codigoBarraCorrecto = true;
            end if;
        end while;
    
   


-- set @a = JSON_ARRAY_APPEND(@a, '$', @j);
-- set @a = JSON_ARRAY_APPEND(@a, '$', "2");
-- select JSON_SEARCH(@a,"one", '2');

-- Aqui vamos a obtener las loterias
set @contador = 0;
set @loterias = JSON_ARRAY();
while @contador < JSON_LENGTH(jugadas) do
	set @idLoteria = JSON_EXTRACT(jugadas, CONCAT('$[', @contador, '].idLoteria'));
    set @idLoteria = cast(@idLoteria as char);
	set @idLoteria = JSON_UNQUOTE(@idLoteria);
        
    if JSON_SEARCH(@loterias,"one", @idLoteria) is null
    then
		set @loterias = JSON_ARRAY_APPEND(@loterias, '$', @idLoteria);
	end if;
    set @contador = @contador + 1;
end while;



-- Validamos la existencia de la jugada
set @contadorLoterias = 0;
while @contadorLoterias < JSON_LENGTH(@loterias) do
		set @contadorJugadas = 0;
        set @idLoteria = JSON_EXTRACT(@loterias, CONCAT('$[', @contadorLoterias, ']'));
        set @idLoteria = JSON_UNQUOTE(@idLoteria);
	while @contadorJugadas < JSON_LENGTH(jugadas) do
		 set @idLoteriaJugada = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].idLoteria'));
         set @idLoteriaJugada = JSON_UNQUOTE(@idLoteriaJugada);
		 if @idLoteria = @idLoteriaJugada
			then
				if exists(select id from awards where idLoteria = @idLoteria and date(created_at) = date(now()) )
                then
					set @errores = 1;
					select 1 as errores, 'La loteria ya tiene premios registrados' as mensaje;
                end if;
                
                -- DETERMINAR ID SORTEO
                set @jugada = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].jugada'));
                set @jugada = JSON_UNQUOTE(@jugada);
                set @idSorteo = 0;
                if length(@jugada) = 2 then
					set @idSorteo = 1;
                elseif length(@jugada) = 3 then
					select id from draws where descripcion = 'Pick 3 Straight' into @idSorteo;
				elseif length(@jugada) = 4 then
					if instr(@jugada, '+') = 4 then
						select id from draws where descripcion = 'Pick 3 Box' into @idSorteo;
					else 
						-- Validamos de que la loteria tenga el sorteo super pale y que el drawsrealations sea mayor que 1
						if exists(select d.id from draws d inner join draw_lottery dl on dl.idSorteo = d.id where dl.idLoteria = @idLoteria and d.descripcion = 'Super pale' COLLATE utf8mb4_unicode_ci) and (select count(id) from drawsrelations where idLoteriaPertenece = @idLoteria) > 1
                        then
							set @idSorteo = 4;
						else
							set @idSorteo = 2;
						end if;
					end if;
				elseif length(@jugada) = 5 then
					if instr(@jugada, '+') = 5 then
						select id from draws where descripcion = 'Pick 4 Box' into @idSorteo;
					elseif instr(@jugada, '-') = 5 then
						select id from draws where descripcion = 'Pick 4 Straight' into @idSorteo;
					end if;
				elseif length(@jugada) = 6 then
					set @idSorteo = 3;
				end if;
                -- END DETERMINAR ID SORTEO
                
                
					  -- VALIDAR DECIMALES DEL MONTO ES VALIDO
				set @montoValido = false;
				set @monto = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].monto'));
                set @monto = JSON_UNQUOTE(@monto);
				if instr(@monto, '.') != 0 then
					set @sorteo = (select draws.descripcion from draws where draws.id = @idSorteo);
					if @sorteo = 'Pick 3 Box' || @sorteo = 'Pick 3 Straight' || @sorteo = 'Pick 4 Straight' || @sorteo = 'Pick 4 Box' 
					then
					-- si el monto redondeado es igual al monto normal eso quiere decir que le monto tiene cero como decimales por lo tanto es correcto, ejemplo 1 == 1.00
						
							if @monto = 0.50 or round(@monto) = @monto then
								set @montoValido = true;
							end if;
					else
						if round(@monto) = @monto then
								set @montoValido = true;
							end if;
					end if;
				else
					set @montoValido = true;
				end if;
				
				if @montoValido = false then
					set @errores = 1;
					select 1 as errores, concat('Error: El monto de la jugada ' , @jugada , ' es incorrecto') as mensaje;
				end if;
			-- END MONTO VALIDO
			
			-- banca-> loteriaExisteYTienePagoCombinaciones, no creo que esta valiacion sea necesaria pero voy a dejar este comentario por si acaso
			-- END banca-> loteriaExisteYTienePagoCombinaciones
			
			-- VERIFICAMOS SI EL SORTEO PERTENECE A ESTA LOTERIA
				if not exists (select d.id from draws d inner join draw_lottery dl on d.id = dl.idSorteo where dl.idLoteria = @idLoteria and dl.idSorteo = @idSorteo)
				then
					set @errores = 1;
					select 1 as errores, concat('El sorteo no existe para la loteria ' , (select descripcion from lotteries where id = @idLoteria)) as mensaje; 
					-- select 1 as errores, 'El sorteo no existe para la loteria es incorrecto' as mensaje, @idSorteo, JSON_UNQUOTE(@idLoteria); 
					-- select d.id as existe from draws d inner join draw_lottery dl on d.id = dl.idSorteo where dl.idLoteria = JSON_UNQUOTE(@idLoteria) and dl.idSorteo = @idSorteo;
				end if;
			-- END sorteoEXISTE
			
			-- VERIFICAMOS SI LA LOTERIA ABRE HOY Y QUE ESTE ABIERTA
			  if DATE_FORMAT(now(),'%H:%i:%s') < (select DATE_FORMAT(concat(date(now()), ' ', dl.horaApertura),'%H:%i:%s') from lotteries l inner join day_lottery dl on l.id = dl.idLoteria inner join days d on d.id = dl.idDia where d.wday = wday and l.id = @idLoteria)
				then
					set @errores = 1;
					select 1 as errores, 'Error: La loteria aun no ha abierto' as mensaje;
				end if;
			-- END LOTERIA ABRE HOY
			
			-- VERIFICAMOS SI LA LOTERIA ESTA CERRADA
			if DATE_FORMAT(now(),'%H:%i:%s') > (select DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') from lotteries l inner join day_lottery dl on l.id = dl.idLoteria inner join days d on d.id = dl.idDia where d.wday = wday and l.id = @idLoteria)
			then
				 if not exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar fuera de horario')
				then
					set @errores = 1;
					select 1 as errores, concat('Error: la loteria ' , (select descripcion from lotteries where id = @idLoteria), ' ha cerrado') as mensaje;
				end if;
			end if;
			-- END VERIFICAMOS SI LA LOTERIA ESTA CERRADA
			
		   
			-- GET MONTO DISPONIBLE
					-- primero quitarUltimoCaracter
			if @sorteo = 'Pick 3 Box' || @sorteo = 'Pick 4 Straight' || @sorteo = 'Pick 4 Box' 
				then
					set @jugada = substring(@jugada, 1, length(@jugada) - 1);
				end if;
				
				set @idDia = (select id from days where days.wday = wday);
				set @montoDisponible = 0;
				set @montoDisponible = (select monto from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = @idLoteria and s.jugada = @jugada COLLATE utf8mb4_unicode_ci and s.idSorteo = @idSorteo);
                if @montoDisponible is null 
				then
					set @montoDisponible = (select monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = @idLoteria and b.jugada = @jugada COLLATE utf8mb4_unicode_ci and b.idSorteo = @idSorteo and b.status = 1);
                    if @montoDisponible is null 
					then
						set @montoDisponible = (select monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = @idLoteria and b.idDia = @idDia and b.idSorteo = @idSorteo);
						
                    end if;
					
				end if;
                
                if @montoDisponible is null 
					then
						set @montoDisponible = 0;
					end if;
				
				if (@montoDisponible <@monto) or (@montoDisponible <@monto) is null then
					set @errores = 1;
					select 1 as errores, concat('No hay existencia suficiente para la jugada ' , @jugada, ' en la loteria ', (select descripcion from lotteries where id = @idLoteria)) as mensaje;
				end if;
				-- END GET MONTO DISPONIBLE
                
               
			end if;
            
            
          
            
            
		set @contadorJugadas = @contadorJugadas + 1;
    end while;
    set @contadorLoterias = @contadorLoterias + 1;
end while;
-- END VALIDAR JUGADAS

if @errores = 0
then
	insert into 
		sales(sales.id, sales.compartido, sales.idUsuario, sales.idBanca, sales.total, sales.subTotal, sales.descuentoMonto, sales.hayDescuento, sales.idTicket, sales.created_at, sales.updated_at)
        values(idVenta, compartido, pidUsuario, idBanca, total, subTotal, descuentoMonto, hayDescuento, idTicket, now(), now());
        
        
      -- INSERTAR JUGADAS  
set @contadorLoterias = 0;
while @contadorLoterias < JSON_LENGTH(@loterias) do
		set @contadorJugadas = 0;
        set @idLoteria = JSON_EXTRACT(@loterias, CONCAT('$[', @contadorLoterias, ']'));
        set @idLoteria = JSON_UNQUOTE(@idLoteria);
	while @contadorJugadas < JSON_LENGTH(jugadas) do
		 set @idLoteriaJugada = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].idLoteria'));
         set @idLoteriaJugada = JSON_UNQUOTE(@idLoteriaJugada);
		 if @idLoteria = @idLoteriaJugada
			then
				
                
                /******************* DETERMINAR ID SORTEO ************************/
                set @jugada = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].jugada'));
                set @jugada = JSON_UNQUOTE(@jugada);
                set @monto = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].monto'));
                set @monto = JSON_UNQUOTE(@monto);
                
                set @idSorteo = 0;
                if length(@jugada) = 2 then
					set @idSorteo = 1;
                    set @sorteo = 'Directo';
                elseif length(@jugada) = 3 then
					select id, descripcion from draws where descripcion = 'Pick 3 Straight' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
				elseif length(@jugada) = 4 then
					if instr(@jugada, '+') = 4 then
						select id, descripcion  from draws where descripcion = 'Pick 3 Box' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
					else 
						-- Validamos de que la loteria tenga el sorteo super pale y que el drawsrealations sea mayor que 1
						if exists(select d.id from draws d inner join draw_lottery dl on dl.idSorteo = d.id where dl.idLoteria = @idLoteria and d.descripcion = 'Super pale' COLLATE utf8mb4_unicode_ci) and (select count(id) from drawsrelations where idLoteriaPertenece = @idLoteria) > 1
                        then
							set @idSorteo = 4;
                            set @sorteo = 'Super pale';
						else
							set @idSorteo = 2;
                            set @sorteo = 'Pale';
						end if;
					end if;
				elseif length(@jugada) = 5 then
					if instr(@jugada, '+') = 5 then
						select id, descripcion from draws where descripcion = 'Pick 4 Box' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
					elseif instr(@jugada, '-') = 5 then
						select id, descripcion from draws where descripcion = 'Pick 4 Straight' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
					end if;
				elseif length(@jugada) = 6 then
					set @idSorteo = 3;
                     set @sorteo = 'Tripleta';
				end if;
                
                
				/******************* END DETERMINAR ID SORTEO *********************/
                
                
                /****************** quitarUltimoCaracter *******************/
                if @sorteo = 'Pick 3 Box' || @sorteo = 'Pick 4 Straight' || @sorteo = 'Pick 4 Box' 
				then
					set @jugada = substring(@jugada, 1, length(@jugada) - 1);
				end if;
				/******** END QUITAR ULTIMO CARACTER ******************/
				
                /******************* INSERTAR BLOQUEO O ACTUALIZAR ************************/
                if exists(select id from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = @idLoteria and s.jugada = @jugada and s.idSorteo = @idSorteo)
                then
					update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = @idLoteria and s.jugada = @jugada and s.idSorteo = @idSorteo;
                else
					/************* OBTENEMOS EL STOCK DE LA TABLA BLOQUEOS JUGADAS *************/
					set @montoBloqueo = (select b.monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = @idLoteria and b.jugada = @jugada and b.idSorteo = @idSorteo and b.status = 1);
					if @montoBloqueo is not null then
						insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.created_at, stocks.updated_at) values(idBanca, @idLoteria, @idSorteo, @jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now());
                    else
						/**************** OBTENEMOS EL STOCK DE LA TABLA BLOCKLOTTERIES *******/
						set @montoBloqueo = (select b.monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = @idLoteria and b.idDia = @idDia and b.idSorteo = @idSorteo);
                        insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.created_at, stocks.updated_at) values(idBanca, @idLoteria, @idSorteo, @jugada, @montoBloqueo, @montoBloqueo - @monto, now(), now());
                    end if;
                end if;
				/************ END INSERTAR BLOQUEO O ACTUALIZAR ***************/
                set @comision = 0;
                set @datosComisiones = (select JSON_OBJECT('directo', c.directo, 'pale', c.pale, 'tripleta', c.tripleta, 'superPale', c.superPale, 'pick3Straight', c.pick3Straight, 'pick3Box', c.pick3Box, 'pick4Straight', c.pick4Straight, 'pick4Box', c.pick4Box) from commissions c where c.idBanca = idBanca and c.idLoteria = @idLoteria);
                if @sorteo = 'Directo' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.directo'))) / 100) * @monto;
				elseif @sorteo = 'Pale' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.pale'))) / 100) * @monto;
				elseif @sorteo = 'Tripleta' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.tripleta'))) / 100) * @monto;
				elseif @sorteo = 'Super pale' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.superPale'))) / 100) * @monto;
				elseif @sorteo = 'Pick 3 Straight' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.pick3Straight'))) / 100) * @monto;
				elseif @sorteo = 'Pick 3 Box' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.pick3Box'))) / 100) * @monto;
				elseif @sorteo = 'Pick 4 Straight' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.pick4Straight'))) / 100) * @monto;
				elseif @sorteo = 'Pick 4 Box' then
					set @comision = (JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.pick4Box'))) / 100) * @monto;
                end if;
                
                -- select @sorteo, @monto, @comision, JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.directo')));
                insert into salesdetails(salesdetails.idVenta, salesdetails.idLoteria, salesdetails.idSorteo, salesdetails.jugada, salesdetails.monto, salesdetails.premio, salesdetails.comision, salesdetails.created_at, salesdetails.updated_at) values(idVenta, @idLoteria, @idSorteo, @jugada, @monto, 0, @comision, now(), now()); 
			end if;
            /************** END IDLOTERIA = IDLOTERIAJUGADAS ***************/
            
          
            
            
		set @contadorJugadas = @contadorJugadas + 1;
    end while;
    set @contadorLoterias = @contadorLoterias + 1;
end while;
-- END INSERTAR JUGADAS
end if;
-- END INSERTAR VENTA
set sql_safe_updates = 1;


/********************************** indexPOst ***************************************/
	
    
  --   if idBanca is null then
-- 		set psalida = json_objectagg('idBanca', idBanca);
--        --  return psalida;
-- 	end if;
	-- aqui terminamos con el idBanca select json_objectagg('idBanca', idBanca);
    
    
    
		
select JSON_ARRAYAGG(JSON_OBJECT(
				'id', s.id, 'total', s.total,
                'idUsuario', s.idUsuario,
                'usuario', u.usuario,
                'idBanca', s.idBanca,
                'codigo', b.codigo,
                'banca', JSON_OBJECT('id', b.id, 'descripcion', b.descripcion, 'codigo', b.codigo, 'piepagina1', b.piepagina1, 'piepagina2', b.piepagina2, 'piepagina3', b.piepagina3, 'piepagina4', b.piepagina4),
                'descuentoPorcentaje', s.descuentoPorcentaje,
                'descuentoMonto', s.descuentoMonto,
                'hayDescuento', s.hayDescuento,
                'subTotal', s.subTotal,
                'idTicket', s.idTicket,
                'ticket', t.id,
                'codigoBarra', t.codigoBarra,
                'codigoQr', TO_BASE64(t.codigoBarra),
                'status', s.status,
                'created_at', s.created_at,
                'pagado', s.pagado,
                'montoPagado', (select sum(premio) from salesdetails where pagado = 1 and idVenta = s.id),
                'premio', (select sum(premio) from salesdetails where idVenta = s.id),
                'montoAPagar', (select sum(premio) from salesdetails where pagado = 0 and idVenta = s.id),
                'montoAPagar', ca.razon,
                'usuarioCancelacion', JSON_OBJECT('id', uc.id, 'usuario', uc.usuario),
                'fechaCancelacion', ca.created_at,
                'loterias', (select JSON_ARRAYAGG(JSON_OBJECT('id', id, 'descripcion', descripcion, 'abreviatura', abreviatura)) from lotteries where id in(select distinct idLoteria from salesdetails where idVenta = s.id)),
                'jugadas', (select JSON_ARRAYAGG(JSON_OBJECT('id', sd.id, 'idVenta', sd.idVenta, 'jugada', sd.jugada, 'idLoteria', sd.idLoteria, 'idSorteo', sd.idSorteo, 'monto', sd.monto, 'premio', sd.premio, 'pagado', sd.pagado, 'status', sd.status, 'sorteo', JSON_OBJECT('id', d.id, 'descripcion', d.descripcion), 'fechaPagado', (select created_at from logs where tabla = 'salesdetails' and idRegistroTablaAccion = sd.id) , 'pagadoPor', (select us.usuario from logs lo inner join users us on us.id = lo.idUsuario where lo.idRegistroTablaAccion = sd.id and lo.tabla = 'salesdetails'))) from salesdetails sd inner join draws d on sd.idSorteo = d.id where sd.idVenta = s.id),
                'fecha', concat(date(s.created_at), ' ', DATE_FORMAT(s.created_at, "%r")),
                'usuario', u.usuario
			)) as ventas from sales s
            inner join users u on u.id = s.idUsuario 
            inner join branches b on b.id = s.idBanca
            inner join tickets t on t.id = s.idTicket
            left join cancellations ca on ca.idTicket = s.idTicket
            left join users uc on uc.id = ca.idUsuario
            where date(s.created_at) = date(now()) and s.status not in(0, 5) and s.idBanca = idBanca into ventas;
            
            
            
            select JSON_ARRAYAGG(JSON_OBJECT(
				'id', s.id, 'total', s.total,
                'idUsuario', s.idUsuario,
                'usuario', u.usuario,
                'idBanca', s.idBanca,
                'codigo', b.codigo,
                'descuentoPorcentaje', s.descuentoPorcentaje,
                'descuentoMonto', s.descuentoMonto,
                'hayDescuento', s.hayDescuento,
                'subTotal', s.subTotal,
                'idTicket', s.idTicket,
                'ticket', t.id,
                'codigoBarra', t.codigoBarra,
                'codigoQr', TO_BASE64(t.codigoBarra),
                'status', s.status,
                'created_at', s.created_at,
                'pagado', s.pagado,
                'montoPagado', (select sum(premio) from salesdetails where pagado = 1 and idVenta = s.id),
                'premio', (select sum(premio) from salesdetails where idVenta = s.id),
                'montoAPagar', (select sum(premio) from salesdetails where pagado = 0 and idVenta = s.id),
                'montoAPagar', ca.razon,
                'usuarioCancelacion', JSON_OBJECT('id', uc.id, 'usuario', uc.usuario),
                'fechaCancelacion', ca.created_at,
                'loterias', (select JSON_ARRAYAGG(JSON_OBJECT('id', lotteries.id, 'descripcion', lotteries.descripcion, 'abreviatura', lotteries.abreviatura)) from lotteries where id in(select distinct salesdetails.idLoteria from salesdetails where salesdetails.idVenta = s.id)),
                'jugadas', (select JSON_ARRAYAGG(JSON_OBJECT('id', sd.id, 'idVenta', sd.idVenta, 'jugada', sd.jugada, 'idLoteria', sd.idLoteria, 'idSorteo', sd.idSorteo, 'monto', sd.monto, 'premio', sd.premio, 'pagado', sd.pagado, 'status', sd.status, 'sorteo', JSON_OBJECT('id', d.id, 'descripcion', d.descripcion), 'fechaPagado', (select created_at from logs where tabla = 'salesdetails' and idRegistroTablaAccion = sd.id) , 'pagadoPor', (select us.usuario from logs lo inner join users us on us.id = lo.idUsuario where lo.idRegistroTablaAccion = sd.id and lo.tabla = 'salesdetails'))) from salesdetails sd inner join draws d on sd.idSorteo = d.id where sd.idVenta = s.id),
                'fecha', concat(date(s.created_at), ' ', DATE_FORMAT(s.created_at, "%r")),
                'usuario', u.usuario,
                'banca', JSON_OBJECT('id', b.id, 'descripcion', b.descripcion, 'codigo', b.codigo, 'piepagina1', b.piepagina1, 'piepagina2', b.piepagina2, 'piepagina3', b.piepagina3, 'piepagina4', b.piepagina4),
                'usuarioObject', JSON_OBJECT('id', u.id, 'nombres', u.nombres, 'usuario', u.usuario)
			)) as ventas from sales s
            inner join users u on u.id = s.idUsuario 
            inner join branches b on b.id = s.idBanca
            inner join tickets t on t.id = s.idTicket
            left join cancellations ca on ca.idTicket = s.idTicket
            left join users uc on uc.id = ca.idUsuario
            where s.id = idVenta and s.idBanca = idBanca into venta;
	
			select sum(sales.total) from sales where date(created_at) = date(now()) and status not in(0, 5) and sales.idBanca = idBanca into total_ventas;
            select count(jugada) from salesdetails where date(created_at) = date(now()) and status not in(0, 5) and idBanca = idBanca into total_jugadas;

	
    
	
    select JSON_ARRAYAGG(JSON_OBJECT(
				'id', id, 'descuentoPorcentaje', descuentoPorcentaje, 'cantidadAplicar', cantidadAplicar, 'descuentoValor', descuentoValor, 'minutosParaCancelar', minutosParaCancelar
			)) from generals into caracteristicasGenerales;
            
            
	select JSON_ARRAYAGG(JSON_OBJECT(
				'id', b.id, 'descripcion', b.descripcion,
                'codigo', b.codigo,
                'status', b.status,
                'idUsuario', b.idUsuario,
                'usuario', u.usuario,
                'dueno', b.dueno,
                'localidad', b.localidad,
                'balanceDesactivacion', b.balanceDesactivacion,
                'limiteVenta', b.limiteVenta,
                'descontar', b.descontar,
                'deCada', b.deCada,
                'minutosCancelarTicket', b.minutosCancelarTicket,
                'piepagina1', b.piepagina1,
                'piepagina2', b.piepagina2,
                'piepagina3', b.piepagina3,
                'piepagina4', b.piepagina4,
                'dias', (select JSON_ARRAYAGG(JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)) from days d inner join branches_days bd on d.id = bd.idDia where bd.idBanca = b.id),
                'loterias', (select JSON_ARRAYAGG(JSON_OBJECT('id', l.id, 'descripcion', l.descripcion)) from lotteries l inner join branches_lotteries bl on l.id = bl.idLoteria where bl.idBanca = b.id),
                'comisiones', (select JSON_ARRAYAGG(JSON_OBJECT('id', id, 'idBanca', idBanca, 'idLoteria', idLoteria, 'directo', directo, 'pale', pale, 'tripleta', tripleta, 'superPale', superPale, 'pick3Straight', pick3Straight, 'pick3Box', pick3Box, 'pick4Straight', pick4Straight, 'pick4Box', pick4Box, 'created_at', created_at)) from commissions where idBanca = b.id),
                'pagosCombinaciones', (select JSON_ARRAYAGG(JSON_OBJECT('id', id, 'idBanca', idBanca, 'idLoteria', idLoteria, 'primera', primera, 'segunda', segunda, 'tercera', tercera, 'primeraSegunda', primeraSegunda, 'primeraTercera', primeraTercera, 'segundaTercera', segundaTercera, 'tresNumeros', tresNumeros, 'dosNumeros', dosNumeros, 'primerPago', primerPago, 'pick3TodosEnSecuencia', pick3TodosEnSecuencia, 'pick33Way', pick33Way, 'pick36Way', pick36Way, 'pick4TodosEnSecuencia', pick4TodosEnSecuencia, 'pick44Way', pick44Way, 'pick46Way', pick46Way, 'pick412Way', pick412Way, 'pick424Way', pick424Way, 'created_at', created_at)) from payscombinations where idBanca = b.id),
                'ventasDelDia', (select sum(sales.total) from sales where date(created_at) = date(now()) and status not in(0, 5) and sales.idBanca = b.id),
                'ticketsDelDia', (select count(sales.id) from sales where date(created_at) = date(now()) and status not in(0, 5) and sales.idBanca = b.id)
			)) as bancas from branches b
            inner join users u on u.id = b.idUsuario into bancas;
            
			-- Convert wday to wday laravel
           set wday =  weekday(now());
           -- le sumamos uno porque en mysql el wday del lunes comienza en el cero pero en php el wday del lunes empieza desde el 1
           set wday = wday + 1;
           -- si el wday es igual a 7 entonces lo hacemos igual a cero ya que el wday en php solo llega hasta el 6
           if wday = 7 then
				set wday = 0;
            end if;
     
     if exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar fuera de horario')
			then
				INSERT INTO TempTable(loterias) select JSON_OBJECT(
					'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura, 'horaCierre', dl.horaCierre
				) as loterias from lotteries l
				inner join day_lottery dl on dl.idLoteria = l.id
				inner join days d on d.id = dl.idDia
				where l.id not in(select idLoteria from awards where date(created_at) = date(now())) and d.wday = wday and l.status = 1 order by dl.horaCierre asc;
	else
    INSERT INTO TempTable(loterias) select JSON_OBJECT(
				'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura, 'horaCierre', dl.horaCierre
			) as loterias from lotteries l
            inner join day_lottery dl on dl.idLoteria = l.id
            inner join days d on d.id = dl.idDia
            where l.id not in(select idLoteria from awards where date(created_at) = date(now())) and d.wday = wday and l.status = 1 and DATE_FORMAT(now(),'%H:%i:%s') < DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') order by dl.horaCierre asc; 
		
        
        -- select JSON_ARRAYAGG(JSON_OBJECT(
-- 				'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura, 'horaCierre', dl.horaCierre
-- 			)) as loterias from lotteries l
--             inner join day_lottery dl on dl.idLoteria = l.id
--             inner join days d on d.id = dl.idDia
--             where l.id not in(select idLoteria from awards where date(created_at) = date(now())) and d.wday = wday and DATE_FORMAT(now(),'%H:%i:%s') < DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') order by dl.horaCierre desc into loterias;
	end if;
    
    -- select JSON_ARRAYAGG(JSON_OBJECT(
-- 				'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura
-- 			)) as loterias from lotteries l
--             inner join day_lottery dl on dl.idLoteria = l.id
--             inner join days d on d.id = dl.idDia
--             where l.id not in(select idLoteria from awards where date(created_at) = date(now())) and d.wday = wday and DATE_FORMAT(now(),'%H:%i:%s') < DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') order by dl.horaCierre asc into loterias;
    
    select json_arrayagg(TempTable.loterias) from TempTable into loterias;
    
    
    -- 				CREAR IDVENTA TEMPORAL 
		set idVentaHash = null;
		select max(id) from sales into siguienteIdVenta;
        if siguienteIdVenta is null then
			set siguienteIdVenta = 0;
		end if;
		set siguienteIdVenta = siguienteIdVenta + 1;
        
        select idventatemporals.idBanca from idventatemporals where idventatemporals.idVenta = siguienteIdVenta into idBancaIdVentaTemporal;
        if idBancaIdVentaTemporal is not null then
			if idBanca = idBancaIdVentaTemporal then
				 select idventatemporals.idVentaHash from idventatemporals where idventatemporals.idVenta = siguienteIdVenta into idVentaHash;
            else 
				select max(idventatemporals.idVenta) from idventatemporals into siguienteIdVenta;
                if siguienteIdVenta is null then
					set siguienteIdVenta = 0;
				end if;
				set siguienteIdVenta = siguienteIdVenta + 1;
            end if;
		end if;
        
        if idVentaHash is null then
			set @idVentaHash = AES_ENCRYPT(siguienteIdVenta, 'Sistema de loteria jean y valentin');
           -- AES_ENCRYPT retorna BLOB entonces lo convertimos a hex
           set @idVentaHash = HEX(@idVentaHash);
           set idVentaHash = @idVentaHash;
           -- para desencriptar le quitamos el HEX con la funcion UNHEX  
           -- select CAST(AES_DECRYPT(UNHEX(@j), 'hola') AS CHAR(50)) as desencriptado;
			-- select CAST(AES_DECRYPT(@j, 'Sistema de loteria jean y valentin') AS CHAR(50)) as desencriptado;
            -- select 'dentro insert idVentaHash';
			 insert into idventatemporals(idventatemporals.idBanca, idventatemporals.idVenta, idventatemporals.idVentaHash) values(idBanca, siguienteIdVenta, idVentaHash);
		end if;
         
	if @errores = 0 then
		select  0 as errores, 'Se ha guardado correctamente' as mensaje, venta, ventas, idBanca, total_ventas, total_jugadas, caracteristicasGenerales, pidUsuario, bancas, loterias, idVentaHash;
   end if;
END
 ;;
delimiter ;