<?php
// Debug autoloader
$autoloaderPath = 'D:\wamp64\www\test\vendor\autoload.php';
if (!file_exists($autoloaderPath)) {
    die("Autoloader not found at: " . $autoloaderPath);
}
require_once $autoloaderPath;

// Load ADOdb
// require_once 'vendor/adodb/adodb-php/adodb.inc.php';

// --- Setup DB Connection ---
$server = "localhost";
$user = "root";
$password = "";
$database = "test";

$db = NewADOConnection('mysqli'); 
$db->Connect($server, $user, $password, $database);

// --- Setup Smarty ---
$smarty = new Smarty\Smarty();
$smarty->setTemplateDir(__DIR__ . '/templates/');
$smarty->setCompileDir(__DIR__ . '/templates_c/');

// --- Prepare Data ---
$connection_status = $db->IsConnected() 
    ? "✅ Connected to MariaDB via ADOdb" 
    : "❌ Failed to connect";

$rows = [];
if ($db->IsConnected()) {
    $sql = "SELECT * FROM test_table";
    $result = $db->Execute($sql);
    if ($result) {
        while (!$result->EOF) {
            $rows[] = [
                'id' => $result->fields['id'],
                'message' => $result->fields['message']
            ];
            $result->MoveNext();
        }
    }
}

// --- Assign to Smarty ---
$smarty->assign('connection_status', $connection_status);
$smarty->assign('rows', $rows);

// --- Display Template ---
$smarty->display('test.tpl');