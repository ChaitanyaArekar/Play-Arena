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
    $request = $cancelRequestsCollection->findOne([
        '_id' => new ObjectId($data['requestId'])
    ]);

    if (!$request) {
        throw new Exception('Request not found');
    }

    if ($data['action'] === 'approve') {
        // Perform slot cancellation
        $result = $db->cancelSlot($request['date'], $request['hour'], $request['sport']);

        if ($result['success']) {
            // Remove the cancellation request
            $cancelRequestsCollection->deleteOne(['_id' => $request['_id']]);
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Failed to cancel slot');
        }
    } else {
        // Reject request - simply delete the cancellation request
        $cancelRequestsCollection->deleteOne(['_id' => $request['_id']]);
        echo json_encode(['success' => true]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
