<?php
// ============================================
// Database Connection (PDO)
// ============================================
$host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
$dbname = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'ticket_booking_sl');
$username = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? 'root');
$password = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? '');

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

// ---------- Generate unique 8-char booking code ----------
function generateBookingCode(PDO $pdo): string {
    do {
        $code = strtoupper(bin2hex(random_bytes(4))); // e.g. SL7F3A9C2
        $stmt = $pdo->prepare("SELECT 1 FROM bookings WHERE booking_code = ?");
        $stmt->execute([$code]);
    } while ($stmt->fetch());
    return $code;
}
?>
