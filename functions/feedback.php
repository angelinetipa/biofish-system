<?php
/**
 * Feedback Management Functions
 * BIO-FISH Bioplastic Formation System
 */

/**
 * Get all feedback
 */
function get_all_feedback($limit = null) {
    global $conn;
    
    $sql = "SELECT f.*, b.batch_code 
            FROM feedback f 
            LEFT JOIN batches b ON f.batch_id = b.batch_id 
            ORDER BY f.submitted_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return $conn->query($sql);
}

/**
 * Get feedback statistics
 */
function get_feedback_statistics() {
    global $conn;
    
    $stats = [];
    
    // Average rating
    $result = $conn->query("SELECT AVG(rating) as avg FROM feedback");
    $stats['avg_rating'] = $result->fetch_assoc()['avg'] ?? 0;
    
    // Total feedback
    $result = $conn->query("SELECT COUNT(*) as count FROM feedback");
    $stats['total_feedback'] = $result->fetch_assoc()['count'];
    
    // Total comments
    $result = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE comments IS NOT NULL AND comments != ''");
    $stats['total_comments'] = $result->fetch_assoc()['count'];
    
    // Total bug reports
    $result = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE bug_report IS NOT NULL AND bug_report != ''");
    $stats['total_bugs'] = $result->fetch_assoc()['count'];
    
    // Total feature requests
    $result = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE feature_request IS NOT NULL AND feature_request != ''");
    $stats['total_features'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

/**
 * Get feedback types for a feedback entry
 */
function get_feedback_types($feedback) {
    $types = [];
    
    if (!empty($feedback['comments'])) {
        $types[] = 'Comment';
    }
    if (!empty($feedback['bug_report'])) {
        $types[] = 'Bug';
    }
    if (!empty($feedback['feature_request'])) {
        $types[] = 'Feature';
    }
    
    return $types;
}

/**
 * Get feedback preview text
 */
function get_feedback_preview($feedback, $length = 60) {
    $text = '';
    
    if (!empty($feedback['comments'])) {
        $text = $feedback['comments'];
    } elseif (!empty($feedback['bug_report'])) {
        $text = $feedback['bug_report'];
    } elseif (!empty($feedback['feature_request'])) {
        $text = $feedback['feature_request'];
    }
    
    return truncate($text, $length);
}

/**
 * Submit feedback
 */
function submit_feedback($batch_id, $rating, $user_name, $comments, $bug_report, $feature_request) {
    global $conn;
    
    $sql = "INSERT INTO feedback (batch_id, rating, user_name, comments, bug_report, feature_request, submitted_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $batch_id, $rating, $user_name, $comments, $bug_report, $feature_request);
    
    if ($stmt->execute()) {
        log_activity('feedback_submitted', "Feedback submitted for batch ID: $batch_id");
        return true;
    }
    
    return false;
}
?>