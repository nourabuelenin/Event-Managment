<?php
require_once __DIR__ . '/../config/config.php';

session_destroy();
setFlashMessage('Logged out successfully.', 'success');
header('Location: ' . BASE_URL . '/login.php');
exit;