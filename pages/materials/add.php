<?php
/**
 * Add Material Page
 * BIO-FISH Bioplastic Formation System
 */

require_once __DIR__ . '/../../config/init.php';
require_login();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_type = clean_input($_POST['item_type']);
    
    if ($item_type == 'fish_scales') {
        // Handle fish scale material
        $fish_scale_type = clean_input($_POST['fish_scale_type']);
        $source_location = clean_input($_POST['source_location']);
        $quantity_kg = floatval($_POST['quantity_kg']);
        $date_collected = clean_input($_POST['date_collected']);
        
        // Validation
        $errors = validate_required([
            'fish_scale_type' => $fish_scale_type,
            'source_location' => $source_location,
            'date_collected' => $date_collected
        ]);
        
        if (!empty($errors)) {
            $error = implode('<br>', $errors);
        } elseif ($quantity_kg <= 0) {
            $error = "Quantity must be greater than 0.";
        } elseif (strtotime($date_collected) > time()) {
            $error = "Collection date cannot be in the future.";
        } else {
            // Determine status
            $status = get_material_status($quantity_kg);
            
            $sql = "INSERT INTO materials (material_type, fish_scale_type, source_location, quantity_kg, date_collected, status) 
                    VALUES ('fish_scales', ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdss", $fish_scale_type, $source_location, $quantity_kg, $date_collected, $status);
            
            if ($stmt->execute()) {
                log_activity('material_added', "Fish scale material added: $fish_scale_type, $quantity_kg kg");
                set_flash('success', '✓ Fish scale material added successfully to inventory!');
                redirect(BASE_URL . '/pages/dashboard/index.php');
            } else {
                $error = "Error adding material: " . $conn->error;
            }
        }
        
    } elseif ($item_type == 'additive') {
        // Handle additive/process material
        $additive_name = $_POST['additive_name'] == 'custom' 
            ? clean_input($_POST['custom_additive_name']) 
            : clean_input($_POST['additive_name']);
        $quantity_ml = floatval($_POST['quantity_ml']);
        $minimum_level = floatval($_POST['minimum_level']);
        
        // Validation
        if (empty($additive_name)) {
            $error = "Additive name is required.";
        } elseif ($quantity_ml <= 0) {
            $error = "Quantity must be greater than 0.";
        } elseif ($minimum_level < 0) {
            $error = "Minimum level cannot be negative.";
        } else {
            // Check if additive already exists
            $check = $conn->prepare("SELECT additive_id FROM additives WHERE additive_name = ?");
            $check->bind_param("s", $additive_name);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows > 0) {
                // Update existing additive stock
                if (update_additive_stock($result->fetch_assoc()['additive_id'], $quantity_ml)) {
                    log_activity('additive_restocked', "Additive restocked: $additive_name, +$quantity_ml mL");
                    set_flash('success', "✓ Additive stock updated successfully! Added $quantity_ml mL to existing stock.");
                    redirect(BASE_URL . '/pages/dashboard/index.php');
                } else {
                    $error = "Error updating additive: " . $conn->error;
                }
            } else {
                // Insert new additive
                $sql = "INSERT INTO additives (additive_name, quantity_ml, minimum_level, last_restocked) 
                        VALUES (?, ?, ?, CURDATE())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdd", $additive_name, $quantity_ml, $minimum_level);
                
                if ($stmt->execute()) {
                    log_activity('additive_added', "New additive added: $additive_name, $quantity_ml mL");
                    set_flash('success', '✓ New additive added successfully to inventory!');
                    redirect(BASE_URL . '/pages/dashboard/index.php');
                } else {
                    $error = "Error adding additive: " . $conn->error;
                }
            }
        }
    }
}

