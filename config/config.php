<?php
session_start(); // Required for authentication

// Vendor autoloader
$autoloaderPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloaderPath)) {
    die("Autoloader not found at: " . $autoloaderPath);
}
require_once $autoloaderPath;

// Connection settings
$server = "localhost";
$user = "root";
$password = ""; 
$database = "event_manager";

try {
    $db = NewADOConnection('mysqli'); 
    $db->Connect($server, $user, $password, $database);

} catch (Exception $e) {
    error_log("❌ Exception: " . $e->getMessage());
}

// Error handling
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Constants
define('BASE_URL', 'http://localhost/test/public');
define('ASSETS_URL', BASE_URL . '/assets');

// Generate CSRF token only if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include helpers
require_once __DIR__ . '/../helpers/functions.php';