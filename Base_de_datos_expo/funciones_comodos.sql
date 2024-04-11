USE expo_comodos;


DELIMITER //

CREATE FUNCTION generar_saludo(nombre_usuario VARCHAR(100))
RETURNS VARCHAR(255)
BEGIN
    DECLARE saludo VARCHAR(255);
    SET saludo = CONCAT('¡Hola ', nombre_usuario, '! Bienvenido/a.');
    RETURN saludo;
END;
//

DELIMITER ;

SELECT nombre, generar_saludo(nombre) AS saludo FROM tb_usuarios;

