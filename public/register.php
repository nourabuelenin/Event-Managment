<?php
require_once __DIR__ . '/../config/smarty_config.php';
require_once __DIR__ . '/../models/UserModel.php';

$model = new UserModel($db);
$flash = getFlashMessage();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        setFlashMessage('Invalid CSRF token.', 'error');
        header('Location: ' . BASE_URL . '/register.php');
        exit;
    }

    $data = [
        'username' => trim($_POST['username'] ?? ''),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];

    // Validate input
    if ($data['password'] !== $data['confirm_password']) {
        setFlashMessage('Passwords do not match.', 'error');
        header('Location: ' . BASE_URL . '/register.php');
        exit;
    }

    // Debug: Log ADOdb errors and connection status
    $db->Execute("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
    if ($model->register($data)) {
        var_dump($data);
        setFlashMessage('Registration successful! Please log in.', 'success');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    } else {
        error_log("Registration failed: " . $db->ErrorMsg() . " | Data: " . json_encode($data));
        setFlashMessage('Username or email already exists, or database error occurred: ' . $db->ErrorMsg(), 'error');
    }
}

// Assign Smarty variables
$smarty->assign('flash', $flash);
$smarty->assign('csrf_token', $csrf_token);
$smarty->assign('base_url', BASE_URL);
$smarty->assign('assets_url', ASSETS_URL);
$smarty->display('register.tpl');