-- -------------------------------------------------------------
-- Schema para MarginLab (Refactorizado)
-- -------------------------------------------------------------

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Limpieza de tablas antiguas y nuevas
DROP TABLE IF EXISTS `lineas_oferta`;
DROP TABLE IF EXISTS `articulos`;
DROP TABLE IF EXISTS `presupuestos`;

-- -------------------------------------------------------------
-- Tabla: presupuestos (Cabeceras)
-- Contiene la información común del presupuesto y cliente
-- -------------------------------------------------------------
CREATE TABLE `presupuestos` (
  `cod_presupuesto` bigint(20) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `nombre_version` varchar(100) DEFAULT NULL,
  
  -- Datos del cliente
  `cod_cliente` varchar(50) DEFAULT NULL,
  `nomb_cliente` varchar(100) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `obra` varchar(100) DEFAULT NULL,

  -- Comentarios y observaciones
  `obs_comercial` text DEFAULT NULL,
  `obs_revision` text DEFAULT NULL,
  
  -- Auditoría
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`cod_presupuesto`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabla: articulos (Líneas)
-- Contiene solo la información específica de cada producto
-- -------------------------------------------------------------
CREATE TABLE `articulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_presupuesto` bigint(20) NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  
  `proveedor` varchar(50) DEFAULT NULL,
  `cod_art` varchar(50) DEFAULT NULL, -- Mantenemos cod_art para compatibilidad PHP
  `descripcion` text DEFAULT NULL,
  `unidades` int(11) DEFAULT 0,
  
  -- Campos económicos
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
  `porte` decimal(10,2) DEFAULT 0.00,
  `pc_porte` decimal(10,2) DEFAULT 0.00,
  `descuento_ne` decimal(10,2) DEFAULT 0.00,
  `rappel` decimal(10,2) DEFAULT 0.00,
  `programa` decimal(10,2) DEFAULT 0.00,
  `pc_rappel` decimal(10,2) DEFAULT 0.00,
  
  -- Campos BSV
  `bsv_porc_fact` decimal(10,2) DEFAULT 0.00,
  `bsv_porc_rappel` decimal(10,2) DEFAULT 0.00,
  `bsv_eur_fact` decimal(10,2) DEFAULT 0.00,
  `bsv_eur_rappel` decimal(10,2) DEFAULT 0.00,
  
  PRIMARY KEY (`id`),
  KEY `idx_presupuesto` (`cod_presupuesto`, `version`),
  CONSTRAINT `fk_presupuesto_articulo` FOREIGN KEY (`cod_presupuesto`, `version`) REFERENCES `presupuestos` (`cod_presupuesto`, `version`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Volcado de Datos (Separado en Cabeceras y Líneas)
-- -------------------------------------------------------------

-- 1. Insertamos las CABECERAS (Presupuestos únicos)
INSERT INTO `presupuestos` (cod_presupuesto, version, nombre_version, cod_cliente, nomb_cliente, referencia, obra, obs_comercial) VALUES
(123456789, 1, 'Versión Inicial', 'CLI-001', 'Construcciones Norte S.L.', 'REF-2024-01', 'Reforma Hospital', 'Cliente solicita entrega urgente'),
(123456789, 2, 'Primeros cambios', 'CLI-001', 'Construcciones Norte S.L.', 'REF-2024-01', 'Reforma Hospital', 'Actualización de precio en segunda versión'),
(987654321, 1, 'Versión Inicial', 'CLI-002', 'Reformas Garcia', 'OBRA-22', 'Chalet La Moraleja', 'Descuento especial por volumen'),
(456123789, 1, 'Versión Inicial', 'CLI-003', 'Electricidad Industrial SA', 'IND-55', 'Nave Logística', 'Pendiente de aprobación técnica');

-- 2. Insertamos las LÍNEAS (Artículos vinculados)
INSERT INTO `articulos` 
(cod_presupuesto, version, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta, neto_venta, importe_venta, precio_compra, descuento_factura, rappel) 
VALUES
-- Presupuesto 123456789 V1
(123456789, 1, 'P0000163', '556056400020', 'Panel Aislante Térmico 50mm', 50, 8.50, 10.00, 7.65, 382.50, 4.20, 15.00, 25.00),
(123456789, 1, 'P0000636', '556056800015', 'Tubería PVC 110mm', 100, 3.20, 5.00, 3.04, 304.00, 1.50, 10.00, 5.00),

-- Presupuesto 123456789 V2 (Cambios en precios/unidades)
(123456789, 2, 'P0000163', '556056400020', 'Panel Aislante Térmico 50mm', 60, 8.00, 12.00, 7.04, 422.40, 4.00, 18.00, 30.00),
(123456789, 2, 'P0000636', '556056800015', 'Tubería PVC 110mm', 120, 3.00, 7.00, 2.79, 334.80, 1.40, 12.00, 6.00),

-- Presupuesto 987654321 V1
(987654321, 1, 'P0000103', '111011501094', 'Cemento Cola Flexible 25kg', 200, 5.00, 20.00, 4.00, 800.00, 2.10, 30.00, 10.00),
(987654321, 1, 'P0000103', '111011501095', 'Azulejo Blanco Mate 30x60', 45, 12.00, 0.00, 12.00, 540.00, 6.00, 25.00, 12.50),

-- Presupuesto 456123789 V1
(456123789, 1, 'P0000999', 'ELEC-001', 'Cableado 2.5mm Libre Halógenos', 500, 0.80, 15.00, 0.68, 340.00, 0.35, 40.00, 2.00);

SET FOREIGN_KEY_CHECKS = 1;