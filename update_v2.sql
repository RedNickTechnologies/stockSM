-- Ejecuta este script en tu gestor de base de datos para actualizar a la versión con Metas de Venta
USE supermarket_stock;

ALTER TABLE users ADD COLUMN monthly_goal DECIMAL(10,2) DEFAULT 0.00 AFTER role;
