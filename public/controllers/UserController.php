<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private $db;
    private $smarty;

    public function __construct($db, $smarty) {
        $this->db = $db;
        $this->smarty = $smarty;
    }

    public function register() {
        $model = new UserModel($this->db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('Invalid CSRF token.', 'error');
                header('Location: ' . BASE_URL . '/register');
                exit;
            }

            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? ''
            ];

            if ($data['password'] !== $data['confirm_password']) {
                setFlashMessage('Passwords do not match.', 'error');
                header('Location: ' . BASE_URL . '/register');
                exit;
            }

            if ($model->register($data)) {
                setFlashMessage('Registration successful! Please log in.', 'success');
                header('Location: ' . BASE_URL . '/login');
                exit;
            } else {
                setFlashMessage('Username or email already exists.', 'error');
            }
        }

        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('register.tpl');
    }

    public function login() {
        $model = new UserModel($this->db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('Invalid CSRF token.', 'error');
                header('Location: ' . BASE_URL . '/login');
                exit;
            }

            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($model->login($username, $password)) {
                setFlashMessage('Login successful!', 'success');
                header('Location: ' . BASE_URL . '/home');
                exit;
            } else {
                setFlashMessage('Invalid username or password.', 'error');
            }
        }
            // $_SESSION['user_id'] = $user['id'];
            // $_SESSION['role'] = $user['role'];

        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('login.tpl');
    }

    public function logout() {
        session_destroy();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        setFlashMessage('Logged out successfully.', 'success');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}