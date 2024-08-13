USE expo_comodos; -- Establece la base de datos en la que se trabajará.

DELIMITER // -- Cambia el delimitador para que puedas utilizar ";" dentro de la función.

CREATE FUNCTION generar_saludo(nombre_usuario VARCHAR(100)) -- Crea una función llamada generar_saludo que toma un parámetro de nombre_usuario.
RETURNS VARCHAR(255)
BEGIN
    DECLARE saludo VARCHAR(255); -- Declara una variable llamada saludo de tipo VARCHAR(255).
    SET saludo = CONCAT('¡Hola ', nombre_usuario, '! Bienvenido/a.'); -- Construye el saludo utilizando el nombre proporcionado.
    RETURN saludo; -- Devuelve el saludo.
END; -- Finaliza la definición de la función.
//

DELIMITER ; -- Restaura el delimitador predeterminado.

SELECT nombre, generar_saludo(nombre) AS saludo FROM tb_usuarios; -- Selecciona el nombre de los usuarios y llama a la función generar_saludo para obtener el saludo correspondiente.

ALTER TABLE tb_productos
MODIFY COLUMN id_descuento INT UNSIGNED DEFAULT NULL,
ADD CONSTRAINT ck_descuento 
    FOREIGN KEY (id_descuento) 
    REFERENCES tb_descuentos(id_descuento) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE;

