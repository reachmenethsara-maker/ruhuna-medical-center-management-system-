<?php
// Database credentials
$host = "localhost";       // usually localhost
$user = "root";            // your MySQL username
$pass = "";                // your MySQL password
$db   = "mcdatabase";      // your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
