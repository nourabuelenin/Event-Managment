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

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        $db = Database::getInstance(); // Use singleton
        $userModel = new UserModel($db);
        $user = $userModel->getUserById($_SESSION['user_id']);
        if ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ];
        }
    }
    return false;
}

    // Parse search parameters
function parseSearchQuery($query) {
    $params = [];
    $conditions = [];
    $whereClause = '';
    $db = Database::getInstance(); // Use singleton
    
    parse_str($query, $searchArray);
    error_log("Parsed search parameters: " . print_r($searchArray, true));

    foreach ($searchArray as $key => $value) {
        if (!empty($value)) {
            $value = '%' . $db->escape($value) . '%'; // Use the database instance to escape
            $conditions[] = "$key LIKE ?";
            $params[] = $value;
        }
    }

    // Build WHERE clause
    if (!empty($conditions)) {
        $whereClause = "WHERE " . implode(' AND ', $conditions);
    } else {
        $whereClause = '';
    }
    
    return [
        'whereClause' => $whereClause,
        'params' => $params
    ];
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlashMessage() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}