<!-- Tab 2: Material Inventory -->
<div id="inventory" class="tab-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Material Inventory</h2>
        <a href="<?php echo BASE_URL; ?>/pages/materials/add.php">
            <button class="btn-primary btn-icon" style="padding: 12px 24px; cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                </svg>
                Add Material
            </button>
        </a>
    </div>
    
    <h3>Fish Scales Stock</h3>
    <div class="table-scroll-container">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Source Location</th>
                    <th>Quantity (kg)</th>
                    <th>Date Collected</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $materials = $conn->query("SELECT * FROM materials WHERE material_type = 'fish_scales' ORDER BY date_collected DESC");
                $material_count = 0;
                while ($mat = $materials->fetch_assoc()):
                    $material_count++;
                ?>
                <tr>
                    <td><?php echo $mat['fish_scale_type']; ?></td>
                    <td><?php echo $mat['source_location']; ?></td>
                    <td><?php echo number_format($mat['quantity_kg'], 2); ?></td>
                    <td><?php echo date('M d, Y', strtotime($mat['date_collected'])); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $mat['status']; ?>">
                            <?php 
                            echo $mat['status'] == 'available' ? 'Available' : 
                                ($mat['status'] == 'low_stock' ? 'Low Stock' : 
                                ($mat['status'] == 'depleted' ? 'Depleted' : ucfirst($mat['status'])));
                            ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($material_count == 0): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 30px;">
                        No fish scale materials in inventory. Click "Add Material" to add items.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($material_count > 5): ?>
    <div class="scroll-hint">
        <small>Scroll to see all <?php echo $material_count; ?> items →</small>
    </div>
    <?php endif; ?>
    
    <h3 style="margin-top: 40px;">Process Materials / Additives Stock</h3>
    <div class="table-scroll-container">
        <table>
            <thead>
                <tr>
                    <th>Material Name</th>
                    <th>Current Stock (mL)</th>
                    <th>Minimum Level (mL)</th>
                    <th>Last Restocked</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $additives = $conn->query("SELECT * FROM additives ORDER BY additive_name");
                $additive_count = 0;
                while ($add = $additives->fetch_assoc()):
                    $additive_count++;
                    $is_depleted = $add['quantity_ml'] <= 0;
                    $is_low = $add['quantity_ml'] > 0 && $add['quantity_ml'] <= $add['minimum_level'];
                    $status = $is_depleted ? 'depleted' : ($is_low ? 'low_stock' : 'available');
                ?>
                <tr>
                    <td><?php echo $add['additive_name']; ?></td>
                    <td><?php echo number_format($add['quantity_ml'], 2); ?></td>
                    <td><?php echo number_format($add['minimum_level'], 2); ?></td>
                    <td><?php echo $add['last_restocked'] ? date('M d, Y', strtotime($add['last_restocked'])) : '—'; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $status; ?>">
                            <?php 
                            echo $status == 'available' ? 'Available' : 
                                ($status == 'low_stock' ? 'Low Stock' : 'Depleted');
                            ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($additive_count == 0): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 30px;">
                        No process materials in inventory. Click "Add Material" to add items.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($additive_count > 5): ?>
    <div class="scroll-hint">
        <small>Scroll to see all <?php echo $additive_count; ?> items →</small>
    </div>
    <?php endif; ?>
</div>