<?php
require '../vendor/autoload.php';
require_once dirname(__DIR__) . '/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

function sendBookingConfirmationEmail($booking, $session)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['SMTP_PORT'];

        // Recipients
        $mail->setFrom($_ENV['SMTP_FROM_ADDRESS'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($booking['user_email'], $booking['user_name']);

        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - ' . ucfirst($booking['sport']) . ' Turf';

        $slotTimes = array_map(function ($slot) {
            $hour = intval($slot);
            $period = $hour >= 12 ? 'PM' : 'AM';
            $displayHour = $hour % 12;
            $displayHour = $displayHour == 0 ? 12 : $displayHour;
            return sprintf("%d:00 %s", $displayHour, $period);
        }, $booking['slots']);

        $mail->Body = "
        <h2>Booking Confirmation</h2>
        <p>Dear {$booking['user_name']},</p>
        <p>Your booking for {$booking['sport']} turf has been confirmed.</p>
        <strong>Booking Details:</strong>
        <ul>
            <li>Date: " . date('F j, Y', strtotime($booking['date'])) . "</li>
            <li>Sport: " . ucfirst($booking['sport']) . "</li>
            <li>Time Slots: " . implode(', ', $slotTimes) . "</li>
            <li>Total Amount: ₹{$booking['amount']}</li>
        </ul>
        <p>Thank you for choosing Play Arena!</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
    }
}

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
            $result = $db->bookSlot(
                $booking['date'], 
                $hour, 
                $booking['sport'], 
                $session->id,
                null,
                $booking['amount'] / count($booking['slots']) // Add amount per slot
            );
            if ($result['success']) {
                $booked_slots[] = $hour;
            } else {
                throw new Exception('Failed to book slot: ' . $hour);
            }
        }

        // Send booking confirmation email
        sendBookingConfirmationEmail($booking, $session);

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