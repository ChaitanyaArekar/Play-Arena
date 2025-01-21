<?php
//payment.php
require '../vendor/autoload.php';
session_start();
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);
$sport = $_POST['sport'] ?? '';
$date = $_POST['date'] ?? '';
$slots = isset($_POST['slots']) ? json_decode($_POST['slots'], true) : [];
$amount = $_POST['amount'] ?? 0;
$userName = $_SESSION['user']['full_name'] ?? 'Guest';
$userEmail = $_SESSION['user']['email'] ?? '';

try {
    $slotTimes = array_map(function ($slot) {
        $hour = intval($slot);
        $period = $hour >= 12 ? 'PM' : 'AM';
        $displayHour = $hour % 12;
        $displayHour = $displayHour == 0 ? 12 : $displayHour;
        return sprintf("%d:00 %s", $displayHour, $period);
    }, $slots);

    $slotTimeStr = implode(', ', $slotTimes);



    $description = "Booking Details:\n" .
        "Name: {$userName}\n" .
        "Date: " . date('F j, Y', strtotime($date)) . "\n" .
        "Field: {$sport}\n" .
        "Time slots: " . $slotTimeStr;

    $checkout_session = $stripe->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'customer_email' => $userEmail,
        'line_items' => [[
            'price_data' => [
                'currency' => 'inr',
                'unit_amount' => $amount * 100,
                'product_data' => [
                    'name' => ucfirst($sport) . ' Turf Booking',
                    'description' => $description,
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost:8000/src/process_booking.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost:8000/src/book.php',
        'metadata' => [
            'sport' => $sport,
            'date' => $date,
            'slots' => json_encode($slots),
            'user_email' => $userEmail,
            'user_name' => $userName,
            'slot_times' => $slotTimeStr,
        ],
    ]);

    $_SESSION['pending_booking'] = [
        'sport' => $sport,
        'date' => $date,
        'slots' => $slots,
        'amount' => $amount,
        'checkout_session_id' => $checkout_session->id,
        'timestamp' => time(),
        'user_name' => $userName,
        'user_email' => $userEmail
    ];

    echo json_encode(['id' => $checkout_session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}