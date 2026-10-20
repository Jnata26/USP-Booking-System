<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Ensure the user is a logged-in admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Handle GET request: Fetch all bookings with full request details
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("
        SELECT 
            b.id, 
            b.status, 
            b.use_date, 
            b.use_time, 
            b.duration_hours, 
            b.fine_amount, 
            b.fine_status, 
            b.equipment_id, 
            e.name AS equipment_name, 
            u.username AS student_username 
        FROM bookings b
        JOIN equipment e ON b.equipment_id = e.id 
        JOIN users u ON b.student_id = u.id 
        ORDER BY b.id DESC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = (int)$data['booking_id'];
    $new_status = $data['status'] ?? null;
    $eq_id = isset($data['equipment_id']) ? (int)$data['equipment_id'] : 0;
    $action = $data['action'] ?? null;

    // Handle Admin Action to clear a fine
    if ($action === 'pay_fine') {
        $stmt = $pdo->prepare("UPDATE bookings SET fine_status = 'Paid' WHERE id = ?");
        if ($stmt->execute([$booking_id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    }

    // Existing Status update logic...
    if ($new_status === 'Returned') {
        $stmt = $pdo->prepare("SELECT use_date, use_time FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        $scheduled_datetime = strtotime($booking['use_date'] . ' ' . $booking['use_time']);
        $current_datetime = time();
        $grace_period = 300; // 5 min grace
        $is_late = $current_datetime > ($scheduled_datetime + $grace_period + 7200);

        $fine_amount = $is_late ? 5.00 : 0.00;
        $fine_status = $is_late ? 'Unpaid' : 'Paid';

        $update = $pdo->prepare("UPDATE bookings SET status = ?, actual_return_time = NOW(), fine_amount = ?, fine_status = ? WHERE id = ?");
        $update->execute([$new_status, $fine_amount, $fine_status, $booking_id]);
    } else if ($new_status) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
    }

    if ($new_status === 'Rejected' || $new_status === 'Returned') {
        $updateEq = $pdo->prepare("UPDATE equipment SET qty = qty + 1 WHERE id = ?");
        $updateEq->execute([$eq_id]);
    }
    
    // --- EMAIL DISPATCH BLOCK ---
    $stmtInfo = $pdo->prepare("
        SELECT b.*, e.name AS equipment_name, u.username AS student_username 
        FROM bookings b 
        JOIN equipment e ON b.equipment_id = e.id 
        JOIN users u ON b.student_id = u.id 
        WHERE b.id = ?
    ");
    $stmtInfo->execute([$booking_id]);
    $bookingDetails = $stmtInfo->fetch(PDO::FETCH_ASSOC);

    if ($bookingDetails) {
        $student_email = $bookingDetails['student_username'] . '@student.usp.ac.fj';
        $eq_name = $bookingDetails['equipment_name'];
        $subject = "USP Equipment Booking Update: " . $new_status;
        
        $body = "Bula " . $bookingDetails['student_username'] . ",\n\n"
            . "Your booking status for " . $eq_name . " has been updated to: " . $new_status . ".\n\n";
        
        if ($new_status === 'Approved') {
            $body .= "Your reservation is approved. Please collect your item at the counter with your Student ID.\n";
        } elseif ($new_status === 'Returned') {
            if ($bookingDetails['fine_amount'] > 0) {
                $body .= "Note: Your item was returned late. A penalty fee of $" . number_format($bookingDetails['fine_amount'], 2) . " has been applied, and an automatic hold has been placed on your account until settled.\n";
            } else {
                $body .= "Thank you for returning the equipment on time! No penalties incurred.\n";
            }
        }
        
        $body .= "\nVinaka,\nUSP Equipment Support Team";
        @mail($student_email, $subject, $body, "From: equipment.support@usp.ac.fj");
    }

    echo json_encode(['success' => true]);
    exit;
}
?>