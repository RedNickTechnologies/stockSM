<?php
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->query("SHOW TABLES LIKE 'arca_invoices'");
if ($stmt->fetch()) {
    echo "EXISTS";
} else {
    echo "DOES NOT EXIST";
}
?>
