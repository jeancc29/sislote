USE `valentin`;
DROP procedure IF EXISTS `guardarVenta`;

USE `valentin`;
DROP procedure IF EXISTS `valentin`.`guardarVenta`;
;

DELIMITER $$
USE `valentin`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `guardarVenta`(IN pidUsuario varchar(30), idBanca int, idVentaHash varchar(200), compartido int, descuentoMonto int, hayDescuento boolean, total decimal(20, 2), jugadas json)
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
    DECLARE EXIT HANDLER FOR SQLEXCEPTION 
	BEGIN
		  ROLLBACK;
		  select 1 as errores, @mensaje as mensaje;
	END;
    
	DROP TEMPORARY TABLE IF EXISTS `TempTable`;
    CREATE TEMPORARY TABLE TempTable (id int primary key auto_increment, loterias json not null); 
	set @errores = 0;
    
     set sql_safe_updates = 0;
     
      if not exists(select id from branches where branches.status = 1 and branches.id = idBanca)
    then
		set @mensaje = 'Error: Esta banca esta desactivada';
        SIGNAL SQLSTATE '45000';
    end if;
     
    -- getIdVentaTemporal
    -- select idVenta from idventatemporals where idventatemporals.idVentaHash = idVentaHash COLLATE utf8mb4_unicode_ci and idventatemporals.idBanca = idBanca into idVenta;
   set idVenta = (select idventatemporals.idVenta from idventatemporals where idventatemporals.idVentaHash = idVentaHash COLLATE utf8mb4_unicode_ci and idventatemporals.idBanca = idBanca);
   
   
   if idVenta is null 
		then
			set @mensaje = 'Error de seguridad: idVenta incorrecto';
            SIGNAL SQLSTATE '45000';
        end if;
    -- END getIdVentaTemporal
    
    -- EN CASO DE QUE LA VENTA EXISTA LA VAMOS A BORRAR PORQUE ESO QUIERE DECIR QUE FUE UNA VENTA QUE SE REALIZO PERO POR UN PROBLEMA DE CONEXION EL SERVIDOR NO LA RETORNO
    -- BORRAR VENTA ERRONEA
    select id from sales where id = idVenta and sales.idBanca = idBanca into idTicket;
    if idTicket is not null
	then
		-- delete from salesdetails where salesdetails.idVenta = idVenta;
        -- delete from sales where sales.id = idVenta;
		-- delete from tickets where id = idTicket;
        
        -- eliminamos el ticket asignandole el status = 5
        -- update sales set status = 5 where sales.id = idVenta;
        select idVenta(idBanca) into idVentaHash;
        set idVenta = (select idventatemporals.idVenta from idventatemporals where idventatemporals.idVentaHash = idVentaHash COLLATE utf8mb4_unicode_ci and idventatemporals.idBanca = idBanca);
    end if;
    -- END BORRAR VENTA ERRONEA
    
    
    if not exists(select id from users where id = pidUsuario and status = 1)
		then
			set @mensaje = 'Error: El usuario no existe';
            SIGNAL SQLSTATE '45000';
        end if;
       
	if (select count(p.descripcion) from users u inner join permission_user pu on u.id = pu.idUsuario inner join permissions p on p.id = pu.idPermiso where p.descripcion in('Vender tickets', 'Acceso al sistema') and u.id = pidUsuario) < 2
    then
		set @mensaje = 'Error: No tiene permiso para realizar esta accion vender y acceso';
        SIGNAL SQLSTATE '45000';
    end if;
    
   
    
    if (select id from branches where branches.idUsuario = pidUsuario) != idBanca
    then
		if not exists(select count(p.descripcion) from users u inner join permission_user pu on u.id = pu.idUsuario inner join permissions p on p.id = pu.idPermiso where p.descripcion = 'Jugar como cualquier banca' and u.id = pidUsuario)
        then
			set @mensaje = 'Error: No tiene permiso para realizar esta accion jugar como cualquier banca';
            SIGNAL SQLSTATE '45000';
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
            
            set @idDia = (select id from days where days.wday = wday);
            
    -- DATE_FORMAT(now(),'%H:%i:%s') < DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') 
    
    if DATE_FORMAT(now(),'%H:%i:%s') < (select DATE_FORMAT(concat(date(now()), ' ', bd.horaApertura),'%H:%i:%s') from branches b inner join branches_days bd on b.id = bd.idBanca inner join days d on d.id = bd.idDia where d.wday = wday and b.id = idBanca)
    then
		set @mensaje = 'Error: la banca aun no ha abierto';
        SIGNAL SQLSTATE '45000';
    end if;
    
    if DATE_FORMAT(now(),'%H:%i:%s') > (select DATE_FORMAT(concat(date(now()), ' ', bd.horaCierre),'%H:%i:%s') from branches b inner join branches_days bd on b.id = bd.idBanca inner join days d on d.id = bd.idDia where d.wday = wday and b.id = idBanca)
    then
		set @mensaje = 'Error: la banca ha cerrado';
        SIGNAL SQLSTATE '45000';
    end if;
    
    
    if total > (select limiteVenta from branches where id = idBanca)
    then
		set @mensaje = 'Error: A excedido el limite de ventas de la banca';
        SIGNAL SQLSTATE '45000';
    end if;

    

    
   


