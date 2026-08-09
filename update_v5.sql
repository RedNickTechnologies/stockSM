USE supermarket_stock;

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
