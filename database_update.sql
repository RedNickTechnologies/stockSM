-- Actualización para Sistema de Reportes Automáticos
CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL
);

INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('auto_report_day', '1');


-- Actualización para Logística de Ventas (Envío por Transporte)
ALTER TABLE sales
ADD COLUMN delivery_type ENUM('direct', 'transport') DEFAULT 'direct' AFTER status,
ADD COLUMN transporter_id INT NULL AFTER delivery_type,
ADD COLUMN transport_status ENUM('pending', 'accepted', 'in_transit', 'delivered', 'rejected') NULL AFTER transporter_id,
ADD FOREIGN KEY (transporter_id) REFERENCES users(id) ON DELETE SET NULL;
