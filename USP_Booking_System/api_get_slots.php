<?php
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'usp_equipment'; 
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$date = $_GET['date'] ?? date('Y-m-d');
$equipmentId = $_GET['equipment_id'] ?? null;

if ($equipmentId) {
    // Fetch slots specifically for this equipment and date
    $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE equipment_id = ? AND slot_date = ? ORDER BY start_time");
    $stmt->execute([$equipmentId, $date]);
} else {
    // Fallback for general browse filter if no specific equipment is selected
    $stmt = $pdo->prepare("SELECT * FROM time_slots WHERE slot_date = ? ORDER BY start_time");
    $stmt->execute([$date]);
}

$slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Auto-populate default daily slots if none exist yet for this date/equipment
if (empty($slots) && $equipmentId) {
    $defaultTimes = [
        ['08:00:00', '09:00:00', '8:00 AM'],
        ['09:00:00', '10:00:00', '9:00 AM'],
        ['10:00:00', '11:00:00', '10:00 AM'],
        ['11:00:00', '12:00:00', '11:00 AM'],
        ['12:00:00', '13:00:00', '12:00 PM'],
        ['14:00:00', '15:00:00', '2:00 PM'],
        ['15:00:00', '16:00:00', '3:00 PM'],
        ['16:00:00', '17:00:00', '4:00 PM'],
        ['17:00:00', '18:00:00', '5:00 PM'],
        ['18:00:00', '19:00:00', '6:00 PM'],
        ['19:00:00', '20:00:00', '7:00 PM'],
        ['20:00:00', '21:00:00', '8:00 PM']    
    ];

    $insert = $pdo->prepare("INSERT INTO time_slots (equipment_id, slot_date, start_time, end_time, formatted_time, is_blocked, is_booked) VALUES (?, ?, ?, ?, ?, 0, 0)");
    foreach ($defaultTimes as $t) {
        $insert->execute([$equipmentId, $date, $t[0], $t[1], $t[2]]);
    }

    $stmt->execute([$equipmentId, $date]);
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($slots);
?>