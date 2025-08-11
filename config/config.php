<?php
// Debug autoloader
$autoloaderPath = __DIR__ . '/vendor/autoload.php';
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
    // Optionally, rethrow or handle as needed
}

$db->SetFetchMode(ADODB_FETCH_ASSOC);

// Smarty
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../templates/');
$smarty->setCompileDir(__DIR__ . '/../templates_c/');

