<?php
require_once __DIR__ . '/includes/header.php';

$code = strtoupper(trim($_GET['code'] ?? ''));

$stmt = $pdo->prepare(
    'SELECT b.*, e.title, e.venue, e.city, e.event_date, e.price
     FROM bookings b
     JOIN events e ON e.event_id = b.event_id
     WHERE b.booking_code = ?'
);
$stmt->execute([$code]);
$ticket = $stmt->fetch();

if (!$ticket) {
    header('Location: index.php?err=Invalid booking code.');
    exit;
}
?>

<div class="container">
    <div class="ticket-box">
        <div class="ticket-head">
            <h2><?php echo $ticket['status'] === 'cancelled' ? 'Booking Cancelled' : 'Booking Confirmed'; ?></h2>
            <p>Show this code at the entrance</p>
            <div class="booking-code"><?php echo htmlspecialchars($ticket['booking_code']); ?></div>
        </div>
        <div class="ticket-body">
            <p>Event <strong><?php echo htmlspecialchars($ticket['title']); ?></strong></p>
            <p>Venue <strong><?php echo htmlspecialchars($ticket['venue']) . ', ' . htmlspecialchars($ticket['city']); ?></strong></p>
            <p>Date <strong><?php echo date('d M Y · h:i A', strtotime($ticket['event_date'])); ?></strong></p>
            <p>Name <strong><?php echo htmlspecialchars($ticket['full_name']); ?></strong></p>
            <p>Email <strong><?php echo htmlspecialchars($ticket['email']); ?></strong></p>
            <p>Seats <strong><?php echo (int)$ticket['seat_count']; ?></strong></p>
            <p>Total Paid <strong>LKR <?php echo number_format($ticket['total_price'], 2); ?></strong></p>
            <p>Status <strong><?php echo ucfirst($ticket['status']); ?></strong></p>
        </div>
        <div class="ticket-actions">
            <a class="btn btn-outline" href="index.php">More Events</a>
            <?php if ($ticket['status'] === 'confirmed'): ?>
                <a class="btn btn-danger" href="cancel.php?code=<?php echo urlencode($ticket['booking_code']); ?>">Cancel Booking</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
