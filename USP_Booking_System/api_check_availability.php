<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $equipment_id = (int)($_GET['equipment_id'] ?? 0);
    $date = $_GET['date'] ?? '';
    $time = $_GET['time'] ?? '';
    $duration = (int)($_GET['duration'] ?? 1);

    if (!$equipment_id || !$date || !$time) {
        echo json_encode(['available' => false, 'error' => 'Missing parameters']);
        exit;
    }

    // Calculate requested start and end timestamps
    $req_start = strtotime("$date $time");
    $req_end = $req_start + ($duration * 3600);

    // Fetch all active/approved bookings for this equipment on this date
    $stmt = $pdo->prepare("
        SELECT use_time, duration_hours 
        FROM bookings 
        WHERE equipment_id = ? AND use_date = ? AND status IN ('Pending', 'Approved')
    ");
    $stmt->execute([$equipment_id, $date]);
    $existing_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $is_clashing = false;
    foreach ($existing_bookings as $b) {
        $book_start = strtotime("$date " . $b['use_time']);
        $book_end = $book_start + ($b['duration_hours'] * 3600);

        // Check for time overlap
        if ($req_start < $book_end && $req_end > $book_start) {
            $is_clashing = true;
            break;
        }
    }

    // Also check physical total stock quantity
    $eqStmt = $pdo->prepare("SELECT qty FROM equipment WHERE id = ?");
    $eqStmt->execute([$equipment_id]);
    $eq = $eqStmt->fetch();

    $available = (!$is_clashing && $eq && $eq['qty'] > 0);

    echo json_encode([
        'available' => $available,
        'clashing' => $is_clashing
    ]);
    exit;
}
?>
