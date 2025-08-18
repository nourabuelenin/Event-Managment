<?php
require_once __DIR__ . '/../models/EventModel.php';

class HomeController {
    private $db;
    private $smarty;

    public function __construct($db, $smarty) {
        $this->db = Database::getInstance(); // Get singleton DB
        // $this->db = $db;
        $this->smarty = $smarty;
    }

    public function index() {
        $eventModel = new EventModel($this->db);
        $events = $eventModel->getRecentEvents(5);
        error_log("Events fetched: " . print_r($events, true));
        $this->smarty->assign('events', $events);
        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('home.tpl');
    }
}