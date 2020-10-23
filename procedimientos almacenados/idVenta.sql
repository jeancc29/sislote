use loterias;
DROP FUNCTION IF EXISTS `idVenta`;
DELIMITER $$

CREATE FUNCTION idVenta(idBanca int) RETURNS varchar(200)
     READS SQL DATA
DETERMINISTIC
BEGIN
declare idVenta bigint; 
declare idVentaHash varchar(200);
declare siguienteIdVenta bigInt;
declare idBancaIdVentaTemporal int;

-- 				CREAR IDVENTA TEMPORAL 
		set idVentaHash = null;
		select max(id) from sales into siguienteIdVenta;
        if siguienteIdVenta is null then
			set siguienteIdVenta = 0;
		end if;
		set siguienteIdVenta = siguienteIdVenta + 1;
        
        select idventatemporals.idBanca from idventatemporals where idventatemporals.idVenta = siguienteIdVenta order by idventatemporals.id desc limit 1 into idBancaIdVentaTemporal;
        if idBancaIdVentaTemporal is not null then
			if idBanca = idBancaIdVentaTemporal then
				 select idventatemporals.idVentaHash from idventatemporals where idventatemporals.idVenta = siguienteIdVenta order by idventatemporals.id desc limit 1 into idVentaHash;
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
        
        return idVentaHash;
end;

