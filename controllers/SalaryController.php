<?php
require_once 'config/database.php';
require_once 'config/config.php';

class SalaryController {
    private $conn;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function adminIndex() {
        if ($_SESSION['role'] !== 'admin') die("No autorizado");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            if ($_POST['action'] === 'generate_salary') {
                $user_id = (int)$_POST['user_id'];
                $month = (int)$_POST['period_month'];
                $year = (int)$_POST['period_year'];
                $base = (float)$_POST['base_salary'];
                $deductions = (float)$_POST['deductions'];
                $net = $base - $deductions;
                
                $stmt = $this->conn->prepare("INSERT INTO salary_liquidations (user_id, period_month, period_year, base_salary, deductions, net_salary) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $month, $year, $base, $deductions, $net]);
                
                log_audit($this->conn, $_SESSION['user_id'], 'GENERATE_SALARY', "Generó recibo de sueldo ($month/$year) para empleado ID $user_id");
            }
            header("Location: index.php?page=admin_salaries");
            exit;
        }

        // Get employees
        $emp_stmt = $this->conn->query("SELECT id, username, role FROM users WHERE role != 'admin' AND is_active = 1 ORDER BY username ASC");
        $employees = $emp_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get recent salaries
        $sal_stmt = $this->conn->query("SELECT s.*, u.username, u.role FROM salary_liquidations s JOIN users u ON s.user_id = u.id ORDER BY s.id DESC LIMIT 50");
        $salaries = $sal_stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/salaries.php';
        require_once 'views/layout/footer.php';
    }

    public function userIndex() {
        // Para todos los usuarios no-admin, o admin también si quiere ver los suyos
        $stmt = $this->conn->prepare("SELECT * FROM salary_liquidations WHERE user_id = ? ORDER BY period_year DESC, period_month DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/shared/my_salaries.php';
        require_once 'views/layout/footer.php';
    }

    public function viewPdf() {
        if (!isset($_GET['id'])) die("ID no proporcionado");
        $id = (int)$_GET['id'];
        
        $stmt = $this->conn->prepare("SELECT s.*, u.username, u.role FROM salary_liquidations s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
        $stmt->execute([$id]);
        $salary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$salary) die("Liquidación no encontrada");
        
        if ($_SESSION['role'] !== 'admin' && $salary['user_id'] !== $_SESSION['user_id']) {
            die("No tienes permiso para ver este recibo.");
        }
        
        // Cargar configuración de la empresa
        $s_stmt = $this->conn->query("SELECT * FROM settings");
        $settings = $s_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        require_once 'views/shared/salary_pdf.php';
    }
}
?>
