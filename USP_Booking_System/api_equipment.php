<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// GET: Fetch all equipment
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM equipment ORDER BY name ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Security Check for modifications
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized admin access']);
    exit;
}

// POST: Add new equipment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name']);
    $category = trim($data['category']);
    $qty = (int)$data['qty'];
    $description = trim($data['description'] ?? '');
    $image_url = trim($data['image_url'] ?? '');

    if (!empty($name) && !empty($category) && $qty >= 0) {
        $stmt = $pdo->prepare("INSERT INTO equipment (name, category, description, image_url, qty) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $description, $image_url, $qty])) {
            echo json_encode(['success' => true]); exit;
        }
    }
    echo json_encode(['error' => 'Invalid data']); exit;
}

// PUT: Update existing equipment
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)$data['id'];
    $name = trim($data['name']);
    $category = trim($data['category']);
    $qty = (int)$data['qty'];
    $description = trim($data['description'] ?? '');
    $image_url = trim($data['image_url'] ?? '');

    if ($id > 0 && !empty($name) && !empty($category) && $qty >= 0) {
        $stmt = $pdo->prepare("UPDATE equipment SET name=?, category=?, description=?, image_url=?, qty=? WHERE id=?");
        if ($stmt->execute([$name, $category, $description, $image_url, $qty, $id])) {
            echo json_encode(['success' => true]); exit;
        }
    }
    echo json_encode(['error' => 'Invalid data']); exit;
}

// DELETE: Remove equipment
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)$data['id'];
    
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM equipment WHERE id=?");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true]); exit;
        }
    }
    echo json_encode(['error' => 'Invalid data']); exit;
}
?>