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


INSERT INTO tb_direcciones (id_direccion, direccion, id_usuario)
VALUES 
(1, 'San Salvador', 1),
(2, 'Santa Ana', 2),
(3, 'San Miguel', 3),
(4, 'La Libertad', 4),
(5, 'Usulután', 5),
(6, 'Sonsonate', 6),
(7, 'Chalatenango', 7),
(8, 'La Paz', 8),
(9, 'Cuscatlán', 9),
(10, 'Ahuachapán', 10);

SELECT*FROM tb_direcciones;

INSERT INTO tb_niveles_usuarios (id_nivel_usuario, nombre_nivel)
VALUES 
(1, 'administrador'),
(2, 'inventaristas'),
(3, 'vendedoras'),
(4, 'inventaristas'),
(5, 'vendedoras'),
(6, 'vendedoras'),
(7, 'vendedoras'),
(8, 'vendedoras'),
(9, 'vendedoras'),
(10, 'vendedoras');

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

INSERT INTO tb_productos (id_producto, nombre_producto, codigo_interno, referencia_proveedor, imagen)
VALUES 
(1, 'Zapatillas Running Nike Air Max', 'NIKE001', 'NIKE123', 'running.png'),
(2, 'Botas de Invierno Timberland', 'TIMBER001', 'TIMBER123', 'botas.png'),
(3, 'Zapatos Casuales Converse Chuck Taylor', 'CONVERSE001', 'CONVERSE123', 'casuales.png'),
(4, 'Sandalias de Verano Birkenstock', 'BIRKEN001', 'BIRKEN123', 'botines.png'),
(5, 'Botines de Moda Dr. Martens', 'MARTENS001', 'MARTENS123', 'moda.png'),
(6, 'Zapatos Formales Clarks', 'CLARKS001', 'CLARKS123', 'formales.png'),
(7, 'Zapatillas para Correr Adidas Ultraboost', 'ADIDAS001', 'ADIDAS123', 'zapatillas.png'),
(8, 'Chanclas de Playa Havaianas', 'HAVAIANAS001', 'HAVAIANAS123', 'chanclas.png'),
(9, 'Mocasines Elegantes Gucci', 'GUCCI001', 'GUCCI123', 'mocasines.png'),
(10, 'Botas de Lluvia Hunter', 'HUNTER001', 'HUNTER123', 'botasdelluvia.png');

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

INSERT INTO tb_detalles_productos (id_detalle_producto, id_producto, material, id_talla, precio, existencias, id_color, id_marca, id_descuento, descripcion, id_genero, id_categoria)
VALUES
(1, 1, 'Malla transpirable', 1, 129.99, 50, 1, 1, 1, 'Zapatillas Nike Air Max', 1, 1),
(2, 1, 'Malla transpirable', 2, 129.99, 75, 2, 1, 2, 'Zapatillas Nike Air Max', 2, 1),
(3, 2, 'Cuero', 3, 189.99, 30, 8, 3, 3, 'Botas Timberland', 3,  2),
(4, 2, 'Cuero', 4, 189.99, 40, 1, 3, 4, 'Botas Timberland', 4,  2),
(5, 3, 'Lona', 5, 59.99, 100, 3, 4, 5, 'Zapatillas Converse Chuck Taylor', 5, 3),
(6, 3, 'Lona', 6, 59.99, 80, 2, 4, 6, 'Zapatillas Converse Chuck Taylor', 6, 3),
(7, 4, 'Corcho', 7, 79.99, 60, 1, 5, 7, 'Sandalias Birkenstock', 7, 4),
(8, 4, 'Corcho', 8, 79.99, 70, 2, 5, 8, 'Sandalias Birkenstock', 8, 4),
(9, 5, 'Cuero', 9, 169.99, 45, 1, 6, 9, 'Botas Dr. Martens', 9, 5),
(10, 5, 'Cuero', 10, 169.99, 55, 4, 6, 9, 'Botas Dr. Martens',10,  5);

SELECT*FROM tb_detalles_productos;

INSERT INTO tb_reservas (id_reserva, id_usuario, fecha_reserva, id_direccion) VALUES
(1, 1, '2024-04-06 10:00:00', 1),
(2, 2, '2024-04-07 11:00:00', 2),
(3, 3, '2024-04-08 12:00:00', 3),
(4, 4, '2024-04-09 13:00:00', 4),
(5, 5, '2024-04-10 14:00:00', 5),
(6, 6, '2024-04-11 15:00:00', 6),
(7, 7, '2024-04-12 16:00:00', 7),
(8, 8, '2024-04-13 17:00:00', 8),
(9, 9, '2024-04-14 18:00:00', 9),
(10, 10, '2024-04-15 19:00:00', 10);

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
