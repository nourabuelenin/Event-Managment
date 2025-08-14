<?php
require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../vendor/agile-bm/ado-db/adodb-active-record.inc.php';


class Database {
    private static $instance = null;
    private $db;

    private function __construct() {
        $server = "localhost";
        $user = "root";
        $password = ""; 
        $database = "event_manager";
        error_log("Creating new Database instance");

        try {
            $this->db = NewADOConnection('mysqli');
            $this->db->Connect($server, $user, $password, $database);
            ADODB_Active_Record::SetDatabaseAdapter($this->db); // Set for Active Records
        } catch (Exception $e) {
            error_log("❌ Database Exception: " . $e->getMessage());
            die("Database connection failed.");
        }
    }

    // Get the single instance of the database connection
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Database();
        }
        return self::$instance->db;
    }
}