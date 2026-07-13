<?php
// ============================================================
// InnerCanvas Authentication Functions
// File: includes/auth.php
// ============================================================

require_once dirname(__DIR__) . '/config/db_connection.php';

// ============================================================
// START SESSION
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// REGISTER YOUTH MEMBER
// ============================================================
function registerYouthMember($full_name, $username, $email, $password) {
    global $conn;
    
    // Check if username/email exists
    $check_query = "SELECT member_id FROM YouthMember WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'error' => 'Username or email already exists'];
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Insert new member
    $insert_query = "INSERT INTO YouthMember (full_name, username, email, password_hash, admin_role, is_active) 
                     VALUES (?, ?, ?, ?, 'none', 1)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $username, $email, $password_hash);
    
    if (mysqli_stmt_execute($stmt)) {
        $member_id = mysqli_insert_id($conn);
        $_SESSION['member_id'] = $member_id;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['admin_role'] = 'none';
        return ['success' => true, 'member_id' => $member_id];
    }
    
    return ['success' => false, 'error' => 'Registration failed'];
}

// ============================================================
// LOGIN YOUTH MEMBER / ADMIN
// ============================================================
function loginYouthMember($username, $password) {
    global $conn;
    
    $query = "SELECT member_id, full_name, username, email, password_hash, admin_role FROM YouthMember 
              WHERE (username = ? OR email = ?) AND is_active = 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if (!$user) {
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    // Check password
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    // Set session
    $_SESSION['member_id'] = $user['member_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['admin_role'] = $user['admin_role'];
    
    return ['success' => true, 'admin_role' => $user['admin_role']];
}

// ============================================================
// CHECK IF LOGGED IN
// ============================================================
function isLoggedIn() {
    return isset($_SESSION['member_id']) && !empty($_SESSION['member_id']);
}

// ============================================================
// GET CURRENT USER
// ============================================================
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'member_id' => $_SESSION['member_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'email' => $_SESSION['email'] ?? '',
            'admin_role' => $_SESSION['admin_role'] ?? 'none'
        ];
    }
    return null;
}

// ============================================================
// REQUIRE LOGIN
// ============================================================
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../../pages/public/login.php");
        exit();
    }
}

// ============================================================
// LOGOUT USER
// ============================================================
function logoutUser() {
    session_destroy();
    header("Location: ../../pages/public/login.php");
    exit();
}
?>