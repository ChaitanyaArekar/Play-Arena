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
$cancelRequestsCollection = $client->turf->cancel_requests;

if ($_SESSION['user_type'] === 'owner') {
    // Get all bookings for owners
    $userBookings = $bookingsCollection->find(
        [],
        ['sort' => ['date' => 1, 'hour' => 1]]
    )->toArray();
    $cancelRequests = $cancelRequestsCollection->find(
        [],
        ['sort' => ['date' => -1, 'hour' => -1]]
    )->toArray();
} else {
    // Get only user's bookings for regular users
    $userBookings = $bookingsCollection->find(
        ['email' => $_SESSION['email']],
        ['sort' => ['date' => -1, 'hour' => -1]]
    )->toArray();
    $cancelRequests = $cancelRequestsCollection->find(
        ['email' => $_SESSION['email']],
        ['sort' => ['date' => -1, 'hour' => -1]]
    )->toArray();
}
$cancelRequestBookingIds = array_map(function ($request) {
    return (string)$request['bookingId'];
}, $cancelRequests);

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

usort($upcomingBookings, function ($a, $b) {
    $aDateTime = new DateTime($a['date'] . ' ' . str_pad($a['hour'], 2, '0', STR_PAD_LEFT) . ':00:00');
    $bDateTime = new DateTime($b['date'] . ' ' . str_pad($b['hour'], 2, '0', STR_PAD_LEFT) . ':00:00');
    return $aDateTime <=> $bDateTime;
});

usort($pastBookings, function ($a, $b) {
    $aDateTime = new DateTime($a['date'] . ' ' . str_pad($a['hour'], 2, '0', STR_PAD_LEFT) . ':00:00');
    $bDateTime = new DateTime($b['date'] . ' ' . str_pad($b['hour'], 2, '0', STR_PAD_LEFT) . ':00:00');
    return $bDateTime <=> $aDateTime;
});

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/src/profile.css">
</head>

