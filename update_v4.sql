USE supermarket_stock;

-- Add accountant role
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'transporter', 'accountant') DEFAULT 'user';

-- Vehicles table
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_plate VARCHAR(20) NOT NULL UNIQUE,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    type ENUM('car', 'pickup', 'truck') NOT NULL,
    weight_capacity DECIMAL(10,2) NOT NULL,
    volume_capacity DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'requested', 'in_use', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert 6 default vehicles
INSERT INTO vehicles (license_plate, brand, model, type, weight_capacity, volume_capacity) VALUES 
('AA123BB', 'Ford', 'Fiesta', 'car', 400.00, 1.50),
('AC456DD', 'Chevrolet', 'Cruze', 'car', 450.00, 1.60),
('AB789CC', 'Toyota', 'Hilux', 'pickup', 1000.00, 3.50),
('AD012EE', 'Volkswagen', 'Amarok', 'pickup', 1100.00, 3.80),
('AE345FF', 'Mercedes', 'Delivery', 'truck', 5000.00, 15.00),
('AF678GG', 'Iveco', 'Daily', 'truck', 6000.00, 18.00)
ON DUPLICATE KEY UPDATE id=id;

-- Vehicle Requests (Trips)
CREATE TABLE IF NOT EXISTS vehicle_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transporter_id INT NOT NULL,
    transfer_id INT DEFAULT NULL,
    vehicle_id INT DEFAULT NULL,
    is_own_vehicle BOOLEAN DEFAULT FALSE,
    own_license_plate VARCHAR(20) DEFAULT NULL,
    own_type VARCHAR(50) DEFAULT NULL,
    own_weight_capacity DECIMAL(10,2) DEFAULT NULL,
    own_volume_capacity DECIMAL(10,2) DEFAULT NULL,
    estimated_cost DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transporter_id) REFERENCES users(id),
    FOREIGN KEY (transfer_id) REFERENCES transfers(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

-- ARCA Invoices Mock
CREATE TABLE IF NOT EXISTS arca_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    cuit_cliente VARCHAR(20) NOT NULL,
    tipo_factura VARCHAR(10) NOT NULL,
    cae VARCHAR(50) NOT NULL,
    vto_cae DATE NOT NULL,
    status ENUM('emitted') DEFAULT 'emitted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

-- Sworn Declarations (Declaraciones Juradas)
CREATE TABLE IF NOT EXISTS declaraciones_juradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accountant_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    total_sales DECIMAL(15,2) NOT NULL,
    total_taxes DECIMAL(15,2) NOT NULL,
    file_url VARCHAR(255) DEFAULT NULL,
    status ENUM('pending_admin', 'approved', 'rejected', 'sent_to_arca') DEFAULT 'pending_admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (accountant_id) REFERENCES users(id),
    FOREIGN KEY (admin_id) REFERENCES users(id)
);
