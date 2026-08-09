<?php
/**
 * Script de generación automática de reportes.
 * Debe configurarse para ejecutarse diariamente vía Cron o Tareas Programadas de Windows.
 */

if (php_sapi_name() !== 'cli') {
    die("Este script solo puede ejecutarse por línea de comandos o cron.");
}

chdir(__DIR__);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'controllers/AdminController.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Obtener el día configurado
    $stmt = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'auto_report_day'");
    $auto_day = (int)$stmt->fetchColumn();
    if (!$auto_day) $auto_day = 1;

    $current_day = (int)date('d');

    if ($current_day === $auto_day) {
        $month = (int)date('m');
        $year = (int)date('Y');
        
        $admin = new AdminController();
        $admin->generateMonthlyReportData($month, $year);
        echo "Reporte automático para $month/$year generado con éxito.\n";
    } else {
        echo "Hoy es día $current_day. El reporte está configurado para el día $auto_day. No se genera nada.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
