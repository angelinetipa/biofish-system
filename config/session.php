<?php
/**
 * Session Management
 * BIO-FISH Bioplastic Formation System
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Secure session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    
    session_start();
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require login - redirect if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        redirect(BASE_URL . '/pages/auth/login.php');
        exit();
    }
}

/**
 * Check if user is admin
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require admin access
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect(BASE_URL . '/pages/dashboard/index.php');
        exit();
    }
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 */
function get_user_name() {
    return $_SESSION['full_name'] ?? 'Guest';
}

/**
 * Get current user role
 */
function get_user_role() {
    return $_SESSION['role'] ?? 'guest';
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
?>