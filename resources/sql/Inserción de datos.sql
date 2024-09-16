USE expo_comodos;

INSERT INTO tb_usuarios (id_usuario, nombre, usuario, correo, clave, direccion_cliente, telefono, dui_cliente)
VALUES 
(1, 'Lionel Messi', 'messi10', 'lionel@gmail.com', 'messi123', 'Alameda Franklin Delano Roosevelt', '7555-1001', '123456789'),
(2, 'Harry Kane', 'Kane3', 'HKane@gmail.com', 'Kane456', 'Alameda Franklin Delano Roosevelt', '7555-1002', '987654321'),
(3, 'Sergio Busquets', 'busquets5', 'busquets@gmail.com', 'busquets789', 'Alameda Franklin Delano Roosevelt', '7555-1003', '112233445'),
(4, 'Jordi Alba', 'alba18', 'alba@gmail.com', 'alba123', 'Alameda Franklin Delano Roosevelt', '7555-1004', '543210987'),
(5, 'Ansu Fati', 'fati22', 'fati@gmail.com', 'fati456', 'Alameda Franklin Delano Roosevelt', '7555-1005', '678905432'),
(6, 'Frenkie de Jong', 'jong21', 'jong@gmail.com', 'jong789', 'Alameda Franklin Delano Roosevelt', '7555-1006', '876543210'),
(7, 'Pedri', 'pedri16', 'pedri@gmail.com', 'pedri123', 'Alameda Franklin Delano Roosevelt', '7555-1007', '234567890'),
(8, 'Luis Suarez', 'Suarez11', 'luchosuarez@gmail.com', 'suarez456', 'Alameda Franklin Delano Roosevelt', '7555-1008', '098765432'),
(9, 'Marc-André ter Stegen', 'terstegen1', 'terstegen@gmail.com', 'terstegen123', 'Alameda Franklin Delano Roosevelt', '5755-1009', '456789012'),
(10, 'Ronald Araújo', 'araujo4', 'araujo@gmail.com', 'araujo789', 'Alameda Franklin Delano Roosevelt', '7555-1010', '789012345');


SELECT*FROM tb_usuarios;

