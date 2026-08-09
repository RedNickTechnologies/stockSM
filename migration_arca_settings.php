<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = "
    INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
    ('company_name', 'Mi Supermercado S.A.'),
    ('company_address', 'Av. Falsa 123, CABA'),
    ('company_cuit', '30-12345678-9'),
    ('company_vat', 'Responsable Inscripto'),
    ('company_iibb', '901-123456-1'),
    ('company_start_date', '01/01/2020');
    ";
    
    $conn->exec($sql);
    echo "Migración exitosa.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
