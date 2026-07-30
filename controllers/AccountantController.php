<?php
class AccountantController {
    private $conn;

    public function __construct() {
        require_once 'config.php';
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'accountant') {
            header("Location: index.php?page=login");
            exit;
        }
    }

    public function dashboard() {
        // Métricas básicas para el contador
        $m_stmt = $this->conn->query("SELECT COUNT(*) FROM sales WHERE status = 'pending'");
        $pending_sales = $m_stmt->fetchColumn();

        $dd_stmt = $this->conn->query("SELECT COUNT(*) FROM declaraciones_juradas WHERE status = 'pending_admin'");
        $pending_ddjj = $dd_stmt->fetchColumn();

        require_once 'views/layout/header.php';
        require_once 'views/accountant/dashboard.php';
        require_once 'views/layout/footer.php';
    }

    public function sales() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'approve' || $_POST['action'] === 'reject') {
                $sale_id = (int)$_POST['sale_id'];
                $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
                
                $this->conn->beginTransaction();
                try {
                    $stmt = $this->conn->prepare("UPDATE sales SET status = ? WHERE id = ? AND status = 'pending'");
                    $stmt->execute([$status, $sale_id]);
                    
                    if ($stmt->rowCount() > 0) {
                        if ($status === 'approved') {
                            $d_stmt = $this->conn->prepare("SELECT product_id, quantity FROM sale_details WHERE sale_id = ?");
                            $d_stmt->execute([$sale_id]);
                            $details = $d_stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            $u_stmt = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                            foreach ($details as $d) {
                                $u_stmt->execute([$d['quantity'], $d['product_id']]);
                            }
                            log_audit($this->conn, $_SESSION['user_id'], 'APPROVE_SALE', "Contador aprobó venta #$sale_id.");
                        } else {
                            log_audit($this->conn, $_SESSION['user_id'], 'REJECT_SALE', "Contador rechazó venta #$sale_id.");
                        }
                    }
                    $this->conn->commit();
                } catch(Exception $e) {
                    $this->conn->rollBack();
                    die($e->getMessage());
                }
            } elseif ($_POST['action'] === 'emit_arca') {
                $sale_id = (int)$_POST['sale_id'];
                $cuit = $_POST['cuit_cliente'];
                $tipo = $_POST['tipo_factura'];
                
                // MOCK DE CONEXIÓN CON ARCA
                sleep(2); // Simula el tiempo de conexión
                
                // Generar CAE aleatorio (14 dígitos)
                $cae = rand(1000000, 9999999) . rand(1000000, 9999999);
                // Vencimiento en 10 días
                $vto_cae = date('Y-m-d', strtotime('+10 days'));

                $stmt = $this->conn->prepare("INSERT INTO arca_invoices (sale_id, cuit_cliente, tipo_factura, cae, vto_cae) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$sale_id, $cuit, $tipo, $cae, $vto_cae]);
                
                log_audit($this->conn, $_SESSION['user_id'], 'EMIT_ARCA', "Facturó en ARCA (Mock) Venta #$sale_id - CAE $cae");
            }
            header("Location: index.php?page=accountant_sales");
            exit;
        }

        $stmt = $this->conn->query("
            SELECT s.*, u.username, a.id as arca_id, a.cae
            FROM sales s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN arca_invoices a ON s.id = a.sale_id
            ORDER BY FIELD(s.status, 'pending', 'approved', 'rejected'), s.id DESC
        ");
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/accountant/sales.php';
        require_once 'views/layout/footer.php';
    }

    public function ddjj() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'generate') {
                $month = (int)$_POST['month'];
                $year = (int)$_POST['year'];
                
                // Calcular total facturado (ventas aprobadas del mes)
                $calc_stmt = $this->conn->prepare("SELECT SUM(total) FROM sales WHERE status = 'approved' AND MONTH(created_at) = ? AND YEAR(created_at) = ?");
                $calc_stmt->execute([$month, $year]);
                $total_sales = $calc_stmt->fetchColumn() ?: 0;
                
                // Calcular impuestos (Ej: 21% de las ventas)
                $total_taxes = $total_sales * 0.21;
                
                $stmt = $this->conn->prepare("INSERT INTO declaraciones_juradas (accountant_id, month, year, total_sales, total_taxes) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $month, $year, $total_sales, $total_taxes]);
                log_audit($this->conn, $_SESSION['user_id'], 'GENERATE_DDJJ', "Generó DDJJ de $month/$year");
            }
            header("Location: index.php?page=accountant_ddjj");
            exit;
        }

        $stmt = $this->conn->query("SELECT * FROM declaraciones_juradas ORDER BY year DESC, month DESC");
        $ddjjs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/accountant/ddjj.php';
        require_once 'views/layout/footer.php';
    }
}
?>
