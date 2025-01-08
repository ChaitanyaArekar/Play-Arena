<?php
require 'vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

session_start(); // Start the session to access session variables

class Database
{
    private $client;
    private $bookingsCollection;

    public function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();

            $uri = $_ENV['MONGODB_URI'];
            $this->client = new Client($uri);
            $this->bookingsCollection = $this->client->turf->bookings;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to connect to database']);
            exit;
        }
    }

    public function generateSlots($date, $sport)
    {
        $slotsCollection = $this->client->turf->{$sport . '_slots'};
        $existing = $slotsCollection->findOne(['date' => $date]);
        if ($existing) {
            return $existing;
        }

        $startHour = 8;
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

    public function getSlots($date, $sport)
    {
        try {
            return $this->generateSlots($date, $sport);
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching slots'];
        }
    }

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

                    // Replace user_id with full_name and email from session
                    if (isset($_SESSION['user'])) {
                        $full_name = $_SESSION['user']['full_name'];
                        $email = $_SESSION['user']['email'];
                    } else {
                        return ['success' => false, 'message' => 'User not authenticated'];
                    }

                    $this->bookingsCollection->insertOne([
                        'date' => $date,
                        'hour' => $hour,
                        'sport' => $sport,
                        'full_name' => $full_name,
                        'email' => $email
                    ]);

                    return ['success' => true, 'message' => 'Slot booked successfully'];
                }
            }

            return ['success' => false, 'message' => 'Invalid slot'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error booking slot'];
        }
    }

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

                    // Cancel booking by matching the user
                    $this->bookingsCollection->deleteOne([
                        'date' => $date,
                        'hour' => $hour,
                        'sport' => $sport,
                        'email' => $_SESSION['user']['email'] // Match by email (user-specific)
                    ]);

                    return ['success' => true, 'message' => 'Slot cancelled successfully'];
                }
            }

            return ['success' => false, 'message' => 'Slot not booked or invalid'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error cancelling slot'];
        }
    }

    public function getBookings($userEmail)
    {
        try {
            return $this->bookingsCollection->find(['email' => $userEmail])->toArray();
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching bookings'];
        }
    }
}

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['bookings'])) {
        // Fetch email from session instead of using a user_id query parameter
        if (isset($_SESSION['user'])) {
            $userEmail = $_SESSION['user']['email'];
            echo json_encode($db->getBookings($userEmail));
        } else {
            echo json_encode(['success' => false, 'message' => 'User not authenticated']);
        }
    } else {
        $date = $_GET['date'] ?? date('Y-m-d');
        $sport = $_GET['sport'] ?? 'cricket';
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