-- set @a = JSON_ARRAY_APPEND(@a, '$', @j);
-- set @a = JSON_ARRAY_APPEND(@a, '$', "2");
-- select JSON_SEARCH(@a,"one", '2');

-- Aqui vamos a obtener las loterias
-- Aquie termina de obtener las loterias


if @errores = 0
then
	/********** INICIAMOS LA TRANSACCION *****************/    
	start transaction;
    
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

	insert into 
		sales(sales.id, sales.compartido, sales.idUsuario, sales.idBanca, sales.total, sales.subTotal, sales.descuentoMonto, sales.hayDescuento, sales.idTicket, sales.created_at, sales.updated_at)
        values(idVenta, compartido, pidUsuario, idBanca, total, subTotal, descuentoMonto, hayDescuento, idTicket, now(), now());
        
       set @contadorJugadas = 0;
      -- INSERTAR JUGADAS  
      
	while @contadorJugadas < JSON_LENGTH(jugadas) do
		 set @idLoteriaJugada = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].idLoteria'));
         set @idLoteria = JSON_UNQUOTE(@idLoteriaJugada);
         set @idLoteriaSuperpale = JSON_EXTRACT(jugadas, CONCAT('$[', @contadorJugadas, '].idLoteriaSuperpale'));
         set @idLoteriaSuperpale = JSON_UNQUOTE(@idLoteriaSuperpale);

        
				if exists(select id from awards where idLoteria = @idLoteria and date(created_at) = date(now()) )
                then
					set @mensaje = 'La loteria ya tiene premios registrados';
                    SIGNAL SQLSTATE '45000';
                end if;
                
                
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
						-- if exists(select d.id from draws d inner join draw_lottery dl on dl.idSorteo = d.id where dl.idLoteria = @idLoteria and d.descripcion = 'Super pale' COLLATE utf8mb4_unicode_ci) and (select count(id) from drawsrelations where idLoteriaPertenece = @idLoteria) > 1
                        -- then
							-- set @idSorteo = 4;
                            -- set @sorteo = 'Super pale';
						-- else
							-- set @idSorteo = 2;
                            -- set @sorteo = 'Pale';
						-- end if;
                        set @idSorteo = 2;
                        set @sorteo = 'Pale';
					end if;
				elseif length(@jugada) = 5 then
					if instr(@jugada, '+') = 5 then
						select id, descripcion from draws where descripcion = 'Pick 4 Box' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
					elseif instr(@jugada, '-') = 5 then
						select id, descripcion from draws where descripcion = 'Pick 4 Straight' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
					elseif instr(@jugada, 's') = 5 then
						select id, descripcion from draws where descripcion = 'Super pale' COLLATE utf8mb4_unicode_ci into @idSorteo, @sorteo;
					end if;
				elseif length(@jugada) = 6 then
					set @idSorteo = 3;
                     set @sorteo = 'Tripleta';
				end if;
                
                
				/******************* END DETERMINAR ID SORTEO *********************/
                
                -- VERIFICAMOS SI EL SORTEO PERTENECE A ESTA LOTERIA
				if not exists (select d.id from draws d inner join draw_lottery dl on d.id = dl.idSorteo where dl.idLoteria = @idLoteria and dl.idSorteo = @idSorteo)
					then
					set @mensaje = concat('El sorteo no existe para la loteria ' , (select descripcion from lotteries where id = @idLoteria));
                    SIGNAL SQLSTATE '45000';
					-- select 1 as errores, 'El sorteo no existe para la loteria es incorrecto' as mensaje, @idSorteo, JSON_UNQUOTE(@idLoteria); 
					-- select d.id as existe from draws d inner join draw_lottery dl on d.id = dl.idSorteo where dl.idLoteria = JSON_UNQUOTE(@idLoteria) and dl.idSorteo = @idSorteo;
				-- VERIFICAMOS SI EL SORTEO PERTENECE A la LOTERIA SUPER PALE
                elseif @idSorteo = 4 then
					if not exists (select d.id from draws d inner join draw_lottery dl on d.id = dl.idSorteo where dl.idLoteria = @idLoteriaSuperpale and dl.idSorteo = @idSorteo)
					then
						set @mensaje = concat('El sorteo no existe para la loteria ' , (select descripcion from lotteries where id = @idLoteriaSuperpale)); 
                        SIGNAL SQLSTATE '45000';
					end if;
                end if;
			-- END sorteoEXISTE
            
            
            -- VERIFICAMOS SI LA LOTERIA ABRE HOY Y QUE ESTE ABIERTA
			  if DATE_FORMAT(now(),'%H:%i:%s') < (select DATE_FORMAT(concat(date(now()), ' ', dl.horaApertura),'%H:%i:%s') from lotteries l inner join day_lottery dl on l.id = dl.idLoteria inner join days d on d.id = dl.idDia where d.wday = wday and l.id = @idLoteria)
				then
					set @mensaje = 'Error: La loteria aun no ha abierto';
                    SIGNAL SQLSTATE '45000';
				end if;
			-- END LOTERIA ABRE HOY
            
            -- VERIFICAMOS SI LA LOTERIA ESTA CERRADA
            -- DATE_FORMAT(now(),'%H:%i:%s') > (select DATE_FORMAT(concat(date(now()), ' ', dl.horaCierre),'%H:%i:%s') from lotteries l inner join day_lottery dl on l.id = dl.idLoteria inner join days d on d.id = dl.idDia where d.wday = wday and l.id = @idLoteria)
            set @horaCierre = null;
            set @minutosExtras = null;
            select dl.horaCierre, dl.minutosExtras from lotteries l inner join day_lottery dl on l.id = dl.idLoteria inner join days d on d.id = dl.idDia where d.wday = wday and l.id = @idLoteria into @horaCierre, @minutosExtras;
			if DATE_FORMAT(now(),'%H:%i:%s') > (select DATE_FORMAT(concat(date(now()), ' ', @horaCierre),'%H:%i:%s'))
			then
				 if not exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar fuera de horario')
				then
					if exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar minutos extras')
					then
						-- verificamos si la hora actual es mayor que la hora de cierre con los minutos extras sumados
						if DATE_FORMAT(now(),'%H:%i:%s') > (select DATE_FORMAT(date_add(concat(date(now()), ' ', @horaCierre), INTERVAL @minutosExtras MINUTE),'%H:%i:%s'))
						then
							set @mensaje = concat('Error: minutos extras han pasado, la loteria ' , (select descripcion from lotteries where id = @idLoteria), ' ha cerrado');
                            SIGNAL SQLSTATE '45000';
						end if;
                    else
						set @mensaje = concat('Error: la loteria ' , (select descripcion from lotteries where id = @idLoteria), ' ha cerrado');
                        SIGNAL SQLSTATE '45000';
                    end if;
				end if;
			end if;
			-- END VERIFICAMOS SI LA LOTERIA ESTA CERRADA
            
            -- GET MONTO DISPONIBLE
            set @montoDisponible = (select montoDisponible(@jugada, @idLoteria, idBanca, @idLoteriaSuperpale));
			
				
			if (@montoDisponible <@monto) or (@montoDisponible <@monto) is null then
			if not exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar sin disponibilidad') then
					set @mensaje = concat('No hay existencia suficiente para la jugada ' , @jugada, ' en la loteria ', (select descripcion from lotteries where id = @idLoteria));
                    SIGNAL SQLSTATE '45000';
				end if;
			end if;
			-- END GET MONTO DISPONIBLE
            
                
                /****************** quitarUltimoCaracter *******************/
                if @sorteo = 'Pick 3 Box' or @sorteo = 'Pick 4 Straight' or @sorteo = 'Pick 4 Box' or @sorteo = 'Super pale' 
				then
					set @jugada = substring(@jugada, 1, length(@jugada) - 1);
				end if;
				/******** END QUITAR ULTIMO CARACTER ******************/
                
                
				
                /******************* INSERTAR BLOQUEO O ACTUALIZAR ************************/
                
                set @idStock = (select insertarBloqueo(@jugada, @idLoteria, @idSorteo, @sorteo, idBanca, @idLoteriaSuperpale));
				-- set @idStock = 1;
               
               if @idStock = -1 then
					set @mensaje = concat('No hay bloqueos registrados ' , @jugada, ' en la loteria ', (select descripcion from lotteries where id = @idLoteria));
                    SIGNAL SQLSTATE '45000';
               end if;
                
                /************ END INSERTAR BLOQUEO O ACTUALIZAR ***************/
                set @comision = 0;
                set @datosComisiones = (select JSON_OBJECT('directo', c.directo, 'pale', c.pale, 'tripleta', c.tripleta, 'superPale', c.superPale, 'pick3Straight', c.pick3Straight, 'pick3Box', c.pick3Box, 'pick4Straight', c.pick4Straight, 'pick4Box', c.pick4Box) from commissions c where c.idBanca = idBanca and c.idLoteria = @idLoteria ORDER BY c.id DESC LIMIT 1);
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
                
                if @comision is null then
					set @comision = 0;
				end if;
                -- select @sorteo, @monto, @comision, JSON_UNQUOTE(JSON_EXTRACT(@datosComisiones, CONCAT('$.directo')));
                insert into realtimes(idAfectado, tabla) values(@idStock, 'stocks');
                insert into salesdetails(salesdetails.idVenta, salesdetails.idLoteria, salesdetails.idSorteo, salesdetails.jugada, salesdetails.monto, salesdetails.premio, salesdetails.comision, salesdetails.idStock, salesdetails.idLoteriaSuperpale, salesdetails.created_at, salesdetails.updated_at) values(idVenta, @idLoteria, @idSorteo, @jugada, @monto, 0, @comision, @idStock, @idLoteriaSuperpale, now(), now()); 
			
            
		set @contadorJugadas = @contadorJugadas + 1;
        
    end while;
