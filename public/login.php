<?php
require_once __DIR__ . '/../config/smarty_config.php';
require_once __DIR__ . '/../models/UserModel.php';

$model = new UserModel($db);
$flash = getFlashMessage();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        setFlashMessage('Invalid CSRF token.', 'error');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    $username =  trim($_POST['username'] ?? '');
    $password = $_POST['password'];

    if ($model->login($username, $password)) {
        setFlashMessage('Login successful!', 'success');
        header('Location: ' . BASE_URL . '/home.php');
        exit;
    } else {
        setFlashMessage('Invalid username or password.', 'error');
    }
}

// Assign Smarty variables
$smarty->assign('flash', $flash);
$smarty->assign('csrf_token', $csrf_token);
$smarty->assign('base_url', BASE_URL);
$smarty->assign('assets_url', ASSETS_URL);
$smarty->display('login.tpl');