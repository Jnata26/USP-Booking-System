<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = (int)$data['booking_id'];

    $stmt = $pdo->prepare("UPDATE bookings SET fine_status = 'Paid' WHERE id = ?");
    if ($stmt->execute([$booking_id])) {
        echo json_encode(['success' => true]);
        exit;
    }
}
echo json_encode(['error' => 'Failed to process payment']);
?>