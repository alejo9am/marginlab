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
  `cod_presupuesto` varchar(64) NOT NULL,
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
  `cod_presupuesto` varchar(64) NOT NULL,
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
-- Volcado de Datos (Seed Avanzado para Pruebas)
-- -------------------------------------------------------------

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
  `cod_presupuesto` varchar(64) NOT NULL,
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
  `cod_presupuesto` varchar(64) NOT NULL,
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
-- Volcado de Datos (Seed Final - Optimizado para Sandbox)
-- -------------------------------------------------------------

-- 1. Insertamos las CABECERAS (Presupuestos)
INSERT INTO `presupuestos` (cod_presupuesto, version, nombre_version, cod_cliente, nomb_cliente, referencia, obra, obs_comercial) VALUES
('10001', 1, 'Versión Inicial', 'CLI-A01', 'Construcciones Sólidas S.L.', 'REF-2024-01', 'Residencial Los Olivos', 'Presupuesto estándar con márgenes comerciales correctos.'),
('20002', 1, 'Versión Inicial', 'CLI-B02', 'Reformas Rápidas SA', 'REF-2024-02', 'Hospital Central', 'Versión base aprobada por dirección.'),
('20002', 2, 'Revisión Proveedores', 'CLI-B02', 'Reformas Rápidas SA', 'REF-2024-02', 'Hospital Central', 'ATENCIÓN: Subida de precios en grifería provoca pérdidas en una partida.'),
('30003', 1, 'Oferta Licitación', 'CLI-C03', 'Ayuntamiento de Madrid', 'REF-PUB-99', 'Polideportivo Municipal', 'OJO: Partidas netas y artículos gancho a coste.');

-- 2. Insertamos las LÍNEAS (Articulos)
INSERT INTO `articulos` 
(cod_presupuesto, version, proveedor, cod_art, descripcion, unidades, precio_venta, descuento_venta, precio_compra, descuento_factura, palet, cantidad, plv, extra, porte, rappel) 
VALUES

