<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    $sql = "
    ALTER TABLE sales
    ADD COLUMN delivery_type ENUM('direct', 'transport') DEFAULT 'direct' AFTER status,
    ADD COLUMN transporter_id INT NULL AFTER delivery_type,
    ADD COLUMN transport_status ENUM('pending', 'accepted', 'in_transit', 'delivered', 'rejected') NULL AFTER transporter_id,
    ADD FOREIGN KEY (transporter_id) REFERENCES users(id) ON DELETE SET NULL;
    ";
    
    $conn->exec($sql);
    echo "Migración exitosa.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
