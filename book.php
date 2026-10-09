<?php
require_once __DIR__ . '/includes/header.php';

$eventId = filter_input(INPUT_GET, 'event', FILTER_VALIDATE_INT) ?? 0;
$errors  = [];

// Fetch event (with row lock happens on POST submit)
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND status = 'active'");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: index.php?err=Event not found or unavailable.');
    exit;
}

// ---------- POST: process booking ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName  = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $seatCount = (int)($_POST['seat_count'] ?? 0);

    // --- Server-side validation ---
    if ($fullName === '' || strlen($fullName) < 3)            $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))           $errors[] = 'Please enter a valid email address.';
    if (!preg_match('/^[0-9+\- ]{9,15}$/', $phone))           $errors[] = 'Please enter a valid phone number.';
    if ($seatCount < 1)                                       $errors[] = 'Seat count must be at least 1.';
    if ($seatCount > 10)                                      $errors[] = 'Maximum 10 seats per booking.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Lock the event row so two buyers can't grab the last seat
            $lock = $pdo->prepare('SELECT seats_left FROM events WHERE event_id = ? FOR UPDATE');
            $lock->execute([$eventId]);
            $row = $lock->fetch();

            if (!$row || $row['seats_left'] < $seatCount) {
                $pdo->rollBack();
                $errors[] = 'Sorry, only ' . (int)($row['seats_left'] ?? 0) . ' seat(s) remaining. Please try fewer seats.';
            } else {
                // Atomic decrement — never lets the count drift
                $upd = $pdo->prepare(
                    'UPDATE events SET seats_left = seats_left - ?,
                        status = IF(seats_left - ? = 0, "soldout", status)
                     WHERE event_id = ? AND seats_left >= ?'
                );
                $upd->execute([$seatCount, $seatCount, $eventId, $seatCount]);

                if ($upd->rowCount() !== 1) {
                    throw new Exception('Seat update failed — please retry.');
                }

                $totalPrice  = $seatCount * $event['price'];
                $bookingCode = generateBookingCode($pdo);

                $ins = $pdo->prepare(
                    'INSERT INTO bookings (event_id, full_name, email, phone, seat_count, total_price, booking_code)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $ins->execute([$eventId, $fullName, $email, $phone, $seatCount, $totalPrice, $bookingCode]);

                $pdo->commit();
                header('Location: ticket.php?code=' . $bookingCode);
                exit;
            }
        } catch (Exception $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = 'Booking failed: ' . $ex->getMessage();
        }
    }
}
?>

<div class="container">
    <div class="form-card">
        <h2>Book Your Tickets</h2>
        <p class="sub">
            <?php echo htmlspecialchars($event['title']); ?> &middot;
            <?php echo htmlspecialchars($event['venue']) . ', ' . htmlspecialchars($event['city']); ?> &middot;
            <?php echo date('d M Y · h:i A', strtotime($event['event_date'])); ?>
        </p>

        <?php foreach ($errors as $err): ?>
            <div class="alert error">&#10007; <?php echo htmlspecialchars($err); ?></div>
        <?php endforeach; ?>

        <form method="POST" action="">
            <div class="field-row">
                <div>
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="07X-XXXXXXX"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                </div>
            </div>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="you@example.lk"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

            <label for="seat_count">Number of Seats (max 10)</label>
            <input type="number" id="seat_count" name="seat_count" min="1" max="10"
                   value="<?php echo htmlspecialchars($_POST['seat_count'] ?? 1); ?>" required>

            <div class="summary">
                Price per seat <span>LKR <?php echo number_format($event['price'], 2); ?></span><br>
                Available <span><?php echo (int)$event['seats_left']; ?> seats</span>
            </div>

            <br>
            <button type="submit" class="btn btn-primary">Confirm Booking</button>
            <br><br>
            <a class="btn btn-outline" href="index.php">Back to Events</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
