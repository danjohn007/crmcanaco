<?php
/**
 * Authentication Controller
 * CRM Cámara de Comercio de Querétaro
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Usuario y contraseña son requeridos';
            } else {
                $user = $this->userModel->authenticate($username, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['full_name'] = $this->userModel->getFullName($user);
                    $_SESSION['login_time'] = time();

                    header('Location: /dashboard');
                    exit;
                } else {
                    $error = 'Usuario o contraseña incorrectos';
                }
            }
        }

        include __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['login_time']) && 
               (time() - $_SESSION['login_time']) < SESSION_TIMEOUT;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireRole($requiredRole) {
        $this->requireLogin();
        
        $userRole = $_SESSION['role'] ?? '';
        if (!$this->userModel->hasPermission($userRole, $requiredRole)) {
            header('HTTP/1.1 403 Forbidden');
            include __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->findById($_SESSION['user_id']);
    }

    public function updateSession() {
        if ($this->isLoggedIn()) {
            $_SESSION['login_time'] = time();
        }
    }
}
?>