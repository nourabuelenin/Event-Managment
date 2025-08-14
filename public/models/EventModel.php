<?php
class Event extends ADODB_Active_Record {
    public $_table = 'events';
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

    public function getAllEvents() {
        $event = new Event();
        $events = $event->Find('1=1', [], 'start_time DESC');
        foreach ($events as &$evt) {
            $evt->venue_name = $this->db->GetOne("SELECT name FROM venues WHERE id = ?", [$evt->venue_id]) ?: 'N/A';
            $evt->organizer_name = $this->db->GetOne("SELECT username FROM users WHERE id = ?", [$evt->organizer_id]);
        }
        return $events;
    }

    public function getRecentEvents($limit = 5) {
        $event = new Event();
        return $event->Find('start_time >= NOW()', [], 'start_time ASC', $limit);
    }

    public function getEventById($id) {
        $event = new Event();
        if ($event->Load('id = ?', [$id])) {
            $event->venue_name = $this->db->GetOne("SELECT name FROM venues WHERE id = ?", [$event->venue_id]) ?: 'N/A';
            $event->organizer_name = $this->db->GetOne("SELECT username FROM users WHERE id = ?", [$event->organizer_id]);
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
}
