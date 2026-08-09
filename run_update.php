<?php
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
$sql = file_get_contents('update_v6.sql');
try {
    $conn->exec($sql);
    echo "SQL executed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
