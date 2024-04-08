DROP DATABASE IF EXISTS expo_comodos;

CREATE DATABASE expo_comodos;

USE expo_comodos;

CREATE TABLE tb_usuarios (
  id_usuario INT UNSIGNED AUTO_INCREMENT,
  nombre VARCHAR(100),
  usuario VARCHAR(100) UNIQUE,
  correo VARCHAR(100) UNIQUE,
  clave VARCHAR(100), 
  telefono VARCHAR(20) UNIQUE, 
  dui_cliente VARCHAR(20) UNIQUE, 
  PRIMARY KEY (id_usuario)
);

CREATE TABLE tb_distritos (
  id_distrito INT UNSIGNED AUTO_INCREMENT,
  distrito VARCHAR(50),
  PRIMARY KEY (id_distrito)
);

CREATE TABLE tb_direcciones (
  id_direccion INT UNSIGNED AUTO_INCREMENT,
  departamento VARCHAR(50),
  id_distrito INT UNSIGNED,
  PRIMARY KEY (id_direccion),
  CONSTRAINT fk_distrito FOREIGN KEY (id_distrito) REFERENCES tb_distritos(id_distrito)
);

CREATE TABLE tb_niveles_usuarios (
  id_nivel_usuario INT UNSIGNED AUTO_INCREMENT,
  nombre_nivel ENUM ('administrador', 'inventaristas', 'vendedoras'),
  PRIMARY KEY (id_nivel_usuario)
);

CREATE TABLE tb_generos (
  id_genero INT UNSIGNED AUTO_INCREMENT,
  nombre_genero VARCHAR(100),
  imagen_genero VARCHAR(20),
  PRIMARY KEY (id_genero)
);

CREATE TABLE tb_admins (
  id_administrador INT UNSIGNED AUTO_INCREMENT,
  nombre_administrador VARCHAR(50),
  usuario_administrador VARCHAR(50) UNIQUE,
  correo_administrador VARCHAR(50) UNIQUE,
  clave_administrador VARCHAR(100),
  id_nivel_usuario INT UNSIGNED,
  PRIMARY KEY (id_administrador),
  CONSTRAINT fk_nivel_usuario FOREIGN KEY (id_nivel_usuario) REFERENCES tb_niveles_usuarios(id_nivel_usuario)
);

CREATE TABLE tb_categorias (
  id_categoria INT UNSIGNED AUTO_INCREMENT,
  nombre_categoria VARCHAR(100),
  imagen VARCHAR(20),
  PRIMARY KEY (id_categoria)
);

CREATE TABLE tb_tallas (
  id_talla INT UNSIGNED AUTO_INCREMENT,
  nombre_talla VARCHAR(20),
  PRIMARY KEY (id_talla)
);

CREATE TABLE tb_marcas (
  id_marca INT UNSIGNED AUTO_INCREMENT,
  marca VARCHAR(50),
  PRIMARY KEY (id_marca)
);

CREATE TABLE tb_colores (
  id_color INT UNSIGNED AUTO_INCREMENT,
  color VARCHAR(20),
  PRIMARY KEY (id_color)
);

CREATE TABLE tb_productos (
  id_producto INT UNSIGNED AUTO_INCREMENT,
  nombre_producto VARCHAR(100),
  codigo_interno VARCHAR(50),
  referencia_proveedor VARCHAR(50),
  imagen VARCHAR(20),
  PRIMARY KEY (id_producto)
);

CREATE TABLE tb_descuentos (
  id_descuento INT UNSIGNED AUTO_INCREMENT,
  nombre_descuento VARCHAR(100),
  descripcion VARCHAR(200),
  valor DECIMAL(10,2),
  PRIMARY KEY (id_descuento)
);

CREATE TABLE tb_detalles_productos (
  id_detalle_producto INT UNSIGNED AUTO_INCREMENT,
  id_producto INT UNSIGNED,
  material VARCHAR(50),
  id_talla INT UNSIGNED,
  precio DECIMAL(10,2),
  existencias INT,
  id_color INT UNSIGNED,
  id_marca INT UNSIGNED,
  id_descuento INT UNSIGNED,
  descripcion VARCHAR(200),
  id_genero INT UNSIGNED,
  id_categoria INT UNSIGNED,
  PRIMARY KEY (id_detalle_producto),
  CONSTRAINT fk_producto FOREIGN KEY (id_producto) REFERENCES tb_productos(id_producto),
  CONSTRAINT fk_talla FOREIGN KEY (id_talla) REFERENCES tb_tallas(id_talla),
  CONSTRAINT fk_color FOREIGN KEY (id_color) REFERENCES tb_colores(id_color),
  CONSTRAINT fk_marca FOREIGN KEY (id_marca) REFERENCES tb_marcas(id_marca),
  CONSTRAINT fk_descuento FOREIGN KEY (id_descuento) REFERENCES tb_descuentos(id_descuento),
  CONSTRAINT fk_genero FOREIGN KEY (id_genero) REFERENCES tb_generos(id_genero),
  CONSTRAINT fk_categoria FOREIGN KEY (id_categoria) REFERENCES tb_categorias(id_categoria)
);

CREATE TABLE tb_reservas (
  id_reserva INT UNSIGNED AUTO_INCREMENT,
  id_usuario INT UNSIGNED,
  fecha_reserva DATETIME DEFAULT CURRENT_DATE(), 
  id_direccion INT UNSIGNED,
  descripcion_direccion INT UNSIGNED,
  PRIMARY KEY (id_reserva),
  CONSTRAINT fk_usuario FOREIGN KEY (id_usuario) REFERENCES tb_usuarios(id_usuario),
  CONSTRAINT fk_direccion FOREIGN KEY (id_direccion) REFERENCES tb_direcciones(id_direccion)
);

CREATE TABLE tb_detalles_reservas (
  id_detalle_reserva INT UNSIGNED AUTO_INCREMENT,
  id_reserva INT UNSIGNED,
  id_producto INT UNSIGNED,
  cantidad INT,
  precio_unitario DECIMAL(10,2),
  id_detalle_producto INT UNSIGNED,
  PRIMARY KEY (id_detalle_reserva),
  CONSTRAINT fk_reserva FOREIGN KEY (id_reserva) REFERENCES tb_reservas(id_reserva),
  CONSTRAINT fk_detalle_producto FOREIGN KEY (id_detalle_producto) REFERENCES tb_detalles_productos(id_detalle_producto)
);

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
