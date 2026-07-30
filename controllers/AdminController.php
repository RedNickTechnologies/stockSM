<?php
require_once 'config/database.php';
require_once 'config/config.php';

class AdminController {
    private $conn;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?page=login");
            exit;
        }
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function dashboard() {
        $stats = [];
        $stats['users'] = $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['products'] = $this->conn->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
        $stats['pending_sales'] = $this->conn->query("SELECT COUNT(*) FROM sales WHERE status='pending'")->fetchColumn();

        // Chart 1: Sales over the last 30 days (by day)
        $chart_sales = $this->conn->query("
            SELECT DATE(created_at) as date, SUM(total) as total
            FROM sales
            WHERE status = 'approved' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Chart 2: Top Sellers (Current Month)
        $chart_sellers = $this->conn->query("
            SELECT u.username, SUM(s.total) as total, u.monthly_goal
            FROM sales s
            JOIN users u ON s.user_id = u.id
            WHERE s.status = 'approved' AND MONTH(s.created_at) = MONTH(CURDATE()) AND YEAR(s.created_at) = YEAR(CURDATE())
            GROUP BY s.user_id
            ORDER BY total DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/dashboard.php';
        require_once 'views/layout/footer.php';
    }

    public function users() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid");
            }
            if ($_POST['action'] === 'create') {
                $username = $_POST['username'];
                $email = $_POST['email'] ?? null;
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $role = $_POST['role'];
                $monthly_goal = $_POST['monthly_goal'] ?? 0;
                $stmt = $this->conn->prepare("INSERT INTO users (username, email, password_hash, role, monthly_goal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password, $role, $monthly_goal]);
                log_audit($this->conn, $_SESSION['user_id'], 'CREATE_USER', "Creó al usuario: $username con meta $$monthly_goal");
            } elseif ($_POST['action'] === 'edit_goal') {
                $id = $_POST['user_id'];
                $goal = $_POST['monthly_goal'];
                $stmt = $this->conn->prepare("UPDATE users SET monthly_goal = ? WHERE id = ?");
                $stmt->execute([$goal, $id]);
                log_audit($this->conn, $_SESSION['user_id'], 'EDIT_USER_GOAL', "Editó meta del usuario ID: $id a $$goal");
            } elseif ($_POST['action'] === 'toggle_status') {
                $id = $_POST['user_id'];
                $status = $_POST['status'] == '1' ? 0 : 1;
                $stmt = $this->conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
                $stmt->execute([$status, $id]);
                log_audit($this->conn, $_SESSION['user_id'], 'TOGGLE_USER', "Cambió estado de usuario ID: $id a " . ($status ? 'Activo' : 'Inactivo'));
            }
            header("Location: index.php?page=admin_users");
            exit;
        }

        $stmt = $this->conn->query("SELECT id, username, email, role, monthly_goal, is_active, created_at FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/users.php';
        require_once 'views/layout/footer.php';
    }

    public function products() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid");
            }
            if ($_POST['action'] === 'create') {
                $name = $_POST['name'];
                $price = $_POST['price'];
                $stock = $_POST['stock'];
                $image_url = null;
                
                if (!empty($_FILES['image_file']['name'])) {
                    $target_dir = "public/uploads/";
                    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                    $file_extension = pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION);
                    $file_name = uniqid() . '.' . $file_extension;
                    $target_file = $target_dir . $file_name;
                    if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                        $image_url = $target_file;
                    }
                } elseif (!empty($_POST['image_link'])) {
                    $image_url = $_POST['image_link'];
                }

                $stmt = $this->conn->prepare("INSERT INTO products (name, price, stock, image_url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $stock, $image_url]);
                log_audit($this->conn, $_SESSION['user_id'], 'CREATE_PRODUCT', "Creó producto: $name");
            } elseif ($_POST['action'] === 'toggle_status') {
                $id = $_POST['product_id'];
                $status = $_POST['status'] == '1' ? 0 : 1;
                $stmt = $this->conn->prepare("UPDATE products SET is_active = ? WHERE id = ?");
                $stmt->execute([$status, $id]);
                log_audit($this->conn, $_SESSION['user_id'], 'TOGGLE_PRODUCT', "Cambió estado de producto ID: $id a " . ($status ? 'Activo' : 'Inactivo'));
            }
            header("Location: index.php?page=admin_products");
            exit;
        }

        $stmt = $this->conn->query("SELECT * FROM products ORDER BY id DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/products.php';
        require_once 'views/layout/footer.php';
    }

    public function sales() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
             if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid");
            }
            $sale_id = (int)$_POST['sale_id'];
            if ($_POST['action'] === 'approve') {
                try {
                    $this->conn->beginTransaction();
                    $stmt = $this->conn->prepare("UPDATE sales SET status = 'approved' WHERE id = ? AND status = 'pending'");
                    $stmt->execute([$sale_id]);
                    if ($stmt->rowCount() > 0) {
                        $details_stmt = $this->conn->prepare("SELECT product_id, quantity FROM sale_details WHERE sale_id = ?");
                        $details_stmt->execute([$sale_id]);
                        $details = $details_stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach($details as $item) {
                            $upd = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                            $upd->execute([$item['quantity'], $item['product_id']]);
                        }
                        log_audit($this->conn, $_SESSION['user_id'], 'APPROVE_SALE', "Aprobó venta ID: $sale_id");
                    }
                    $this->conn->commit();
                } catch(Exception $e) {
                    $this->conn->rollBack();
                }
            } elseif ($_POST['action'] === 'reject') {
                $stmt = $this->conn->prepare("UPDATE sales SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$sale_id]);
                log_audit($this->conn, $_SESSION['user_id'], 'REJECT_SALE', "Rechazó venta ID: $sale_id");
            }
            header("Location: index.php?page=admin_sales");
            exit;
        }

        $stmt = $this->conn->query("SELECT s.*, u.username FROM sales s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC");
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/sales.php';
        require_once 'views/layout/footer.php';
    }

    public function auditLogs() {
        $stmt = $this->conn->query("SELECT a.*, u.username FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.id DESC LIMIT 100");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/layout/header.php';
        require_once 'views/admin/audit.php';
        require_once 'views/layout/footer.php';
    }

    public function logistics() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'resolve_renewal') {
                $renewal_id = $_POST['renewal_id'];
                $status = $_POST['status']; // 'approved' or 'rejected'
                
                $stmt = $this->conn->prepare("UPDATE stock_renewals SET status = ? WHERE id = ? AND status = 'pending'");
                $stmt->execute([$status, $renewal_id]);
                
                if ($status === 'approved') {
                    $r_stmt = $this->conn->prepare("SELECT product_id, quantity FROM stock_renewals WHERE id = ?");
                    $r_stmt->execute([$renewal_id]);
                    $renewal = $r_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $u_stmt = $this->conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                    $u_stmt->execute([$renewal['quantity'], $renewal['product_id']]);
                    log_audit($this->conn, $_SESSION['user_id'], 'APPROVE_RENEWAL', "Aprobó ingreso de stock (Req #$renewal_id).");
                } else {
                    log_audit($this->conn, $_SESSION['user_id'], 'REJECT_RENEWAL', "Rechazó ingreso de stock (Req #$renewal_id).");
                }
            } elseif ($_POST['action'] === 'create_transfer') {
                $branch_address = $_POST['branch_address'];
                $product_ids = $_POST['product_id'];
                $quantities = $_POST['quantity'];
                
                $this->conn->beginTransaction();
                try {
                    $t_stmt = $this->conn->prepare("INSERT INTO transfers (admin_id, branch_address) VALUES (?, ?)");
                    $t_stmt->execute([$_SESSION['user_id'], $branch_address]);
                    $transfer_id = $this->conn->lastInsertId();
                    
                    for ($i=0; $i < count($product_ids); $i++) {
                        $pid = $product_ids[$i];
                        $qty = $quantities[$i];
                        if ($pid && $qty > 0) {
                            $d_stmt = $this->conn->prepare("INSERT INTO transfer_details (transfer_id, product_id, quantity) VALUES (?, ?, ?)");
                            $d_stmt->execute([$transfer_id, $pid, $qty]);
                            
                            $u_stmt = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                            $u_stmt->execute([$qty, $pid, $qty]);
                            if ($u_stmt->rowCount() == 0) {
                                throw new Exception("Stock insuficiente para el producto ID $pid");
                            }
                        }
                    }
                    $this->conn->commit();
                    log_audit($this->conn, $_SESSION['user_id'], 'CREATE_TRANSFER', "Creó envío #$transfer_id a $branch_address");
                } catch(Exception $e) {
                    $this->conn->rollBack();
                    die("Error en la transferencia: " . $e->getMessage());
                }
            }
            header("Location: index.php?page=admin_logistics");
            exit;
        }

        $prod_stmt = $this->conn->query("SELECT id, name, stock FROM products WHERE is_active = 1 AND stock > 0");
        $products = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);

        $ren_stmt = $this->conn->query("SELECT sr.*, p.name, u.username as transporter_name FROM stock_renewals sr JOIN products p ON sr.product_id = p.id JOIN users u ON sr.transporter_id = u.id ORDER BY FIELD(sr.status, 'pending', 'approved', 'rejected'), sr.id DESC");
        $renewals = $ren_stmt->fetchAll(PDO::FETCH_ASSOC);

        $tr_stmt = $this->conn->query("SELECT t.*, u.username as transporter_name FROM transfers t LEFT JOIN users u ON t.transporter_id = u.id ORDER BY t.id DESC");
        $transfers = $tr_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/logistics.php';
        require_once 'views/layout/footer.php';
    }

    public function fleet() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'create_vehicle') {
                $plate = $_POST['license_plate'];
                $brand = $_POST['brand'];
                $model = $_POST['model'];
                $type = $_POST['type'];
                $weight = $_POST['weight_capacity'];
                $volume = $_POST['volume_capacity'];
                $stmt = $this->conn->prepare("INSERT INTO vehicles (license_plate, brand, model, type, weight_capacity, volume_capacity) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$plate, $brand, $model, $type, $weight, $volume]);
                log_audit($this->conn, $_SESSION['user_id'], 'CREATE_VEHICLE', "Agregó vehículo $plate");
            } elseif ($_POST['action'] === 'toggle_vehicle') {
                $id = $_POST['vehicle_id'];
                $status = $_POST['status'] === 'maintenance' ? 'available' : 'maintenance';
                $stmt = $this->conn->prepare("UPDATE vehicles SET status = ? WHERE id = ?");
                $stmt->execute([$status, $id]);
            } elseif ($_POST['action'] === 'resolve_request') {
                $req_id = $_POST['request_id'];
                $status = $_POST['status']; // approved or rejected
                
                $r_stmt = $this->conn->prepare("SELECT * FROM vehicle_requests WHERE id = ?");
                $r_stmt->execute([$req_id]);
                $req = $r_stmt->fetch(PDO::FETCH_ASSOC);

                if ($req) {
                    $this->conn->prepare("UPDATE vehicle_requests SET status = ? WHERE id = ?")->execute([$status, $req_id]);
                    if ($req['vehicle_id']) {
                        $v_status = $status === 'approved' ? 'in_use' : 'available';
                        $this->conn->prepare("UPDATE vehicles SET status = ? WHERE id = ?")->execute([$v_status, $req['vehicle_id']]);
                    }
                    log_audit($this->conn, $_SESSION['user_id'], 'VEHICLE_REQ', "Resolvió solicitud #$req_id como $status");
                }
            }
            header("Location: index.php?page=admin_fleet");
            exit;
        }

        $v_stmt = $this->conn->query("SELECT * FROM vehicles ORDER BY id DESC");
        $vehicles = $v_stmt->fetchAll(PDO::FETCH_ASSOC);

        $r_stmt = $this->conn->query("SELECT vr.*, u.username as transporter_name, v.license_plate as comp_plate, v.type as comp_type FROM vehicle_requests vr JOIN users u ON vr.transporter_id = u.id LEFT JOIN vehicles v ON vr.vehicle_id = v.id ORDER BY FIELD(vr.status, 'pending', 'approved', 'rejected', 'completed'), vr.id DESC");
        $requests = $r_stmt->fetchAll(PDO::FETCH_ASSOC);

        $exp_stmt = $this->conn->query("SELECT SUM(estimated_cost) FROM vehicle_requests WHERE status IN ('approved', 'completed') AND is_own_vehicle = 1");
        $total_expenses = $exp_stmt->fetchColumn() ?: 0;

        require_once 'views/layout/header.php';
        require_once 'views/admin/fleet.php';
        require_once 'views/layout/footer.php';
    }

    public function ddjj() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'resolve_ddjj') {
                $ddjj_id = $_POST['ddjj_id'];
                $status = $_POST['status'];
                $stmt = $this->conn->prepare("UPDATE declaraciones_juradas SET status = ?, admin_id = ? WHERE id = ?");
                $stmt->execute([$status, $_SESSION['user_id'], $ddjj_id]);
                log_audit($this->conn, $_SESSION['user_id'], 'RESOLVE_DDJJ', "Admin resolvió DDJJ #$ddjj_id como $status");
            }
            header("Location: index.php?page=admin_ddjj");
            exit;
        }

        $stmt = $this->conn->query("SELECT d.*, u.username as accountant_name FROM declaraciones_juradas d JOIN users u ON d.accountant_id = u.id ORDER BY FIELD(d.status, 'pending_admin', 'approved', 'sent_to_arca', 'rejected'), d.year DESC, d.month DESC");
        $ddjjs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/ddjj.php';
        require_once 'views/layout/footer.php';
    }
}
?>
