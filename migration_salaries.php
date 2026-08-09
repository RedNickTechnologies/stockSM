<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS salary_liquidations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        period_month INT NOT NULL,
        period_year INT NOT NULL,
        base_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        deductions DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        net_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";
    
    $conn->exec($sql);
    echo "Migración de salarios exitosa.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
