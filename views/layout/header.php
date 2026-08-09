<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket Stock</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* Layout Structure */
        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }
        
        .main-content {
            flex-grow: 1;
            width: calc(100% - 260px);
            order: 1; /* Main content on the left */
            /* Fade transition */
            animation: fadeIn 0.4s ease-in-out;
            padding-bottom: 2rem;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-right {
            width: 260px;
            background-color: #212529; /* bg-dark */
            color: white;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            order: 2; /* Sidebar on the right */
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            height: 100vh;
            overflow-y: auto;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sidebar Styling */
        .sidebar-brand {
            font-size: 1.5rem;
            padding: 1.5rem 1rem;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #343a40;
            color: white;
            text-decoration: none;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
            flex-grow: 1;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1.5rem;
            border-radius: 0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .sidebar-nav .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar-nav .nav-link.active {
            color: white;
            background-color: #0d6efd; /* Primary color */
            border-left: 4px solid #fff;
            font-weight: bold;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #343a40;
        }
        
        .user-info-box {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        /* Ajustar contenedor para login cuando no hay sesión */
        .login-wrapper {
            animation: fadeIn 0.4s ease-in-out;
        }
    </style>
</head>
<body>

<?php
// Function to check active page
$current_page = isset($_GET['page']) ? $_GET['page'] : '';
function is_active($page_name, $current_page) {
    return $page_name === $current_page ? 'active' : '';
}
?>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="app-container">
    
    <div class="sidebar-right">
        <a href="#" class="sidebar-brand">
            <i class="bi bi-cart3"></i> SuperMarket
        </a>
        
        <ul class="sidebar-nav">
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a class="nav-link <?= is_active('admin_dashboard', $current_page) ?>" href="index.php?page=admin_dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li><a class="nav-link <?= is_active('admin_fleet', $current_page) ?>" href="index.php?page=admin_fleet"><i class="bi bi-truck me-2"></i> Flota y Gastos</a></li>
                <li><a class="nav-link <?= is_active('admin_users', $current_page) ?>" href="index.php?page=admin_users"><i class="bi bi-people me-2"></i> Usuarios</a></li>
                <li><a class="nav-link <?= is_active('admin_products', $current_page) ?>" href="index.php?page=admin_products"><i class="bi bi-box-seam me-2"></i> Productos</a></li>
                <li><a class="nav-link <?= is_active('admin_sales', $current_page) ?>" href="index.php?page=admin_sales"><i class="bi bi-receipt me-2"></i> Ventas</a></li>
                <li><a class="nav-link <?= is_active('admin_ddjj', $current_page) ?>" href="index.php?page=admin_ddjj"><i class="bi bi-file-earmark-bar-graph me-2"></i> Balances (DDJJ)</a></li>
                <li><a class="nav-link <?= is_active('admin_reports', $current_page) ?>" href="index.php?page=admin_reports"><i class="bi bi-graph-up me-2"></i> Reportes</a></li>
                <li><a class="nav-link <?= is_active('admin_settings', $current_page) ?>" href="index.php?page=admin_settings"><i class="bi bi-gear me-2"></i> Configuración (ARCA)</a></li>
                <li><a class="nav-link <?= is_active('admin_audit', $current_page) ?>" href="index.php?page=admin_audit"><i class="bi bi-shield-check me-2"></i> Auditoría / Logs</a></li>
                <li><a class="nav-link <?= is_active('admin_tickets', $current_page) ?>" href="index.php?page=admin_tickets"><i class="bi bi-headset me-2"></i> Tickets (Soporte)</a></li>
            <?php elseif ($_SESSION['role'] === 'transporter'): ?>
                <li><a class="nav-link <?= is_active('transporter_dashboard', $current_page) ?>" href="index.php?page=transporter_dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li><a class="nav-link <?= is_active('transporter_tickets', $current_page) ?>" href="index.php?page=transporter_tickets"><i class="bi bi-headset me-2"></i> Soporte</a></li>
            <?php elseif ($_SESSION['role'] === 'accountant'): ?>
                <li><a class="nav-link <?= is_active('accountant_dashboard', $current_page) ?>" href="index.php?page=accountant_dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard Contador</a></li>
                <li><a class="nav-link <?= is_active('accountant_sales', $current_page) ?>" href="index.php?page=accountant_sales"><i class="bi bi-receipt me-2"></i> Ventas y ARCA</a></li>
                <li><a class="nav-link <?= is_active('accountant_ddjj', $current_page) ?>" href="index.php?page=accountant_ddjj"><i class="bi bi-file-earmark-bar-graph me-2"></i> Balances / DDJJ</a></li>
            <?php else: ?>
                <li><a class="nav-link <?= is_active('user_dashboard', $current_page) ?>" href="index.php?page=user_dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li><a class="nav-link <?= is_active('user_sale', $current_page) ?>" href="index.php?page=user_sale"><i class="bi bi-cart-plus me-2"></i> Nueva Venta</a></li>
                <li><a class="nav-link <?= is_active('user_products', $current_page) ?>" href="index.php?page=user_products"><i class="bi bi-box-seam me-2"></i> Catálogo</a></li>
                <li><a class="nav-link <?= is_active('user_tickets', $current_page) ?>" href="index.php?page=user_tickets"><i class="bi bi-headset me-2"></i> Soporte</a></li>
            <?php endif; ?>
        </ul>

        <?php
        $db_header = new Database();
        $conn_header = $db_header->getConnection();
        
        $notif_stmt = $conn_header->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
        $notif_stmt->execute([$_SESSION['user_id']]);
        $notifications = $notif_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $usr_stmt = $conn_header->prepare("SELECT avatar_url FROM users WHERE id = ?");
        $usr_stmt->execute([$_SESSION['user_id']]);
        $usr_data = $usr_stmt->fetch(PDO::FETCH_ASSOC);
        $avatar = $usr_data['avatar_url'] ?? '';
        ?>
        
        <div class="sidebar-footer">
            <div class="user-info-box">
                <?php if($avatar): ?>
                    <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                <?php else: ?>
                    <i class="bi bi-person-circle fs-2 me-2 text-secondary"></i>
                <?php endif; ?>
                <div>
                    <div style="font-size: 0.9rem;">Hola,</div>
                    <strong style="font-size: 1.1rem;"><?= htmlspecialchars($_SESSION['username']) ?></strong>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-3">
                <!-- Dropup for notifications (since it's at the bottom of the screen) -->
                <div class="btn-group dropup w-50 pe-1">
                    <button type="button" class="btn btn-outline-light w-100 position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <?php if(count($notifications) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= count($notifications) ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu shadow" style="width: 250px; max-height: 300px; overflow-y: auto; right: 0; left: auto; margin-bottom: 5px;">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <?php if(count($notifications) > 0): ?>
                            <?php foreach($notifications as $n): ?>
                                <li><span class="dropdown-item text-wrap" style="font-size: 0.85rem;"><?= htmlspecialchars($n['message']) ?></span></li>
                            <?php endforeach; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center text-primary" href="index.php?page=mark_notifications_read">Marcar leídas</a></li>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted text-center">No hay nuevas</span></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <a href="index.php?page=logout" class="btn btn-outline-danger w-50 ps-1"><i class="bi bi-box-arrow-right"></i> Salir</a>
            </div>
        </div>
    </div>
    
    <div class="main-content">
<?php else: ?>
    <div class="login-wrapper">
<?php endif; ?>
