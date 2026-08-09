<?php
require_once 'config/database.php';
require_once 'config/config.php';

class TransporterController {
    private $conn;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'transporter') {
            header("Location: index.php?page=login");
            exit;
        }
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function dashboard() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'request_stock') {
                $product_id = $_POST['product_id'];
                $quantity = $_POST['quantity'];
                $stmt = $this->conn->prepare("INSERT INTO stock_renewals (transporter_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
                log_audit($this->conn, $_SESSION['user_id'], 'STOCK_RENEWAL_REQ', "Solicitó ingreso de $quantity uds del prod ID $product_id");
            } elseif ($_POST['action'] === 'accept_transfer') {
                $transfer_id = $_POST['transfer_id'];
                $stmt = $this->conn->prepare("UPDATE transfers SET transporter_id = ?, status = 'accepted' WHERE id = ? AND status = 'pending'");
                $stmt->execute([$_SESSION['user_id'], $transfer_id]);
                log_audit($this->conn, $_SESSION['user_id'], 'ACCEPT_TRANSFER', "Aceptó viaje de sucursal ID $transfer_id");
            } elseif ($_POST['action'] === 'finish_transfer') {
                $transfer_id = $_POST['transfer_id'];
                $outcome = $_POST['outcome']; 
                $stmt = $this->conn->prepare("UPDATE transfers SET status = ? WHERE id = ? AND transporter_id = ?");
                $stmt->execute([$outcome, $transfer_id, $_SESSION['user_id']]);
                
                if ($outcome === 'failed') {
                    $d_stmt = $this->conn->prepare("SELECT product_id, quantity FROM transfer_details WHERE transfer_id = ?");
                    $d_stmt->execute([$transfer_id]);
                    $details = $d_stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($details as $d) {
                        $upd = $this->conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                        $upd->execute([$d['quantity'], $d['product_id']]);
                    }
                    log_audit($this->conn, $_SESSION['user_id'], 'TRANSFER_FAILED', "Falló entrega $transfer_id. Stock devuelto.");
                } else {
                    log_audit($this->conn, $_SESSION['user_id'], 'TRANSFER_DELIVERED', "Entregó con éxito viaje $transfer_id.");
                }
            } elseif ($_POST['action'] === 'request_vehicle') {
                $is_own = $_POST['vehicle_choice'] === 'own';
                $vehicle_id = !$is_own ? $_POST['vehicle_id'] : null;
                
                if ($vehicle_id) {
                    $this->conn->prepare("UPDATE vehicles SET status = 'requested' WHERE id = ?")->execute([$vehicle_id]);
                }
                
                $plate = $is_own ? $_POST['own_license_plate'] : null;
                $type = $is_own ? $_POST['own_type'] : null;
                $weight = $is_own ? $_POST['own_weight_capacity'] : null;
                $volume = $is_own ? $_POST['own_volume_capacity'] : null;
                $cost = $is_own ? $_POST['estimated_cost'] : 0;
                
                $stmt = $this->conn->prepare("INSERT INTO vehicle_requests (transporter_id, vehicle_id, is_own_vehicle, own_license_plate, own_type, own_weight_capacity, own_volume_capacity, estimated_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $vehicle_id, $is_own ? 1 : 0, $plate, $type, $weight, $volume, $cost]);
            } elseif ($_POST['action'] === 'end_shift') {
                $req_id = $_POST['request_id'];
                $r_stmt = $this->conn->prepare("SELECT * FROM vehicle_requests WHERE id = ? AND transporter_id = ?");
                $r_stmt->execute([$req_id, $_SESSION['user_id']]);
                $req = $r_stmt->fetch(PDO::FETCH_ASSOC);
                if ($req) {
                    $this->conn->prepare("UPDATE vehicle_requests SET status = 'completed' WHERE id = ?")->execute([$req_id]);
                    if ($req['vehicle_id']) {
                        $this->conn->prepare("UPDATE vehicles SET status = 'available' WHERE id = ?")->execute([$req['vehicle_id']]);
                    }
                }
            } elseif ($_POST['action'] === 'update_sale_status') {
                $sale_id = $_POST['sale_id'];
                $status = $_POST['transport_status'];
                
                $stmt = $this->conn->prepare("UPDATE sales SET transport_status = ? WHERE id = ? AND transporter_id = ?");
                $stmt->execute([$status, $sale_id, $_SESSION['user_id']]);
                log_audit($this->conn, $_SESSION['user_id'], 'UPDATE_TRANSPORT_SALE', "Actualizó estado de venta ID $sale_id a $status");
            }
            header("Location: index.php?page=transporter_dashboard");
            exit;
        }

        $prod_stmt = $this->conn->query("SELECT id, name FROM products WHERE is_active = 1");
        $products = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);

        $ren_stmt = $this->conn->prepare("SELECT sr.*, p.name FROM stock_renewals sr JOIN products p ON sr.product_id = p.id WHERE sr.transporter_id = ? ORDER BY sr.id DESC LIMIT 10");
        $ren_stmt->execute([$_SESSION['user_id']]);
        $renewals = $ren_stmt->fetchAll(PDO::FETCH_ASSOC);

        $tr_stmt = $this->conn->prepare("
            SELECT t.*, u.username as admin_name 
            FROM transfers t 
            JOIN users u ON t.admin_id = u.id 
            WHERE t.status = 'pending' OR t.transporter_id = ?
            ORDER BY FIELD(t.status, 'pending', 'accepted', 'failed', 'delivered'), t.id DESC
        ");
        $tr_stmt->execute([$_SESSION['user_id']]);
        $transfers = $tr_stmt->fetchAll(PDO::FETCH_ASSOC);

        $vreq_stmt = $this->conn->prepare("SELECT vr.*, v.brand, v.model, v.license_plate, v.weight_capacity FROM vehicle_requests vr LEFT JOIN vehicles v ON vr.vehicle_id = v.id WHERE vr.transporter_id = ? AND vr.status IN ('pending', 'approved') ORDER BY vr.id DESC LIMIT 1");
        $vreq_stmt->execute([$_SESSION['user_id']]);
        $active_vehicle_request = $vreq_stmt->fetch(PDO::FETCH_ASSOC);

        $veh_stmt = $this->conn->query("SELECT * FROM vehicles WHERE status = 'available'");
        $available_vehicles = $veh_stmt->fetchAll(PDO::FETCH_ASSOC);

        $sales_stmt = $this->conn->prepare("SELECT s.*, u.username as seller_name FROM sales s JOIN users u ON s.user_id = u.id WHERE s.transporter_id = ? AND s.delivery_type = 'transport' ORDER BY s.id DESC");
        $sales_stmt->execute([$_SESSION['user_id']]);
        $assigned_sales = $sales_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/transporter/dashboard.php';
        require_once 'views/layout/footer.php';
    }
}
?>
