<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    echo json_encode($settings);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    $allow_weekends = $data['allow_weekends'] ?? '0';
    $start_time = $data['start_time'] ?? '08:00';
    $end_time = $data['end_time'] ?? '20:00';
    $future_weeks = $data['future_weeks'] ?? '1';

    $update = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    
    $update->execute(['allow_weekends', $allow_weekends]);
    $update->execute(['start_time', $start_time]);
    $update->execute(['end_time', $end_time]);
    $update->execute(['future_weeks_allowed', $future_weeks]);

    echo json_encode(['success' => true]);
    exit;
}
?>