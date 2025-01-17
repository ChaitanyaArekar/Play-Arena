<?php
// booking_confirmation.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if no booking status exists
if (!isset($_SESSION['booking_status'])) {
    header('Location: index.php');
    exit();
}

$booking_status = $_SESSION['booking_status'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
            <?php if ($booking_status['type'] === 'success'): ?>
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-green-600 mb-2">Booking Successful!</h2>
                    <p class="text-gray-600"><?php echo htmlspecialchars($booking_status['message']); ?></p>
                </div>

                <div class="space-y-4">
                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-2">Booking Details</h3>
                        <p><strong>Sport:</strong> <?php echo htmlspecialchars($booking_status['details']['sport']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($booking_status['details']['date']); ?></p>
                        <p><strong>Amount Paid:</strong> ₹<?php echo number_format($booking_status['details']['amount'], 2); ?></p>
                        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($booking_status['details']['transaction_id']); ?></p>
                    </div>

                    <div class="border-t pt-4">
                        <h3 class="font-semibold mb-2">Booked Slots</h3>
                        <ul class="list-disc list-inside">
                            <?php foreach ($booking_status['details']['booked_slots'] as $slot): ?>
                                <li>Hour <?php echo htmlspecialchars($slot); ?>:00</li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if (!empty($booking_status['details']['failed_slots'])): ?>
                        <div class="border-t pt-4">
                            <h3 class="font-semibold mb-2 text-red-600">Failed to Book</h3>
                            <ul class="list-disc list-inside">
                                <?php foreach ($booking_status['details']['failed_slots'] as $slot): ?>
                                    <li>Hour <?php echo htmlspecialchars($slot); ?>:00</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6 text-center">
                    <a href="index.php" class="inline-block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                        Return to Home
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-red-600 mb-2">Booking Error</h2>
                    <p class="text-gray-600"><?php echo htmlspecialchars($booking_status['message']); ?></p>
                    <div class="mt-6">
                        <a href="index.php" class="inline-block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                            Return to Home
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>