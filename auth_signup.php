<?php
/**
 * Pure PHP signup processor - NO HTML OUTPUT
 * This file only handles database logic and redirects.
 * The form in signup.php posts to this file.
 */
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
    header('Location: signup.php?error=' . urlencode('Please fill in all fields'));
    exit();
}
if ($password !== $confirmPassword) {
    header('Location: signup.php?error=' . urlencode('Passwords do not match'));
    exit();
}
if (strlen($password) < 6) {
    header('Location: signup.php?error=' . urlencode('Password must be at least 6 characters long'));
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: signup.php?error=' . urlencode('Email already registered'));
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);
    $userId = $pdo->lastInsertId();

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;

    header('Location: index.php');
    exit();
} catch (Exception $e) {
    header('Location: signup.php?error=' . urlencode('Error: ' . $e->getMessage()));
    exit();
}