-- =================================================================================
-- PRESUPUESTO 1 (10001) - 28 Artículos - TODO CORRECTO
-- =================================================================================
('10001', 1, 'P01', 'MAT-001', 'Saco Cemento 25kg', 100, 12.50, 10.00, 6.00, 15.00, 0, 0, 0, 0, 0, 2),
('10001', 1, 'P01', 'MAT-002', 'Arena de Río (m3)', 15, 45.00, 10.00, 25.00, 10.00, 0, 0, 0, 0, 15, 2),
('10001', 1, 'P01', 'MAT-003', 'Grava 12-20mm (m3)', 10, 42.00, 10.00, 22.00, 10.00, 0, 0, 0, 0, 15, 2),
('10001', 1, 'P02', 'LAD-001', 'Ladrillo Hueco Doble', 2500, 0.35, 5.00, 0.15, 20.00, 0, 0, 0, 0, 0, 5),
('10001', 1, 'P02', 'LAD-002', 'Ladrillo Macizo', 1000, 0.60, 5.00, 0.30, 20.00, 0, 0, 0, 0, 0, 5),
('10001', 1, 'P02', 'LAD-003', 'Rasillón 40cm', 500, 0.45, 5.00, 0.20, 20.00, 0, 0, 0, 0, 0, 5),
('10001', 1, 'P02', 'LAD-004', 'Bloque Hormigón 40x20', 200, 1.20, 5.00, 0.60, 20.00, 0, 0, 0, 0, 0, 5),
('10001', 1, 'P03', 'AIS-001', 'Panel Lana Roca 40mm', 80, 14.50, 15.00, 8.00, 25.00, 0, 0, 0, 0, 0, 3),
('10001', 1, 'P03', 'AIS-002', 'Panel Poliestireno XPS', 60, 18.00, 15.00, 9.50, 25.00, 0, 0, 0, 0, 0, 3),
('10001', 1, 'P03', 'AIS-003', 'Rollo Impacto Acústico', 10, 85.00, 15.00, 45.00, 25.00, 0, 0, 0, 0, 0, 3),
('10001', 1, 'P04', 'YES-001', 'Placa Yeso 13mm Standard', 150, 9.50, 12.00, 4.50, 18.00, 0, 0, 0, 0, 0, 4),
('10001', 1, 'P04', 'YES-002', 'Placa Yeso Hidrófuga', 50, 14.50, 12.00, 7.50, 18.00, 0, 0, 0, 0, 0, 4),
('10001', 1, 'P04', 'YES-003', 'Perfil Montante 46mm', 200, 3.20, 12.00, 1.50, 18.00, 0, 0, 0, 0, 0, 4),
('10001', 1, 'P04', 'YES-004', 'Perfil Canal 48mm', 100, 2.80, 12.00, 1.30, 18.00, 0, 0, 0, 0, 0, 4),
('10001', 1, 'P04', 'YES-005', 'Pasta Juntas 25kg', 10, 22.00, 10.00, 11.00, 18.00, 0, 0, 0, 0, 0, 4),
('10001', 1, 'P05', 'PNT-001', 'Pintura Blanca Interior 15L', 30, 45.00, 20.00, 22.00, 30.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P05', 'PNT-002', 'Rodillo Antigota 22cm', 10, 6.50, 20.00, 3.00, 30.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P05', 'PNT-003', 'Cinta Carrocero 50m', 20, 2.50, 10.00, 1.00, 30.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P05', 'PNT-004', 'Plástico Cubretodo', 50, 1.50, 10.00, 0.60, 30.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P06', 'ELE-001', 'Tubo Corrugado 20mm (Rollo)', 20, 18.00, 25.00, 8.00, 35.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P06', 'ELE-002', 'Tubo Corrugado 25mm (Rollo)', 15, 22.00, 25.00, 10.00, 35.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P06', 'ELE-003', 'Caja Registro 100x100', 40, 3.50, 25.00, 1.50, 35.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P06', 'ELE-004', 'Cable 1.5mm Azul', 10, 35.00, 20.00, 18.00, 35.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P06', 'ELE-005', 'Cable 1.5mm Negro', 10, 35.00, 20.00, 18.00, 35.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P06', 'ELE-006', 'Cable 1.5mm T/T', 10, 35.00, 20.00, 18.00, 35.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P07', 'CAR-001', 'Puerta Paso Blanca', 12, 160.00, 15.00, 90.00, 20.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P07', 'CAR-002', 'Manilla Inox', 12, 18.00, 15.00, 8.00, 20.00, 0, 0, 0, 0, 0, 0),
('10001', 1, 'P07', 'CAR-003', 'Rodapié Blanco 9cm (ml)', 150, 4.50, 15.00, 2.00, 20.00, 0, 0, 0, 0, 0, 0),

-- =================================================================================
-- PRESUPUESTO 2 (20002) V1 - 30 Artículos - TODO CORRECTO
-- =================================================================================
('20002', 1, 'P08', 'FON-001', 'Tubería Cobre 15mm', 50, 6.50, 10.00, 3.00, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P08', 'FON-002', 'Tubería Cobre 18mm', 40, 8.50, 10.00, 4.00, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P08', 'FON-003', 'Codo Cobre 15mm', 30, 0.80, 10.00, 0.30, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P08', 'FON-004', 'Codo Cobre 18mm', 30, 1.20, 10.00, 0.50, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P08', 'FON-005', 'Grifo Lavabo Monomando', 20, 65.00, 15.00, 30.00, 25.00, 0, 0, 0, 0, 0, 2), -- ESTE CAMBIARÁ EN V2
('20002', 1, 'P08', 'FON-006', 'Grifo Ducha Termostático', 20, 120.00, 15.00, 60.00, 25.00, 0, 0, 0, 0, 0, 2),
('20002', 1, 'P08', 'FON-007', 'Mecanismo Cisterna', 20, 25.00, 15.00, 12.00, 20.00, 0, 0, 0, 0, 0, 2),
('20002', 1, 'P09', 'SAN-001', 'Inodoro Compacto', 20, 180.00, 20.00, 95.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P09', 'SAN-002', 'Lavabo Porcelana', 20, 55.00, 20.00, 25.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P09', 'SAN-003', 'Plato Ducha Resina 120', 20, 220.00, 20.00, 110.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P09', 'SAN-004', 'Mampara Ducha Fijo', 20, 190.00, 20.00, 90.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P10', 'SOL-001', 'Gres Porcelánico 60x60', 400, 22.00, 12.00, 12.00, 18.00, 0, 0, 0, 0, 0, 3),
('20002', 1, 'P10', 'SOL-002', 'Azulejo Blanco Mate 30x90', 500, 18.00, 12.00, 9.00, 18.00, 0, 0, 0, 0, 0, 3),
('20002', 1, 'P10', 'SOL-003', 'Cemento Cola Flex', 60, 14.50, 10.00, 6.50, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P10', 'SOL-004', 'Junta Color 5kg', 20, 8.50, 10.00, 4.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P10', 'SOL-005', 'Crucetas 2mm (Bolsa)', 10, 3.00, 10.00, 1.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P11', 'VEN-001', 'Ventana PVC 120x120', 15, 280.00, 15.00, 150.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P11', 'VEN-002', 'Ventana PVC 60x60', 8, 160.00, 15.00, 85.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P11', 'VEN-003', 'Puerta Balconera', 5, 450.00, 15.00, 240.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P11', 'VEN-004', 'Espuma Poliuretano', 20, 6.50, 10.00, 2.50, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P12', 'CLI-001', 'Split AA 3000fg', 10, 450.00, 25.00, 220.00, 35.00, 0, 0, 0, 0, 0, 5),
('20002', 1, 'P12', 'CLI-002', 'Conductos Fibra (m2)', 50, 25.00, 15.00, 12.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P12', 'CLI-003', 'Rejilla Impulsión', 15, 35.00, 15.00, 15.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P12', 'CLI-004', 'Rejilla Retorno', 15, 35.00, 15.00, 15.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P13', 'DOM-001', 'Pantalla Táctil Domótica', 5, 350.00, 20.00, 180.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P13', 'DOM-002', 'Actuador Persianas', 15, 85.00, 20.00, 45.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P13', 'DOM-003', 'Termostato Inteligente', 10, 120.00, 20.00, 60.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P14', 'SEG-001', 'Cámara IP Exterior', 8, 140.00, 15.00, 75.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P14', 'SEG-002', 'Grabador NVR 8ch', 1, 280.00, 15.00, 150.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 1, 'P14', 'SEG-003', 'Disco Duro 4TB', 1, 120.00, 10.00, 80.00, 15.00, 0, 0, 0, 0, 0, 0),

-- =================================================================================
-- PRESUPUESTO 2 (20002) V2 - Copia de V1 con ALERTA BSV NEGATIVO
-- =================================================================================
-- Copiamos las líneas normales (excepto la 5)
('20002', 2, 'P08', 'FON-001', 'Tubería Cobre 15mm', 50, 6.50, 10.00, 3.00, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P08', 'FON-002', 'Tubería Cobre 18mm', 40, 8.50, 10.00, 4.00, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P08', 'FON-003', 'Codo Cobre 15mm', 30, 0.80, 10.00, 0.30, 15.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P08', 'FON-004', 'Codo Cobre 18mm', 30, 1.20, 10.00, 0.50, 15.00, 0, 0, 0, 0, 0, 0),

-- !!! ALERTA AQUÍ: Precio Compra (100.00) > Precio Venta Neto (aprox 55.00)
('20002', 2, 'P08', 'FON-005', 'Grifo Lavabo Monomando', 20, 65.00, 15.00, 80.00, 25.00, 0, 0, 0, 0, 0, 0), 

('20002', 2, 'P08', 'FON-006', 'Grifo Ducha Termostático', 20, 120.00, 15.00, 60.00, 25.00, 0, 0, 0, 0, 0, 2),
('20002', 2, 'P08', 'FON-007', 'Mecanismo Cisterna', 20, 25.00, 15.00, 12.00, 20.00, 0, 0, 0, 0, 0, 2),
('20002', 2, 'P09', 'SAN-001', 'Inodoro Compacto', 20, 180.00, 20.00, 95.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P09', 'SAN-002', 'Lavabo Porcelana', 20, 55.00, 20.00, 25.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P09', 'SAN-003', 'Plato Ducha Resina 120', 20, 220.00, 20.00, 110.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P09', 'SAN-004', 'Mampara Ducha Fijo', 20, 190.00, 20.00, 90.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P10', 'SOL-001', 'Gres Porcelánico 60x60', 400, 22.00, 12.00, 12.00, 18.00, 0, 0, 0, 0, 0, 3),
('20002', 2, 'P10', 'SOL-002', 'Azulejo Blanco Mate 30x90', 500, 18.00, 12.00, 9.00, 18.00, 0, 0, 0, 0, 0, 3),
('20002', 2, 'P10', 'SOL-003', 'Cemento Cola Flex', 60, 14.50, 10.00, 6.50, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P10', 'SOL-004', 'Junta Color 5kg', 20, 8.50, 10.00, 4.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P10', 'SOL-005', 'Crucetas 2mm (Bolsa)', 10, 3.00, 10.00, 1.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P11', 'VEN-001', 'Ventana PVC 120x120', 15, 280.00, 15.00, 150.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P11', 'VEN-002', 'Ventana PVC 60x60', 8, 160.00, 15.00, 85.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P11', 'VEN-003', 'Puerta Balconera', 5, 450.00, 15.00, 240.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P11', 'VEN-004', 'Espuma Poliuretano', 20, 6.50, 10.00, 2.50, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P12', 'CLI-001', 'Split AA 3000fg', 10, 450.00, 25.00, 220.00, 35.00, 0, 0, 0, 0, 0, 5),
('20002', 2, 'P12', 'CLI-002', 'Conductos Fibra (m2)', 50, 25.00, 15.00, 12.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P12', 'CLI-003', 'Rejilla Impulsión', 15, 35.00, 15.00, 15.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P12', 'CLI-004', 'Rejilla Retorno', 15, 35.00, 15.00, 15.00, 20.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P13', 'DOM-001', 'Pantalla Táctil Domótica', 5, 350.00, 20.00, 180.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P13', 'DOM-002', 'Actuador Persianas', 15, 85.00, 20.00, 45.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P13', 'DOM-003', 'Termostato Inteligente', 10, 120.00, 20.00, 60.00, 30.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P14', 'SEG-001', 'Cámara IP Exterior', 8, 140.00, 15.00, 75.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P14', 'SEG-002', 'Grabador NVR 8ch', 1, 280.00, 15.00, 150.00, 25.00, 0, 0, 0, 0, 0, 0),
('20002', 2, 'P14', 'SEG-003', 'Disco Duro 4TB', 1, 120.00, 10.00, 80.00, 15.00, 0, 0, 0, 0, 0, 0),

-- =================================================================================
-- PRESUPUESTO 3 (30003) - 35 Artículos - MIXTO (NETO + BSV)
-- =================================================================================
-- 1. ALERTA NETO (Mortero Técnico que no tiene descuento comercial)
-- 'descuento_venta' = 0.00 hace saltar la alerta de Precio Neto
('30003', 1, 'P99', 'MOR-TEC-01', 'Mortero Reparación Estructural R4', 25, 42.50, 0.00, 22.00, 15.00, 0, 0, 0, 0, 0, 0),

-- Resto (Normales)
('30003', 1, 'P15', 'URB-001', 'Banco Parque Madera', 12, 180.00, 15.00, 95.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P15', 'URB-002', 'Papelera Metálica', 20, 80.00, 15.00, 40.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P15', 'URB-003', 'Bolardo Fundición', 50, 45.00, 15.00, 22.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P15', 'URB-004', 'Alcorque Árbol', 15, 60.00, 15.00, 30.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P15', 'URB-005', 'Fuente Bebedero', 2, 850.00, 20.00, 400.00, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P16', 'LUM-001', 'Farola LED 4m', 20, 350.00, 25.00, 160.00, 30.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P16', 'LUM-002', 'Luminaria Vial', 20, 180.00, 25.00, 90.00, 30.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P16', 'LUM-003', 'Proyector LED 50W', 30, 45.00, 25.00, 20.00, 30.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P16', 'LUM-004', 'Báculo Acero', 20, 120.00, 20.00, 60.00, 25.00, 0, 0, 0, 0, 0, 0),

-- 2. ALERTA BSV y ALERTA NETO (Lámina donde el coste supera al precio de venta)
-- 'descuento_venta' = 0.00
-- Venta Neta: 85.00 - 10% = 76.50
-- Coste Compra: 90.00 (Superior a la venta -> Pérdidas)
('30003', 1, 'P99', 'IMP-LAM-05', 'Lámina Drenante Nods (Rollo)', 10, 85.00, 0.00, 90.00, 0.00, 0, 0, 0, 0, 0, 0),

-- Resto (Normales)
('30003', 1, 'P16', 'LUM-005', 'Arqueta Alumbrado', 20, 35.00, 15.00, 15.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P17', 'JAR-001', 'Tierra Vegetal (m3)', 40, 28.00, 10.00, 12.00, 15.00, 0, 0, 0, 0, 10, 0),
('30003', 1, 'P17', 'JAR-002', 'Césped Tepes (m2)', 200, 9.50, 10.00, 4.00, 15.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P17', 'JAR-003', 'Corteza Pino (saco)', 100, 6.00, 10.00, 2.50, 15.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P17', 'JAR-004', 'Árbol Sombra 14/16', 15, 140.00, 20.00, 70.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P17', 'JAR-005', 'Arbusto Flor Variado', 80, 12.00, 15.00, 5.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P18', 'RIE-001', 'Tubería PE 32mm', 100, 1.20, 20.00, 0.50, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P18', 'RIE-002', 'Tubería PE 16mm Goteo', 200, 0.45, 20.00, 0.20, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P18', 'RIE-003', 'Difusor Emergente', 50, 6.00, 20.00, 2.50, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P18', 'RIE-004', 'Electroválvula 1"', 12, 25.00, 20.00, 12.00, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P18', 'RIE-005', 'Programador Riego WiFi', 2, 180.00, 25.00, 90.00, 30.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P19', 'JUE-001', 'Columpio Mixto', 1, 1400.00, 15.00, 800.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P19', 'JUE-002', 'Tobogán Inox', 1, 1100.00, 15.00, 600.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P19', 'JUE-003', 'Muelle Individual', 4, 300.00, 15.00, 150.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P19', 'JUE-004', 'Suelo Caucho Continuo', 100, 55.00, 10.00, 30.00, 15.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P20', 'VAL-001', 'Valla Metálica (ml)', 200, 28.00, 15.00, 14.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P20', 'VAL-002', 'Puerta Acceso Vehículos', 2, 1500.00, 15.00, 800.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P20', 'VAL-003', 'Puerta Peatonal', 4, 450.00, 15.00, 200.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P21', 'SEN-001', 'Señal Vertical Tráfico', 15, 90.00, 20.00, 40.00, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P21', 'SEN-002', 'Espejo Convexo', 5, 120.00, 20.00, 60.00, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P21', 'SEN-003', 'Hito Flexible', 30, 35.00, 15.00, 15.00, 20.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P21', 'SEN-004', 'Pintura Vial (Lata)', 20, 60.00, 20.00, 30.00, 25.00, 0, 0, 0, 0, 0, 0),
('30003', 1, 'P21', 'SEN-005', 'Microesferas Vidrio', 5, 25.00, 15.00, 10.00, 20.00, 0, 0, 0, 0, 0, 0);

SET FOREIGN_KEY_CHECKS = 1;