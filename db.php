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
            $dotenv = Dotenv::createImmutable(__DIR__);
            $dotenv->load();

            $uri = $_ENV['MONGODB_URI'];
            $this->client = new Client($uri);
            $this->bookingsCollection = $this->client->turf->bookings;
        } catch (Exception $e) {
            throw new Exception('Failed to connect to database');
        }
    }

    public function generateSlots($date, $sport)
    {
        $slotsCollection = $this->client->turf->{$sport . '_slots'};
        $existing = $slotsCollection->findOne(['date' => $date]);

        if ($existing) {
            // If user is owner, add booking info to booked slots
            if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'owner') {
                foreach ($existing['slots'] as &$slot) {
                    if ($slot['status'] === 'booked') {
                        $booking = $this->bookingsCollection->findOne([
                            'date' => $date,
                            'hour' => $slot['hour'],
                            'sport' => $sport
                        ]);
                        if ($booking) {
                            $slot['booking_info'] = [
                                'full_name' => $booking['full_name'],
                                'email' => $booking['email']
                            ];
                        }
                    }
                }
            }
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

                    if (!isset($_SESSION['user'])) {
                        return ['success' => false, 'message' => 'User not authenticated'];
                    }

                    $this->bookingsCollection->insertOne([
                        'date' => $date,
                        'hour' => $hour,
                        'sport' => $sport,
                        'full_name' => $_SESSION['user']['full_name'],
                        'email' => $_SESSION['user']['email']
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
            if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'owner') {
                return ['success' => false, 'message' => 'Unauthorized to cancel bookings'];
            }

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

                    $this->bookingsCollection->deleteOne([
                        'date' => $date,
                        'hour' => $hour,
                        'sport' => $sport
                    ]);

                    return ['success' => true, 'message' => 'Booking cancelled successfully'];
                }
            }

            return ['success' => false, 'message' => 'Slot not booked or invalid'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error cancelling booking'];
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
