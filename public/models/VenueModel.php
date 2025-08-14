<?php
class Venue extends ADOdb_Active_Record { // Assuming ADOdb Active Record
    public $_table = 'venues';
    public $_primarykey = 'id';


    // Get all venues
    public static function getAllVenues() {
        $venue = new self();
        return $venue->Find('1=1'); // or Find('', array()) for all
    }
}