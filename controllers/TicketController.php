<?php
require_once 'config/database.php';
require_once 'config/config.php';

class TicketController {
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            $ticket_id = $_POST['ticket_id'];
            $reply = $_POST['reply'];
            
            $stmt = $this->conn->prepare("UPDATE tickets SET admin_reply = ?, status = 'answered' WHERE id = ?");
            $stmt->execute([$reply, $ticket_id]);
            log_audit($this->conn, $_SESSION['user_id'], 'REPLY_TICKET', "Respondió ticket #$ticket_id");
            header("Location: index.php?page=admin_tickets");
            exit;
        }

        $stmt = $this->conn->query("SELECT t.*, u.username, u.email, u.role FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.status ASC, t.id DESC");
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/layout/header.php';
        require_once 'views/admin/tickets.php';
        require_once 'views/layout/footer.php';
    }

    public function userIndex() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Token Invalid");
            
            $subject = $_POST['subject'];
            $message = $_POST['message'];
            
            $stmt = $this->conn->prepare("INSERT INTO tickets (user_id, subject, message) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $subject, $message]);
            
            $return_page = $_SESSION['role'] === 'transporter' ? 'transporter_tickets' : 'user_tickets';
            header("Location: index.php?page=$return_page&success=1");
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
