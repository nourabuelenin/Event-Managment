<?php
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/VenueModel.php';
require_once __DIR__ . '/../helpers/functions.php';

class EventController {
    private $db;
    private $smarty;
    private $eventModel;

    public function __construct($db, $smarty) {
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
        // error_log("Events in index: " . print_r($events, true));
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
        $event = $this->eventModel->getEventById($id); // Use $this->eventModel
        if (!$event) {
            setFlashMessage('Event not found.', 'error');
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Event not found']);
                exit;
            }
            header('Location: ' . BASE_URL . '/events');
            exit;
        }
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            // Format the event data for JSON response
            $eventData = [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'organizer_name' => $event->organizer_name ?? 'N/A',
                'venue_name' => $event->venue_name ?? 'N/A'
            ];
            echo json_encode(['status' => 'success', 'data' => $eventData]);
            exit;
        }
        $this->smarty->assign('event', $event);
        $this->smarty->assign('flash', getFlashMessage());
        // $this->smarty->display('event_view.tpl');
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

            if ($this->eventModel->createEvent($data)) {
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
        // Assign a default event object
        $this->smarty->assign('event', (object) [
            'id' => null,
            'name' => '',
            'description' => '',
            'start_time' => '',
            'end_time' => '',
            'venue_id' => null
        ]);
        $this->smarty->display('events_form.tpl');
    }

    public function update() {
        requireLogin();
        requireRole(['organizer', 'admin']);
        $id = $_GET['id'] ?? 0;
        $event = $this->eventModel->getEventById($id); // Use $this->eventModel
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
        if ($this->eventModel->deleteEvent($id)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete event']);
        }
        exit;
    }

    public function register() {
        requireLogin();
        $id = $_GET['id'] ?? 0;
        $event = $this->eventModel->getEventById($id); // Use $this->eventModel
        if (!$event) {
            setFlashMessage('Event not found.', 'error');
            header('Location: ' . BASE_URL . '/events');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('Invalid CSRF token.', 'error');
                header('Location: ' . BASE_URL . '/events/register/' . $id);
                exit;
            }

            if ($eventModel->registerAttendee($_SESSION['user_id'], $id)) {
                setFlashMessage('Successfully registered for the event.', 'success');
                header('Location: ' . BASE_URL . '/events/' . $id);
                exit;
            } else {
                setFlashMessage('Registration failed or you are already registered.', 'error');
                header('Location: ' . BASE_URL . '/events/register/' . $id);
                exit;
            }
        }

        $this->smarty->assign('event', $event);
        $this->smarty->assign('flash', getFlashMessage());
        $this->smarty->display('event_register.tpl');
    }

public function apiList() {
    header('Content-Type: application/json');
    try {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $pageSize = isset($_GET['pageSize']) ? max(1, (int)$_GET['pageSize']) : 10;
        $search = trim($_GET['search'] ?? '');
        // $name = trim($_GET['name'] ?? '');
        // $venue_name = trim($_GET['venue_name'] ?? '');
        // $organizer_name = trim($_GET['organizer_name'] ?? '');
        // $start_time = trim($_GET['start_time'] ?? '');
        

        error_log("apiList called: page=$page, pageSize=$pageSize, $search");
        $result = $this->eventModel->getAllEvents($page, $pageSize, $search);
        error_log("getAllEvents result: " . print_r($result, true));

        if (isset($result['status']) && $result['status'] === 'error') {
            http_response_code(500);
            echo json_encode($result);
            exit;
        }

        $response = [
            'status' => 'success',
            'data' => $result['data'] ?? [],
            'pagination' => [
                'totalItems' => $result['totalItems'] ?? 0,
                'totalPages' => $result['totalPages'] ?? 1,
                'offset' => $result['offset'] ?? 0,
                'currentPage' => $result['currentPage'] ?? $page,
                'pageSize' => $result['pageSize'] ?? $pageSize
            ]
        ];

        $jsonOutput = json_encode($response, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($jsonOutput === false) {
            error_log("json_encode failed: " . json_last_error_msg());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'JSON encoding failed: ' . json_last_error_msg()]);
            exit;
        }

        error_log("Sending response: " . $jsonOutput);
        echo $jsonOutput;
    } catch (Exception $e) {
        error_log('apiList error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
    exit;
}
    
    public function apiView() {
        // requireLogin();
        $id = $_GET['id'] ?? 0;
        $event = $this->eventModel->getEventById($id); // Use $this->eventModel
        header('Content-Type: application/json');
        if (!$event) {
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            exit;
        }
        $eventData = [
            'id' => $event->id,
            'name' => $event->name,
            'description' => $event->description,
            'start_time' => $event->start_time,
            'end_time' => $event->end_time,
            'organizer_name' => $event->organizer_name ?? 'N/A',
            'venue_name' => $event->venue_name ?? 'N/A'
        ];
        echo json_encode(['status' => 'success', 'data' => $eventData]);
        exit;
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

    public function apiUpdate() {
        requireLogin();
        requireRole(['organizer', 'admin']);
        $id = $_GET['id'] ?? 0;
        $event = $this->eventModel->getEventById($id); // Use $this->eventModel 
        if (!$event) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            return;
        }

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

        if ($eventModel->updateEvent($id, $data)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Event updated']);
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to update event']);
        }        
    }

    public function apiRegister() {
        requireLogin();
        requireRole(['attendee']); // Restrict to attendees only
        $id = $_GET['id'] ?? 0;
        $event = $this->eventModel->getEventById($id); // Use $this->eventModel
        if (!$event) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            return;
        }

        if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
            return;
        }

        $result = $eventModel->registerAttendee($_SESSION['user_id'], $id);  //eventModel 2
        header('Content-Type: application/json');
        if ($result === true) {
            echo json_encode(['status' => 'success', 'message' => 'Successfully registered for the event']);
        } elseif ($result === 'already_registered') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'You are already registered for this event']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to register due to a server error']);
        }
    }

}