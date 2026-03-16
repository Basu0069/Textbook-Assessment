<?php
include 'config.php';

if ($conn) {
    echo "✅ Database connection successful!";
    // Optional: Show all table names to confirm DB content
    // SQLite equivalent of SHOW TABLES
    $stmt = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    
    echo "<strong>Tables in DB:</strong><br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['name'] . "<br>";
    }
} else {
    echo "❌ Database connection failed.";
}
?>



to check  
http://localhost:8000/test-db.php
 