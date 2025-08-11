<?php
// Debug autoloader
$autoloaderPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloaderPath)) {
    die("Autoloader not found at: " . $autoloaderPath);
}
require_once $autoloaderPath;

// 1. Connect
$db = NewADOConnection('mysqli');
$db->Connect('localhost', 'root', 'na123');
echo "✅ Connected to MariaDB<br>";

// Create database if not exists
$db->Execute("CREATE DATABASE IF NOT EXISTS test_db");
$db->SelectDB('test_db');

// 2. CREATE test_table
$db->Execute("CREATE TABLE IF NOT EXISTS test_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255)
)");

// 3. INSERT (Create)
$db->Execute("INSERT INTO test_table (message) VALUES ('Hello from MariaDB!')");
echo "✅ Insert done<br>";

// 4. SELECT (Read)
$rs = $db->Execute("SELECT * FROM test_table");
echo "✅ Select results:<br>";
while (!$rs->EOF) {
    echo $rs->fields['id'] . " - " . $rs->fields['message'] . "<br>";
    $rs->MoveNext();
}

// 5. UPDATE
$db->Execute("UPDATE test_table SET message='Updated via ADOdb' WHERE id=1");
echo "✅ Update done<br>";

// 6. DELETE
$db->Execute("DELETE FROM test_table WHERE id=1");
echo "✅ Delete done<br>";

// Close connection
$db->Close();
?>
