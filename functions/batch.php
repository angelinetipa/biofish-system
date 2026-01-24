<?php
/**
 * Batch Management Functions
 * BIO-FISH Bioplastic Formation System
 */

/**
 * Get all batches
 */
function get_all_batches($limit = null) {
    global $conn;
    
    $sql = "SELECT b.*, m.fish_scale_type, u.full_name 
            FROM batches b 
            LEFT JOIN materials m ON b.material_id = m.material_id 
            LEFT JOIN users u ON b.user_id = u.user_id 
            WHERE b.status NOT IN ('cleaning')
            ORDER BY b.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return $conn->query($sql);
}

/**
 * Get running batch
 */
function get_running_batch() {
    global $conn;
    
    $sql = "SELECT b.*, m.fish_scale_type, u.full_name 
            FROM batches b 
            LEFT JOIN materials m ON b.material_id = m.material_id 
            LEFT JOIN users u ON b.user_id = u.user_id 
            WHERE b.status IN ('running', 'paused', 'cleaning') 
            ORDER BY b.start_time DESC LIMIT 1";
    
    $result = $conn->query($sql);
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/**
 * Get batch by ID
 */
function get_batch_by_id($batch_id) {
    global $conn;
    
    $sql = "SELECT b.*, m.fish_scale_type, u.full_name 
            FROM batches b 
            LEFT JOIN materials m ON b.material_id = m.material_id 
            LEFT JOIN users u ON b.user_id = u.user_id 
            WHERE b.batch_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/**
 * Check if machine is busy
 */
function is_machine_busy() {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM batches WHERE status IN ('running', 'paused', 'cleaning')";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

/**
 * Get machine status
 */
function get_machine_status() {
    global $conn;
    
    $sql = "SELECT status, batch_code, current_stage 
            FROM batches 
            WHERE status IN ('running', 'paused', 'cleaning') 
            ORDER BY start_time DESC LIMIT 1";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return ['status' => 'idle', 'batch_code' => null, 'current_stage' => null];
}

/**
 * Get batch statistics
 */
function get_batch_statistics() {
    global $conn;
    
    $stats = [];
    
    // Total batches
    $result = $conn->query("SELECT COUNT(*) as count FROM batches WHERE status NOT IN ('cleaning')");
    $stats['total_batches'] = $result->fetch_assoc()['count'];
    
    // Completed batches
    $result = $conn->query("SELECT COUNT(*) as count FROM batches WHERE status='completed'");
    $stats['completed_batches'] = $result->fetch_assoc()['count'];
    
    // Success rate
    $stats['success_rate'] = $stats['total_batches'] > 0 
        ? round(($stats['completed_batches'] / $stats['total_batches']) * 100, 1) 
        : 0;
    
    // Average processing time
    $result = $conn->query("SELECT AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_time 
                           FROM batches WHERE end_time IS NOT NULL AND status='completed'");
    $stats['avg_time'] = round($result->fetch_assoc()['avg_time'], 0);
    
    return $stats;
}

/**
 * Update batch status
 */
function update_batch_status($batch_id, $status, $end_time = null) {
    global $conn;
    
    if ($end_time) {
        $sql = "UPDATE batches SET status = ?, end_time = ? WHERE batch_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $end_time, $batch_id);
    } else {
        $sql = "UPDATE batches SET status = ? WHERE batch_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $batch_id);
    }
    
    return $stmt->execute();
}
?>