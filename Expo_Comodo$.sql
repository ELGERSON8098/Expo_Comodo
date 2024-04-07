DROP DATABASE IF EXISTS Expo_Comodo$;

CREATE DATABASE Expo_Comodo$;

USE Expo_Comodo$;

CREATE TABLE tbUsuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT,
    nombre VARCHAR(100),
    usuario VARCHAR(100),
    correo VARCHAR(100),
    clave VARCHAR(100) UNIQUE,
    telefono VARCHAR(20) UNIQUE,
    dui_cliente int,
    PRIMARY KEY (`id_usuario`)
);

INSERT INTO tbUsuarios (nombre, usuario, correo, clave, telefono, dui_cliente) 
VALUES 
('Juan Perez', 'juanperez', 'juanperez@example.com', 'clave123', '555-1234', 123456789),
('María López', 'marialopez', 'marialopez@example.com', 'clave456', '555-5678', 987654321),
('Carlos Ramirez', 'carlosramirez', 'carlosramirez@example.com', 'clave789', '555-9101', 112233445);


CREATE TABLE tbdirecciones(
id_direccion INT UNSIGNED AUTO_INCREMENT,
departamento VARCHAR (50),
descripcion_direccion VARCHAR (500),
id_usuario INT UNSIGNED,
id_distrito INT UNSIGNED,
PRIMARY KEY (`id_direccion`)
);

CREATE TABLE tbdistritos(
id_distrito INT UNSIGNED AUTO_INCREMENT,
distrito VARCHAR (50),
PRIMARY KEY (`id_distrito`)
);

CREATE TABLE tbAdmins (
    id_administrador INT UNSIGNED AUTO_INCREMENT,
    nombre_administrador VARCHAR(50),
    user_administrador VARCHAR(50) UNIQUE,
    correo_administrador VARCHAR(50),
    clave_administrador VARCHAR(1000) UNIQUE,
    id_nivel_usuario INT UNSIGNED,
    PRIMARY KEY (`id_administrador`)
);

SELECT * FROM tbAdmins;

-- Crear la tabla tbniveles_usuario
CREATE TABLE tbniveles_usuario (
    id_nivel_usuario INT UNSIGNED AUTO_INCREMENT,
    nombre_nivel ENUM ('Administrador', 'Inventaristas', 'Vendedoras'),
    PRIMARY KEY (`id_nivel_usuario`)
);

INSERT INTO tbniveles_usuario (id_nivel_usuario, nombre_nivel) VALUES
(1, 'Administrador'),
(2, 'Inventaristas'),
(3, 'Vendedoras');

INSERT INTO tbAdmins (nombre_administrador, user_administrador, correo_administrador, clave_administrador, id_nivel_usuario) VALUES
	('Administrador1', 'admin1', 'admin1@example.com', 'clave1', 2),
	('Administrador2', 'admin2', 'admin2@example.com', 'clave2', 3);

SELECT * FROM tbniveles_usuario;

CREATE TABLE tbcategorias (
    id_categoria INT UNSIGNED AUTO_INCREMENT,
    nombre_categoria VARCHAR(100),
    imagen_categoria VARCHAR(20),
    PRIMARY KEY (`id_categoria`)
);

CREATE TABLE tbsubcategorias (
    id_subcategoria INT UNSIGNED AUTO_INCREMENT,
    id_categoria INT UNSIGNED,
    nombre_subcategoria VARCHAR(100),
    imagen VARCHAR(20),
    PRIMARY KEY (`id_subcategoria`)
);

CREATE TABLE tbtallas (
    id_talla INT UNSIGNED AUTO_INCREMENT,
    nombre_talla VARCHAR(20),
    PRIMARY KEY (`id_talla`)
);

INSERT INTO tbtallas (nombre_talla) VALUES
('32'),
('38'),
('40'),
('42');


CREATE TABLE tbproductos (
    id_producto INT UNSIGNED AUTO_INCREMENT,
    nombre_producto VARCHAR(100),
    codigo_interno VARCHAR(50),
    Referencia_provedor VARCHAR(50),
    PRIMARY KEY (`id_producto`)
);

CREATE TABLE tbdetalles_producto (
    id_detalle_producto INT UNSIGNED AUTO_INCREMENT,
    id_producto INT UNSIGNED,
    material VARCHAR(50),
    descripcion VARCHAR(200),
    id_talla INT UNSIGNED,
    precio DECIMAL(10,2),
    imagen_detale_producto VARCHAR(20),
    existencias INT,
    id_color INT UNSIGNED,
    id_marca INT UNSIGNED,
    id_descuento INT UNSIGNED,
    id_categoria INT UNSIGNED,
    id_subcategoria INT UNSIGNED,
    PRIMARY KEY (`id_detalle_producto`)
);


CREATE TABLE tbmarca(
id_marca INT UNSIGNED AUTO_INCREMENT,
marca VARCHAR (50),
PRIMARY KEY (`id_marca`)
);

SELECT * FROM tbmarca;

INSERT INTO tbmarca (marca) VALUES
('Nike'),
('Adidas'),
('Puma'),
('Reebok'),
('Under Armour');

CREATE TABLE tbcolor(
id_color INT UNSIGNED AUTO_INCREMENT,
Color VARCHAR(20),
PRIMARY KEY (`id_color`)
);

INSERT INTO tbcolor (Color) VALUES
('Rojo'),
('Azul'),
('Verde'),
('Amarillo'),
('Negro');

SELECT * FROM tbcolor; 

CREATE TABLE tbdescuentos (
    id_descuento INT UNSIGNED AUTO_INCREMENT,
    nombre_descuento VARCHAR(100),
    descripcion VARCHAR(200),
    valor DECIMAL(10, 2),
    PRIMARY KEY (`id_descuento`)
);

