<?php
// ============================================================
// InnerCanvas Database Connection
// File: config/db_connection.php
// Purpose: Establish connection to MySQL database
// ============================================================

// Database credentials
$db_host = "localhost";      // MySQL server location
$db_username = "root";       // MySQL username (XAMPP default)
$db_password = "";           // MySQL password (XAMPP default - blank)
$db_name = "innercanvas";    // Database name

// ============================================================
// Create connection
// ============================================================

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// ============================================================
// Check for connection errors
// ============================================================

if ($conn->connect_error) {
    // If connection fails, stop and show error
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8 for proper text handling
$conn->set_charset("utf8mb4");

// ============================================================
// Connection successful
// ============================================================

// Optional: Uncomment for debugging (remove in production)
// echo "Connected to InnerCanvas database successfully!";

?>