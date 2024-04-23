USE expo_comodos;

INSERT INTO tb_usuarios (id_usuario, nombre, usuario, correo, clave, telefono, dui_cliente)
VALUES 
(1, 'Lionel Messi', 'messi10', 'lionel@gmail.com', 'messi123', '7555-1001', '123456789'),
(2, 'Harry Kane', 'Kane3', 'HKane@gmail.com', 'Kane456', '7555-1002', '987654321'),
(3, 'Sergio Busquets', 'busquets5', 'busquets@gmail.com', 'busquets789', '7555-1003', '112233445'),
(4, 'Jordi Alba', 'alba18', 'alba@gmail.com', 'alba123', '7555-1004', '543210987'),
(5, 'Ansu Fati', 'fati22', 'fati@gmail.com', 'fati456', '7555-1005', '678905432'),
(6, 'Frenkie de Jong', 'jong21', 'jong@gmail.com', 'jong789', '7555-1006', '876543210'),
(7, 'Pedri', 'pedri16', 'pedri@gmail.com', 'pedri123', '7555-1007', '234567890'),
(8, 'Luis Suarez', 'Suarez11', 'luchosuarez@gmail.com', 'suarez456', '7555-1008', '098765432'),
(9, 'Marc-André ter Stegen', 'terstegen1', 'terstegen@gmail.com', 'terstegen123', '5755-1009', '456789012'),
(10, 'Ronald Araújo', 'araujo4', 'araujo@gmail.com', 'araujo789', '7555-1010', '789012345');

SELECT*FROM tb_usuarios;


INSERT INTO tb_departamentos (id_departamento, departamento)
VALUES 
(1, 'San Salvador'),
(2, 'Santa Ana'),
(3, 'San Miguel'),
(4, 'La Libertad'),
(5, 'Usulután'),
(6, 'Sonsonate'),
(7, 'Chalatenango'),
(8, 'La Paz'),
(9, 'Cuscatlán'),
(10, 'Ahuachapán'),
(11, 'Cabañas'),
(12, 'Morazán'),
(13, 'La Unión'),
(14, 'San Vicente');

SELECT*FROM tb_departamentos;

INSERT INTO tb_municipios (id_municipio, municipio, id_departamento)
VALUES
(1, 'San Salvador Norte', 1),
(2, 'San Salvador Oeste', 1),
(3, 'San Salvador Este', 1),
(4, 'San Salvador Centro', 1),
(5, 'San Salvador Sur', 1),
(6, 'La Libertad Norte', 4),
(7, 'La Libertad Centro', 4),
(8, 'La Libertad Oeste', 4),
(9, 'La Libertad Este', 4),
(10, 'La Libertad Costa', 4),
(11, 'La Libertad Sur', 4),
(12, 'Chalatenango Norte', 7),
(13, 'Chalatenango Centro', 7),
(14, 'Chalatenango Sur', 7),
(15, 'Cuscatlán Norte', 9),
(16, 'Cuscatlán Sur', 9),
(17, 'Cabañas Este', 11),
(18, 'Cabañas Oeste', 11),
(19, 'La Paz Oeste', 8),
(20, 'La Paz Centro', 8),
(21, 'La Paz Este', 8),
(22, 'La Unión Norte', 13),
(23, 'La Unión Sur', 13),
(24, 'Usulután Norte', 5),
(25, 'Usulután Este', 5),
(26, 'Usulután Oeste', 5),
(27, 'Sonsonate Norte', 6),
(28, 'Sonsonate Centro', 6),
(29, 'Sonsonate Este', 6),
(30, 'Sonsonate Oeste', 6),
(31, 'Santa Ana Norte', 2),
(32, 'Santa Ana Centro', 2),
(33, 'Santa Ana Este', 2),
(34, 'Santa Ana Oeste', 2),
(35, 'San Vicente Norte', 14),
(36, 'San Vicente Sur', 14),
(37, 'San Miguel Norte', 3),
(38, 'San Miguel Centro', 3),
(39, 'San Miguel Oeste', 3),
(40, 'Morazán Norte', 12),
(41, 'Morazán Sur', 12);

SELECT*FROM tb_municipios;

INSERT INTO tb_distritos (id_distrito, distrito, id_municipio)
VALUES
(1, 'Centro Histórico', 1),
(2, 'Colonia Escalón', 1),
(3, 'Zona Rosa', 1),
(4, 'Soyapango', 1),
(5, 'Apopa', 1),
(6, 'Ilopango', 1),
(7, 'Santa Tecla', 2),
(8, 'San Marcos', 1),
(9, 'Mejicanos', 1),
(10, 'Antiguo Cuscatlán', 3);


INSERT INTO tb_niveles_usuarios (id_nivel_usuario, nombre_nivel)
VALUES 
(1, 'administrador'),
(2, 'inventaristas'),
(3, 'vendedoras');

SELECT*FROM tb_niveles_usuarios;

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

