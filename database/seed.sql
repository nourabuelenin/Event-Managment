-- Insert Users
INSERT INTO `users` (`username`, `password`, `email`, `role`)
VALUES
('admin_user', 'admin123', 'admin@example.com', 'admin'),
('john_organizer', 'orgpass', 'organizer@example.com', 'organizer'),
('jane_attendee', 'attendee123', 'jane@example.com', 'attendee'),
('mark_attendee', 'attendee456', 'mark@example.com', 'attendee');

-- Insert Venues
INSERT INTO `venues` (`name`, `location`, `capacity`)
VALUES
('Grand Hall', '123 Main St', 500),
('Conference Center', '456 Center Rd', 300),
('Open Air Park', '789 Park Ave', 1000);

-- Insert Events
INSERT INTO `events` (`name`, `description`, `start_time`, `end_time`, `venue_id`, `organizer_id`)
VALUES
('Tech Conference 2025', 'Annual technology event with multiple speakers.', '2025-09-10 09:00:00', '2025-09-10 17:00:00', 1, 2),
('Music Festival', 'Outdoor music festival with live performances.', '2025-09-15 14:00:00', '2025-09-15 23:00:00', 3, 2),
('Startup Pitch Night', 'Event for startups to pitch their ideas.', '2025-09-20 18:00:00', '2025-09-20 21:00:00', 2, 2);

-- Insert Tickets
INSERT INTO `tickets` (`ticket_type`, `price`, `quantity`, `event_id`)
VALUES
('Standard', 50.00, 200, 1),
('VIP', 150.00, 50, 1),
('General Admission', 30.00, 500, 2),
('Backstage Pass', 120.00, 30, 2),
('Free Entry', 0.00, 100, 3);

-- Insert Event Attendees
INSERT INTO `event_attendees` (`user_id`, `event_id`)
VALUES
(3, 1), -- Jane attends Tech Conference
(4, 1), -- Mark attends Tech Conference
(3, 2), -- Jane attends Music Festival
(4, 3); -- Mark attends Startup Pitch Night
