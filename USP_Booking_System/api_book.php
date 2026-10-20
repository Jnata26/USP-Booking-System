<?php
header('Content-Type: application/json');
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$host = 'localhost';
$db   = 'usp_equipment';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$studentId = $_SESSION['user_id'];
$studentUsername = $_SESSION['username'] ?? 'Student';
$equipmentId = $data['equipment_id'] ?? null;
$useDate = $data['use_date'] ?? null;
$slotIds = $data['slot_ids'] ?? []; // Array of selected slot IDs (max 2)

if (!$equipmentId || !$useDate || empty($slotIds)) {
    echo json_encode(['success' => false, 'error' => 'Missing required booking information or time slots.']);
    exit;
}

// Check if student has an active account hold
$holdCheck = $pdo->prepare("SELECT id FROM bookings WHERE student_id = ? AND fine_amount > 0 AND fine_status = 'Unpaid' LIMIT 1");
$holdCheck->execute([$studentId]);
if ($holdCheck->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Cannot book: You have an unpaid fine and an active account hold.']);
    exit;
}

// Check equipment stock
$eqStmt = $pdo->prepare("SELECT name, qty FROM equipment WHERE id = ?");
$eqStmt->execute([$equipmentId]);
$equipment = $eqStmt->fetch(PDO::FETCH_ASSOC);

if (!$equipment || $equipment['qty'] <= 0) {
    echo json_encode(['success' => false, 'error' => 'Equipment is out of stock.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Fetch formatted time text for the selected slots to store in the booking record
    $placeholders = implode(',', array_fill(0, count($slotIds), '?'));
    $timeStmt = $pdo->prepare("SELECT formatted_time FROM time_slots WHERE id IN ($placeholders)");
    $timeStmt->execute($slotIds);
    $slotsData = $timeStmt->fetchAll(PDO::FETCH_COLUMN); // Fixed from PDO::COLUMN_KEY
    $useTimeFormatted = implode(' & ', $slotsData); // e.g., "9:00 AM & 10:00 AM"

    // 2. Insert into bookings table
    $insertBooking = $pdo->prepare("
        INSERT INTO bookings (student_id, student_username, equipment_id, equipment_name, use_date, use_time, duration_hours, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");
    $insertBooking->execute([
        $studentId, 
        $studentUsername, 
        $equipmentId, 
        $equipment['name'], 
        $useDate, 
        $useTimeFormatted, 
        count($slotIds) // Fixed missing $ on variable name
    ]);

    // 3. Mark the chosen time slots as booked so other students can't pick them
    $updateSlots = $pdo->prepare("UPDATE time_slots SET is_booked = 1 WHERE id IN ($placeholders)");
    $updateSlots->execute($slotIds);

    // 4. Reduce equipment quantity
    $updateEq = $pdo->prepare("UPDATE equipment SET qty = qty - 1 WHERE id = ?");
    $updateEq->execute([$equipmentId]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (\Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Booking transaction failed: ' . $e->getMessage()]);
}
?>