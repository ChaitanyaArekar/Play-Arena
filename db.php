<?php
require 'vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

class Database
{
    private $client;
    private $bookingsCollection;

    public function __construct()
    {
        try {
            // Load environment variables from .env file
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();

            // Get MongoDB URI from environment variables
            $uri = $_ENV['MONGODB_URI']; // Accessing Mongo URI from .env file
            $this->client = new Client($uri);
            $this->bookingsCollection = $this->client->turf->bookings;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to connect to database']);
            exit;
        }
    }

    // Generate slots dynamically based on the sport
    public function generateSlots($date, $sport)
    {
        $slotsCollection = $this->client->turf->{$sport . '_slots'};
        $existing = $slotsCollection->findOne(['date' => $date]);
        if ($existing) {
            return $existing; // Return existing slots
        }

        $startHour = 10;
        $endHour = 23;

        $slots = [];
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            $slots[] = ['hour' => $hour, 'status' => 'available'];
        }

        $slotsCollection->insertOne([
            'date' => $date,
            'slots' => $slots
        ]);

        return ['date' => $date, 'slots' => $slots];
    }

    // Fetch slots for a specific date and sport
    public function getSlots($date, $sport)
    {
        try {
            return $this->generateSlots($date, $sport);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching slots'];
        }
    }

    // Book a slot for a specific sport
    public function bookSlot($date, $hour, $sport)
    {
        try {
            $slotsCollection = $this->client->turf->{$sport . '_slots'};
            $slotsData = $slotsCollection->findOne(['date' => $date]);
            if (!$slotsData) {
                return ['success' => false, 'message' => 'Slots not available for this date'];
            }

            foreach ($slotsData['slots'] as &$slot) {
                if ($slot['hour'] == $hour) {
                    if ($slot['status'] === 'booked') {
                        return ['success' => false, 'message' => 'Slot already booked'];
                    }
                    $slot['status'] = 'booked';

                    $slotsCollection->updateOne(
                        ['date' => $date],
                        ['$set' => ['slots' => $slotsData['slots']]]
                    );

                    $this->bookingsCollection->insertOne([
                        'date' => $date,
                        'hour' => $hour,
                        'sport' => $sport,
                        'user_id' => 'example-user-id' // Replace with actual user ID
                    ]);

                    return ['success' => true, 'message' => 'Slot booked successfully'];
                }
            }

            return ['success' => false, 'message' => 'Invalid slot'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error booking slot'];
        }
    }

    // Cancel a booked slot for a specific sport
    public function cancelSlot($date, $hour, $sport)
    {
        try {
            $slotsCollection = $this->client->turf->{$sport . '_slots'};
            $slotsData = $slotsCollection->findOne(['date' => $date]);
            if (!$slotsData) {
                return ['success' => false, 'message' => 'Slots not available for this date'];
            }

            foreach ($slotsData['slots'] as &$slot) {
                if ($slot['hour'] == $hour && $slot['status'] === 'booked') {
                    $slot['status'] = 'available';

                    $slotsCollection->updateOne(
                        ['date' => $date],
                        ['$set' => ['slots' => $slotsData['slots']]]
                    );

                    // Remove the booking from the bookings collection
                    $this->bookingsCollection->deleteOne([
                        'date' => $date,
                        'hour' => $hour,
                        'sport' => $sport
                    ]);

                    return ['success' => true, 'message' => 'Slot cancelled successfully'];
                }
            }

            return ['success' => false, 'message' => 'Slot not booked or invalid'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error cancelling slot'];
        }
    }

    // Fetch bookings for a user
    public function getBookings($userId)
    {
        try {
            return $this->bookingsCollection->find(['user_id' => $userId])->toArray();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching bookings'];
        }
    }
}

// Handle HTTP Requests
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['bookings'])) {
        $userId = $_GET['user_id'] ?? 'example-user-id'; // Replace with actual user ID logic
        echo json_encode($db->getBookings($userId));
    } else {
        $date = $_GET['date'] ?? date('Y-m-d');
        $sport = $_GET['sport'] ?? 'cricket'; // Default to cricket
        echo json_encode($db->getSlots($date, $sport));
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? null;
    $hour = $data['hour'] ?? null;
    $sport = $data['sport'] ?? null;

    if ($date && $hour && $sport) {
        $result = $db->bookSlot($date, $hour, $sport);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? null;
    $hour = $data['hour'] ?? null;
    $sport = $data['sport'] ?? null;

    if ($date && $hour && $sport) {
        $result = $db->cancelSlot($date, $hour, $sport);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}
