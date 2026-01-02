<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth_functions.php';
require_once '../includes/admin_functions.php';

// Require admin login
requireAdmin();

// Check if user ID and role are provided
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['role']) || empty($_GET['role'])) {
    header("Location: users.php");
    exit();
}

$user_id = $_GET['id'];
$new_role = $_GET['role'];

// Prevent changing own role
if ($user_id == $_SESSION['user_id']) {
    header("Location: users.php?error=cannot_change_own_role");
    exit();
}

// Validate role
if ($new_role != 'admin' && $new_role != 'user') {
    header("Location: users.php?error=invalid_role");
    exit();
}

// Change user role
if (changeUserRole($conn, $user_id, $new_role)) {
    // Redirect with success message
    header("Location: users.php?role_changed=1");
} else {
    // Redirect with error message
    header("Location: users.php?error=1");
}
exit();
