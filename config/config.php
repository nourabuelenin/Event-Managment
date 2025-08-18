<?php
// Vendor autoloader
require_once __DIR__ . '/../vendor/autoload.php';
/// Database as singleton
require_once __DIR__ . '/Database.php';

session_start(); // Required for authentication

// Get database connection
// $db = Database::getInstance();

// Error handling
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Constants
define('BASE_URL', 'http://localhost/test/public/router');
define('ASSETS_URL', 'http://localhost/test/public/assets');
define('VIEWS_URL', 'http://localhost/test/public/views');

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include helpers
require_once __DIR__ . '/../public/helpers/functions.php';