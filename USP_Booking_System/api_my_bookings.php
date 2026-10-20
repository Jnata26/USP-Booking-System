<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$student_id = $_SESSION['user_id'];

// Fixed SQL syntax: JOIN comes first, then WHERE, then a single ORDER BY id DESC
$stmt = $pdo->prepare("
    SELECT b.id, b.status, b.use_date, b.use_time, b.duration_hours, b.fine_amount, b.fine_status, e.name AS equipment_name 
    FROM bookings b 
    JOIN equipment e ON b.equipment_id = e.id 
    WHERE b.student_id = ? 
    ORDER BY b.id DESC
");
$stmt->execute([$student_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$holdStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE student_id = ? AND fine_status = 'Unpaid' AND fine_amount > 0");
$holdStmt->execute([$student_id]);
$has_hold = $holdStmt->fetchColumn() > 0;

echo json_encode([
    'bookings' => $bookings,
    'has_hold' => $has_hold
]);
?>