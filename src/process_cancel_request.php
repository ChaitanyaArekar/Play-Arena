<?php
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
require '../vendor/autoload.php';
require_once dirname(__DIR__) . '/db.php';
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
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
