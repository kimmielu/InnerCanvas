<?php
// ============================================================
// Authentication Functions
// File: includes/auth.php
// Purpose: Handle user registration, login, and session management
// ============================================================

require_once __DIR__ . '/../config/db_connection.php';

// ============================================================
// FUNCTION 1: Register a new Youth Member
// ============================================================

function registerYouthMember($username, $email, $full_name, $password, $confirm_password) {
    global $conn;
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        return ["success" => false, "message" => "All fields are required"];
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        return ["success" => false, "message" => "Passwords do not match"];
    }
    
    // Check if password is strong (minimum 8 characters)
    if (strlen($password) < 8) {
        return ["success" => false, "message" => "Password must be at least 8 characters"];
    }
    
    // Check if username already exists
    $check_username = $conn->prepare("SELECT member_id FROM YouthMember WHERE username = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    $check_username->store_result();
    
    if ($check_username->num_rows > 0) {
        return ["success" => false, "message" => "Username already exists"];
    }
    $check_username->close();
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT member_id FROM YouthMember WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        return ["success" => false, "message" => "Email already registered"];
    }
    $check_email->close();
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert new member
    $insert = $conn->prepare("INSERT INTO YouthMember (username, email, full_name, password_hash) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $username, $email, $full_name, $password_hash);
    
    if ($insert->execute()) {
        $insert->close();
        return ["success" => true, "message" => "Registration successful! You can now login."];
    } else {
        $insert->close();
        return ["success" => false, "message" => "Registration failed. Please try again."];
    }
}

// ============================================================
// FUNCTION 2: Login a Youth Member
// ============================================================

function loginYouthMember($username, $password) {
    global $conn;
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        return ["success" => false, "message" => "Username and password are required"];
    }
    
    // Find user by username
    $login = $conn->prepare("SELECT member_id, password_hash, full_name FROM YouthMember WHERE username = ? AND is_active = TRUE");
    $login->bind_param("s", $username);
    $login->execute();
    $result = $login->get_result();
    
    if ($result->num_rows == 0) {
        return ["success" => false, "message" => "Username or password is incorrect"];
    }
    
    $user = $result->fetch_assoc();
    $login->close();
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        return ["success" => false, "message" => "Username or password is incorrect"];
    }
    
    // Create session
    session_start();
    $_SESSION['member_id'] = $user['member_id'];
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['user_type'] = 'youth_member';
    
    // Update last login
    $update_login = $conn->prepare("UPDATE YouthMember SET last_login = NOW() WHERE member_id = ?");
    $update_login->bind_param("i", $user['member_id']);
    $update_login->execute();
    $update_login->close();
    
    return ["success" => true, "message" => "Login successful"];
}

// ============================================================
// FUNCTION 3: Check if user is logged in
// ============================================================

function isLoggedIn() {
    if (!isset($_SESSION['member_id'])) {
        return false;
    }
    return true;
}

// ============================================================
// FUNCTION 4: Get current logged-in user info
// ============================================================

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'member_id' => $_SESSION['member_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name']
    ];
}

// ============================================================
// FUNCTION 5: Logout user
// ============================================================

function logoutUser() {
    session_start();
    session_destroy();
    return true;
}

// ============================================================
// FUNCTION 6: Redirect if not logged in
// ============================================================

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../public/login.php");
        exit();
    }
}

?>
