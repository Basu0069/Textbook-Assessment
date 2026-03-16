<?php
include 'config.php';

if ($conn) {
    echo "✅ Database connection successful!";
    // Optional: Show all table names to confirm DB content
    // SQLite equivalent of SHOW TABLES
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    
    echo "<br><strong>Database Structure Check:</strong><br>";
    // Check if 'users' table exists
    $usersTableExists = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->rowCount() > 0;
    if ($usersTableExists) {
        echo "✅ 'users' table found.<br>";
    } else {
        echo "❌ 'users' table not found.<br>";
    }
} else {
    echo "❌ Database connection failed.";
}
?>



to check  
http://localhost:8000/test-db.php
 