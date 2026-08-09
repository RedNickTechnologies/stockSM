<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Sanitización de ruta (Front Controller)
$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_SANITIZE_STRING) : 'login';

// Lista blanca de páginas permitidas
$allowed_pages = [
    'login', 'logout', 
    'admin_dashboard', 'admin_users', 'admin_products', 'admin_sales', 'admin_audit', 'admin_tickets', 'admin_logistics', 'admin_fleet', 'admin_ddjj',
    'user_dashboard', 'user_sale', 'user_products', 'user_tickets',
    'transporter_dashboard', 'transporter_tickets',
    'accountant_dashboard', 'accountant_sales', 'accountant_ddjj',
    'view_invoice', 'view_transfer_pdf', 'view_transporter_stock',
    'mark_notifications_read'
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
    case 'admin_reports':
    case 'export_products_pdf':
    case 'export_users_pdf':
    case 'export_audit_pdf':
    case 'export_vehicles_pdf':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        if ($page == 'admin_dashboard') $controller->dashboard();
        elseif ($page == 'admin_users') $controller->users();
        elseif ($page == 'admin_products') $controller->products();
        elseif ($page == 'admin_sales') $controller->sales();
        elseif ($page == 'admin_audit') $controller->auditLogs();
        elseif ($page == 'admin_reports') $controller->monthlyReports();
        elseif ($page == 'export_products_pdf') $controller->exportProductsPDF();
        elseif ($page == 'export_users_pdf') $controller->exportUsersPDF();
        elseif ($page == 'export_audit_pdf') $controller->exportAuditPDF();
        elseif ($page == 'export_vehicles_pdf') $controller->exportVehiclesPDF();
        break;

    case 'user_dashboard':
    case 'user_sale':
    case 'user_products':
    case 'user_tickets':
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        if ($page == 'user_dashboard') $controller->dashboard();
        elseif ($page == 'user_sale') $controller->createSale();
        elseif ($page == 'user_products') $controller->products();
        elseif ($page == 'user_tickets') $controller->tickets();
        break;

    case 'view_invoice':
    case 'view_transfer_pdf':
    case 'view_transporter_stock':
        require_once 'controllers/InvoiceController.php';
        $controller = new InvoiceController();
        if ($page === 'view_invoice') $controller->view();
        elseif ($page === 'view_transfer_pdf') $controller->viewTransfer();
        else $controller->viewTransporterStock();
        break;

    case 'admin_tickets':
    case 'user_tickets':
    case 'transporter_tickets':
        require_once 'controllers/TicketController.php';
        $controller = new TicketController();
        if ($page == 'admin_tickets') $controller->adminIndex();
        else $controller->userIndex();
        break;

    case 'transporter_dashboard':
        require_once 'controllers/TransporterController.php';
        $controller = new TransporterController();
        $controller->dashboard();
        break;

    case 'admin_fleet':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->fleet();
        break;

    case 'admin_ddjj':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->ddjj();
        break;

    case 'accountant_dashboard':
    case 'accountant_sales':
    case 'accountant_ddjj':
        require_once 'controllers/AccountantController.php';
        $controller = new AccountantController();
        if ($page == 'accountant_dashboard') $controller->dashboard();
        elseif ($page == 'accountant_sales') $controller->sales();
        elseif ($page == 'accountant_ddjj') $controller->ddjj();
        break;

    case 'mark_notifications_read':
        if (isset($_SESSION['user_id'])) {
            $stmt = (new Database())->getConnection()->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;

    default:
        echo "404 Not Found";
        break;
}
?>
