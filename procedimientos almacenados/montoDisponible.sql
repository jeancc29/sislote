use loterias;
DROP FUNCTION IF EXISTS `montoDisponible`;
DELIMITER $$

CREATE FUNCTION montoDisponible(jugada varchar(8), idLoteria int, idBanca int) RETURNS DECIMAL(10,2)
     READS SQL DATA
DETERMINISTIC
BEGIN
    
		declare wday int;
        declare idSorteo int;
        declare sorteo varchar(100);
        declare idDia int;
        declare montoDisponible decimal(10, 2);
    
    -- Convert wday to wday laravel
           set wday =  weekday(now());
           -- le sumamos uno porque en mysql el wday del lunes comienza en el cero pero en php el wday del lunes empieza desde el 1
           set wday = wday + 1;
           -- si el wday es igual a 7 entonces lo hacemos igual a cero ya que el wday en php solo llega hasta el 6
           if wday = 7 then
				set wday = 0;
            end if;
            
            
             -- DETERMINAR ID SORTEO
               
             
                set idSorteo = 0;
                if length(jugada) = 2 then
					set idSorteo = 1;
                elseif length(jugada) = 3 then
					select id from draws where descripcion = 'Pick 3 Straight' into idSorteo;
				elseif length(jugada) = 4 then
					if instr(jugada, '+') = 4 then
						select id from draws where descripcion = 'Pick 3 Box' into idSorteo;
					else 
						-- Validamos de que la loteria tenga el sorteo super pale y que el drawsrealations sea mayor que 1
						if exists(select d.id from draws d inner join draw_lottery dl on dl.idSorteo = d.id where dl.idLoteria = idLoteria and d.descripcion = 'Super pale' COLLATE utf8mb4_unicode_ci) and (select count(id) from drawsrelations where idLoteriaPertenece = idLoteria) > 1
                        then
							set idSorteo = 4;
						else
							set idSorteo = 2;
						end if;
					end if;
				elseif length(jugada) = 5 then
					if instr(jugada, '+') = 5 then
						select id from draws where descripcion = 'Pick 4 Box'  into idSorteo;
					elseif instr(jugada, '-') = 5 then
						select id from draws where descripcion = 'Pick 4 Straight' into idSorteo;
					end if;
				elseif length(jugada) = 6 then
					set idSorteo = 3;
				end if;
                
                set sorteo = (select descripcion from draws where draws.id = idSorteo);
                -- END DETERMINAR ID SORTEO
            
            
            
            
					-- primero quitarUltimoCaracter
			if sorteo = 'Pick 3 Box' || sorteo = 'Pick 4 Straight' || sorteo = 'Pick 4 Box' 
				then
					set jugada = substring(jugada, 1, length(jugada) - 1);
				end if;
				-- end quitarUltimoCaracter
                
				set idDia = (select id from days where days.wday = wday);
				
				select s.monto from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.jugada = jugada COLLATE utf8mb4_unicode_ci and s.idSorteo = idSorteo into montoDisponible;
                if montoDisponible is null 
				then
					select b.monto from blocksplays b where date(b.fechaDesde) <= date(now()) and date(b.fechaHasta) >= date(now()) and b.idBanca = idBanca and b.idLoteria = idLoteria and b.jugada = jugada COLLATE utf8mb4_unicode_ci and b.idSorteo = idSorteo and b.status = 1 into montoDisponible;
                    if montoDisponible is null 
					then
						select b.monto from blockslotteries b where b.idBanca = idBanca and b.idLoteria = idLoteria and b.idDia = idDia and b.idSorteo = idSorteo into montoDisponible;
						
                    end if;
					
				end if;
                
                 if montoDisponible is null 
					then
 						set montoDisponible = 0;
 					end if;
				
				
				-- END GET MONTO DISPONIBLE
      
	-- select 'hola';
      return montoDisponible; -- (select s.monto from stocks s where date(s.created_at) = date(now()) and s.idBanca = idBanca and s.idLoteria = idLoteria and s.jugada = jugada COLLATE utf8mb4_unicode_ci and s.idSorteo = idSorteo);
END$$
DELIMITER $$


-- select montoDisponible("17", 11, 11)