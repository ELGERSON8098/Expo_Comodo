DROP PROCEDURE IF EXISTS eliminar_reservas_pendientes;
DROP EVENT IF EXISTS eliminar_reservas_event;
DELIMITER //

CREATE PROCEDURE eliminar_reservas_pendientes()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE usuario_id INT;
    DECLARE reserva_id INT;
    DECLARE usuario_correo VARCHAR(100);
    
    DECLARE cur CURSOR FOR 
        SELECT r.id_usuario, r.id_reserva, u.correo 
        FROM tb_reservas r
        JOIN tb_usuarios u ON r.id_usuario = u.id_usuario
        WHERE r.estado_reserva = 'Pendiente' 
          AND r.fecha_reserva < NOW() - INTERVAL 72 HOUR;  -- Manteniendo el intervalo de 72 horas
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO usuario_id, reserva_id, usuario_correo;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Eliminar todos los detalles de la reserva
        DELETE FROM tb_detalles_reservas WHERE id_reserva = reserva_id;

        -- Eliminar la reserva
        DELETE FROM tb_reservas WHERE id_reserva = reserva_id;

        -- Llamar a la función para enviar correo
        CALL enviar_correo(usuario_correo, 'Su pedido ha sido eliminado por políticas de la app.');
    END LOOP;

    CLOSE cur;
END //

DELIMITER ;


DELIMITER //

CREATE EVENT eliminar_reservas_event
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
    CALL eliminar_reservas_pendientes();
END //

DELIMITER ;

SET GLOBAL event_scheduler = ON;


SELECT * FROM tb_detalles_reservas;
SELECT * FROM tb_reservas;
SELECT * FROM tb_detalles_productos;
SELECT * FROM tb_productos;

