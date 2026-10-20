<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password.";
        header("Location: index.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];     
        $_SESSION['full_name'] = $user['full_name'];

        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: student.php");
        }
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: index.php");
    }
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>