<?php
require_once 'config/database.php';
require_once 'config/config.php';

class InvoiceController {
    private $conn;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            die("No autorizado");
        }
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function view() {
        if (!isset($_GET['id'])) {
            die("ID de factura no proporcionado");
        }
        $sale_id = (int)$_GET['id'];

        $stmt = $this->conn->prepare("
            SELECT s.*, u.username, a.cae, a.vto_cae, a.tipo_factura, a.cuit_cliente
            FROM sales s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN arca_invoices a ON s.id = a.sale_id
            WHERE s.id = ?
        ");
        $stmt->execute([$sale_id]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sale) {
            die("Factura no encontrada");
        }

        if ($_SESSION['role'] !== 'admin' && $sale['user_id'] !== $_SESSION['user_id']) {
            die("No tienes permiso para ver esta factura.");
        }

        $d_stmt = $this->conn->prepare("
            SELECT sd.*, p.name 
            FROM sale_details sd 
            JOIN products p ON sd.product_id = p.id 
            WHERE sd.sale_id = ?
        ");
        $d_stmt->execute([$sale_id]);
        $details = $d_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/shared/invoice_pdf.php';
    }

    public function viewTransfer() {
        if (!isset($_GET['id'])) die("ID no proporcionado");
        $transfer_id = (int)$_GET['id'];

        $stmt = $this->conn->prepare("
            SELECT t.*, ua.username as admin_name, ut.username as transporter_name 
            FROM transfers t 
            JOIN users ua ON t.admin_id = ua.id 
            LEFT JOIN users ut ON t.transporter_id = ut.id 
            WHERE t.id = ?
        ");
        $stmt->execute([$transfer_id]);
        $transfer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transfer) die("Envío no encontrado");

        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'transporter') {
            die("No autorizado");
        }

        $d_stmt = $this->conn->prepare("
            SELECT td.*, p.name 
            FROM transfer_details td 
            JOIN products p ON td.product_id = p.id 
            WHERE td.transfer_id = ?
        ");
        $d_stmt->execute([$transfer_id]);
        $details = $d_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/shared/transfer_pdf.php';
    }

    public function viewTransporterStock() {
        if ($_SESSION['role'] !== 'transporter') die("No autorizado");
        
        $vreq_stmt = $this->conn->prepare("SELECT vr.*, v.license_plate, v.brand, v.model FROM vehicle_requests vr LEFT JOIN vehicles v ON vr.vehicle_id = v.id WHERE vr.transporter_id = ? AND vr.status = 'approved' ORDER BY vr.id DESC LIMIT 1");
        $vreq_stmt->execute([$_SESSION['user_id']]);
        $vehicle = $vreq_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$vehicle) die("No tienes un vehículo asignado y aprobado para generar la hoja de ruta.");

        $d_stmt = $this->conn->prepare("
            SELECT p.id, p.name, SUM(td.quantity) as total_qty
            FROM transfers t
            JOIN transfer_details td ON t.id = td.transfer_id
            JOIN products p ON td.product_id = p.id
            WHERE t.transporter_id = ? AND t.status = 'accepted'
            GROUP BY p.id
        ");
        $d_stmt->execute([$_SESSION['user_id']]);
        $stock_items = $d_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/shared/transporter_pdf.php';
    }
}
?>