INSERT INTO tbdescuentos (nombre_descuento, descripcion, valor) VALUES
('Descuento de temporada', 'Descuento aplicable a productos de temporada.', 10.00),
('Oferta especial', 'Descuento aplicable a productos seleccionados.', 15.50),
('Cupón de descuento', 'Descuento aplicable con un cupón especial.', 20.00);

SELECT * FROM tbdescuentos;


CREATE TABLE tbreserva (
    id_reserva INT UNSIGNED AUTO_INCREMENT,
    id_usuario INT UNSIGNED,
    fecha_reserva DATETIME,
    estado_pago ENUM('Pendiente', 'Completado', 'Cancelado'),
    PRIMARY KEY (`id_reserva`)
	);

CREATE TABLE tbdetalles_reserva (
    id_detalle_reserva INT UNSIGNED AUTO_INCREMENT,
    id_reserva INT UNSIGNED,
    id_producto INT UNSIGNED,
    cantidad INT,
    precio_unitario DECIMAL(10, 2),
    id_detalle_producto INT UNSIGNED,
    PRIMARY KEY (`id_detalle_reserva`)
);


-- Agregar clave foránea a la tabla tbdirecciones para conectar con la tabla tbUsuarios
ALTER TABLE tbdirecciones
ADD FOREIGN KEY (id_usuario) REFERENCES tbUsuarios(id_usuario);


-- Agregar clave foránea a la tabla tbdirecciones para conectar con la tabla tbdistritos
ALTER TABLE tbdirecciones
ADD FOREIGN KEY (id_distrito) REFERENCES tbdistritos(id_distrito);

-- Agregar clave foránea a la tabla tbAdmins para conectar con la tabla tbniveles_usuario
ALTER TABLE tbAdmins
ADD FOREIGN KEY (id_nivel_usuario) REFERENCES tbniveles_usuario(id_nivel_usuario);

-- Agregar clave foránea a la tabla tbsubcategorias para conectar con la tabla tbcategorias
ALTER TABLE tbsubcategorias
ADD FOREIGN KEY (id_categoria) REFERENCES tbcategorias(id_categoria);

-- Agregar clave foránea a la tabla tbproductos para conectar con la tabla tbsubcategorias
ALTER TABLE tbproductos
ADD FOREIGN KEY (id_subcategoria) REFERENCES tbsubcategorias(id_subcategoria);

-- Agregar clave foránea a la tabla tbproductos para conectar con la tabla tbAdmins
ALTER TABLE tbproductos
ADD FOREIGN KEY (id_administrador) REFERENCES tbAdmins(id_administrador);

-- Agregar clave foránea a la tabla tbdetalles_producto para conectar con la tabla tbproductos
ALTER TABLE tbdetalles_producto
ADD FOREIGN KEY (id_producto) REFERENCES tbproductos(id_producto);

-- Agregar clave foránea a la tabla tbdetalles_producto para conectar con la tabla tbtallas
ALTER TABLE tbdetalles_producto
ADD FOREIGN KEY (id_talla) REFERENCES tbtallas(id_talla);

-- Agregar clave foránea a la tabla tbdetalles_producto para conectar con la tabla tbmarca
ALTER TABLE tbdetalles_producto
ADD FOREIGN KEY (id_marca) REFERENCES tbmarca(id_marca);

-- Agregar clave foránea a la tabla tbdetalles_producto para conectar con la tabla tbcolor
ALTER TABLE tbdetalles_producto
ADD FOREIGN KEY (id_color) REFERENCES tbcolor(id_color);

-- Agregar clave foránea a la tabla tbdetalles_producto para conectar con la tabla tbdescuentos
ALTER TABLE tbdetalles_producto
ADD FOREIGN KEY (id_descuento) REFERENCES tbdescuentos(id_descuento);

-- Agregar clave foránea a la tabla tbreserva para conectar con la tabla tbUsuarios
ALTER TABLE tbreserva
ADD FOREIGN KEY (id_usuario) REFERENCES tbUsuarios(id_usuario);

-- Agregar clave foránea a la tabla tbdetalles_reserva para conectar con la tabla tbreserva
ALTER TABLE tbdetalles_reserva
ADD FOREIGN KEY (id_reserva) REFERENCES tbreserva(id_reserva);

-- Agregar clave foránea a la tabla tbdetalles_reserva para conectar con la tabla tbproductos
ALTER TABLE tbdetalles_reserva
ADD FOREIGN KEY (id_producto) REFERENCES tbproductos(id_producto);

-- Agregar clave foránea a la tabla tbdetalles_reserva para conectar con la tabla tbdetalles_producto
ALTER TABLE tbdetalles_reserva
ADD FOREIGN KEY (id_detalle_producto) REFERENCES tbdetalles_producto(id_detalle_producto);

DELIMITER //

CREATE FUNCTION ObtenerIdNivelAdministrador()
RETURNS INT
BEGIN
    DECLARE nivel_id INT;

    -- Obtener el ID del nivel de usuario correspondiente a "Administrador"
    SELECT id_nivel_usuario INTO nivel_id FROM tbniveles_usuario WHERE nombre_nivel = 'Administrador';

    RETURN nivel_id;
END//

DELIMITER ;

DELIMITER //

CREATE TRIGGER before_insert_nivel_usuario
BEFORE INSERT ON tbniveles_usuario
FOR EACH ROW
BEGIN
    -- Verificar si la tabla está vacía
    DECLARE count_rows INT;
    SELECT COUNT(*) INTO count_rows FROM tbniveles_usuario;
    
    -- Si la tabla está vacía, forzar el nombre del nivel a "Administrador"
    IF count_rows = 0 THEN
        SET NEW.nombre_nivel = 'Administrador';
    END IF;
END;
//

DELIMITER ;