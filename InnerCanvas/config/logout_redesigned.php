<?php
// ============================================================
// Logout Script
// File: config/logout.php
// Purpose: Handle user logout and session destruction
// ============================================================

require_once 'db_connection.php';
require_once '../includes/auth.php';

// Logout user
logoutUser();

// Redirect to login page
header("Location: ../pages/public/login_redeigned.php");
exit();
?>