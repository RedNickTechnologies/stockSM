<?php
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SELECT * FROM vehicles");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Vehicles count: " . count($vehicles) . "\n";
foreach($vehicles as $v) {
    echo "ID: {$v['id']}, Plate: {$v['license_plate']}, Status: {$v['status']}\n";
}