INSERT INTO tb_admins (nombre_administrador, usuario_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
VALUES 
('Pep Guardiola', 'guardiola', 'pep@gmail.com', 'guardiola123', 1),
('Kevin De Bruyne', 'debruyne17', 'kevin@gmail.com', 'debruyne456', 2),
('Raheem Sterling', 'sterling7', 'raheem@gmail.com', 'sterling789', 2),
('Ruben Dias', 'dias3', 'ruben@gmail.com', 'dias123', 2),
('Ederson', 'ederson1', 'ederson@gmail.com', 'ederson456', 2),
('Phil Foden', 'foden47', 'phil@gmail.com', 'foden789', 2),
('Joao Cancelo', 'cancelo27', 'joao@gmail.com', 'cancelo123', 2),
('Ilkay Gundogan', 'gundogan8', 'ilkay@gmail.com', 'gundogan456', 2),
('Bernardo Silva', 'silva20', 'bernardo@gmail.com', 'silva789', 2),
('Kyle Walker', 'walker2', 'kyle@gmail.com', 'walker123', 2);

SELECT*FROM tb_admins;

INSERT INTO tb_generos_zapatos (id_genero, nombre_genero, imagen_genero)
VALUES 
(1, 'Zapatillas deportivas', 'zapatillas.png'),
(2, 'Botas de invierno', 'botas_invierno.png'),
(3, 'Zapatos casuales', 'zapatos_casuales.png'),
(4, 'Sandalias de verano', 'sandalias_verano.png'),
(5, 'Botines de moda', 'botines_moda.png'),
(6, 'Zapatos formales', 'zapatos_formales.png'),
(7, 'Zapatillas para correr', 'zapatilla_correr.png'),
(8, 'Chanclas de playa', 'chanclas_playa.png'),
(9, 'Mocasines elegantes', 'mocasin_elegante.png'),
(10, 'Botas de lluvia', 'botas_lluvia.png');

SELECT*FROM tb_generos_zapatos;


INSERT INTO tb_categorias (id_categoria, nombre_categoria, imagen)
VALUES 
(1, 'Running', 'running.png'),
(2, 'Baloncesto', 'baloncesto.png'),
(3, 'Fútbol', 'futbol.png'),
(4, 'Nieve', 'nieve.png'),
(5, 'Lluvia', 'lluvia.png'),
(6, 'Casual', 'casual.png'),
(7, 'Formal', 'formal.png'),
(8, 'Playa', 'playa.png'),
(9, 'Piscina', 'piscina.png'),
(10, 'Moda', 'moda.png');

SELECT*FROM tb_categorias;

INSERT INTO tb_tallas (id_talla, nombre_talla) VALUES
(1, '35'),
(2, '36'),
(3, '37'),
(4, '38'),
(5, '39'),
(6, '40'),
(7, '41'),
(8, '42'),
(9, '43'),
(10, '44');

SELECT*FROM tb_tallas;

INSERT INTO tb_marcas (id_marca, marca) VALUES
(1, 'Nike'),
(2, 'Adidas'),
(3, 'Timberland'),
(4, 'Converse'),
(5, 'Birkenstock'),
(6, 'Dr. Martens'),
(7, 'Clarks'),
(8, 'Gucci'),
(9, 'Hunter'),
(10, 'Puma');

SELECT*FROM tb_marcas;

INSERT INTO tb_colores (id_color, color) VALUES
(1, 'Negro'),
(2, 'Blanco'),
(3, 'Azul'),
(4, 'Rojo'),
(5, 'Verde'),
(6, 'Amarillo'),
(7, 'Gris'),
(8, 'Marrón'),
(9, 'Beige'),
(10, 'Blanco/Negro');

SELECT*FROM tb_colores;

INSERT INTO tb_descuentos (id_descuento, nombre_descuento, descripcion, valor) VALUES
(1, 'Descuento Primavera', 'Descuento especial de primavera', 15.00),
(2, 'Oferta Verano', 'Oferta especial de verano', 20.00),
(3, 'Promoción Otoño', 'Promoción de temporada de otoño', 10.00),
(4, 'Venta de Invierno', 'Gran venta de invierno', 25.00),
(5, 'Descuento Estudiante', 'Descuento para estudiantes', 30.00),
(6, 'Oferta Black Friday', 'Ofertas exclusivas para el Black Friday', 40.00),
(7, 'Promoción Cyber Monday', 'Grandes descuentos para el Cyber Monday', 35.00),
(8, 'Descuento Cumpleaños', 'Descuento especial de cumpleaños', 10.00),
(9, 'Descuento Cliente Frecuente', 'Descuento para clientes frecuentes', 20.00),
(10, 'Oferta Fin de Temporada', 'Ofertas de liquidación al final de la temporada', 50.00);

SELECT*FROM tb_descuentos;

INSERT INTO tb_materiales (id_material, nombre)
VALUES
(1, 'Cuero'),
(2, 'Tela'),
(3, 'Sintético'),
(4, 'Goma'),
(5, 'Nylon'),
(6, 'Lona'),
(7, 'Lienzo'),
(8, 'Seda'),
(9, 'Lana'),
(10, 'Algodón');

SELECT*FROM tb_materiales;

INSERT INTO tb_productos (id_producto, nombre_producto, codigo_interno, referencia_proveedor, precio, id_marca, id_genero, id_categoria, imagen, id_material, id_descuento)
VALUES 
(1, 'Zapatillas Running Nike Air Max', 'NIKE001', 'NIKE123', 129.99, 1, 1, 1, 'running.png', 1, 1),
(2, 'Botas de Invierno Timberland', 'TIMBER001', 'TIMBER123', 189.99, 3, 2, 4, 'botas.png', 2, 3),
(3, 'Zapatos Casuales Converse Chuck Taylor', 'CONVERSE001', 'CONVERSE123', 59.99, 4, 3, 6, 'casuales.png', 3, 5),
(4, 'Sandalias de Verano Birkenstock', 'BIRKEN001', 'BIRKEN123', 79.99, 5, 4, 9, 'botines.png', 4, 7),
(5, 'Botines de Moda Dr. Martens', 'MARTENS001', 'MARTENS123', 169.99, 6, 5, 10, 'moda.png', 5, 9),
(6, 'Zapatos Formales Clarks', 'CLARKS001', 'CLARKS123', 99.99, 7, 6, 7, 'formales.png', 6, 1),
(7, 'Zapatillas para Correr Adidas Ultraboost', 'ADIDAS001', 'ADIDAS123', 119.99, 2, 7, 1, 'zapatillas.png', 7, 2),
(8, 'Chanclas de Playa Havaianas', 'HAVAIANAS001', 'HAVAIANAS123', 39.99, 10, 8, 8, 'chanclas.png', 4, 4),
(9, 'Mocasines Elegantes Gucci', 'GUCCI001', 'GUCCI123', 149.99, 8, 9, 7, 'mocasines.png', 8, 6),
(10, 'Botas de Lluvia Hunter', 'HUNTER001', 'HUNTER123', 199.99, 9, 10, 5, 'botasdelluvia.png', 2, 8);


SELECT*FROM tb_productos;

-- Insertar registros en tb_detalles_productos
INSERT INTO tb_detalles_productos (id_detalle_producto, id_producto, id_talla, existencias, id_color, descripcion)
VALUES
  (1, 1, 1, 50, 1, 'Zapatillas Nike Air Max'),
  (2, 1, 2, 75, 2, 'Zapatillas Nike Air Max'),
  (3, 2, 3, 30, 8, 'Botas Timberland'),
  (4, 2, 4, 40, 3, 'Botas Timberland'),
  (5, 3, 5, 100, 3, 'Zapatillas Converse Chuck Taylor'),
  (6, 3, 6, 80, 2, 'Zapatillas Converse Chuck Taylor'),
  (7, 4, 7, 60, 1, 'Sandalias Birkenstock'),
  (8, 4, 8, 70, 2, 'Sandalias Birkenstock'),
  (9, 5, 9, 45, 1, 'Botas Dr. Martens'),
  (10, 5, 10, 55, 4, 'Botas Dr. Martens');
SELECT * FROM tb_detalles_productos;

INSERT INTO tb_reservas (id_reserva, id_usuario, fecha_reserva, estado_reserva)
VALUES 
(1, 1, '2024-04-06 10:00:00', 'Pendiente'),
(2, 2, '2024-04-07 11:00:00', 'Pendiente'),
(3, 3, '2024-04-08 12:00:00', 'Pendiente'),
(4, 4, '2024-04-09 13:00:00', 'Pendiente'),
(5, 5, '2024-04-10 14:00:00', 'Pendiente'),
(6, 6, '2024-04-11 15:00:00', 'Pendiente'),
(7, 7, '2024-04-12 16:00:00','Pendiente'),
(8, 8, '2024-04-13 17:00:00', 'Pendiente'),
(9, 9, '2024-04-14 18:00:00', 'Pendiente'),
(10, 10, '2024-04-15 19:00:00', 'Pendiente');


SELECT*FROM tb_reservas;

INSERT INTO tb_detalles_reservas (id_detalle_reserva, id_reserva, cantidad, precio_unitario, id_detalle_producto)
VALUES
(1, 1, 2, 129.99, 1),
(2, 7, 1,  119.99, 7),
(3, 2, 1,  189.99, 3),
(4, 4, 2,  79.99, 7),
(5, 3, 3,  59.99, 5),
(6, 6, 1,  99.99, 6),
(7, 1, 1,  129.99, 2),
(8, 5, 1, 169.99, 9),
(9, 2, 2, 189.99, 4),
(10, 8, 1,  39.99, 8),
(11, 3, 1,  59.99, 6),
(12, 7, 2,  119.99, 7),
(13, 4, 1,  79.99, 8),
(14, 9, 1,  149.99, 9),
(15, 5, 1, 169.99, 10),
(16, 10, 1,  199.99, 3),
(17, 1, 2,  129.99, 1),
(18, 6, 1,  99.99, 6),
(19, 2, 1,  189.99, 3),
(20, 8, 2,  39.99, 8);
SELECT * FROM tb_detalles_reservas;


-- Inserta reservas y detalles de reservas para los últimos 6 meses
INSERT INTO tb_reservas (id_reserva, id_usuario, fecha_reserva, estado_reserva)
VALUES 
(11, 1, '2024-01-05 10:00:00', 'Aceptado'),
(12, 2, '2024-01-15 11:00:00', 'Aceptado'),
(13, 3, '2024-02-02 12:00:00', 'Aceptado'),
(14, 4, '2024-02-20 13:00:00', 'Aceptado'),
(15, 5, '2024-03-10 14:00:00', 'Aceptado'),
(16, 6, '2024-03-25 15:00:00', 'Aceptado'),
(17, 7, '2024-04-05 16:00:00', 'Aceptado'),
(18, 8, '2024-04-20 17:00:00', 'Aceptado'),
(19, 9, '2024-05-10 18:00:00', 'Aceptado'),
(20, 10, '2024-05-25 19:00:00', 'Aceptado'),
(21, 1, '2024-06-10 10:00:00', 'Aceptado');

INSERT INTO tb_detalles_reservas (id_detalle_reserva, id_reserva, cantidad, precio_unitario, id_detalle_producto)
VALUES
(21, 11, 1, 129.99, 1),
(22, 12, 2, 119.99, 2),
(23, 13, 1, 189.99, 3),
(24, 14, 3, 79.99, 4),
(25, 15, 1, 59.99, 5),
(26, 16, 2, 99.99, 6),
(27, 17, 1, 129.99, 7),
(28, 18, 2, 169.99, 8),
(29, 19, 1, 189.99, 9),
(30, 20, 3, 39.99, 10);


-- Inserta reservas y detalles de reservas para los próximos 3 meses
INSERT INTO tb_reservas (id_reserva, id_usuario, fecha_reserva, estado_reserva)
VALUES 
(22, 1, '2024-07-05 10:00:00', 'Pendiente'),
(23, 2, '2024-07-15 11:00:00', 'Pendiente'),
(24, 3, '2024-08-02 12:00:00', 'Pendiente'),
(25, 4, '2024-08-20 13:00:00', 'Pendiente'),
(26, 5, '2024-09-10 14:00:00', 'Pendiente'),
(27, 6, '2024-09-25 15:00:00', 'Pendiente');

INSERT INTO tb_detalles_reservas (id_detalle_reserva, id_reserva, cantidad, precio_unitario, id_detalle_producto)
VALUES
(32, 22, 1, 129.99, 1),
(33, 23, 2, 119.99, 2),
(34, 24, 1, 189.99, 3),
(35, 25, 2, 79.99, 4),
(36, 26, 1, 59.99, 5),
(37, 27, 1, 99.99, 6);

