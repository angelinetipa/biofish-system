<?php
/**
 * BIO-FISH Application Entry Point
 * Redirects users to appropriate page based on authentication status
 */

require_once __DIR__ . '/config/init.php';

// Check if user is logged in
if (is_logged_in()) {
    // Redirect to dashboard
    redirect(BASE_URL . '/pages/dashboard/index.php');
} else {
    // Redirect to login
    redirect(BASE_URL . '/pages/auth/login.php');
}
?>