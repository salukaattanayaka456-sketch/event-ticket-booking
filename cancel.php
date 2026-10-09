<?php
require_once __DIR__ . '/includes/header.php';

$message = '';
$error   = '';
$code    = strtoupper(trim($_GET['code'] ?? $_POST['code'] ?? ''));

// ---------- POST: confirm cancellation ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $code !== '') {
    try {
        $pdo->beginTransaction();

        // Lock the booking row
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_code = ? FOR UPDATE");
        $stmt->execute([$code]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $pdo->rollBack();
            $error = 'No booking found with code ' . htmlspecialchars($code) . '.';
        } elseif ($booking['status'] === 'cancelled') {
            $pdo->rollBack();
            $error = 'This booking was already cancelled.';
        } else {
            // Flip status + release seats atomically
            $upd = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND status = 'confirmed'");
            $upd->execute([$booking['booking_id']]);

            $rel = $pdo->prepare(
                'UPDATE events SET seats_left = LEAST(seats_left + ?, total_seats),
                    status = IF(status = "soldout" AND seats_left + ? > 0, "active", status)
                 WHERE event_id = ?'
            );
            $rel->execute([$booking['seat_count'], $booking['seat_count'], $booking['event_id']]);

            $pdo->commit();
            $message = 'Booking ' . htmlspecialchars($code) . ' cancelled. Your seat(s) have been released back to the pool.';
            $code = '';
        }
    } catch (Exception $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = 'Cancellation failed: ' . $ex->getMessage();
    }
}
?>

<div class="container">
    <div class="form-card">
        <h2>Cancel a Booking</h2>
        <p class="sub">Enter your 8-character booking code. Seats are released instantly.</p>

        <?php if ($message): ?>
            <div class="alert success">&#10003; <?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error">&#10007; <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="cancel.php">
            <label for="code">Booking Code</label>
            <div class="search-box">
                <input type="text" id="code" name="code" maxlength="8" placeholder="e.g. SL7F3A9C2"
                       value="<?php echo htmlspecialchars($code); ?>" required>
                <button type="submit" class="btn btn-danger" style="width:auto; padding:12px 22px;">Cancel</button>
            </div>
        </form>

        <br>
        <a class="btn btn-outline" href="index.php">Back to Events</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
