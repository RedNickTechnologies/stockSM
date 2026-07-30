<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Sanitización de ruta (Front Controller)
$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_SANITIZE_STRING) : 'login';

// Lista blanca de páginas permitidas
$allowed_pages = [
    'login', 'logout', 
    'admin_dashboard', 'admin_users', 'admin_products', 'admin_sales', 'admin_audit',
    'user_dashboard', 'user_sale', 'export_products_pdf'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'login';
}

// Enrutamiento básico
switch ($page) {
    case 'login':
    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        if ($page == 'login') {
            $controller->login();
        } else {
            $controller->logout();
        }
        break;
        
    case 'admin_dashboard':
    case 'admin_users':
    case 'admin_products':
    case 'admin_sales':
    case 'admin_audit':
    case 'export_products_pdf':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        if ($page == 'admin_dashboard') $controller->dashboard();
        elseif ($page == 'admin_users') $controller->users();
        elseif ($page == 'admin_products') $controller->products();
        elseif ($page == 'admin_sales') $controller->sales();
        elseif ($page == 'admin_audit') $controller->auditLogs();
        elseif ($page == 'export_products_pdf') $controller->exportProductsPDF();
        break;

    case 'user_dashboard':
    case 'user_sale':
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        if ($page == 'user_dashboard') $controller->dashboard();
        elseif ($page == 'user_sale') $controller->createSale();
        break;

    default:
        echo "404 Not Found";
        break;
}
?>
