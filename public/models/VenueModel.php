<?php
class Venue extends ADOdb_Active_Record { // Assuming ADOdb Active Record
    public $_table = 'venues';
    public $_primarykey = 'id';
}

class VenueModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    // Get all venues
    public static function getAllVenues() {
        $venue = new Venue();;
        return $venue->Find('1=1'); // or Find('', array()) for all
    }
}