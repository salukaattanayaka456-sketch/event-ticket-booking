# SriLanka Tickets - PHP & MySQL Event Ticket Booking System

An online event-ticket reservation system. Browse Sri Lankan events, book
seats instantly with a transaction-safe booking flow, and receive a unique
8-character booking code.

## Tech Stack
- **PHP 8.2** with PDO prepared statements
- **MySQL** via phpMyAdmin (2 tables: `events`, `bookings` with FK + cascade)
- **CSS3** responsive card-based UI (green = action/success, amber accents, red = errors)
- **XAMPP / Apache** local server

## Setup Instructions (XAMPP)
1. Copy the `ticket_booking_sl` folder into `C:\xampp\htdocs\`
2. Start **Apache** and **MySQL** from the XAMPP Control Panel
3. Open phpMyAdmin: http://localhost/phpmyadmin
4. Import `sql/database.sql` (it creates the `ticket_booking_sl` database
   with sample Sri Lankan events)
5. Open the site: http://localhost/ticket_booking_sl/

## Features
- Real-time seat availability on every event card
- Race-condition safe booking: `SELECT ... FOR UPDATE` + atomic
  `UPDATE ... WHERE seats_left >= ?` inside a transaction
- Unique unguessable booking codes via `random_bytes()`
- Instant cancellation — seats return to the pool in one transaction
- Fully responsive (desktop grid -> single-column mobile)

## File Structure
```
ticket_booking_sl/
 index.php            - event listing page
 book.php             - booking form + transaction logic
 ticket.php           - booking confirmation page
 cancel.php           - cancellation by booking code
 includes/
   db.php             - PDO connection + booking code generator
   header.php         - shared header + logo
   footer.php         - shared footer
 css/style.css        - full stylesheet
 assets/logo.png      - site logo
 sql/database.sql     - database schema + sample data
```

## Demo Flow
1. Browse the live event listing
2. Open an event and pick seat count
3. Confirm -> receive a code like `SL7F3A9C2`
4. Cancel -> seat released instantly
