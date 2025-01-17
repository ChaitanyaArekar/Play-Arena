<?php
// payment_success.php
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable detailed error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log all important data
error_log('Payment success callback received');
error_log('SESSION data: ' . print_r($_SESSION, true));
error_log('GET parameters: ' . print_r($_GET, true));

// Check if session contains booking data
if (!isset($_SESSION['pending_booking'])) {
    error_log('No pending booking found in session!');
    die('Session data missing - please try booking again');
}

// Rest of your payment success code...

// Fix the file paths - assuming vendor is in the project root
$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/vendor/autoload.php';
require_once $projectRoot . '/db.php';

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log('Payment success callback initiated');

try {
    // Verify payment intent ID exists
    $payment_intent_id = $_GET['payment_intent'] ?? null;
    if (!$payment_intent_id) {
        throw new Exception('No payment intent ID provided');
    }

    \Stripe\Stripe::setApiKey('sk_test_51Qi7qaHgsEGcE4nXK3Q29MHEqn9kfR2EgUBCk9mA94uX5t8qrmsDJASrTIBnWH8MOgRvCBoIC39Km9llQ1zQo3tE00GtZk6cOC');

    // Retrieve the payment intent
    $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

    if ($payment_intent->status === 'succeeded') {
        // Get booking details from session
        if (!isset($_SESSION['pending_booking'])) {
            error_log('Session data: ' . print_r($_SESSION, true));
            throw new Exception('No pending booking found in session');
        }

        $booking = $_SESSION['pending_booking'];
        error_log('Booking data: ' . print_r($booking, true));

        // Validate booking data
        if (!isset($booking['sport']) || !isset($booking['date']) || !isset($booking['slots'])) {
            throw new Exception('Invalid booking data');
        }

        $db = new Database();
        $booked_slots = [];
        $failed_slots = [];

        // Book each slot
        foreach ($booking['slots'] as $hour) {
            error_log("Attempting to book slot for hour: $hour");
            $result = $db->bookSlot($booking['date'], $hour, $booking['sport']);
            error_log("Booking result: " . print_r($result, true));

            if ($result['success']) {
                $booked_slots[] = $hour;
            } else {
                $failed_slots[] = $hour;
            }
        }

        // Handle booking results
        if (count($booked_slots) > 0) {
            // Store booking confirmation in session
            $_SESSION['booking_status'] = [
                'type' => 'success',
                'message' => 'Payment successful! Your slots have been booked.',
                'details' => [
                    'sport' => $booking['sport'],
                    'date' => $booking['date'],
                    'amount' => $booking['amount'],
                    'transaction_id' => $payment_intent_id,
                    'booked_slots' => $booked_slots,
                    'failed_slots' => $failed_slots
                ]
            ];

            // Clear pending booking
            unset($_SESSION['pending_booking']);

            // Redirect to confirmation page
            header('Location: booking_confirmation.php');
            exit();
        } else {
            throw new Exception('Failed to book any slots');
        }
    } else {
        throw new Exception('Payment not successful. Status: ' . $payment_intent->status);
    }
} catch (Exception $e) {
    error_log('Payment processing error: ' . $e->getMessage());
    $_SESSION['booking_status'] = [
        'type' => 'error',
        'message' => 'Payment Error: ' . $e->getMessage()
    ];
    header('Location: error.php');
    exit();
}
