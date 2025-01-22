<?php
require '../vendor/autoload.php';
require_once 'db.php';
session_start();

header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit;
}

// Handle GET request for payment status
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Validate session ID
    if (!isset($_GET['session_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Payment session ID is required'
        ]);
        exit;
    }

    try {
        // Load environment variables
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        // Initialize Stripe client
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

        // Retrieve checkout session
        $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);

        // Verify session belongs to current user
        if ($session->metadata->user_email !== $_SESSION['user']['email']) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized access to payment session'
            ]);
            exit;
        }

        // Get booking details from metadata
        $sport = $session->metadata->sport;
        $date = $session->metadata->date;
        $slots = json_decode($session->metadata->slots, true);
        $slotTimes = $session->metadata->slot_times;

        // Prepare response data
        $response = [
            'success' => true,
            'data' => [
                'payment' => [
                    'transaction_id' => $session->payment_intent,
                    'status' => $session->payment_status,
                    'amount' => $session->amount_total / 100,
                    'currency' => $session->currency
                ],
                'booking' => [
                    'sport' => $sport,
                    'date' => $date,
                    'slots' => $slots,
                    'slot_times' => $slotTimes,
                    'customer' => [
                        'name' => $session->metadata->user_name,
                        'email' => $session->metadata->user_email
                    ]
                ]
            ]
        ];

        // Verify booking status in database
        $db = new Database();
        $bookings = $db->getBookings($session->metadata->user_email);

        // Filter bookings to find matching date and sport
        $bookedSlots = array_filter($bookings, function ($booking) use ($date, $sport) {
            return $booking['date'] === $date && $booking['sport'] === $sport;
        });

        // Add booking status to response
        $response['data']['booking']['status'] = !empty($bookedSlots) ? 'confirmed' : 'failed';

        // Send success response
        echo json_encode($response);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle Stripe API errors
        error_log('Stripe API Error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error processing payment information',
            'error' => $e->getMessage()
        ]);
    } catch (Exception $e) {
        // Handle general errors
        error_log('Payment Status Error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error retrieving payment status',
            'error' => $e->getMessage()
        ]);
    }
    exit;
} else {
    // Handle invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}
