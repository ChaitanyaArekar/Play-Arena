<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
require '../vendor/autoload.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$uri = $_ENV['MONGODB_URI'];
$client = new MongoDB\Client($uri);
$bookingsCollection = $client->turf->bookings;

$userBookings = $bookingsCollection->find(
    ['email' => $_SESSION['email']],
    ['sort' => ['date' => 1, 'hour' => 1]]
)->toArray();

$upcomingBookings = [];
$pastBookings = [];
$currentDateTime = new DateTime();

foreach ($userBookings as $booking) {
    $bookingDateTime = new DateTime($booking['date'] . ' ' . str_pad($booking['hour'], 2, '0', STR_PAD_LEFT) . ':00:00');
    if ($bookingDateTime > $currentDateTime) {
        $upcomingBookings[] = $booking;
    } else {
        $pastBookings[] = $booking;
    }
}

function formatTime($hour)
{
    return date('h:i A', strtotime("$hour:00"));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Play Arena</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/src/profile.css">
</head>

<body>
    <?php $textColor = 'text-black'; ?>
    <header class="<?php echo $textColor; ?> p-3 mr-6">
        <?php include 'navbar.php'; ?>
    </header>

    <div class="container">
        <div class="profile-grid">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-picture">
                    <?php echo strtoupper(substr($_SESSION['user_full_name'], 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($_SESSION['user_full_name']); ?></h2>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user-tag"></i>
                        <span><?php echo ucfirst(htmlspecialchars($_SESSION['user_type'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Bookings Section -->
            <div class="bookings-section">
                <div class="bookings-header">
                    <h2>Your Bookings</h2>
                </div>

                <div class="bookings-tabs">
                    <button class="tab-button active" onclick="showTab('upcoming')">Upcoming Bookings</button>
                    <button class="tab-button" onclick="showTab('past')">Past Bookings</button>
                </div>

                <!-- Upcoming Bookings Tab -->
                <div id="upcoming" class="tab-content active">
                    <?php if (empty($upcomingBookings)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-plus"></i>
                            <h3>No Upcoming Bookings</h3>
                            <p>Book your next game session now!</p>
                        </div>
                    <?php else: ?>
                        <div class="bookings-grid">
                            <?php foreach ($upcomingBookings as $booking): ?>
                                <div class="booking-card">
                                    <div class="booking-header">
                                        <div class="booking-title">
                                            <div class="sport-icon">
                                                <i class="fas fa-<?php
                                                                    echo $booking['sport'] === 'cricket' ? 'baseball-ball' : ($booking['sport'] === 'football' ? 'futbol' : 'basketball-ball');
                                                                    ?>"></i>
                                            </div>
                                            <h3><?php echo ucfirst(htmlspecialchars($booking['sport'])); ?></h3>
                                        </div>
                                        <span class="booking-status status-upcoming">Upcoming</span>
                                    </div>
                                    <div class="booking-info">
                                        <div class="info-item">
                                            <i class="far fa-calendar"></i>
                                            <?php echo date('D, M j, Y', strtotime($booking['date'])); ?>
                                        </div>
                                        <div class="info-item">
                                            <i class="far fa-clock"></i>
                                            <?php echo formatTime($booking['hour']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Past Bookings Tab -->
                <div id="past" class="tab-content">
                    <?php if (empty($pastBookings)): ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No Past Bookings</h3>
                            <p>Your booking history will appear here</p>
                        </div>
                    <?php else: ?>
                        <div class="bookings-grid">
                            <?php foreach ($pastBookings as $booking): ?>
                                <div class="booking-card">
                                    <div class="booking-header">
                                        <div class="booking-title">
                                            <div class="sport-icon">
                                                <i class="fas fa-<?php
                                                                    echo $booking['sport'] === 'cricket' ? 'baseball-ball' : ($booking['sport'] === 'football' ? 'futbol' : 'basketball-ball');
                                                                    ?>"></i>
                                            </div>
                                            <h3><?php echo ucfirst(htmlspecialchars($booking['sport'])); ?></h3>
                                        </div>
                                        <span class="booking-status status-past">Past</span>
                                    </div>
                                    <div class="booking-info">
                                        <div class="info-item">
                                            <i class="far fa-calendar"></i>
                                            <?php echo date('D, M j, Y', strtotime($booking['date'])); ?>
                                        </div>
                                        <div class="info-item">
                                            <i class="far fa-clock"></i>
                                            <?php echo formatTime($booking['hour']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Update tab button states
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>

</html>