<?php
// Load ADOdb
require_once 'vendor/adodb/adodb-php/adodb.inc.php';

// Connection settings
$server = "localhost";
$user = "root";
$password = ""; 
$database = "test";

try {
    // Create connection
    $db = NewADOConnection('mysqli'); 
    $db->Connect($server, $user, $password, $database);

    if ($db->IsConnected()) {
        echo "<p>✅ Connection to MariaDB via ADOdb is successful!</p>";

        // Test query
        $sql = "SELECT * FROM test_table";
        $result = $db->Execute($sql);

        if ($result === false) {
            echo "<p>❌ Query failed: " . $db->ErrorMsg() . "</p>";
        } else {
            echo "<h3>Results:</h3><ul>";
            while (!$result->EOF) {
                echo "<li>ID: " . $result->fields['id'] . 
                     " - Message: " . $result->fields['message'] . "</li>";
                $result->MoveNext();
            }
            echo "</ul>";
        }
    } else {
        echo "<p>❌ Failed to connect to MariaDB.</p>";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage();
}
?>
