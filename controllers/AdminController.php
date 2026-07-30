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
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $role = $_POST['role'];
                $stmt = $this->conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $password, $role]);
                log_audit($this->conn, $_SESSION['user_id'], 'CREATE_USER', "Creó al usuario: $username");
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

        $stmt = $this->conn->query("SELECT id, username, role, is_active, created_at FROM users");
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
                $stmt = $this->conn->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
                $stmt->execute([$name, $price, $stock]);
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
}
?>
