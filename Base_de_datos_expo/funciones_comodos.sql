USE expo_comodos;

-- Crear la función para obtener el ID de nivel de administrador
DELIMITER //

CREATE FUNCTION obtener_id_nivel_administrador()
RETURNS INT
BEGIN
    DECLARE nivel_id INT;

    -- Obtener el ID del nivel de usuario correspondiente a "administrador"
    SELECT id_nivel_usuario INTO nivel_id FROM tb_niveles_usuarios WHERE nombre_nivel = 'administrador';

    RETURN nivel_id;
END//

DELIMITER ;

SELECT obtener_id_nivel_administrador()

-- Crear el trigger antes de insertar en tb_niveles_usuario
DELIMITER //

CREATE TRIGGER before_insert_nivel_usuario
BEFORE INSERT ON tb_niveles_usuarios
FOR EACH ROW
BEGIN
    -- Verificar si la tabla está vacía
    DECLARE count_rows INT;
    SELECT COUNT(*) INTO count_rows FROM tb_niveles_usuarios;
    
    -- Si la tabla está vacía, forzar el nombre del nivel a "administrador"
    IF count_rows = 0 THEN
        SET NEW.nombre_nivel = 'administrador';
    END IF;
END;
//

DELIMITER ;
