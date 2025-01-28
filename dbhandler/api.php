<?php
require_once '../dbhandler/db.php';
session_start();

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['bookings'])) {
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
    $action = $data['action'] ?? null;

    if ($date && $hour !== null && $sport) {
        $result = $db->bookSlot($date, $hour, $sport, null, $action);
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

    if ($date && $hour !== null && $sport) {
        $result = $db->cancelSlot($date, $hour, $sport);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
    exit;
}