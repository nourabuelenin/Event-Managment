<?php
require_once __DIR__ . '/../models/UserModel.php';

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        setFlashMessage('Please log in to access this page.', 'error');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

function requireRole($roles) {
    $user = getCurrentUser();
    if (!$user || !in_array($user['role'], (array)$roles)) {
        setFlashMessage('Access denied.', 'error');
        header('Location: ' . BASE_URL . '/home');
        exit;
    }
}

function isAttendee() {
    $user = getCurrentUser();
    return $user && $user['role'] === 'attendee';
}

function getCurrentUser($db = null) {
    if (!isset($_SESSION['user_id'])) return null;
    if ($db) {
        $model = new UserModel($db);
        return $model->getUserById($_SESSION['user_id']);
    }
    return ['id' => $_SESSION['user_id'], 'role' => $_SESSION['role']];
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlashMessage() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}