-- END INSERTAR JUGADAS

	commit;
-- END TRANSACTION COMMIT
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
                
                'idBanca', s.idBanca,
                'codigo', b.codigo,
                'banca', JSON_OBJECT('id', b.id, 'descripcion', b.descripcion, 'codigo', b.codigo, 'piepagina1', b.piepagina1, 'piepagina2', b.piepagina2, 'piepagina3', b.piepagina3, 'piepagina4', b.piepagina4, 'imprimirCodigoQr', b.imprimirCodigoQr),
                
                'idTicket', s.idTicket,
                'ticket', t.id,
                'codigoBarra', t.codigoBarra,
                'codigoQr', TO_BASE64(t.codigoBarra)
             
			)) as ventas from sales s
            inner join users u on u.id = s.idUsuario 
            inner join branches b on b.id = s.idBanca
            inner join tickets t on t.id = s.idTicket
            left join cancellations ca on ca.idTicket = s.idTicket
            left join users uc on uc.id = ca.idUsuario
            where date(s.created_at) = date(now()) and s.status not in(0, 5) and s.idBanca = idBanca order by t.id desc into ventas;
            
            
            
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
                'razon', ca.razon,
                'usuarioCancelacion', JSON_OBJECT('id', uc.id, 'usuario', uc.usuario),
                'fechaCancelacion', ca.created_at,
                'loterias', (select JSON_ARRAYAGG(JSON_OBJECT('id', lotteries.id, 'descripcion', lotteries.descripcion, 'abreviatura', lotteries.abreviatura, 'loteriaSuperpale', (select JSON_ARRAYAGG(JSON_OBJECT('id', lott.id, 'descripcion', lott.descripcion, 'abreviatura', lott.abreviatura)) from lotteries lott where lott.id in (select distinct salesdetails.idLoteriaSuperpale from salesdetails where salesdetails.idVenta = s.id and salesdetails.idLoteria = lotteries.id and salesdetails.idLoteriaSuperpale is not null)) )) from lotteries where id in(select distinct salesdetails.idLoteria from salesdetails where salesdetails.idVenta = s.id)),
                'jugadas', (select JSON_ARRAYAGG(JSON_OBJECT('id', sd.id, 'idVenta', sd.idVenta, 'jugada', sd.jugada, 'idLoteria', sd.idLoteria, 'idSorteo', sd.idSorteo, 'monto', sd.monto, 'premio', sd.premio, 'pagado', sd.pagado, 'status', sd.status, 'sorteo', d.descripcion, 'fechaPagado', (select created_at from logs where tabla = 'salesdetails' and idRegistroTablaAccion = sd.id) , 'pagadoPor', (select us.usuario from logs lo inner join users us on us.id = lo.idUsuario where lo.idRegistroTablaAccion = sd.id and lo.tabla = 'salesdetails'), 'idLoteriaSuperpale', sd.idLoteriaSuperpale, 'loteriaSuperpale', (select JSON_OBJECT('id', lott.id, 'descripcion', lott.descripcion, 'abreviatura', lott.abreviatura) from lotteries lott where lott.id = sd.idLoteriaSuperpale) )) from salesdetails sd inner join draws d on sd.idSorteo = d.id where sd.idVenta = s.id),
                'fecha', concat(date(s.created_at), ' ', DATE_FORMAT(s.created_at, "%r")),
                'usuario', u.usuario,
                'banca', JSON_OBJECT('id', b.id, 'descripcion', b.descripcion, 'codigo', b.codigo, 'piepagina1', b.piepagina1, 'piepagina2', b.piepagina2, 'piepagina3', b.piepagina3, 'piepagina4', b.piepagina4, 'imprimirCodigoQr', b.imprimirCodigoQr),
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
                'monedaAbreviatura', (select c.abreviatura from coins c where c.id = b.idMoneda),
                'codigo', b.codigo,
                'status', b.status,
                'descontar', b.descontar,
                'deCada', b.deCada,
                'idMoneda', b.idMoneda,
                'ventasDelDia', (select sum(sales.total) from sales where date(created_at) = date(now()) and status not in(0, 5) and sales.idBanca = b.id),
                'ticketsDelDia', (select count(sales.id) from sales where date(created_at) = date(now()) and status not in(0, 5) and sales.idBanca = b.id)
			)) as bancas from branches b
            inner join users u on u.id = b.idUsuario where b.status = 1 into bancas;
            
			-- Convert wday to wday laravel
           set wday =  weekday(now());
           -- le sumamos uno porque en mysql el wday del lunes comienza en el cero pero en php el wday del lunes empieza desde el 1
           set wday = wday + 1;
           -- si el wday es igual a 7 entonces lo hacemos igual a cero ya que el wday en php solo llega hasta el 6
           if wday = 7 then
				set wday = 0;
            end if;
     --  or exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar minutos extras')
     if exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and (p.descripcion = 'Jugar fuera de horario' or p.descripcion = 'Jugar minutos extras'))
			then
				INSERT INTO TempTable(loterias) select JSON_OBJECT(
					'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura, 'horaCierre', dl.horaCierre, 'minutosExtras', dl.minutosExtras,
                    'sorteos', (select JSON_ARRAYAGG(JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)) from draws d where id in(select dl.idSorteo from draw_lottery dl where dl.idLoteria = l.id))
				) as loterias from lotteries l
				inner join day_lottery dl on dl.idLoteria = l.id
				inner join days d on d.id = dl.idDia
				where l.id not in(select idLoteria from awards where date(created_at) = date(now())) and d.wday = wday and l.status = 1 order by dl.horaCierre asc;
	else
    INSERT INTO TempTable(loterias) select JSON_OBJECT(
				'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura, 'horaCierre', dl.horaCierre, 'minutosExtras', dl.minutosExtras,
                'sorteos', (select JSON_ARRAYAGG(JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)) from draws d where id in(select dl.idSorteo from draw_lottery dl where dl.idLoteria = l.id))
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
    
    
    -- 				CREAR IDVENTA TEMPORAL .
		select idVenta(idBanca) into idVentaHash;
         
	if @errores = 0 then
		select  0 as errores, 'Se ha guardado correctamente' as mensaje, venta, ventas, idBanca, total_ventas, total_jugadas, caracteristicasGenerales, pidUsuario, bancas, loterias, idVentaHash;
   end if;
END$$

DELIMITER ;
;

