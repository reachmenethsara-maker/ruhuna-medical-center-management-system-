<?php
include('../config/db.php');

try {
    $stmt = $pdo->query("SELECT NOW() AS current_time"); // Simple query
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database connected successfully! Current time: " . $row['current_time'];
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
