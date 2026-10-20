<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = (int)$data['booking_id'];
    $student_id = $_SESSION['user_id'];

    // Fetch the booking details
    $stmt = $pdo->prepare("SELECT equipment_id, use_date, use_time, status FROM bookings WHERE id = ? AND student_id = ?");
    $stmt->execute([$booking_id, $student_id]);
    $booking = $stmt->fetch();

    if (!$booking || $booking['status'] === 'Cancelled' || $booking['status'] === 'Returned') {
        echo json_encode(['error' => 'Invalid booking or already processed.']);
        exit;
    }

    // Calculate time difference between now and scheduled time
    $scheduled_timestamp = strtotime($booking['use_date'] . ' ' . $booking['use_time']);
    $current_timestamp = time(); // Current time
    $diff_seconds = $scheduled_timestamp - $current_timestamp;
    
    // 1 hour = 3600 seconds
    $is_late_cancellation = $diff_seconds < 3600;

    $fine_amount = 0.00;
    $fine_status = 'Paid';

    if ($is_late_cancellation) {
        $fine_amount = 5.00; 
        $fine_status = 'Unpaid';
        
        // Send Penalty Warning Email
        $student_email = $_SESSION['username'] . '@student.usp.ac.fj';
        $subject = "USP Equipment Booking - Late Cancellation Penalty Notice";
        $body = "Bula " . $_SESSION['username'] . ",\n\n"
            . "Your booking has been cancelled. Because this cancellation was made less than 1 hour prior to your scheduled time, a late cancellation penalty fee of $5.00 has been applied to your account.\n\n"
            . "Please settle this fee at the equipment counter to clear any holds.\n\n"
            . "Vinaka,\nUSP Equipment Support Team";
        
        @mail($student_email, $subject, $body, "From: equipment.support@usp.ac.fj");
    }

    // Update booking status to Cancelled and assign fine if late
    $update = $pdo->prepare("UPDATE bookings SET status = 'Cancelled', fine_amount = ?, fine_status = ? WHERE id = ?");
    $update->execute([$fine_amount, $fine_status, $booking_id]);

    // Return the inventory quantity back to stock
    $updateEq = $pdo->prepare("UPDATE equipment SET qty = qty + 1 WHERE id = ?");
    $updateEq->execute([$booking['equipment_id']]);

    echo json_encode([
        'success' => true,
        'penalised' => $is_late_cancellation
    ]);
    exit;
}
echo json_encode(['error' => 'Invalid request']);
?>