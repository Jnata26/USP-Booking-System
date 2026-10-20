<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
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
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("
        SELECT ts.*, e.name AS equipment_name 
        FROM time_slots ts 
        JOIN equipment e ON ts.equipment_id = e.id 
        ORDER BY ts.slot_date DESC, e.name ASC, ts.start_time ASC 
        LIMIT 100
    ");
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($slots);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['slot_id']) && isset($data['is_blocked'])) {
        $stmt = $pdo->prepare("UPDATE time_slots SET is_blocked = ? WHERE id = ?");
        $success = $stmt->execute([$data['is_blocked'], $data['slot_id']]);
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
}
?>