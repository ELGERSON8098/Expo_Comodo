DROP DATABASE IF EXISTS expo_comodos;

CREATE DATABASE expo_comodos;

USE expo_comodos;

CREATE TABLE tb_usuarios (
  id_usuario INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  usuario VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL,
  clave VARCHAR(100) NOT NULL, 
  direccion_cliente LONGTEXT NOT NULL,
  telefono VARCHAR(20) NOT NULL, 
  dui_cliente VARCHAR(20) NOT NULL, 
  estado_cliente TINYINT(1) DEFAULT TRUE,
  PRIMARY KEY (id_usuario),
  CONSTRAINT uc_usuario UNIQUE (usuario),
  CONSTRAINT uc_correo UNIQUE (correo),
  CONSTRAINT uc_telefono UNIQUE (telefono),
  CONSTRAINT uc_dui_cliente UNIQUE (dui_cliente)
);

CREATE TABLE tb_niveles_usuarios (
  id_nivel_usuario INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_nivel VARCHAR (50) NOT NULL,
  PRIMARY KEY (id_nivel_usuario)
);

CREATE TABLE tb_admins (
  id_administrador INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_administrador VARCHAR(50) NOT NULL,
  usuario_administrador VARCHAR(50) NOT NULL,
  correo_administrador VARCHAR(50) NOT NULL,
  clave_administrador VARCHAR(100) NOT NULL,
  id_nivel_usuario INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_administrador),
  CONSTRAINT fk_nivel_usuario FOREIGN KEY (id_nivel_usuario) REFERENCES tb_niveles_usuarios(id_nivel_usuario),
  CONSTRAINT uc_usuario_administrador UNIQUE (usuario_administrador),
  CONSTRAINT uc_correo_administrador UNIQUE (correo_administrador)
);

CREATE TABLE tb_generos_zapatos (
  id_genero INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_genero VARCHAR(100) NOT NULL,
  imagen_genero VARCHAR(20) NULL,
  PRIMARY KEY (id_genero)
);


CREATE TABLE tb_categorias (
  id_categoria INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_categoria VARCHAR(100) NOT NULL,
  imagen VARCHAR(20) NULL,
  PRIMARY KEY (id_categoria)
);

CREATE TABLE tb_tallas (
  id_talla INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_talla VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_talla)
);

CREATE TABLE tb_marcas (
  id_marca INT UNSIGNED AUTO_INCREMENT NOT NULL,
  marca VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_marca)
);

CREATE TABLE tb_colores (
  id_color INT UNSIGNED AUTO_INCREMENT NOT NULL,
  color VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_color)
);

CREATE TABLE tb_descuentos (
  id_descuento INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_descuento VARCHAR(100) NOT NULL,
  descripcion VARCHAR(200) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id_descuento),
  UNIQUE (nombre_descuento),
  CONSTRAINT ck_valor CHECK (valor >= 0)
);


CREATE TABLE tb_materiales (
  id_material INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_material)
);

CREATE TABLE tb_productos (
  id_producto INT UNSIGNED AUTO_INCREMENT NOT NULL,
  nombre_producto VARCHAR(100) NOT NULL,
  codigo_interno VARCHAR(50) NOT NULL,
  referencia_proveedor VARCHAR(50) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  id_marca INT UNSIGNED,
  id_genero INT UNSIGNED,
  id_categoria INT UNSIGNED,
  id_material INT UNSIGNED NOT NULL,
  id_descuento INT UNSIGNED NOT NULL,
  imagen VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_producto),
  CONSTRAINT fk_material FOREIGN KEY (id_material) REFERENCES tb_materiales(id_material),
  CONSTRAINT fk_marcas FOREIGN KEY (id_marca) REFERENCES tb_marcas(id_marca),
  CONSTRAINT fk_generos FOREIGN KEY (id_genero) REFERENCES tb_generos_zapatos(id_genero),
  CONSTRAINT fk_descuento FOREIGN KEY (id_descuento) REFERENCES tb_descuentos(id_descuento),
  CONSTRAINT fk_categorias FOREIGN KEY (id_categoria) REFERENCES tb_categorias(id_categoria),
  CONSTRAINT ck_precio CHECK (precio >= 0),
  CONSTRAINT uc_nombre_producto UNIQUE (nombre_producto),
  CONSTRAINT uc_codigo_interno UNIQUE (codigo_interno),
  CONSTRAINT uc_referencia_proveedor UNIQUE (referencia_proveedor)
);


CREATE TABLE tb_detalles_productos (
  id_detalle_producto INT UNSIGNED AUTO_INCREMENT NOT NULL,
  id_producto INT UNSIGNED NOT NULL,
  id_talla INT UNSIGNED NOT NULL,
  existencias INT UNSIGNED NOT NULL,
  id_color INT UNSIGNED NOT NULL,
  descripcion VARCHAR(200) NOT NULL,
  PRIMARY KEY (id_detalle_producto),
  CONSTRAINT fk_producto FOREIGN KEY (id_producto) REFERENCES tb_productos(id_producto),
  CONSTRAINT fk_talla FOREIGN KEY (id_talla) REFERENCES tb_tallas(id_talla),
  CONSTRAINT fk_color FOREIGN KEY (id_color) REFERENCES tb_colores(id_color),
  CONSTRAINT ck_existencias  CHECK (existencias >= 0)
);

CREATE TABLE tb_reservas (
  id_reserva INT UNSIGNED AUTO_INCREMENT NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  fecha_reserva DATETIME DEFAULT CURRENT_DATE() NOT NULL, 
  estado_reserva ENUM ('Aceptado', 'Pendiente', 'Cancelado') NOT NULL,
  PRIMARY KEY (id_reserva),
  CONSTRAINT fk_reserva_usuario FOREIGN KEY (id_usuario) REFERENCES tb_usuarios (id_usuario)
);

/*tabla para el servidor mysql 8.0*/

CREATE TABLE tb_reservas (
  id_reserva INT UNSIGNED AUTO_INCREMENT NOT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, 
  estado_reserva ENUM ('Aceptado', 'Pendiente', 'Cancelado') NOT NULL,
  PRIMARY KEY (id_reserva),
  CONSTRAINT fk_reserva_usuario FOREIGN KEY (id_usuario) REFERENCES tb_usuarios (id_usuario)
);



CREATE TABLE tb_detalles_reservas (
  id_detalle_reserva INT UNSIGNED AUTO_INCREMENT NOT NULL,
  id_reserva INT UNSIGNED NOT NULL,
  cantidad INT UNSIGNED NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  id_detalle_producto INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_detalle_reserva),
  CONSTRAINT fk_reserva FOREIGN KEY (id_reserva) REFERENCES tb_reservas(id_reserva),
  CONSTRAINT fk_detalle_producto FOREIGN KEY (id_detalle_producto) REFERENCES tb_detalles_productos(id_detalle_producto),
  CONSTRAINT ck_cantidad  CHECK (cantidad >= 0),
  CONSTRAINT ck_precio_unitario CHECK (precio_unitario >= 0)
);

ALTER TABLE tb_usuarios
ADD COLUMN recovery_pin VARCHAR(10) NULL,
ADD COLUMN pin_expiry DATETIME NULL;

INSERT INTO tb_niveles_usuarios (id_nivel_usuario, nombre_nivel)
VALUES 
(1, 'administrador'),
(2, 'inventaristas'),
(3, 'vendedoras');

SELECT * FROM tb_niveles_usuarios


