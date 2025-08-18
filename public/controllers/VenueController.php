<?php
require_once __DIR__ . '/../models/VenueModel.php';

class VenueController {
    private $db;
    private $smarty;
    private $venueModel;

    public function __construct($db, $smarty) {
        $this->db = Database::getInstance(); // Get singleton DB
        $this->smarty = $smarty;
        $this->venueModel = new VenueModel($db); // ✅ pass DB to model
    }

    public function apiList() {
        $venues = $this->venueModel->getAllVenues();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => array_map(function($venue) {
            return [
                'id' => $venue->id,
                'name' => $venue->name
            ];
        }, $venues)]);
    }
}