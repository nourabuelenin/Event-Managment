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