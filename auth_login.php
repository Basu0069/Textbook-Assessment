<?php
/**
 * Pure PHP login processor - NO HTML OUTPUT
 * This file only handles database logic and redirects.
 * The form in login.php posts to this file.
 */
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location: login.php?error=' . urlencode('Please fill in all fields'));
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit();
    } else {
        header('Location: login.php?error=' . urlencode('Invalid email or password'));
        exit();
    }
} catch (Exception $e) {
    header('Location: login.php?error=' . urlencode('Database error: ' . $e->getMessage()));
    exit();
}