INSERT INTO tb_admins (id_administrador, nombre_administrador, usuario_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
VALUES 
(1, 'Pep Guardiola', 'guardiola', 'pep@gmail.com', 'guardiola123', 1),
(2, 'Kevin De Bruyne', 'debruyne17', 'kevin@gmail.com', 'debruyne456', 2),
(3, 'Raheem Sterling', 'sterling7', 'raheem@gmail.com', 'sterling789', 2),
(4, 'Ruben Dias', 'dias3', 'ruben@gmail.com', 'dias123', 2),
(5, 'Ederson', 'ederson1', 'ederson@gmail.com', 'ederson456', 2),
(6, 'Phil Foden', 'foden47', 'phil@gmail.com', 'foden789', 2),
(7, 'Joao Cancelo', 'cancelo27', 'joao@gmail.com', 'cancelo123', 2),
(8, 'Ilkay Gundogan', 'gundogan8', 'ilkay@gmail.com', 'gundogan456', 2),
(9, 'Bernardo Silva', 'silva20', 'bernardo@gmail.com', 'silva789', 2),
(10, 'Kyle Walker', 'walker2', 'kyle@gmail.com', 'walker123', 2);

SELECT*FROM tb_admins;

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

INSERT INTO tb_productos (id_producto, nombre_producto, codigo_interno, referencia_proveedor, id_marca, id_genero, id_categoria, imagen)
VALUES 
(1, 'Zapatillas Running Nike Air Max', 'NIKE001', 'NIKE123', 1, 1, 1, 'running.png'),
(2, 'Botas de Invierno Timberland', 'TIMBER001', 'TIMBER123', 3, 2, 4, 'botas.png'),
(3, 'Zapatos Casuales Converse Chuck Taylor', 'CONVERSE001', 'CONVERSE123', 4, 3, 6, 'casuales.png'),
(4, 'Sandalias de Verano Birkenstock', 'BIRKEN001', 'BIRKEN123', 5, 4, 9, 'botines.png'),
(5, 'Botines de Moda Dr. Martens', 'MARTENS001', 'MARTENS123', 6, 5, 10, 'moda.png'),
(6, 'Zapatos Formales Clarks', 'CLARKS001', 'CLARKS123', 7, 6, 7, 'formales.png'),
(7, 'Zapatillas para Correr Adidas Ultraboost', 'ADIDAS001', 'ADIDAS123', 2, 7, 1, 'zapatillas.png'),
(8, 'Chanclas de Playa Havaianas', 'HAVAIANAS001', 'HAVAIANAS123', 10, 8, 8, 'chanclas.png'),
(9, 'Mocasines Elegantes Gucci', 'GUCCI001', 'GUCCI123', 8, 9, 7, 'mocasines.png'),
(10, 'Botas de Lluvia Hunter', 'HUNTER001', 'HUNTER123', 9, 10, 5, 'botasdelluvia.png');

SELECT*FROM tb_productos;

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

INSERT INTO tb_detalles_productos (id_detalle_producto, id_producto, id_talla, precio, existencias, id_color, id_descuento, descripcion, id_material)
VALUES
(1, 1, 1, 129.99, 50, 1, 1, 'Zapatillas Nike Air Max', 1),
(2, 1, 2, 129.99, 75, 2, 2, 'Zapatillas Nike Air Max', 1),
(3, 2, 3, 189.99, 30, 8, 3,  'Botas Timberland', 2),
(4, 2, 4, 189.99, 40, 3,4,  'Botas Timberland', 2),
(5, 3, 5, 59.99, 100, 3,5, 'Zapatillas Converse Chuck Taylor', 3),
(6, 3, 6, 59.99, 80, 2, 6,'Zapatillas Converse Chuck Taylor', 3),
(7, 4, 7, 79.99, 60, 1, 7, 'Sandalias Birkenstock', 4),
(8, 4,  8, 79.99, 70, 2, 8, 'Sandalias Birkenstock', 4),
(9, 5, 9, 169.99, 45, 1,9, 'Botas Dr. Martens', 5),
(10, 5, 10, 169.99, 55, 4, 10,'Botas Dr. Martens', 5);

SELECT*FROM tb_detalles_productos;

INSERT INTO tb_reservas (id_reserva, id_usuario, fecha_reserva, estado_reserva, id_distrito) VALUES
(1, 1, '2024-04-06 10:00:00', 'Pendiente' , 1),
(2, 2, '2024-04-07 11:00:00', 'Pendiente', 2),
(3, 3, '2024-04-08 12:00:00', 'Pendiente', 3),
(4, 4, '2024-04-09 13:00:00', 'Pendiente',  4),
(5, 5, '2024-04-10 14:00:00', 'Pendiente', 5),
(6, 6, '2024-04-11 15:00:00', 'Pendiente',6),
(7, 7, '2024-04-12 16:00:00','Pendiente', 7),
(8, 8, '2024-04-13 17:00:00', 'Pendiente', 8),
(9, 9, '2024-04-14 18:00:00', 'Pendiente',9),
(10, 10, '2024-04-15 19:00:00', 'Pendiente', 10);

SELECT*FROM tb_reservas;

INSERT INTO tb_detalles_reservas (id_detalle_reserva, id_reserva, id_producto, cantidad, precio_unitario, id_detalle_producto)
VALUES
(1, 1, 1, 2, 129.99, 1),
(2, 2, 2, 1, 189.99, 2),
(3, 3, 3, 3, 59.99, 3),
(4, 4, 4, 2, 79.99, 4),
(5, 5, 5, 1, 169.99, 5),
(6, 6, 6, 4, 99.99, 6),
(7, 7, 7, 2, 119.99, 7),
(8, 8, 8, 3, 39.99, 8),
(9, 9, 9, 1, 149.99, 9),
(10, 10, 10, 2, 199.99, 10);

SELECT*FROM tb_detalles_reservas;
