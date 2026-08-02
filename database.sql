CREATE DATABASE IF NOT EXISTS supermarket_stock;
USE supermarket_stock;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user', 'transporter', 'accountant') DEFAULT 'user',
    monthly_goal DECIMAL(10,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
-- hash for admin123 generated via password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password_hash, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    stock_min INT NOT NULL DEFAULT 5,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sales (Invoices) table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Sale details table
CREATE TABLE IF NOT EXISTS sale_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Audit Logs (Caja de Seguridad)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, 
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tickets (Mesa de Ayuda)
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    admin_reply TEXT,
    status ENUM('open', 'answered', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Stock Renewals (Renovaciones por Transportista)
CREATE TABLE IF NOT EXISTS stock_renewals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transporter_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transporter_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Transfers (Envíos a Sucursales)
CREATE TABLE IF NOT EXISTS transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    transporter_id INT,
    branch_address VARCHAR(255) NOT NULL,
    status ENUM('pending', 'accepted', 'delivered', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id),
    FOREIGN KEY (transporter_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS transfer_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transfer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (transfer_id) REFERENCES transfers(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Vehicles table (Flota)
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

-- Vehicle Requests (Viajes)
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
    FOREIGN KEY (admin_id) REFERENCES users(id)
);

-- Company Monthly Reports (Reportes de Estado General de Empresa)
CREATE TABLE IF NOT EXISTS company_monthly_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    month INT NOT NULL,
    year INT NOT NULL,
    total_sales_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    total_sales_count INT NOT NULL DEFAULT 0,
    new_users_count INT NOT NULL DEFAULT 0,
    total_expenses DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_month_year (month, year)
);
