<?php
require_once 'config/database.php';
require_once 'config/config.php';

class AuthController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['role'] === 'admin') {
                header("Location: index.php?page=admin_dashboard");
            } elseif ($_SESSION['role'] === 'transporter') {
                header("Location: index.php?page=transporter_dashboard");
            } elseif ($_SESSION['role'] === 'accountant') {
                header("Location: index.php?page=accountant_dashboard");
            } else {
                header("Location: index.php?page=user_dashboard");
            }
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $error = "Token de seguridad inválido.";
            } else {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($username) || empty($password)) {
                    $error = "Por favor, complete todos los campos.";
                } else {
                    $stmt = $this->conn->prepare("SELECT id, username, password_hash, role, is_active FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && password_verify($password, $user['password_hash'])) {
                        if ($user['is_active']) {
                            session_regenerate_id(true); 
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['role'] = $user['role'];

                            log_audit($this->conn, $user['id'], 'LOGIN', "El usuario inició sesión exitosamente.");

                            if ($user['role'] === 'admin') {
                                header("Location: index.php?page=admin_dashboard");
                            } elseif ($user['role'] === 'transporter') {
                                header("Location: index.php?page=transporter_dashboard");
                            } elseif ($user['role'] === 'accountant') {
                                header("Location: index.php?page=accountant_dashboard");
                            } else {
                                header("Location: index.php?page=user_dashboard");
                            }
                            exit;
                        } else {
                            $error = "Esta cuenta ha sido inhabilitada. Contacte al administrador.";
                            log_audit($this->conn, $user['id'], 'LOGIN_FAILED', "Intento de login a cuenta inhabilitada.");
                        }
                    } else {
                        $error = "Usuario o contraseña incorrectos.";
                        log_audit($this->conn, null, 'LOGIN_FAILED', "Intento fallido para usuario: $username");
                    }
                }
            }
        }

        require_once 'views/layout/header.php';
        require_once 'views/auth/login.php';
        require_once 'views/layout/footer.php';
    }

    public function logout() {
        if (isset($_SESSION['user_id'])) {
            log_audit($this->conn, $_SESSION['user_id'], 'LOGOUT', "El usuario cerró sesión.");
        }
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }
}
?>
