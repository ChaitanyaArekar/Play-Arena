<?php
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
require '../vendor/autoload.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user_type'] !== 'user') {
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
    $result = $cancelRequestsCollection->insertOne([
        'bookingId' => new ObjectId((string)$data['bookingId']),
        'sport' => $data['sport'],
        'date' => $data['date'],
        'hour' => $data['hour'],
        'reason' => $data['reason'],
        'email' => $_SESSION['user']['email'],
        'full_name' => $_SESSION['user']['full_name'],
        'timestamp' => new UTCDateTime(time() * 1000)
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
