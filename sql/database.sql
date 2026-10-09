-- ============================================
-- Sri Lanka Ticket Booking System - Database
-- Import this file via phpMyAdmin
-- ============================================

CREATE DATABASE IF NOT EXISTS ticket_booking_sl
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ticket_booking_sl;

-- ---------- EVENTS TABLE ----------
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS events;

CREATE TABLE events (
    event_id   INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(150) NOT NULL,
    category   VARCHAR(50)  NOT NULL DEFAULT 'General',
    venue      VARCHAR(150) NOT NULL,
    city       VARCHAR(80)  NOT NULL,
    event_date DATETIME     NOT NULL,
    price      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_seats INT NOT NULL,
    seats_left  INT NOT NULL,
    image_color VARCHAR(20)  DEFAULT '#0e6b3a',
    status      ENUM('active','soldout','cancelled') DEFAULT 'active',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (seats_left >= 0 AND seats_left <= total_seats)
) ENGINE=InnoDB;

-- ---------- BOOKINGS TABLE ----------
CREATE TABLE bookings (
    booking_id   INT AUTO_INCREMENT PRIMARY KEY,
    event_id     INT NOT NULL,
    full_name    VARCHAR(120) NOT NULL,
    email        VARCHAR(150) NOT NULL,
    phone        VARCHAR(20)  NOT NULL,
    seat_count   INT NOT NULL CHECK (seat_count > 0),
    total_price  DECIMAL(10,2) NOT NULL,
    booking_code VARCHAR(8)  NOT NULL UNIQUE,
    status       ENUM('confirmed','cancelled') DEFAULT 'confirmed',
    booked_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_event
        FOREIGN KEY (event_id) REFERENCES events(event_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- SAMPLE DATA: Sri Lankan Events ----------
INSERT INTO events (title, category, venue, city, event_date, price, total_seats, seats_left, image_color) VALUES
('Sri Lanka vs Australia - ODI', 'Cricket', 'R. Premadasa Stadium', 'Colombo', '2026-10-25 14:30:00', 2500.00, 200, 143, '#0e6b3a'),
('Nelum Pokuna Music Festival', 'Music', 'Nelum Pokuna Theatre', 'Colombo', '2026-10-18 19:00:00', 1500.00, 120, 87, '#b45309'),
('Esala Perahera Special Show', 'Cultural', 'Mahinda Rajapaksa Theatre', 'Kandy', '2026-08-05 20:00:00', 1200.00, 150, 0, '#991b1b'),
('Kandy Food & Culture Expo', 'Expo', 'Kandy City Centre', 'Kandy', '2026-11-02 09:00:00', 800.00, 300, 250, '#0e6b3a'),
('Galle Literary Festival Night', 'Literary', 'Dutch Reformed Church', 'Galle', '2026-11-14 18:30:00', 1000.00, 90, 64, '#b45309'),
('Jaffna Music Carnival', 'Music', 'Jaffna Municipal Ground', 'Jaffna', '2026-11-22 17:00:00', 600.00, 250, 210, '#0e6b3a');
