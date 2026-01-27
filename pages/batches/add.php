<?php
/**
 * Add Batch Page - ENHANCED
 * BIO-FISH Bioplastic Formation System
 * Now with quantity tracking for fish scale materials
 */

require_once __DIR__ . '/../../config/init.php';
require_login();

$success = '';
$error = '';

// Check if there's already a running batch
$has_running = is_machine_busy();

// Check material stock
$has_materials = get_available_materials_count() > 0;

// Check additive stock
$critical_additives = $conn->query("SELECT COUNT(*) as count FROM additives WHERE additive_name IN ('Glycerol', 'Distilled Water') AND quantity_ml > minimum_level");
$has_additives = $critical_additives->fetch_assoc()['count'] >= 2;

$can_start = !$has_running && $has_materials && $has_additives;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Double-check no running batch exists
    if (is_machine_busy()) {
        $error = "Cannot start new batch! A batch is currently active. Please wait until it completes or stop it first.";
    } else {
        $batch_code = clean_input($_POST['batch_code']);
        $material_ids = $_POST['materials'] ?? [];
        $material_quantities = $_POST['material_quantities'] ?? [];
        $additive_ids = $_POST['additives'] ?? [];
        $additive_quantities = $_POST['additive_quantities'] ?? [];
        
        // Validation
        if (empty($material_ids)) {
            $error = "Please select at least one fish scale material.";
        } elseif (empty($additive_ids)) {
            $error = "Please select at least one process material for production.";
        } else {
            // Validate batch code doesn't already exist
            $check = $conn->prepare("SELECT batch_id FROM batches WHERE batch_code = ?");
            $check->bind_param("s", $batch_code);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Batch code already exists! Please use a different code.";
            } else {
                // Validate material quantities
                $has_material_qty = false;
                foreach ($material_ids as $index => $mat_id) {
                    $qty = floatval($material_quantities[$index] ?? 0);
                    if ($qty > 0) {
                        $has_material_qty = true;
                        break;
                    }
                }
                
                if (!$has_material_qty) {
                    $error = "Please enter quantities for selected fish scale materials.";
                } else {
                    // Start transaction
                    $conn->begin_transaction();
                    
                    try {
                        // Use first selected material as primary
                        $primary_material_id = $material_ids[0];
                        
                        // Insert batch
                        $sql = "INSERT INTO batches (batch_code, start_time, status, current_stage, user_id, material_id) 
                                VALUES (?, NOW(), 'running', 'extraction', ?, ?)";
                        $stmt = $conn->prepare($sql);
                        
                        if (!$stmt) {
                            throw new Exception("Prepare failed: " . $conn->error);
                        }
                        
                        $stmt->bind_param("sii", $batch_code, $_SESSION['user_id'], $primary_material_id);
                        
                        if (!$stmt->execute()) {
                            throw new Exception("Execute failed: " . $stmt->error);
                        }
                        
                        $batch_id = $stmt->insert_id;
                        
                        if (!$batch_id) {
                            throw new Exception("Failed to get batch ID");
                        }
                        
                        // Update selected materials - deduct quantities
                        foreach ($material_ids as $index => $mat_id) {
                            $qty_used = floatval($material_quantities[$index] ?? 0);
                            if ($qty_used > 0) {
                                // Get current quantity
                                $mat_check = $conn->prepare("SELECT quantity_kg FROM materials WHERE material_id = ?");
                                $mat_check->bind_param("i", $mat_id);
                                $mat_check->execute();
                                $mat_result = $mat_check->get_result();
                                $current_qty = $mat_result->fetch_assoc()['quantity_kg'];
                                
                                $new_qty = $current_qty - $qty_used;
                                $new_status = $new_qty <= 0 ? 'depleted' : ($new_qty < 1.0 ? 'low_stock' : 'available');
                                
                                update_material_stock($mat_id, -$qty_used, $new_status);
                            }
                        }
                        
                        // Insert batch additives
                        $additive_sql = "INSERT INTO batch_additives (batch_id, additive_id, quantity_used_ml) VALUES (?, ?, ?)";
                        $additive_stmt = $conn->prepare($additive_sql);
                        
                        if (!$additive_stmt) {
                            throw new Exception("Additive prepare failed: " . $conn->error);
                        }
                        
                        foreach ($additive_ids as $index => $additive_id) {
                            $quantity = floatval($additive_quantities[$index]);
                            if ($quantity > 0) {
                                $additive_stmt->bind_param("iid", $batch_id, $additive_id, $quantity);
                                
                                if (!$additive_stmt->execute()) {
                                    throw new Exception("Additive insert failed: " . $additive_stmt->error);
                                }
                                
                                // Deduct from additive stock
                                update_additive_stock($additive_id, -$quantity);
                            }
                        }
                        $additive_stmt->close();
                        
                        // Insert initial process log
                        $log_sql = "INSERT INTO process_logs (batch_id, stage, temperature_celsius, notes) 
                                   VALUES (?, 'extraction', 0.00, 'Batch started - Beginning extraction phase')";
                        $log_stmt = $conn->prepare($log_sql);
                        
                        if (!$log_stmt) {
                            throw new Exception("Log prepare failed: " . $conn->error);
                        }
                        
                        $log_stmt->bind_param("i", $batch_id);
                        
                        if (!$log_stmt->execute()) {
                            throw new Exception("Log insert failed: " . $log_stmt->error);
                        }
                        $log_stmt->close();
                        
                        $conn->commit();
                        
                        set_flash('success', '✓ Batch started successfully! Machine is now running.');
                        redirect(BASE_URL . '/pages/dashboard/index.php');
                        
                    } catch (Exception $e) {
                        $conn->rollback();
                        $error = "Error starting batch: " . $e->getMessage();
                    }
                }
            }
        }
    }
}