<body class="bg-gradient-to-br from-green-50 to-blue-50 py-2 px-4">
    <?php $textColor = 'text-black'; ?>
    <header class="<?php echo $textColor; ?>">
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
                <div>

                </div>
            </div>

            <!-- Bookings Section -->
            <div class="bookings-section">
                <div class="bookings-header">
                    <h2><?php echo $_SESSION['user_type'] === 'owner' ? 'All Bookings' : 'Your Bookings'; ?></h2>
                </div>

                <div class="bookings-tabs">
                    <button class="tab-button active" onclick="showTab('upcoming')">Upcoming Bookings
                        <span class="count"><?php echo count($upcomingBookings); ?></span>
                    </button>
                    <button class="tab-button" onclick="showTab('past')">Past Bookings
                        <span class="count"><?php echo count($pastBookings); ?></span>
                    </button>
                    <button class="tab-button" onclick="showTab('cancel-requests')">Cancellation Requests
                        <span class="count"><?php echo count($cancelRequests); ?></span>
                    </button>
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
                                                <i class="fas fa-<?php echo $booking['sport'] === 'cricket' ? 'baseball-ball' : ($booking['sport'] === 'football' ? 'futbol' : 'basketball-ball'); ?>"></i>
                                            </div>
                                            <h3><?php echo ucfirst(htmlspecialchars($booking['sport'])); ?></h3>
                                        </div>
                                        <span class="booking-status <?php
                                                                    echo in_array((string)$booking['_id'], $cancelRequestBookingIds)
                                                                        ? 'status-cancel-request'
                                                                        : 'status-upcoming';
                                                                    ?>">
                                            <?php
                                            echo in_array((string)$booking['_id'], $cancelRequestBookingIds)
                                                ? 'Cancellation Request'
                                                : 'Upcoming';
                                            ?>
                                        </span>
                                        <?php if ($_SESSION['user_type'] === 'user' && !in_array((string)$booking['_id'], $cancelRequestBookingIds)): ?>
                                            <div class="cancel-booking-btn">
                                                <button onclick="initiateBookingCancel('<?php echo $booking['_id']; ?>', '<?php echo $booking['sport']; ?>', '<?php echo $booking['date']; ?>', <?php echo $booking['hour']; ?>)" class="text-red-500 hover:text-red-600 items-center gap-2 px-3 py-1 rounded-lg hover:bg-red-50 disabled:opacity-50">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
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
                                        <?php if ($_SESSION['user_type'] === 'owner'): ?>
                                            <div class="user-details">
                                                <div class="info-item">
                                                    <i class="fas fa-user"></i>
                                                    <?php echo htmlspecialchars($booking['full_name'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

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
                                                <i class="fas fa-<?php echo $booking['sport'] === 'cricket' ? 'baseball-ball' : ($booking['sport'] === 'football' ? 'futbol' : 'basketball-ball'); ?>"></i>
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
                                        <?php if ($_SESSION['user_type'] === 'owner'): ?>
                                            <div class="user-details">
                                                <div class="info-item">
                                                    <i class="fas fa-user"></i>
                                                    <?php echo htmlspecialchars($booking['full_name'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Cancellation Requests Tab -->
                <div id="cancel-requests" class="tab-content">
                    <?php if (empty($cancelRequests)): ?>
                        <div class="empty-state">
                            <i class="fas fa-times-circle"></i>
                            <h3>No Cancellation Requests</h3>
                            <p><?php echo $_SESSION['user_type'] === 'owner' ? 'No pending cancellation requests' : 'You have no pending cancellation requests'; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="bookings-grid">
                            <?php foreach ($cancelRequests as $request): ?>
                                <div class="booking-card">
                                    <div class="booking-header">
                                        <div class="booking-title">
                                            <div class="sport-icon">
                                                <i class="fas fa-<?php echo $request['sport'] === 'cricket' ? 'baseball-ball' : ($request['sport'] === 'football' ? 'futbol' : 'basketball-ball'); ?>"></i>
                                            </div>
                                            <h3><?php echo ucfirst(htmlspecialchars($request['sport'])); ?></h3>
                                        </div>
                                        <span class="booking-status status-cancel-request">Cancellation Request</span>
                                    </div>
                                    <div class="booking-info">
                                        <div class="info-item">
                                            <i class="far fa-calendar"></i>
                                            <?php echo date('D, M j, Y', strtotime($request['date'])); ?>
                                        </div>
                                        <div class="info-item">
                                            <i class="far fa-clock"></i>
                                            <?php echo formatTime($request['hour']); ?>
                                        </div>
                                        <?php if ($_SESSION['user_type'] === 'owner'): ?>
                                            <div class="user-details">
                                                <div class="info-item">
                                                    <i class="fas fa-user"></i>
                                                    <?php echo htmlspecialchars($request['full_name'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <?php echo htmlspecialchars($request['email'] ?? 'N/A'); ?>
                                                </div>
                                                <div class="info-item">
                                                    <i class="fas fa-comment"></i>
                                                    <?php echo htmlspecialchars($request['reason'] ?? 'No reason provided'); ?>
                                                </div>
                                            </div>
                                            <div class="cancel-actions m-2 mt-4 flex justify-between">
                                                <button class="btn-approve bg-green-500 text-white rounded-md p-2 px-4" onclick="approveCancel('<?php echo $request['_id']; ?>')">Approve</button>
                                                <button class="btn-reject bg-red-500 text-white rounded-md p-2 px-4" onclick="rejectCancel('<?php echo $request['_id']; ?>')">Reject</button>
                                            </div>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const swalOptions = {
            width: '448px',
            padding: '10px',
            customClass: {
                popup: 'small-swal',
                title: 'text-lg',
                content: 'text-lg',
                confirmButton: 'bg-red-600 text-white px-8 py-2 rounded text-sm hover:bg-red-700',
                cancelButton: 'bg-gray-400 text-white px-8 py-2 rounded text-sm hover:bg-gray-500'
            }
        };

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function initiateBookingCancel(bookingId, sport, date, hour) {
            Swal.fire({
                ...swalOptions,
                title: 'Cancel Booking',
                input: 'text',
                inputLabel: 'Cancellation Reason',
                inputPlaceholder: 'Why are you cancelling?',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => !value && 'Reason is required'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('cancel_booking.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                bookingId,
                                sport,
                                date,
                                hour,
                                reason: result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                ...swalOptions,
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Request Submitted' : 'Cancellation Failed',
                                text: data.message || (data.success ? 'Cancellation request sent' : 'Unable to submit request'),
                            }).then(() => data.success && location.reload());
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                ...swalOptions,
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred'
                            });
                        });
                }
            });
        }

        function approveCancel(requestId) {
            Swal.fire({
                ...swalOptions,
                title: 'Approve Cancellation?',
                text: 'This will cancel your slot and initiate refund.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('process_cancel_request.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                requestId,
                                action: 'approve'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                ...swalOptions,
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Approved' : 'Approval Failed',
                                text: data.message
                            }).then(() => data.success && location.reload());
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                ...swalOptions,
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred'
                            });
                        });
                }
            });
        }

        function rejectCancel(requestId) {
            Swal.fire({
                ...swalOptions,
                title: 'Reject Cancellation?',
                text: 'This will remove the cancellation request by the user.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('process_cancel_request.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                requestId,
                                action: 'reject'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                ...swalOptions,
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Rejected' : 'Rejection Failed',
                                text: data.message
                            }).then(() => data.success && location.reload());
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                ...swalOptions,
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred'
                            });
                        });
                }
            });
        }
    </script>

    <style>
        .small-swal {
            font-size: 14px;
        }
    </style>

    <style>
        .small-swal {
            font-size: 14px;
        }
    </style>
</body>

</html>