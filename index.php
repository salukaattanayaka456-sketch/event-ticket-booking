<?php
require_once __DIR__ . '/includes/header.php';

function resolveEventImage(string $title, string $category, array $eventImages): array {
    $normalizedTitle = strtolower(trim($title));
    $normalizedCategory = strtolower(trim($category));

    if (strpos($normalizedTitle, 'jaffna music carnival') !== false) {
        return [
            'url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
            'alt' => 'Jaffna Music Carnival live stage with vibrant Sri Lankan festival lights',
        ];
    }

    foreach ($eventImages as $imageKey => $image) {
        if (strtolower((string)$imageKey) === $normalizedCategory) {
            return $image;
        }
    }

    return $eventImages['default'] ?? [
        'url' => 'https://images.unsplash.com/photo-1579989197111-928f586796a3?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Aerial view of Galle, Sri Lanka, beside the sea',
    ];
}

$stmt = $pdo->query("
    SELECT event_id, title, category, venue, city, event_date, price,
           total_seats, seats_left, image_color, status
    FROM events
    WHERE status != 'cancelled'
    ORDER BY event_date ASC
");
$events = $stmt->fetchAll();
$eventImages = [
    'Cricket' => [
        'url' => 'https://images.unsplash.com/photo-1745180267840-2444c3c68eaa?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Cricket match at Galle International Stadium, Sri Lanka',
    ],
    'Music' => [
        'url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Sri Lankan music festival crowd with warm sunset lights',
    ],
    'Cultural' => [
        'url' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Sri Lankan cultural festival with colorful traditional performance',
    ],
    'Expo' => [
        'url' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Sri Lankan food festival with colorful local dishes',
    ],
    'Literary' => [
        'url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Scenic Sri Lankan coastline and beach atmosphere',
    ],
    'default' => [
        'url' => 'https://images.unsplash.com/photo-1579989197111-928f586796a3?auto=format&fit=crop&w=900&q=80',
        'alt' => 'Aerial view of Galle, Sri Lanka, beside the sea',
    ],
];
?>

<div class="container">
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert success">&#10003; <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['err'])): ?>
        <div class="alert error">&#10007; <?php echo htmlspecialchars($_GET['err']); ?></div>
    <?php endif; ?>

    <section class="hero" aria-label="Welcome section">
        <div class="hero-copy">
            <span class="eyebrow">Your trusted ticket partner</span>
            <h2>Discover unforgettable experiences across Sri Lanka.</h2>
            <p>Reserve seats for premier matches, concerts, cultural festivals, and city events with a fast, secure booking experience designed for modern travelers and local audiences.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#events">Browse Events</a>
                <a class="btn btn-outline" href="cancel.php">Check Booking</a>
            </div>
        </div>
        <div class="hero-visual" aria-label="Event booking illustration">
            <img src="assets/home-hero.svg" alt="Illustration of event ticket booking and seat reservations for Sri Lankan events">
        </div>
    </section>

    <section class="feature-strip" aria-label="Site highlights">
        <div class="feature-item">
            <strong>2,500+</strong>
            <span>Tickets booked</span>
        </div>
        <div class="feature-item">
            <strong>40+</strong>
            <span>Events hosted</span>
        </div>
        <div class="feature-item">
            <strong>24/7</strong>
            <span>Easy online access</span>
        </div>
    </section>

    <section class="explore-panel" aria-label="Popular event categories">
        <div class="panel-head">
            <h3>Explore by category</h3>
            <a href="#events">View all events</a>
        </div>
        <div class="category-grid">
            <div class="category-card">
                <span class="icon">🏏</span>
                <h4>Cricket</h4>
                <p>Test matches, domestic showdowns, and iconic game-day energy.</p>
            </div>
            <div class="category-card">
                <span class="icon">🎵</span>
                <h4>Music</h4>
                <p>Live performances from emerging artists and headline acts.</p>
            </div>
            <div class="category-card">
                <span class="icon">🎭</span>
                <h4>Cultural</h4>
                <p>Traditional celebrations, arts festivals, and heritage events.</p>
            </div>
            <div class="category-card">
                <span class="icon">📚</span>
                <h4>Literary</h4>
                <p>Book launches, talks, and thought-provoking community events.</p>
            </div>
        </div>
    </section>

    <section class="showcase" aria-label="Featured experiences">
        <div class="showcase-header">
            <h3>Featured Experiences</h3>
            <p>Handpicked moments that bring Sri Lanka to life.</p>
        </div>
        <div class="showcase-grid">
            <article class="showcase-card tall" style="background-image: url('https://images.unsplash.com/photo-1516483638261-f4dbaf036963?auto=format&fit=crop&w=900&q=80');">
                <div class="showcase-overlay">
                    <span>Cricket</span>
                    <h4>Galle Stadium Nights</h4>
                    <p>High-energy matches with sea-view atmosphere and unforgettable crowd moments.</p>
                </div>
            </article>
            <article class="showcase-card" style="background-image: url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80');">
                <div class="showcase-overlay">
                    <span>Concert</span>
                    <h4>Sunset Music Nights</h4>
                    <p>Vibrant performances and beachside energy from Sri Lanka’s live scene.</p>
                </div>
            </article>
            <article class="showcase-card" style="background-image: url('https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=900&q=80');">
                <div class="showcase-overlay">
                    <span>Cultural</span>
                    <h4>Heritage Celebrations</h4>
                    <p>Traditional dance, colorful rituals, and festive charm in motion.</p>
                </div>
            </article>
            <article class="showcase-card wide" style="background-image: url('https://images.unsplash.com/photo-1521295121783-8a321d551ad2?auto=format&fit=crop&w=1200&q=80');">
                <div class="showcase-overlay">
                    <span>Festivals</span>
                    <h4>Festival Lights of Sri Lanka</h4>
                    <p>Seasonal events celebrating music, food, culture, and community spirit.</p>
                </div>
            </article>
        </div>
    </section>

    <h2 class="section-title" id="events">Upcoming Events in Sri Lanka</h2>

    <div class="card-grid">
        <?php foreach ($events as $e):
            $soldout = ($e['seats_left'] <= 0 || $e['status'] === 'soldout');
            $low     = !$soldout && $e['seats_left'] <= 20;
            $image   = resolveEventImage((string)($e['title'] ?? ''), (string)($e['category'] ?? ''), $eventImages);
        ?>
        <div class="event-card">
            <div class="card-banner" style="background-color:<?php echo htmlspecialchars($e['image_color']); ?>">
                <img src="<?php echo htmlspecialchars($image['url'], ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                <span><?php echo htmlspecialchars($e['category']); ?></span>
            </div>
            <div class="card-body">
                <h3><?php echo htmlspecialchars($e['title']); ?></h3>
                <p class="meta">&#128205; <?php echo htmlspecialchars($e['venue']) . ', ' . htmlspecialchars($e['city']); ?></p>
                <p class="meta">&#128197; <?php echo date('d M Y · h:i A', strtotime($e['event_date'])); ?></p>

                <?php if ($soldout): ?>
                    <span class="badge soldout">SOLD OUT</span>
                <?php elseif ($low): ?>
                    <span class="badge low">ONLY <?php echo (int)$e['seats_left']; ?> LEFT</span>
                <?php else: ?>
                    <span class="badge open">SEATS AVAILABLE</span>
                <?php endif; ?>

                <p class="seat-line">
                    <?php if ($soldout): ?>
                        <span class="none">No seats remaining</span>
                    <?php else: ?>
                        <span class="left"><?php echo (int)$e['seats_left']; ?> / <?php echo (int)$e['total_seats']; ?> seats left</span>
                    <?php endif; ?>
                </p>
                <p class="price">LKR <?php echo number_format($e['price'], 2); ?></p>
            </div>
            <div class="card-footer">
                <?php if ($soldout): ?>
                    <button class="btn" disabled>Sold Out</button>
                <?php else: ?>
                    <a class="btn btn-primary" href="book.php?event=<?php echo (int)$e['event_id']; ?>">Book Now</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align:center; margin-top:35px;">
        <a class="btn btn-outline" style="width:auto; padding:12px 30px;" href="cancel.php">Cancel a Booking</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
