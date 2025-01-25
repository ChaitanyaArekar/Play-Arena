<?php

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

require '../vendor/autoload.php';
require_once dirname(__DIR__) . '/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendCancellationResponseEmail($request, $action)
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
        $mail->addAddress($request['email'], $request['full_name']);

        $mail->isHTML(true);

        // Determine email subject and body based on action
        if ($action === 'approve') {
            $mail->Subject = 'Cancellation Request Approved - ' . ucfirst($request['sport']) . ' Turf';
            $statusMessage = 'Your cancellation request has been <strong style="color: green;">APPROVED</strong>.';
        } else {
            $mail->Subject = 'Cancellation Request Rejected - ' . ucfirst($request['sport']) . ' Turf';
            $statusMessage = 'Your cancellation request has been <strong style="color: red;">REJECTED</strong>.';
        }

        $hour = intval($request['hour']);
        $period = $hour >= 12 ? 'PM' : 'AM';
        $displayHour = $hour % 12;
        $displayHour = $displayHour == 0 ? 12 : $displayHour;

        $mail->Body = "
        <h2>Cancellation Request Status</h2>
        <p>Dear {$request['full_name']},</p>
        <p>{$statusMessage}</p>
        <strong>Booking Details:</strong>
        <ul>
            <li>Date: " . date('F j, Y', strtotime($request['date'])) . "</li>
            <li>Sport: " . ucfirst($request['sport']) . "</li>
            <li>Time Slot: {$displayHour}:00 {$period}</li>
        </ul>
        " . ($action === 'reject' ? "<p>Please contact support if you have any questions.</p>" : "") . "
        <p>Thank you for using Your Turf!</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Cancellation response email sending failed: {$mail->ErrorInfo}");
    }
}

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'owner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$uri = $_ENV['MONGODB_URI'];
$client = new MongoDB\Client($uri);
$cancelRequestsCollection = $client->turf->cancel_requests;
$bookingsCollection = $client->turf->bookings;

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = new Database();

    // Handle direct slot cancellation
    if (isset($data['sport']) && isset($data['date']) && isset($data['hour'])) {
        $cancelRequestsCollection->deleteMany([
            'sport' => $data['sport'],
            'date' => $data['date'],
            'hour' => $data['hour']
        ]);

        $result = $db->cancelSlot($data['date'], $data['hour'], $data['sport']);

        echo json_encode(['success' => $result['success']]);
        exit;
    }

    // Handle existing cancellation request
    $request = $cancelRequestsCollection->findOne([
        '_id' => new ObjectId($data['requestId'])
    ]);

    if (!$request) {
        throw new Exception('Request not found');
    }

    if ($data['action'] === 'approve') {
        $result = $db->cancelSlot($request['date'], $request['hour'], $request['sport']);

        if ($result['success']) {
            $cancelRequestsCollection->deleteMany([
                'sport' => $request['sport'],
                'date' => $request['date'],
                'hour' => $request['hour']
            ]);

            // Send email notification for approval
            sendCancellationResponseEmail($request, 'approve');

            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Failed to cancel slot');
        }
    } else {
        $cancelRequestsCollection->deleteMany([
            'sport' => $request['sport'],
            'date' => $request['date'],
            'hour' => $request['hour']
        ]);

        // Send email notification for rejection
        sendCancellationResponseEmail($request, 'reject');

        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
