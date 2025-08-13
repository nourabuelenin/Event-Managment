<?php
class EventModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // CREATE
    public function createEvent($data) {
        $sql = "INSERT INTO events (name, description, start_time, end_time, venue_id, organizer_id)
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->Execute($sql, [
            $data['name'],
            $data['description'],
            $data['start_time'],
            $data['end_time'],
            $data['venue_id'] ?: null,
            $data['organizer_id']
        ]);
    }

    // READ (all events)
    public function getAllEvents() {
        $sql = "SELECT e.*, v.name AS venue_name, u.username AS organizer_name
                FROM events e
                LEFT JOIN venues v ON e.venue_id = v.id
                JOIN users u ON e.organizer_id = u.id
                ORDER BY start_time DESC";
        return $this->db->GetAll($sql);
    }

    // READ (single event)
    public function getEventById($id) {
        $sql = "SELECT * FROM events WHERE id = ?";
        return $this->db->GetRow($sql, [$id]);
    }

    // UPDATE
    public function updateEvent($id, $data) {
        $sql = "UPDATE events
                SET name = ?, description = ?, start_time = ?, end_time = ?, venue_id = ?, organizer_id = ?
                WHERE id = ?";
        return $this->db->Execute($sql, [
            $data['name'],
            $data['description'],
            $data['start_time'],
            $data['end_time'],
            $data['venue_id'] ?: null,
            $data['organizer_id'],
            $id
        ]);
    }

    // DELETE
    public function deleteEvent($id) {
        $sql = "DELETE FROM events WHERE id = ?";
        return $this->db->Execute($sql, [$id]);
    }
}
