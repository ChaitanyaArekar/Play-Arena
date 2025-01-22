<?php
require '../vendor/autoload.php';
require_once dirname(__DIR__) . '/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

try {
    if (
        !isset($_GET['session_id']) || !isset($_SESSION['pending_booking']) ||
        $_GET['session_id'] !== $_SESSION['pending_booking']['checkout_session_id']
    ) {
        throw new Exception('Invalid session');
    }

    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);

    if (
        $session->payment_status !== 'paid' ||
        $session->metadata->user_email !== $_SESSION['user']['email']
    ) {
        throw new Exception('Payment verification failed');
    }

    $db = new Database();
    $booking = $_SESSION['pending_booking'];
    $success = true;
    $booked_slots = [];

    try {
        foreach ($booking['slots'] as $hour) {
            $result = $db->bookSlot($booking['date'], $hour, $booking['sport'], $session->id);
            if ($result['success']) {
                $booked_slots[] = $hour;
            } else {
                throw new Exception('Failed to book slot: ' . $hour);
            }
        }

        unset($_SESSION['pending_booking']);
        $sport = $booking['sport'];
        header("Location: book.php?sport=$sport&payment_status=success");
        exit;
    } catch (Exception $e) {
        $refund = $stripe->refunds->create([
            'payment_intent' => $session->payment_intent,
            'reason' => 'requested_by_customer'
        ]);
        foreach ($booked_slots as $hour) {
            $db->cancelSlot($booking['date'], $hour, $booking['sport']);
        }

        error_log('Booking Error: ' . $e->getMessage());
        $sport = $booking['sport'];
        header("Location: book.php?sport=$sport&payment_status=booking_failed");
        exit;
    }
} catch (Exception $e) {
    error_log('Payment Processing Error: ' . $e->getMessage());
    $sport = isset($booking['sport']) ? $booking['sport'] : 'cricket';
    header("Location: book.php?sport=$sport&payment_status=error");
    exit;
}
