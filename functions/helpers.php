<?php
/**
 * Helper Functions
 * BIO-FISH Bioplastic Formation System
 */

/**
 * Clean and sanitize user input
 */
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

/**
 * Redirect to a page
 */
function redirect($url, $delay = 0) {
    if ($delay > 0) {
        header("refresh:$delay;url=$url");
    } else {
        header("Location: $url");
        exit();
    }
}

/**
 * Format date/time
 */
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function format_datetime($datetime, $format = 'M d, Y h:i A') {
    return date($format, strtotime($datetime));
}

/**
 * Calculate duration in minutes
 */
function calculate_duration($start, $end) {
    $start_time = strtotime($start);
    $end_time = strtotime($end);
    return round(($end_time - $start_time) / 60);
}

/**
 * Get status badge HTML
 */
function get_status_badge($status) {
    $badges = [
        'running' => '<span class="status-badge status-running">Running</span>',
        'paused' => '<span class="status-badge status-paused">Paused</span>',
        'completed' => '<span class="status-badge status-completed">Completed</span>',
        'stopped' => '<span class="status-badge status-stopped">Stopped</span>',
        'cleaning' => '<span class="status-badge status-cleaning">Cleaning</span>',
        'available' => '<span class="status-badge status-available">Available</span>',
        'low_stock' => '<span class="status-badge status-low_stock">Low Stock</span>',
        'depleted' => '<span class="status-badge status-depleted">Depleted</span>',
    ];
    
    return $badges[$status] ?? '<span class="status-badge">' . ucfirst($status) . '</span>';
}

/**
 * Generate unique batch code
 */
function generate_batch_code() {
    return 'BATCH-' . date('Ymd-His');
}

/**
 * Check if stock is low
 */
function is_low_stock($quantity, $minimum) {
    return $quantity <= $minimum;
}

/**
 * Display alert message
 */
function show_alert($type, $message) {
    $icons = [
        'success' => '✓',
        'error' => '✕',
        'warning' => '⚠',
        'info' => 'ℹ'
    ];
    
    $icon = $icons[$type] ?? 'ℹ';
    
    return '<div class="alert alert-' . $type . '">
        <span style="font-size: 20px;">' . $icon . '</span>
        <span>' . htmlspecialchars($message) . '</span>
    </div>';
}

/**
 * Display flash message if exists
 */
function display_flash() {
    $flash = get_flash();
    if ($flash) {
        echo show_alert($flash['type'], $flash['message']);
    }
}

/**
 * Validate required fields
 */
function validate_required($fields) {
    $errors = [];
    foreach ($fields as $name => $value) {
        if (empty($value)) {
            $errors[] = ucfirst(str_replace('_', ' ', $name)) . " is required.";
        }
    }
    return $errors;
}

/**
 * Debug function (only in development)
 */
function dd($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die();
    }
}

/**
 * Log activity
 */
function log_activity($action, $details = '') {
    global $conn;
    
    // Check if activity_logs table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'activity_logs'");
    if ($table_check->num_rows == 0) {
        // Table doesn't exist, skip logging silently
        return false;
    }
    
    $user_id = get_user_id();
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("isss", $user_id, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
        return true;
    }
    return false;
}

/**
 * Get page title
 */
function get_page_title($page = '') {
    return $page ? $page . ' - ' . APP_NAME : APP_NAME;
}

/**
 * Generate star rating HTML
 */
function get_star_rating($rating) {
    return str_repeat('⭐', $rating);
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}
?>