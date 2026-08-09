<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sql = "
    CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value VARCHAR(255) NOT NULL
    );
    INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('auto_report_day', '1');
    ";
    
    $conn->exec($sql);
    echo "Tabla settings creada correctamente.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
