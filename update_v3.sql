USE supermarket_stock;

-- Update users table
ALTER TABLE users ADD COLUMN email VARCHAR(100) DEFAULT NULL AFTER username;
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'transporter') DEFAULT 'user';

-- Update products table for images
ALTER TABLE products ADD COLUMN image_url VARCHAR(255) DEFAULT NULL AFTER description;

-- Create tickets table
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

-- Create stock renewals (Transporter requests)
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

-- Create transfers (Branch shipments)
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
