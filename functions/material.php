<?php
/**
 * Material Management Functions
 * BIO-FISH Bioplastic Formation System
 */

/**
 * Get all fish scale materials
 */
function get_fish_scales($status = null) {
    global $conn;
    
    $sql = "SELECT * FROM materials WHERE material_type = 'fish_scales'";
    
    if ($status) {
        $sql .= " AND status = '" . $conn->real_escape_string($status) . "'";
    }
    
    $sql .= " ORDER BY date_collected DESC";
    
    return $conn->query($sql);
}

/**
 * Get all additives
 */
function get_additives() {
    global $conn;
    
    $sql = "SELECT * FROM additives ORDER BY additive_name";
    return $conn->query($sql);
}

/**
 * Get low stock count
 */
function get_low_stock_count() {
    global $conn;
    
    // Count low stock materials
    $material_result = $conn->query("SELECT COUNT(*) as count FROM materials 
                                     WHERE status = 'available' AND quantity_kg < " . LOW_STOCK_THRESHOLD_KG);
    $material_count = $material_result->fetch_assoc()['count'];
    
    // Count low stock additives
    $additive_result = $conn->query("SELECT COUNT(*) as count FROM additives 
                                     WHERE quantity_ml <= minimum_level");
    $additive_count = $additive_result->fetch_assoc()['count'];
    
    return $material_count + $additive_count;
}

/**
 * Get available materials count
 */
function get_available_materials_count() {
    global $conn;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM materials WHERE status = 'available'");
    return $result->fetch_assoc()['count'];
}

/**
 * Get additive stock status
 */
function get_additive_status($quantity_ml, $minimum_level) {
    if ($quantity_ml <= 0) {
        return 'depleted';
    } elseif ($quantity_ml <= $minimum_level) {
        return 'low_stock';
    } else {
        return 'available';
    }
}

/**
 * Get material status based on quantity
 */
function get_material_status($quantity_kg) {
    if ($quantity_kg <= 0) {
        return 'depleted';
    } elseif ($quantity_kg < LOW_STOCK_THRESHOLD_KG) {
        return 'low_stock';
    } else {
        return 'available';
    }
}

/**
 * Update material stock
 */
function update_material_stock($material_id, $quantity_change, $new_status = null) {
    global $conn;
    
    if ($new_status) {
        $sql = "UPDATE materials SET quantity_kg = quantity_kg + ?, status = ? WHERE material_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsi", $quantity_change, $new_status, $material_id);
    } else {
        $sql = "UPDATE materials SET quantity_kg = quantity_kg + ? WHERE material_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $quantity_change, $material_id);
    }
    
    return $stmt->execute();
}

/**
 * Update additive stock
 */
function update_additive_stock($additive_id, $quantity_change) {
    global $conn;
    
    $sql = "UPDATE additives SET quantity_ml = quantity_ml + ?, last_restocked = CURDATE() WHERE additive_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $quantity_change, $additive_id);
    
    return $stmt->execute();
}
?>