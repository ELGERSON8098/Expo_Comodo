USE expo_comodos;

INSERT INTO tb_generos_zapatos (nombre_genero, imagen_genero)
VALUES
('Masculino', 'masculino.jpg'),
('Femenino', 'femenino.jpg'),
('Unisex', 'unisex.jpg');
 
INSERT INTO tb_categorias (nombre_categoria, imagen)
VALUES
('Deportivos', 'deportivos.jpg'),
('Casuales', 'casuales.jpg'),
('Formales', 'formales.jpg');
 
INSERT INTO tb_tallas (nombre_talla)
VALUES
('32'),
('38'),
('40'),
('8');
 
INSERT INTO tb_marcas (marca)
VALUES
('Converse'),
('Adidas'),
('Puma');
 
INSERT INTO tb_colores (color)
VALUES
('Rojo'),
('Azul'),
('Negro');
 
INSERT INTO tb_descuentos (nombre_descuento, descripcion, valor)
VALUES
('Descuento Verano', 'Descuento del 15% en productos seleccionados', 15.00),
('Black Friday', 'Descuento del 25% en todos los productos', 25.00);
 
INSERT INTO tb_materiales (nombre)
VALUES
('Carton'),
('Sintético'),
('Textil');
 
INSERT INTO tb_productos (nombre_producto, codigo_interno, referencia_proveedor, precio, id_marca, id_genero, id_categoria, id_material, id_descuento, imagen)
VALUES
('Zapato Deportivo Nike', 'NIKE-001', 'REF-001', 120.00, 1, 1, 1, 1, 1, 'nike001.jpg'),
('Zapato Casual Adidas', 'ADIDAS-001', 'REF-002', 90.00, 2, 2, 2, 2, 1, 'adidas001.jpg');
 
INSERT INTO tb_detalles_productos (id_producto, id_talla, existencias, id_color, descripcion)
VALUES
(1, 2, 50, 3, 'Zapato deportivo Nike talla M, color Negro'),
(2, 3, 30, 1, 'Zapato casual Adidas talla L, color Rojo');
 
INSERT INTO tb_reservas (id_usuario, fecha_reserva, estado_reserva)
VALUES
(1, '2024-08-12 10:30:00', 'Pendiente'),
(1, '2024-08-12 11:00:00', 'Aceptado');
 
INSERT INTO tb_detalles_reservas (id_reserva, cantidad, precio_unitario, id_detalle_producto)
VALUES
(1,  2, 120.00, 1),
(2,  1, 90.00, 2);

SELECT * FROM tb_productos