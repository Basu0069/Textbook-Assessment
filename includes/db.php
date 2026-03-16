<?php
require_once 'config.php';

try {
    $pdo = new PDO(DB_DSN);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create reviews table if it doesn't exist (separate exec calls!)
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        book_id TEXT NOT NULL,
        user_id INTEGER NOT NULL,
        content_quality INTEGER NOT NULL CHECK (content_quality BETWEEN 1 AND 5),
        explanation_clarity INTEGER NOT NULL CHECK (explanation_clarity BETWEEN 1 AND 5),
        examples_quality INTEGER NOT NULL CHECK (examples_quality BETWEEN 1 AND 5),
        exercises_quality INTEGER NOT NULL CHECK (exercises_quality BETWEEN 1 AND 5),
        language_clarity INTEGER NOT NULL CHECK (language_clarity BETWEEN 1 AND 5),
        average_rating REAL NOT NULL,
        comments TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Create index separately
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_book_id ON reviews(book_id)");

} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}