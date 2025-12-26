-- -------------------------------------------------------------
-- Schema para MarginLab
-- Generado para entorno de desarrollo y pruebas públicas
-- -------------------------------------------------------------

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- Tabla: lineas_oferta
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lineas_oferta`;

CREATE TABLE `lineas_oferta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linea_oferta` int(11) DEFAULT 0,
  `cod_oferta` bigint(20) DEFAULT NULL,
  `version` int(11) DEFAULT 0,
  `proveedor` varchar(50) DEFAULT NULL,
  `cod_art` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `unidades` int(11) DEFAULT 0,
  
  -- Campos económicos (Decimal 10,2 para manejar céntimos correctamente)
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `descuento_venta` decimal(10,2) DEFAULT 0.00,
  `neto_venta` decimal(10,2) DEFAULT 0.00,
  `importe_venta` decimal(10,2) DEFAULT 0.00,
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `descuento_factura` decimal(10,2) DEFAULT 0.00,
  
  -- Logística y extras
  `camion` varchar(50) DEFAULT '',
  `palet` decimal(10,2) DEFAULT 0.00,
  `cantidad` int(11) DEFAULT 0,
  `plv` decimal(10,2) DEFAULT 0.00,
  `extra` decimal(10,2) DEFAULT 0.00,
  `especial_venta` varchar(50) DEFAULT '',
  
  -- Totales y cálculos
  `precio_factura` decimal(10,2) DEFAULT 0.00,
  `porte` varchar(50) DEFAULT '',
  `pc_porte` decimal(10,2) DEFAULT 0.00,
  `descuento_ne` decimal(10,2) DEFAULT 0.00,
  `rappel` decimal(10,2) DEFAULT 0.00,
  `programa` decimal(10,2) DEFAULT 0.00,
  `pc_rappel` decimal(10,2) DEFAULT 0.00,
  
  -- Campos BSV (Parecen ser porcentajes o euros adicionales)
  `bsv_porc_fact` decimal(10,2) DEFAULT 0.00,
  `bsv_porc_rappel` decimal(10,2) DEFAULT 0.00,
  `bsv_eur_fact` decimal(10,2) DEFAULT 0.00,
  `bsv_eur_rappel` decimal(10,2) DEFAULT 0.00,
  
  -- Datos del Cliente y Documento
  `documento` int(11) DEFAULT 0,
  `cod_cliente` varchar(50) DEFAULT NULL,
  `nomb_cliente` varchar(100) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `obra` varchar(100) DEFAULT NULL,
  `obs_comercial` text DEFAULT NULL,
  `obs_revision` text DEFAULT NULL,
  
  -- Auditoría básica
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_cod_oferta` (`cod_oferta`),
  KEY `idx_proveedor` (`proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Datos de prueba (Dump de datos anonimizados)
-- -------------------------------------------------------------

INSERT INTO `lineas_oferta` 
(cod_oferta, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta, neto_venta, importe_venta, precio_compra, descuento_factura, rappel, documento, cod_cliente, nomb_cliente, referencia, obra, obs_comercial) 
VALUES
(123456789, 'P0000163', '556056400020', 'Panel Aislante Térmico 50mm', 50, 8.50, 10.00, 7.65, 382.50, 4.20, 15.00, 25.00, 987654, 'CLI-001', 'Construcciones Norte S.L.', 'REF-2024-01', 'Reforma Hospital', 'Cliente solicita entrega urgente'),
(123456789, 'P0000636', '556056800015', 'Tubería PVC 110mm', 100, 3.20, 5.00, 3.04, 304.00, 1.50, 10.00, 5.00, 987654, 'CLI-001', 'Construcciones Norte S.L.', 'REF-2024-01', 'Reforma Hospital', 'Validar stock antes de confirmar'),
(987654321, 'P0000103', '111011501094', 'Cemento Cola Flexible 25kg', 200, 5.00, 20.00, 4.00, 800.00, 2.10, 30.00, 10.00, 555666, 'CLI-002', 'Reformas Garcia', 'OBRA-22', 'Chalet La Moraleja', 'Descuento especial por volumen'),
(987654321, 'P0000103', '111011501095', 'Azulejo Blanco Mate 30x60', 45, 12.00, 0.00, 12.00, 540.00, 6.00, 25.00, 12.50, 555666, 'CLI-002', 'Reformas Garcia', 'OBRA-22', 'Chalet La Moraleja', NULL),
(456123789, 'P0000999', 'ELEC-001', 'Cableado 2.5mm Libre Halógenos', 500, 0.80, 15.00, 0.68, 340.00, 0.35, 40.00, 2.00, 112233, 'CLI-003', 'Electricidad Industrial SA', 'IND-55', 'Nave Logística', 'Pendiente de aprobación técnica');

-- insertar segunda version (version = 1) para el primer presupuesto (lineas con cod_oferta = 123456789)
INSERT INTO `lineas_oferta`
(cod_oferta, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta, neto_venta, importe_venta, precio_compra, descuento_factura, rappel, documento, cod_cliente, nomb_cliente, referencia, obra, obs_comercial, version)
VALUES
(123456789, 'P0000163', '556056400020', 'Panel Aislante Térmico 50mm', 60, 8.00, 12.00, 7.04, 422.40, 4.00, 18.00, 30.00, 987654, 'CLI-001', 'Construcciones Norte S.L.', 'REF-2024-01', 'Reforma Hospital', 'Actualización de precio en segunda versión', 1),
(123456789, 'P0000636', '556056800015', 'Tubería PVC 110mm', 120, 3.00, 7.00, 2.79, 334.80, 1.40, 12.00, 6.00, 987654, 'CLI-001', 'Construcciones Norte S.L.', 'REF-2024-01', 'Reforma Hospital', 'Actualización de precio en segunda versión', 1);

SET FOREIGN_KEY_CHECKS = 1;