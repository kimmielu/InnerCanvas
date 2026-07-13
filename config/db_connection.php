<?php
// ============================================================
// InnerCanvas Database Connection
// File: config/db_connection.php
// ============================================================

$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "innercanvas";

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>