// Get available materials
$materials = get_fish_scales('available');

// Get available additives
$additives = get_additives();

// Set page variables for template
$page_title = 'Start New Batch';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_page_title($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/auth.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/variables.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/main.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/components.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/page-specific.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/logout-modal.css">
    <script src="<?php echo JS_URL; ?>/logout-modal.js"></script>
</head>
<body>
    <?php include ROOT_PATH . '/includes/header.php'; ?>
    
    <div class="container">
        <div class="form-card">
            <h2>🚀 <?php echo $page_title; ?></h2>
            <p class="subtitle">Fill in the details below to begin a new bioplastic production cycle</p>
            
            <?php if (!$can_start): ?>
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                    </svg>
                    <div>
                        <strong>Cannot start new batch due to the following issues:</strong>
                        <ul style="margin: 8px 0 0 20px;">
                            <?php if ($has_running): ?>
                                <li>A batch is currently active. Please wait until it completes or stop it first.</li>
                            <?php endif; ?>
                            <?php if (!$has_materials): ?>
                                <li>No fish scale materials available in inventory. Please add materials first.</li>
                            <?php endif; ?>
                            <?php if (!$has_additives): ?>
                                <li>Insufficient process materials (Water or Glycerol stock is low). Please restock first.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error && !$has_running): ?>
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                    </svg>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="batchForm">
                <div class="form-group">
                    <label for="batch_code">
                        Batch Code <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="batch_code" 
                        name="batch_code" 
                        placeholder="e.g., BATCH-2026-004"
                        value="<?php echo generate_batch_code(); ?>"
                        required
                        <?php echo !$can_start ? 'disabled' : ''; ?>
                    >
                    <div class="help-text">Unique identifier for this production batch</div>
                </div>
                
                <div class="form-group">
                    <label>
                        Select Fish Scale Materials <span class="required">*</span>
                    </label>
                    <div class="selection-box">
                        <?php 
                        if ($materials->num_rows > 0):
                            while ($mat = $materials->fetch_assoc()): 
                        ?>
                            <div class="selection-item">
                                <input 
                                    type="checkbox" 
                                    name="materials[]" 
                                    value="<?php echo $mat['material_id']; ?>" 
                                    id="material_<?php echo $mat['material_id']; ?>"
                                    onchange="toggleMaterialQuantity(this)"
                                    <?php echo !$can_start ? 'disabled' : ''; ?>
                                >
                                <label for="material_<?php echo $mat['material_id']; ?>">
                                    <strong><?php echo $mat['fish_scale_type']; ?></strong><br>
                                    <small>
                                        Available: <?php echo number_format($mat['quantity_kg'], 2); ?> kg | 
                                        Source: <?php echo $mat['source_location']; ?> | 
                                        Collected: <?php echo format_date($mat['date_collected']); ?>
                                    </small>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    max="<?php echo $mat['quantity_kg']; ?>"
                                    name="material_quantities[]" 
                                    placeholder="Quantity (kg)" 
                                    disabled
                                    class="quantity-input material-qty"
                                    data-material-id="<?php echo $mat['material_id']; ?>"
                                >
                                <?php echo get_status_badge('available'); ?>
                            </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <p style="text-align: center; color: #999; padding: 20px;">
                                No fish scale materials available. Please add materials to inventory first.
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="help-text">Select fish scale material(s) and specify quantity to use for gelatin extraction</div>
                </div>
                
                <div class="form-group">
                    <label>
                        Select Process Materials <span class="required">*</span>
                    </label>
                    <div class="selection-box">
                        <?php 
                        $additives->data_seek(0);
                        while ($add = $additives->fetch_assoc()): 
                            $status = get_additive_status($add['quantity_ml'], $add['minimum_level']);
                            $out_of_stock = $add['quantity_ml'] <= 0;
                        ?>
                            <div class="selection-item">
                                <input 
                                    type="checkbox" 
                                    name="additives[]" 
                                    value="<?php echo $add['additive_id']; ?>" 
                                    id="additive_<?php echo $add['additive_id']; ?>"
                                    onchange="toggleQuantityInput(this)"
                                    <?php echo (!$can_start || $out_of_stock) ? 'disabled' : ''; ?>
                                >
                                <label for="additive_<?php echo $add['additive_id']; ?>">
                                    <strong><?php echo $add['additive_name']; ?></strong><br>
                                    <small>Stock: <?php echo number_format($add['quantity_ml'], 2); ?> mL</small>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    max="<?php echo $add['quantity_ml']; ?>"
                                    name="additive_quantities[]" 
                                    placeholder="Quantity (mL)" 
                                    disabled
                                    class="quantity-input"
                                    data-additive-id="<?php echo $add['additive_id']; ?>"
                                >
                                <?php echo get_status_badge($status); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="help-text">
                        Select materials needed for each process stage:<br>
                        • <strong>Acetic Acid:</strong> Pretreatment (demineralization)<br>
                        • <strong>Distilled Water:</strong> Extraction stage<br>
                        • <strong>Glycerol/Sorbitol:</strong> Formulation stage
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary" <?php echo !$can_start ? 'disabled' : ''; ?>>
                        ▶ Start Batch Production
                    </button>
                    <a href="<?php echo BASE_URL; ?>/pages/dashboard/index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo JS_URL; ?>/main.js"></script>
    <script>
        function toggleMaterialQuantity(checkbox) {
            const quantityInputs = document.querySelectorAll('.material-qty');
            quantityInputs.forEach(input => {
                if (input.dataset.materialId == checkbox.value) {
                    input.disabled = !checkbox.checked;
                    if (checkbox.checked) {
                        input.focus();
                    } else {
                        input.value = '';
                    }
                }
            });
        }
        
        function toggleQuantityInput(checkbox) {
            const quantityInputs = document.querySelectorAll('.quantity-input');
            quantityInputs.forEach(input => {
                if (input.dataset.additiveId == checkbox.value) {
                    input.disabled = !checkbox.checked;
                    if (checkbox.checked) {
                        input.focus();
                    } else {
                        input.value = '';
                    }
                }
            });
        }
        
        // Form validation
        document.getElementById('batchForm').addEventListener('submit', function(e) {
            const materialCheckboxes = document.querySelectorAll('input[name="materials[]"]:checked');
            if (materialCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one fish scale material.');
                return false;
            }
            
            // Check material quantities
            let hasMaterialQty = false;
            materialCheckboxes.forEach(cb => {
                const qtyInput = document.querySelector(`.material-qty[data-material-id="${cb.value}"]`);
                if (qtyInput && parseFloat(qtyInput.value) > 0) {
                    hasMaterialQty = true;
                }
            });
            
            if (!hasMaterialQty) {
                e.preventDefault();
                alert('Please enter quantities for selected fish scale materials.');
                return false;
            }
            
            const additiveCheckboxes = document.querySelectorAll('input[name="additives[]"]:checked');
            if (additiveCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one process material.');
                return false;
            }
            
            let hasAdditiveQty = false;
            additiveCheckboxes.forEach(cb => {
                const quantityInput = document.querySelector(`.quantity-input[data-additive-id="${cb.value}"]`);
                if (quantityInput && parseFloat(quantityInput.value) > 0) {
                    hasAdditiveQty = true;
                }
            });
            
            if (!hasAdditiveQty) {
                e.preventDefault();
                alert('Please enter quantities for selected process materials.');
                return false;
            }
        });
    </script>
</body>
</html>