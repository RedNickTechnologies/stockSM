<?php
require_once 'config/database.php';
require_once 'config/config.php';

class UserController {
    private $conn;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function dashboard() {
        $stmt = $this->conn->prepare("SELECT * FROM sales WHERE user_id = ? ORDER BY id DESC LIMIT 10");
        $stmt->execute([$_SESSION['user_id']]);
        $my_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch details for each sale
        foreach ($my_sales as &$sale) {
            $d_stmt = $this->conn->prepare("SELECT td.quantity, td.subtotal, p.name FROM sale_details td JOIN products p ON td.product_id = p.id WHERE td.sale_id = ?");
            $d_stmt->execute([$sale['id']]);
            $sale['details'] = $d_stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $u_stmt = $this->conn->prepare("SELECT created_at, monthly_goal FROM users WHERE id = ?");
        $u_stmt->execute([$_SESSION['user_id']]);
        $user_data = $u_stmt->fetch(PDO::FETCH_ASSOC);
        $monthly_goal = (float)$user_data['monthly_goal'];
        $created_at = $user_data['created_at'];

        $prog_stmt = $this->conn->prepare("SELECT SUM(total) as amount, COUNT(id) as count FROM sales WHERE user_id = ? AND status = 'approved' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $prog_stmt->execute([$_SESSION['user_id']]);
        $prog_data = $prog_stmt->fetch(PDO::FETCH_ASSOC);
        
        $current_sales = (float)$prog_data['amount'];
        $current_count = (int)$prog_data['count'];

        $goal_percentage = $monthly_goal > 0 ? min(100, ($current_sales / $monthly_goal) * 100) : 0;

        // Update password logic
        $password_msg = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            $new_pass = $_POST['new_password'];
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $_SESSION['user_id']]);
            $password_msg = 'Contraseña actualizada correctamente.';
            log_audit($this->conn, $_SESSION['user_id'], 'UPDATE_PASSWORD', "El usuario cambió su contraseña");
        }

        require_once 'views/layout/header.php';
        require_once 'views/user/dashboard.php';
        require_once 'views/layout/footer.php';
    }

    public function products() {
        $stmt = $this->conn->query("SELECT * FROM products WHERE is_active = 1 ORDER BY name ASC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/user/products.php';
        require_once 'views/layout/footer.php';
    }

    public function createSale() {
        if ($_SESSION['role'] === 'transporter') {
            die("Acceso denegado. Los transportistas no pueden realizar ventas.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid");
            }
            $products_req = $_POST['products'] ?? [];
            $quantities_req = $_POST['quantities'] ?? [];
            
            if (empty($products_req)) {
                $error = "Debe seleccionar al menos un producto.";
            } else {
                try {
                    $this->conn->beginTransaction();
                    
                    $stmt = $this->conn->prepare("INSERT INTO sales (user_id, total, status) VALUES (?, 0, 'pending')");
                    $stmt->execute([$_SESSION['user_id']]);
                    $sale_id = $this->conn->lastInsertId();
                    
                    $total = 0;
                    foreach ($products_req as $index => $prod_id) {
                        $qty = (int)$quantities_req[$index];
                        if ($qty > 0) {
                            $prod_stmt = $this->conn->prepare("SELECT price FROM products WHERE id = ? AND is_active = 1");
                            $prod_stmt->execute([$prod_id]);
                            $prod = $prod_stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($prod) {
                                $subtotal = $prod['price'] * $qty;
                                $total += $subtotal;
                                
                                $det_stmt = $this->conn->prepare("INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                                $det_stmt->execute([$sale_id, $prod_id, $qty, $prod['price'], $subtotal]);
                            }
                        }
                    }
                    
                    $upd = $this->conn->prepare("UPDATE sales SET total = ? WHERE id = ?");
                    $upd->execute([$total, $sale_id]);
                    
                    log_audit($this->conn, $_SESSION['user_id'], 'CREATE_SALE', "Generó factura #$sale_id por $$total");
                    
                    $this->conn->commit();
                    header("Location: index.php?page=user_dashboard&success=1");
                    exit;
                } catch (Exception $e) {
                    $this->conn->rollBack();
                    $error = "Error al procesar la venta: " . $e->getMessage();
                }
            }
        }

        $stmt = $this->conn->query("SELECT id, name, price, stock FROM products WHERE is_active = 1 AND stock > 0");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/user/create_sale.php';
        require_once 'views/layout/footer.php';
    }

    public function tickets() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'create_ticket') {
                $subject = $_POST['subject'];
                $message = $_POST['message'];
                
                $stmt = $this->conn->prepare("INSERT INTO tickets (user_id, subject, message) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $subject, $message]);
            }
            header("Location: index.php?page=user_tickets");
            exit;
        }

        $stmt = $this->conn->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/user/tickets.php';
        require_once 'views/layout/footer.php';
    }
}
?>
