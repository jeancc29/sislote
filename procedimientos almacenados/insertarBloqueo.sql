use valentin;
DROP FUNCTION IF EXISTS `insertarBloqueo`;
DELIMITER $$

CREATE FUNCTION insertarBloqueo(jugada varchar(8), idLoteria int, idSorteo int, sorteo varchar(50), idBanca int, idLoteriaSuperpale int) RETURNS Bigint
     READS SQL DATA
DETERMINISTIC
BEGIN
	
	/******************* INSERTAR BLOQUEO O ACTUALIZAR ************************/
	set @idStock = null;
	set @idMoneda = (select b.idMoneda from branches b where b.id = idBanca);
    
    if sorteo != 'Super pale'
    then
		if exists(select id from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 0 and s.idMoneda = @idMoneda)
		then
			set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.ignorarDemasBloqueos = 1 and s.idMoneda = @idMoneda);
			-- if exists(select id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.ignorarDemasBloqueos = 1)
			if @idStock is not null
			then 
				-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.ignorarDemasBloqueos = 1;
				update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
			elseif exists(select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.ignorarDemasBloqueos = 1 and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1)
			then
				set @montoBloqueo = (select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.ignorarDemasBloqueos = 1 and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.esGeneral, stocks.ignorarDemasBloqueos, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(1, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, 1, 1, now(), now(), @idMoneda);
				set @idStock = (SELECT LAST_INSERT_ID());
			else
				set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 0 and s.idMoneda = @idMoneda);
				-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 0;
				update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
			end if;
		elseif exists(select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda)
		then
			set @ignorarDemasBloqueos = (select s.ignorarDemasBloqueos from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda);
			if @ignorarDemasBloqueos != 1
			then
				set @montoBloqueo = (select b.monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				if @montoBloqueo is not null then
					insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now(), @idMoneda);
					set @idStock = (SELECT LAST_INSERT_ID());
				else
					set @montoBloqueo = (select b.monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = idLoteria and b.idDia = @idDia and b.idSorteo = idSorteo and b.idMoneda = @idMoneda);
					if @montoBloqueo is not null then
						insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, now(), now(), @idMoneda);
						set @idStock = (SELECT LAST_INSERT_ID());
					else
						-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1;
						set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda);
						update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
					end if;
				end if;
			else
				-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1;
				set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda);
				update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
			end if;
		else
			
			if exists(select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.ignorarDemasBloqueos = 1 and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1)
			then	
				set @montoBloqueo = (select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.esGeneral, stocks.ignorarDemasBloqueos, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(1, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, 1, 1, now(), now(), @idMoneda);
				set @idStock = (SELECT LAST_INSERT_ID());
			else
				
				/************* OBTENEMOS EL STOCK DE LA TABLA BLOQUEOS JUGADAS *************/
				set @montoBloqueo = (select b.monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				if @montoBloqueo is not null then
					insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now(), @idMoneda);
					set @idStock = (SELECT LAST_INSERT_ID());
				else
					/**************** OBTENEMOS EL STOCK DE LA TABLA BLOCKSPLAYSGENERALS *******/
					set @montoBloqueo = (select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
					if @montoBloqueo is not null then
						insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.esGeneral, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(1, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, 1, now(), now(), @idMoneda);
						set @idStock = (SELECT LAST_INSERT_ID());
					end if;
					
					 if @montoBloqueo is null then
					/**************** OBTENEMOS EL STOCK POR BANCA DE LA TABLA BLOCKLOTTERIES *******/
						set @montoBloqueo = (select b.monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = idLoteria and b.idDia = @idDia and b.idSorteo = idSorteo and b.idMoneda = @idMoneda);
						if @montoBloqueo is not null then
							insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, now(), now(), @idMoneda);
							set @idStock = (SELECT LAST_INSERT_ID());
						else
							set @montoBloqueo = (select b.monto from blocksgenerals b where b.idLoteria = idLoteria and b.idDia = @idDia and b.idSorteo = idSorteo and b.idMoneda = @idMoneda);
							insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esGeneral, stocks.created_at, stocks.updated_at, stocks.idMoneda) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now(), @idMoneda);
							set @idStock = (SELECT LAST_INSERT_ID());
					end if;
				end if;
				
				
			
			end if;
		end if;
			
			
		   
		end if;
	else
		/*************************** 
				SUPER PALE EMPIEZA AQUI 
                MONTO SUPER PALE
                Debo ordenar de menor a mayor los idloteria y idloteriaSuperpale, 
               el idLoteria tendra el numero menor y el idLoteriaSuper tendra el numero mayor
		******************************************/
       if idLoteria > idLoteriaSuperpale
	   then
			set @tmp = idLoteria;
			set idLoteria = idLoteriaSuperpale;
			set idLoteriaSuperpale = @tmp;
	   end if;
       
		if exists(select id from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 0 and s.idMoneda = @idMoneda)
		then
			set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.ignorarDemasBloqueos = 1 and s.idMoneda = @idMoneda);
			-- if exists(select id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.ignorarDemasBloqueos = 1)
			if @idStock is not null
			then 
				-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.ignorarDemasBloqueos = 1;
				update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
			elseif exists(select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.ignorarDemasBloqueos = 1 and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1)
			then
				set @montoBloqueo = (select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.ignorarDemasBloqueos = 1 and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.esGeneral, stocks.ignorarDemasBloqueos, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(1, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, 1, 1, now(), now(), @idMoneda, idLoteriaSuperpale);
				set @idStock = (SELECT LAST_INSERT_ID());
			else
				set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 0 and s.idMoneda = @idMoneda);
				-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 0;
				update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
			end if;
		elseif exists(select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda)
		then
			set @ignorarDemasBloqueos = (select s.ignorarDemasBloqueos from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda);
			if @ignorarDemasBloqueos != 1
			then
				set @montoBloqueo = (select b.monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				if @montoBloqueo is not null then
					insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now(), @idMoneda, idLoteriaSuperpale);
					set @idStock = (SELECT LAST_INSERT_ID());
				else
					set @montoBloqueo = (select b.monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = idLoteria and b.idDia = @idDia and b.idSorteo = idSorteo and b.idMoneda = @idMoneda);
					if @montoBloqueo is not null then
						insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, now(), now(), @idMoneda, idLoteriaSuperpale);
						set @idStock = (SELECT LAST_INSERT_ID());
					else
						-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1;
						set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda);
						update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
					end if;
				end if;
			else
				-- update stocks s set s.monto = s.monto - @monto where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.jugada = jugada and s.idSorteo = idSorteo and s.esGeneral = 1;
				set @idStock = (select s.id from stocks s where date(s.created_at) = date(now()) and s.idLoteria = idLoteria and s.idLoteriaSuperpale = idLoteriaSuperpale and s.jugada COLLATE utf8mb4_general_ci = jugada and s.idSorteo = idSorteo and s.esGeneral = 1 and s.idMoneda = @idMoneda);
				update stocks s set s.monto = s.monto - @monto where s.id = @idStock;
			end if;
		else
			
			if exists(select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.ignorarDemasBloqueos = 1 and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1)
			then	
				set @montoBloqueo = (select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.esGeneral, stocks.ignorarDemasBloqueos, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(1, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, 1, 1, now(), now(), @idMoneda, idLoteriaSuperpale);
				set @idStock = (SELECT LAST_INSERT_ID());
			else
				
				/************* OBTENEMOS EL STOCK DE LA TABLA BLOQUEOS JUGADAS *************/
				set @montoBloqueo = (select b.monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
				if @montoBloqueo is not null then
					insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now(), @idMoneda, idLoteriaSuperpale);
					set @idStock = (SELECT LAST_INSERT_ID());
				else
					/**************** OBTENEMOS EL STOCK DE LA TABLA BLOCKSPLAYSGENERALS *******/
					set @montoBloqueo = (select b.monto from blocksplaysgenerals b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idLoteria = idLoteria and b.jugada COLLATE utf8mb4_general_ci = jugada and b.idSorteo = idSorteo and b.status = 1 and b.idMoneda = @idMoneda order by b.id desc limit 1);
					if @montoBloqueo is not null then
						insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esBloqueoJugada, stocks.esGeneral, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(1, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, 1, now(), now(), @idMoneda, idLoteriaSuperpale);
						set @idStock = (SELECT LAST_INSERT_ID());
					end if;
					
					 if @montoBloqueo is null then
					/**************** OBTENEMOS EL STOCK POR BANCA DE LA TABLA BLOCKLOTTERIES *******/
						set @montoBloqueo = (select b.monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = idLoteria and b.idDia = @idDia and b.idSorteo = idSorteo and b.idMoneda = @idMoneda);
						if @montoBloqueo is not null then
							insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, now(), now(), @idMoneda, idLoteriaSuperpale);
							set @idStock = (SELECT LAST_INSERT_ID());
						else
							set @montoBloqueo = (select b.monto from blocksgenerals b where b.idLoteria = idLoteria and b.idDia = @idDia and b.idSorteo = idSorteo and b.idMoneda = @idMoneda);
							insert into stocks(stocks.idBanca, stocks.idLoteria, stocks.idSorteo, stocks.jugada, stocks.montoInicial, stocks.monto, stocks.esGeneral, stocks.created_at, stocks.updated_at, stocks.idMoneda, stocks.idLoteriaSuperpale) values(idBanca, idLoteria, idSorteo, jugada, @montoBloqueo, @montoBloqueo - @monto, 1, now(), now(), @idMoneda, idLoteriaSuperpale);
							set @idStock = (SELECT LAST_INSERT_ID());
					end if;
				end if;
				
				
			
			end if;
		end if;
			
			
		   
 		end if;
    end if;
	/************ END INSERTAR BLOQUEO O ACTUALIZAR ***************/
    
    return @idStock;
    END$$
DELIMITER $$