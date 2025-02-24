<?php
require 'vendor/autoload.php';

use MongoDB\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Database
{
    private $client;
    private $bookingsCollection;
    private $stripe;

    public function __construct()
    {
        try {
            $config = require __DIR__ . '/config.php';

            $uri = $config['MONGODB_URI'];
            $this->client = new Client($uri);
            $this->bookingsCollection = $this->client->turf->bookings;
            $this->stripe = new \Stripe\StripeClient($config['STRIPE_SECRET_KEY']);
        } catch (Exception $e) {
            throw new Exception('Failed to connect to database');
        }
    }

    public function generateSlots($date, $sport)
    {
        $slotsCollection = $this->client->turf->{$sport . '_slots'};
        $existing = $slotsCollection->findOne(['date' => $date]);

        if ($existing) {
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
                                'email' => $booking['email'],
                                'checkout_session_id' => $booking['checkout_session_id'] ?? null
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

    public function bookSlot($date, $hour, $sport, $checkout_session_id = null, $action = null, $amount_per_slot = null)
    {
        try {
            $slotsCollection = $this->client->turf->{$sport . '_slots'};
            $slotsData = $slotsCollection->findOne(['date' => $date]);

            if (!$slotsData) {
                return ['success' => false, 'message' => 'Slots not available for this date'];
            }

            $slotFound = false;
            foreach ($slotsData['slots'] as &$slot) {
                if ($slot['hour'] == $hour) {
                    $slotFound = true;
                    if (isset($action)) {
                        if ($action === 'restrict') {
                            if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'owner') {
                                return ['success' => false, 'message' => 'Unauthorized to restrict slots'];
                            }

                            if ($slot['status'] === 'booked') {
                                return ['success' => false, 'message' => 'Cannot restrict already booked slots'];
                            }

                            $slot['status'] = 'restricted';
                        } elseif ($action === 'unrestrict') {
                            if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'owner') {
                                return ['success' => false, 'message' => 'Unauthorized to unrestrict slots'];
                            }
                            if ($slot['status'] !== 'restricted') {
                                return ['success' => false, 'message' => 'Slot is not currently restricted'];
                            }
                            $slot['status'] = 'available';
                        }

                        $slotsCollection->updateOne(
                            ['date' => $date],
                            ['$set' => ['slots' => $slotsData['slots']]]
                        );

                        return ['success' => true, 'message' => "Slot {$action}ed successfully"];
                    }
                    if ($slot['status'] === 'booked' || $slot['status'] === 'restricted') {
                        return ['success' => false, 'message' => 'Slot not available'];
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
                        'email' => $_SESSION['user']['email'],
                        'checkout_session_id' => $checkout_session_id,
                        'amount_per_slot' => $amount_per_slot
                    ]);

                    return ['success' => true, 'message' => 'Slot booked successfully'];
                }
            }

            if (!$slotFound) {
                return ['success' => false, 'message' => 'Invalid slot'];
            }

            return ['success' => true, 'message' => 'Operation completed successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error processing slot'];
        }
    }

    private function sendBookingCancelledEmail($booking)
    {
        $config = require __DIR__ . '/config.php';
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $config['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['SMTP_USERNAME'];
            $mail->Password   = $config['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $config['SMTP_PORT'];

            $mail->setFrom($config['SMTP_FROM_ADDRESS'], $config['SMTP_FROM_NAME']);
            $mail->addAddress($booking['email'], $booking['full_name']);

            $mail->isHTML(true);
            $mail->Subject = 'Booking Cancelled - ' . ucfirst($booking['sport']) . ' Turf';

            $hour = intval($booking['hour']);
            $period = $hour >= 12 ? 'PM' : 'AM';
            $displayHour = $hour % 12;
            $displayHour = $displayHour == 0 ? 12 : $displayHour;

            $mail->Body = "
            <h2>Booking Cancelled by Owner</h2>
            <p>Dear {$booking['full_name']},</p>
            <p>Your booking has been cancelled by the venue owner. A refund will be processed shortly.</p>
            <strong>Booking Details:</strong>
            <ul>
                <li>Date: " . date('F j, Y', strtotime($booking['date'])) . "</li>
                <li>Sport: " . ucfirst($booking['sport']) . "</li>
                <li>Time Slot: {$displayHour}:00 {$period}</li>
            </ul>
            <p>We apologize for any inconvenience caused.</p>
            <p>Thank you for using Play Arena!</p>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: {$mail->ErrorInfo}");
            return false;
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

            $booking = $this->bookingsCollection->findOne([
                'date' => $date,
                'hour' => $hour,
                'sport' => $sport
            ]);

            if (!$booking) {
                return ['success' => false, 'message' => 'Booking not found'];
            }

            foreach ($slotsData['slots'] as &$slot) {
                if ($slot['hour'] == $hour && $slot['status'] === 'booked') {
                    try {
                        if (isset($booking['checkout_session_id'])) {
                            $session = $this->stripe->checkout->sessions->retrieve(
                                $booking['checkout_session_id'],
                                ['expand' => ['payment_intent']]
                            );

                            if ($session->payment_intent) {
                                $refundAmount = isset($booking['amount_per_slot'])
                                    ? $booking['amount_per_slot'] * 100
                                    : null;

                                $refund = $this->stripe->refunds->create([
                                    'payment_intent' => $session->payment_intent->id,
                                    'amount' => $refundAmount,
                                    'reason' => 'requested_by_customer'
                                ]);
                            }
                        }

                        $this->sendBookingCancelledEmail($booking);

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

                        return [
                            'success' => true,
                            'message' => 'Booking cancelled and partial refund initiated successfully'
                        ];
                    } catch (Exception $e) {
                        error_log('Refund Error: ' . $e->getMessage());
                        return ['success' => false, 'message' => 'Failed to process refund'];
                    }
                }
            }

            return ['success' => false, 'message' => 'Slot not booked or invalid'];
        } catch (Exception $e) {
            error_log('Error in cancelSlot: ' . $e->getMessage());
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