$page_title = 'Add Inventory Item';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_page_title($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/main.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/components.css">
    <style>
        .conditional-fields {
            display: none;
        }
        .conditional-fields.active {
            display: block;
        }
    </style>
</head>
<body>
    <?php include ROOT_PATH . '/includes/header.php'; ?>
    
    <div class="container">
        <div class="form-card">
            <h2>📦 <?php echo $page_title; ?></h2>
            <p class="subtitle">Add fish scale materials or process additives to inventory</p>
            
            <?php if ($success): ?>
                <?php echo show_alert('success', $success); ?>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <?php echo show_alert('error', $error); ?>
            <?php endif; ?>
            
            <form method="POST" id="materialForm">
                <div class="form-group">
                    <label>Item Type <span class="required">*</span></label>
                    <select name="item_type" id="item_type" required onchange="toggleFields()">
                        <option value="">-- Select Type --</option>
                        <option value="fish_scales">Fish Scale Material</option>
                        <option value="additive">Process Material / Additive</option>
                    </select>
                    <div class="help-text">Choose whether you're adding fish scales or process materials</div>
                </div>
                
                <!-- FISH SCALE FIELDS -->
                <div id="fish_scale_fields" class="conditional-fields">
                    <div class="form-group">
                        <label>Fish Scale Type <span class="required">*</span></label>
                        <select name="fish_scale_type" id="fish_scale_type_select">
                            <option value="">-- Select Fish Type --</option>
                            <option value="Tilapia">Tilapia</option>
                            <option value="Bangus">Bangus (Milkfish)</option>
                            <option value="Galunggong">Galunggong (Round Scad)</option>
                            <option value="Maya-maya">Maya-maya (Red Snapper)</option>
                            <option value="Lapu-lapu">Lapu-lapu (Grouper)</option>
                            <option value="Tanigue">Tanigue (Spanish Mackerel)</option>
                            <option value="Dalagang Bukid">Dalagang Bukid (Yellow Tail Fusilier)</option>
                            <option value="Mixed">Mixed Species</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Source Location <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="source_location" 
                            id="source_location"
                            placeholder="e.g., Marikina Public Market - Stall 5"
                        >
                        <div class="help-text">Specify the market stall or vendor location</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity (kg) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0.01"
                            name="quantity_kg" 
                            id="quantity_kg"
                            placeholder="e.g., 5.50"
                        >
                        <div class="help-text">Enter weight in kilograms. Status: Available if ≥1kg, Low Stock if <1kg</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Date Collected <span class="required">*</span></label>
                        <input 
                            type="date" 
                            name="date_collected" 
                            id="date_collected"
                            value="<?php echo date('Y-m-d'); ?>" 
                            max="<?php echo date('Y-m-d'); ?>"
                        >
                        <div class="help-text">Date when the fish scales were collected</div>
                    </div>
                </div>
                
                <!-- ADDITIVE FIELDS -->
                <div id="additive_fields" class="conditional-fields">
                    <div class="form-group">
                        <label>Additive/Material Name <span class="required">*</span></label>
                        <select name="additive_name" id="additive_name_select" onchange="toggleCustomName()">
                            <option value="">-- Select Material --</option>
                            <option value="Glycerol">Glycerol</option>
                            <option value="Sorbitol">Sorbitol</option>
                            <option value="Distilled Water">Distilled Water</option>
                            <option value="Acetic Acid (0.6M)">Acetic Acid (0.6M)</option>
                            <option value="custom">Custom / Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="custom_name_field" style="display: none;">
                        <label>Custom Material Name <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="custom_additive_name" 
                            id="custom_additive_name"
                            placeholder="Enter material name"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity (mL) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0.01"
                            name="quantity_ml" 
                            id="quantity_ml"
                            placeholder="e.g., 1000.00"
                        >
                        <div class="help-text">Enter volume in milliliters. If item exists, this will be added to current stock.</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Minimum Stock Level (mL) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0"
                            name="minimum_level" 
                            id="minimum_level"
                            value="500.00"
                            placeholder="e.g., 500.00"
                        >
                        <div class="help-text">Low stock alert triggers when quantity falls at or below this level</div>
                    </div>
                </div>
                
                <div class="btn-group" style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">✓ Add to Inventory</button>
                    <a href="<?php echo BASE_URL; ?>/pages/dashboard/index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo JS_URL; ?>/main.js"></script>
    <script>
        function toggleFields() {
            const itemType = document.getElementById('item_type').value;
            const fishScaleFields = document.getElementById('fish_scale_fields');
            const additiveFields = document.getElementById('additive_fields');
            
            // Hide both
            fishScaleFields.classList.remove('active');
            additiveFields.classList.remove('active');
            
            // Clear required
            document.querySelectorAll('#fish_scale_fields input, #fish_scale_fields select').forEach(el => {
                el.removeAttribute('required');
            });
            document.querySelectorAll('#additive_fields input, #additive_fields select').forEach(el => {
                el.removeAttribute('required');
            });
            
            // Show and set required based on selection
            if (itemType === 'fish_scales') {
                fishScaleFields.classList.add('active');
                document.getElementById('fish_scale_type_select').setAttribute('required', 'required');
                document.getElementById('source_location').setAttribute('required', 'required');
                document.getElementById('quantity_kg').setAttribute('required', 'required');
                document.getElementById('date_collected').setAttribute('required', 'required');
            } else if (itemType === 'additive') {
                additiveFields.classList.add('active');
                document.getElementById('additive_name_select').setAttribute('required', 'required');
                document.getElementById('quantity_ml').setAttribute('required', 'required');
                document.getElementById('minimum_level').setAttribute('required', 'required');
            }
        }
        
        function toggleCustomName() {
            const select = document.getElementById('additive_name_select');
            const customField = document.getElementById('custom_name_field');
            const customInput = document.getElementById('custom_additive_name');
            
            if (select.value === 'custom') {
                customField.style.display = 'block';
                customInput.setAttribute('required', 'required');
                // Override the name attribute
                select.removeAttribute('name');
                customInput.setAttribute('name', 'additive_name');
            } else {
                customField.style.display = 'none';
                customInput.removeAttribute('required');
                select.setAttribute('name', 'additive_name');
                customInput.removeAttribute('name');
            }
        }
        
        // Prevent future dates
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('date_collected');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate > today) {
                        alert('Collection date cannot be in the future.');
                        this.value = '<?php echo date('Y-m-d'); ?>';
                    }
                });
            }
        });
    </script>
</body>
</html>