<?php
class Event extends ADODB_Active_Record {
    public $_table = 'events';
    public $_primarykey = 'id';
}

class EventAttendee extends ADODB_Active_Record {
    public $_table = 'event_attendees';
    public $_primarykey = 'id';
}

class EventModel {
    private $db;
    // private $event;
    public function __construct($db) {
        $this->db = $db;
        // $this->event = new Event(); // Initialize Event Active Record
    }

    public function createEvent($data) {
        $event = new Event();
        $event->name = $data['name'];
        $event->description = $data['description'];
        $event->start_time = $data['start_time'];
        $event->end_time = $data['end_time'];
        $event->venue_id = $data['venue_id'] ?: null;
        $event->organizer_id = $data['organizer_id'];
        return $event->Save();
    }

    public function getAllEvents($page = 1, $pageSize = 10, $search = '') {
        try {
            $offset = ($page - 1) * $pageSize;
            $params = [];
            $conditions = [];
            $whereClause = '';

            if (!empty($search)) {
                $searchData = parseSearchQuery($search);
                $whereClause = $searchData['whereClause'];
                $params = $searchData['params'];
            }

            $baseQuery = "SELECT * FROM filtered_events $whereClause";
            $countQuery = "SELECT COUNT(*) AS total FROM filtered_events $whereClause";

            error_log("Count query: $countQuery, Params: " . print_r($params, true));
            $countResult = $this->db->Execute($countQuery, $params);
            if ($countResult === false) {
                error_log('Count query failed: ' . $this->db->ErrorMsg());
                throw new Exception('Failed to count events: ' . $this->db->ErrorMsg());
            }
            $totalItems = (int)($countResult->fields['total'] ?? 0);

            $query = "$baseQuery ORDER BY start_time DESC LIMIT ? OFFSET ?";
            $params = array_merge($params, [$pageSize, $offset]);
            error_log("Events query: $query, Params: " . print_r($params, true));
            $eventsResult = $this->db->Execute($query, $params);
            if ($eventsResult === false) {
                error_log('Events query failed: ' . $this->db->ErrorMsg());
                throw new Exception('Failed to fetch events: ' . $this->db->ErrorMsg());
            }

            $events = [];
            while ($row = $eventsResult->FetchRow()) {
                $events[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'] ?? '',
                    'description' => $row['description'] ?? '',
                    'start_time' => $row['start_time'] ?? '',
                    'end_time' => $row['end_time'] ?? '',
                    'venue_id' => $row['venue_id'] ? (int)$row['venue_id'] : null,
                    'organizer_id' => $row['organizer_id'] ? (int)$row['organizer_id'] : null,
                    'venue_name' => $row['venue_name'] ?: 'N/A',
                    'organizer_name' => $row['organizer_name'] ?: 'N/A'
                ];
            }

            return [
                'data' => $events,
                'totalItems' => $totalItems,
                'totalPages' => max(1, (int)ceil($totalItems / $pageSize)),
                'currentPage' => $page,
                'offset' => $offset,
                'pageSize' => $pageSize
            ];
        } catch (Exception $e) {
            error_log('getAllEvents error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to retrieve events: ' . $e->getMessage(),
                'data' => [],
                'totalItems' => 0,
                'totalPages' => 1,
                'currentPage' => $page,
                'pageSize' => $pageSize
            ];
        }
    }

    public function getRecentEvents($limit = 5) {
        $event = new Event();
        return $event->Find('start_time >= NOW()', [], 'start_time ASC', $limit);
    }

    public function getEventById($id) {
        $query= "SELECT e.*, v.name As venue_name, u.username AS organizer_name 
                FROM events e 
                LEFT JOIN venues v ON e.venue_id = v.id 
                LEFT JOIN users u ON e.organizer_id = u.id 
                WHERE e.id = ?";
        $result = $this->db->Execute($query, [$id]);

        if ($result && $result->RecordCount() > 0) {
            $row = $result->FetchRow();
            
            $event = new Event();
            $event->id = $row['id'];
            $event->name = $row['name'];
            $event->description = $row['description'];
            $event->start_time = $row['start_time'];
            $event->end_time = $row['end_time'];
            $event->venue_id = $row['venue_id'];
            $event->organizer_id = $row['organizer_id'];
            $event->venue_name = $row['venue_name'] ?: 'N/A';
            $event->organizer_name = $row['organizer_name'] ?: 'N/A';
            return $event;
        }
        return false;
    }

    public function updateEvent($id, $data) {
        $event = new Event();
        if ($event->Load('id = ?', [$id])) {
            $event->name = $data['name'];
            $event->description = $data['description'];
            $event->start_time = $data['start_time'];
            $event->end_time = $data['end_time'];
            $event->venue_id = $data['venue_id'] ?: null;
            $event->organizer_id = $data['organizer_id'];
            return $event->Save();
        }
        return false;
    }

    public function deleteEvent($id) {
        $event = new Event();
        if ($event->Load('id = ?', [$id])) {
            return $event->Delete();
        }
        return false;
    }

    public function registerAttendee($user_id, $event_id) {
        // // Check if the event exists
        // $event = new Event();
        // if (!$event->Load('id = ?', [$event_id])) {
        //     return false; 
        // }
        // Check if user is already registered
        $attendee = new EventAttendee();
        if ($attendee->Load('user_id = ? AND event_id = ?', [$user_id, $event_id])) {
            return false; 
        }

        // Create new attendee record
        $attendee = new EventAttendee();
        $attendee->user_id = $user_id;
        $attendee->event_id = $event_id;
        $attendee->registration_date = date('Y-m-d H:i:s');
        return $attendee->Save();
    }

    public function getAttendeesByEventId($event_id) {
        $query = "SELECT u.id, u.username, u.email, ea.registration_date 
                FROM event_attendees ea 
                JOIN users u ON ea.user_id = u.id 
                WHERE ea.event_id = ?";
        $result = $this->db->Execute($query, [$event_id]);

        $attendees = [];
        if ($result) {
        while ($row = $result->FetchRow()) {
            if ($row['id']) { // Ensure user exists
                $attendees[] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'registration_date' => $row['registration_date']
                    ];
                }
            }
        }
        return $attendees;
    }
}
