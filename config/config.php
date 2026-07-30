<?php
// Configuración base de la URL
define('BASE_URL', 'http://localhost/supermarket_stock/');

// Iniciar sesión de forma segura si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    session_set_cookie_params([
        'lifetime' => 1800, // 30 minutos
        'path' => '/',
        'domain' => '',
        'secure' => false, // Cambiar a true si se usa HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Función de ayuda para registrar auditoría ("Caja de Seguridad")
function log_audit($conn, $user_id, $action, $details) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $details, $ip]);
    } catch(PDOException $e) {
        // Fallbacks here
    }
}
?>
