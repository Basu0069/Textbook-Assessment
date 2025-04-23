<?php
require_once 'includes/config.php';

try {
    // Test database connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!<br>";

    // Check if database exists
    $result = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "Connected to database: " . $result . "<br>";

    // Check if users table exists
    $usersTableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    echo "Users table exists: " . ($usersTableExists ? "Yes" : "No") . "<br>";

    if (!$usersTableExists) {
        // Create users table
        $pdo->exec("CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "Users table created successfully!<br>";
    }

    // Test inserting a user
    $testEmail = "test@example.com";
    $testName = "Test User";
    $testPassword = password_hash("test123", PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$testName, $testEmail, $testPassword]);
    echo "Test user inserted successfully!<br>";

    // Verify the user was inserted
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();
    echo "Test user found: " . ($user ? "Yes" : "No") . "<br>";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}
?> 