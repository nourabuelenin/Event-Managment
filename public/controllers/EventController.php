<?php
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/VenueModel.php';

class EventController {
    private $db;
    private $smarty;
    private $eventModel;

    public function __construct($db, $smarty) {
        // $this->db = $db; // ✅ from index.php
        $this->db = Database::getInstance(); // Get singleton DB
        $this->smarty = $smarty;
        $this->eventModel = new EventModel($db); // ✅ pass DB to model
    }

    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function index() {
        requireLogin();
        $events = $this->eventModel->getAllEvents();

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode($events);
            return;
        }

        $this->smarty->assign('events', $events);
        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('events_list.tpl');
    }

    public function view() {
        requireLogin();
        $id = $_GET['id'] ?? 0;
        $eventModel = new EventModel($this->db);
        $event = $eventModel->getEventById($id);
        if (!$event) {
            setFlashMessage('Event not found.', 'error');
            header('Location: ' . BASE_URL . '/events');
            exit;
        }
        $this->smarty->assign('event', $event);
        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('event_view.tpl');
    }

    public function create() {
        requireLogin();
        requireRole(['organizer', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('Invalid CSRF token.', 'error');
                header('Location: ' . BASE_URL . '/events/create');
                exit;
            }
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_time' => $_POST['start_time'] ?? '',
                'end_time' => $_POST['end_time'] ?? '',
                'venue_id' => $_POST['venue_id'] ?: null,
                'organizer_id' => $_SESSION['user_id']
            ];

            $eventModel = new EventModel($this->db);
            if ($eventModel->createEvent($data)) {
                setFlashMessage('Event created successfully.', 'success');
                header('Location: ' . BASE_URL . '/events');
                exit;
            } else {
                setFlashMessage('Failed to create event.', 'error');
            }
        }

        // Fallback full page form (uses base.tpl)
        $venueModel = new VenueModel($this->db);
        $venues = $venueModel->getAllVenues();
        $this->smarty->assign('venues', $venues);
        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('events_form.tpl');
    }

    public function update() {
        requireLogin();
        requireRole(['organizer', 'admin']);
        $id = $_GET['id'] ?? 0;
        $eventModel = new EventModel($this->db);
        $event = $eventModel->getEventById($id);
        if (!$event) {
            setFlashMessage('Event not found.', 'error');
            header('Location: ' . BASE_URL . '/events');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('Invalid CSRF token.', 'error');
                header('Location: ' . BASE_URL . '/events/update/' . $id);
                exit;
            }

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_time' => $_POST['start_time'] ?? '',
                'end_time' => $_POST['end_time'] ?? '',
                'venue_id' => $_POST['venue_id'] ?: null,
                'organizer_id' => $_SESSION['user_id']
            ];

            if ($eventModel->updateEvent($id, $data)) {
                setFlashMessage('Event updated successfully.', 'success');
                header('Location: ' . BASE_URL . '/events');
                exit;
            } else {
                setFlashMessage('Failed to update event.', 'error');
            }
        }

        $venueModel = new VenueModel($this->db);
        $venues = $venueModel->getAllVenues();
        $this->smarty->assign('event', $event);
        $this->smarty->assign('venues', $venues);
        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('events_form.tpl');
    }

    public function delete() {
        requireLogin();
        requireRole(['organizer', 'admin']);
        $id = $_GET['id'] ?? 0;
        $eventModel = new EventModel($this->db);
        if ($eventModel->deleteEvent($id)) {
            setFlashMessage('Event deleted successfully.', 'success');
        } else {
            setFlashMessage('Failed to delete event.', 'error');
        }
        header('Location: ' . BASE_URL . '/events');
        exit;
    }

    public function apiList() {
        requireLogin();
        $events = $this->eventModel->getAllEvents();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $events]);
    }
    
    public function apiView() {
        requireLogin();
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $events = $this->eventModel->getAllEvents();
        $event = $events->getEventById($id);
        if ($event) {
            echo json_encode(['status' => 'success', 'data' => $event]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
        }
    }

    public function apiCreate() {
        requireLogin();
        requireRole(['organizer', 'admin']);
        if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $data = [
            'name' => trim($input['name'] ?? ''),
            'description' => trim($input['description'] ?? ''),
            'start_time' => $input['start_time'] ?? '',
            'end_time' => $input['end_time'] ?? '',
            'venue_id' => $input['venue_id'] ?? null,
            'organizer_id' => $_SESSION['user_id']
        ];

        $events = $this->eventModel->getAllEvents();
        if ($this->eventModel->createEvent($data)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Event created']);
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to create event']);
        }
    }
}