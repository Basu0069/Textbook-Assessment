<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'textbook_user');
define('DB_PASS', 'Witch@69');
define('DB_NAME', 'textbook_assessment');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 