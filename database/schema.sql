Create TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'organizer', 'attendee') DEFAULT 'attendee',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

Create TABLE `venues` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `capacity` INT NOT NULL
);

Create TABLE `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    `venue_id` INT NULL,
    `organizer_id` INT NOT NULL,
    FOREIGN KEY (`organizer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`venue_id`) REFERENCES `venues`(`id`) ON DELETE SET NULL
);

-- Tickets Table
CREATE TABLE tickets (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_type` VARCHAR(50) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `event_id` INT NOT NULL,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
);


Create TABLE `event_attendees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `registration_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
);

CREATE VIEW filtered_events AS
SELECT e.*, v.name AS venue_name, u.username AS organizer_name 
FROM events e 
LEFT JOIN venues v ON e.venue_id = v.id 
LEFT JOIN users u ON e.organizer_id = u.id 

            -- // ----------------------------------------------------------------------------
            -- // ADOdb Active Record relations
            -- // ADODB_Active_Record::ClassBelongsTo('Event', 'Venue', 'venue_id', 'id');
            -- // ADODB_Active_Record::ClassBelongsTo('Event', 'User', 'organizer_id', 'id');
            -- // $event = new Event();
            -- // $events = $event->Find('1=1');
            -- // foreach ($events as $event) {
            -- //     // error_log("Event loaded with venue: " . print_r($e->Venue, true));
            -- //     $event->venue_name = $event->Venue ? $event->Venue->name : 'N/A';
            -- //     $event->organizer_name = $event->User ? $event->User->username : 'N/A';
            -- //     // $event->save();
            -- //     error_log("Event after setting venue_name: " . print_r($event, true));
            -- // }
            -- // var_dump($events);
            -- // error_log("Events with relations: " . print_r($events, true));

            -- // ----------------------------------------------------------------------------
            -- // logic for loading relations manually v2
            -- // $event = new Event(); 
            -- // $events = $event->Find('1=1'); 
            -- // $venues = VenueModel::getAllVenues();
            -- // $users = UserModel::getAllUsers();

            -- // foreach ($events as $event) {
            -- //     $event->venue_name = $event->venue_id && isset($venueMap[$event->venue_id]) ? $venueMap[$event->venue_id] : 'N/A';
            -- //     $event->organizer_name = $event->organizer_id && isset($userMap[$event->organizer_id]) ? $userMap[$event->organizer_id] : 'N/A';
            -- //     error_log("Event with venue & organizer names: " . print_r($event, true));
            -- // }

            --  // ----------------------------------------------------------------------------
            -- // logic for loading relations manually
            -- // $event = new Event();
            -- // $events = $event->Find('1=1'); 
            -- // $venues = VenueModel::getAllVenues();
            -- // $users = UserModel::getAllUsers();

            -- // $venueMap = [];
            -- // foreach ($venues as $venue) {
            -- //     $venueMap[$venue->id] = $venue->name;
            -- // }

            -- // $userMap = [];
            -- // foreach ($users as $user) {
            -- //     $userMap[$user->id] = $user->username;
            -- // }

            -- // foreach ($events as $event) {
            -- //     $event->venue_name = $event->venue_id && isset($venueMap[$event->venue_id]) ? $venueMap[$event->venue_id] : 'N/A';
            -- //     $event->organizer_name = $event->organizer_id && isset($userMap[$event->organizer_id]) ? $userMap[$event->organizer_id] : 'N/A';
            -- //     error_log("Event with venue & organizer names: " . print_r($event, true));
            -- // }

            -- // ----------------------------------------------------------------------------
