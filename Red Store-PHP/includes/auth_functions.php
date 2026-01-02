<?php
// User registration function
function registerUser($conn, $name, $email, $password) {
    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
    
    if (mysqli_stmt_execute($stmt)) {
        $user_id = mysqli_insert_id($conn);
        return ['success' => true, 'user_id' => $user_id];
    } else {
        return ['success' => false, 'message' => 'Registration failed: ' . mysqli_error($conn)];
    }
}

// User login function
function loginUser($conn, $email, $password) {
    $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Update last login time for admin
            if ($user['role'] == 'admin') {
                $sql = "INSERT INTO admin_settings (user_id, last_login) 
                        VALUES (?, NOW()) 
                        ON DUPLICATE KEY UPDATE last_login = NOW()";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user['id']);
                mysqli_stmt_execute($stmt);
            }
            
            return ['success' => true, 'role' => $user['role']];
        } else {
            return ['success' => false, 'message' => 'Invalid password'];
        }
    } else {
        return ['success' => false, 'message' => 'User not found'];
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

// Logout user
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "/login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: " . SITE_URL . "/login.php");
        exit();
    }
}

// Get user details
function getUserDetails($conn, $user_id) {
    $sql = "SELECT id, name, email, role, address, city, zip_code, phone, created_at FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Update user profile
function updateUserProfile($conn, $user_id, $name, $address, $city, $zip_code, $phone) {
    $sql = "UPDATE users SET name = ?, address = ?, city = ?, zip_code = ?, phone = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $name, $address, $city, $zip_code, $phone, $user_id);
    
    return mysqli_stmt_execute($stmt);
}

// Change user password
function changeUserPassword($conn, $user_id, $current_password, $new_password) {
    // Get current password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify current password
        if (password_verify($current_password, $user['password'])) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            
            return mysqli_stmt_execute($stmt);
        } else {
            return false;
        }
    }
    
    return false;
}
?>

