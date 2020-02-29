use loterias;
DROP PROCEDURE IF EXISTS `indexPost`;
delimiter ;;
CREATE PROCEDURE `indexPost` (IN pidUsuario varchar(30), pidBanca int)
BEGIN
-- , OUT psalida nvarchar(1000)
	declare idBanca int; declare errores int; declare total_ventas int; declare total_jugadas int; declare wday int;
    declare mensaje varchar(100);
    declare ventas LONGTEXT;
    declare bancas LONGTEXT;
    declare loterias LONGTEXT;
    declare caracteristicasGenerales varchar(200);
    declare siguienteIdVenta bigInt;
    declare idVentaHash varchar(200);
    declare idBancaIdVentaTemporal int;
    
    	
	DROP TEMPORARY TABLE IF EXISTS `TempTable`;
	
    CREATE TEMPORARY TABLE TempTable (id int primary key auto_increment, loterias json not null); 

    
    if pidBanca != 0 then
		set idBanca = pidBanca;
	else
		select id from branches where idUsuario = pidUsuario and status = 1 into idBanca;
		if idBanca is null
		then
			if exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and p.descripcion = 'Jugar como cualquier banca')
				then
					select id from branches where  status = 1 order by id asc limit 1 into idBanca;
				end if;
		end if;
    end if;
    
	
    
  --   if idBanca is null then
-- 		set psalida = json_objectagg('idBanca', idBanca);
--        --  return psalida;
-- 	end if;
	-- aqui terminamos con el idBanca select json_objectagg('idBanca', idBanca);
    
    
    if idBanca = 0 then
		select JSON_ARRAYAGG(JSON_OBJECT(
				'id', id, 'total', total
			)) as ventas from sales where date(created_at) = date(now()) and status not in(0, 5) into ventas;
		
        select sum(total) from sales where date(created_at) = date(now()) and status not in(0, 5) into total_ventas;
		select count(jugada) from salesdetails where date(created_at) = date(now()) and status not in(0, 5) into total_jugadas;
    else 
		
select JSON_ARRAYAGG(JSON_OBJECT(
				'id', s.id, 'total', s.total,
                
                'idBanca', s.idBanca,
                'codigo', b.codigo,
                'banca', JSON_OBJECT('id', b.id, 'descripcion', b.descripcion, 'codigo', b.codigo, 'piepagina1', b.piepagina1, 'piepagina2', b.piepagina2, 'piepagina3', b.piepagina3, 'piepagina4', b.piepagina4, 'imprimirCodigoQr', b.imprimirCodigoQr),
                'idTicket', s.idTicket,
                'ticket', t.id,
                'codigoBarra', t.codigoBarra,
                'codigoQr', TO_BASE64('t.codigoBarra')
                
			)) as ventas from sales s
            inner join users u on u.id = s.idUsuario 
            inner join branches b on b.id = s.idBanca
            inner join tickets t on t.id = s.idTicket
            left join cancellations ca on ca.idTicket = s.idTicket
            left join users uc on uc.id = ca.idUsuario
            where date(s.created_at) = date(now()) and s.status not in(0, 5) and s.idBanca = idBanca into ventas;
	
			select sum(total) from sales where date(created_at) = date(now()) and status not in(0, 5) and idBanca = idBanca into total_ventas;
            select count(jugada) from salesdetails where date(created_at) = date(now()) and status not in(0, 5) and idBanca = idBanca into total_jugadas;
    end if;
	
    
	
    select JSON_ARRAYAGG(JSON_OBJECT(
				'id', id, 'descuentoPorcentaje', descuentoPorcentaje, 'cantidadAplicar', cantidadAplicar, 'descuentoValor', descuentoValor, 'minutosParaCancelar', minutosParaCancelar
			)) from generals into caracteristicasGenerales;
            
            
	select JSON_ARRAYAGG(JSON_OBJECT(
				'id', b.id, 'descripcion', b.descripcion,
                'codigo', b.codigo,
                'status', b.status,
                'descontar', b.descontar,
                'deCada', b.deCada,
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
     
     if exists(select p.id from permissions p inner join permission_user pu on p.id = pu.idPermiso where pu.idUsuario = pidUsuario and (p.descripcion = 'Jugar fuera de horario' or p.descripcion = 'Jugar minutos extras'))
			then
				INSERT INTO TempTable(loterias) select JSON_OBJECT(
					'id', l.id, 'descripcion', l.descripcion, 'abreviatura', 
                    l.abreviatura, 'horaCierre', dl.horaCierre, 
                    'sorteos', (select JSON_ARRAYAGG(JSON_OBJECT('id', d.id, 'descripcion', d.descripcion)) from draws d where id in(select dl.idSorteo from draw_lottery dl where dl.idLoteria = l.id))
				) as loterias from lotteries l
				inner join day_lottery dl on dl.idLoteria = l.id
				inner join days d on d.id = dl.idDia
				where l.id not in(select idLoteria from awards where date(created_at) = date(now())) and d.wday = wday and l.status = 1 order by dl.horaCierre asc;
	else
    INSERT INTO TempTable(loterias) select JSON_OBJECT(
				'id', l.id, 'descripcion', l.descripcion, 'abreviatura', l.abreviatura, 
                'horaCierre', dl.horaCierre,
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
    
    
    -- 				CREAR IDVENTA TEMPORAL 
		select idVenta(idBanca) into idVentaHash;
          
    select ventas, idBanca, total_ventas, total_jugadas, caracteristicasGenerales, pidUsuario, bancas, loterias, idVentaHash;
   
END
 ;;
delimiter ;

-- call indexPost(1, 0);

