<?php

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config instead of directly using dotenv
$config = require dirname(__DIR__) . '/config.php';

function sendCancellationRequestEmail($data, $userEmail, $userName)
{
    global $config;
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['SMTP_USERNAME'];
        $mail->Password   = $config['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['SMTP_PORT'];

        // Recipients
        $mail->setFrom($config['SMTP_FROM_ADDRESS'], $config['SMTP_FROM_NAME']);
        $mail->addAddress($userEmail, $userName);

        $mail->isHTML(true);
        $mail->Subject = 'Cancellation Request - ' . ucfirst($data['sport']) . ' Turf';

        $hour = intval($data['hour']);
        $period = $hour >= 12 ? 'PM' : 'AM';
        $displayHour = $hour % 12;
        $displayHour = $displayHour == 0 ? 12 : $displayHour;

        $mail->Body = "
        <h2>Cancellation Request Submitted</h2>
        <p>Dear {$userName},</p>
        <p>Your cancellation request has been received and is being processed.</p>
        <strong>Cancellation Details:</strong>
        <ul>
            <li>Date: " . date('F j, Y', strtotime($data['date'])) . "</li>
            <li>Sport: " . ucfirst($data['sport']) . "</li>
            <li>Time Slot: {$displayHour}:00 {$period}</li>
            <li>Reason: " . htmlspecialchars($data['reason'] ?? 'Not specified') . "</li>
        </ul>
        <p>Our team will review your request and process the cancellation.</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
    }
}

if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$client = new MongoDB\Client($config['MONGODB_URI']);
$cancelRequestsCollection = $client->turf->cancel_requests;
$bookingsCollection = $client->turf->bookings;

$data = json_decode(file_get_contents('php://input'), true);

try {
    $result = $cancelRequestsCollection->insertOne([
        'bookingId' => new ObjectId((string)$data['bookingId']),
        'sport' => $data['sport'],
        'date' => $data['date'],
        'hour' => $data['hour'],
        'reason' => $data['reason'],
        'email' => $_SESSION['user']['email'],
        'full_name' => $_SESSION['user']['full_name'],
        'timestamp' => time()
    ]);

    // Send cancellation request email
    sendCancellationRequestEmail($data, $_SESSION['user']['email'], $_SESSION['user']['full_name']);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}