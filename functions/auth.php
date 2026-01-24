<?php
/**
 * Authentication Functions
 * BIO-FISH Bioplastic Formation System
 */

/**
 * Attempt user login
 */
function attempt_login($username, $password) {
    global $conn;
    
    $username = clean_input($username);
    
    $sql = "SELECT user_id, username, password, full_name, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // For demo: password is 'password123' for all users
        // In production, use: password_verify($password, $user['password'])
        if ($password === 'password123') {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            // Log activity
            log_activity('login', 'User logged in successfully');
            
            return true;
        }
    }
    
    return false;
}

/**
 * Logout user
 */
function logout_user() {
    // Log activity before destroying session
    log_activity('logout', 'User logged out');
    
    // Destroy session
    session_destroy();
    
    // Redirect to login
    redirect(BASE_URL . '/pages/auth/login.php');
}

/**
 * Hash password (for production use)
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password (for production use)
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}
?>