<!-- Tab 1: Process Monitoring -->
<div id="monitoring" class="tab-content active">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <h2>Real-Time Process Monitoring</h2>
        <a href="<?php echo BASE_URL; ?>/pages/batches/add.php">
            <button class="btn-primary btn-icon" style="padding: 12px 24px; cursor: pointer;" <?php echo $running_batch ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                </svg>
                Start New Batch
            </button>
        </a>
    </div>
    
    <!-- Last Updated Display -->
    <div style="text-align: right; color: #666; font-size: 13px; margin-bottom: 20px;">
        Last updated: <span id="lastUpdate" style="font-weight: 600; color: #4A90A4;"></span>
        <span id="countdown" style="color: #999; margin-left: 10px;"></span>
    </div>
    
    <?php if ($running_batch): ?>
    <div class="process-box">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div>
                <h3>Current Batch: <?php echo $running_batch['batch_code']; ?></h3>
                <p><strong>Operator:</strong> <?php echo $running_batch['full_name']; ?></p>
                <p><strong>Fish Scale Type:</strong> <?php echo $running_batch['fish_scale_type'] ?? 'N/A'; ?></p>
                <p><strong>Start Time:</strong> <?php echo date('M d, Y h:i A', strtotime($running_batch['start_time'])); ?></p>
                <p><strong>Current Stage:</strong> 
                    <span class="status-badge status-<?php echo $running_batch['status']; ?>">
                        <?php echo strtoupper(str_replace('_', ' ', $running_batch['current_stage'] ?? $running_batch['status'])); ?>
                    </span>
                </p>
            </div>
            
            <!-- Machine Control Buttons -->
            <div class="machine-controls" style="display: flex; flex-direction: column; gap: 10px;">
                <?php if ($running_batch['status'] == 'cleaning'): ?>
                    <button class="control-btn cleaning-end-btn btn-icon" onclick="controlMachine('end_cleaning', <?php echo $running_batch['batch_id']; ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                        </svg>
                        End Cleaning
                    </button>
                <?php else: ?>
                    <?php if ($running_batch['status'] == 'running'): ?>
                        <button class="control-btn pause-btn btn-icon" onclick="controlMachine('pause', <?php echo $running_batch['batch_id']; ?>)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M14,19H18V5H14M6,19H10V5H6V19Z"/>
                            </svg>
                            Pause
                        </button>
                    <?php elseif ($running_batch['status'] == 'paused'): ?>
                        <button class="control-btn continue-btn btn-icon" onclick="controlMachine('continue', <?php echo $running_batch['batch_id']; ?>)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                            </svg>
                            Continue
                        </button>
                    <?php endif; ?>
                    
                    <button class="control-btn stop-btn btn-icon" onclick="confirmStop(<?php echo $running_batch['batch_id']; ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M18,18H6V6H18V18Z"/>
                        </svg>
                        Stop
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($running_batch['status'] != 'cleaning'): ?>
        <div class="process-stage">
            <div class="stage-item <?php echo $running_batch['current_stage'] == 'extraction' ? 'active' : ''; ?> <?php echo in_array($running_batch['current_stage'], ['filtration', 'formulation', 'film_formation']) ? 'completed' : ''; ?>">
                <strong>1. Extraction</strong>
                <p>60-80°C</p>
                <small>4 hours</small>
            </div>
            <div class="stage-item <?php echo $running_batch['current_stage'] == 'filtration' ? 'active' : ''; ?> <?php echo in_array($running_batch['current_stage'], ['formulation', 'film_formation']) ? 'completed' : ''; ?>">
                <strong>2. Filtration</strong>
                <p>~25°C</p>
                <small>15 min</small>
            </div>
            <div class="stage-item <?php echo $running_batch['current_stage'] == 'formulation' ? 'active' : ''; ?> <?php echo $running_batch['current_stage'] == 'film_formation' ? 'completed' : ''; ?>">
                <strong>3. Formulation</strong>
                <p>~80°C</p>
                <small>1 hour</small>
            </div>
            <div class="stage-item <?php echo $running_batch['current_stage'] == 'film_formation' ? 'active' : ''; ?>">
                <strong>4. Film Formation</strong>
                <p>Air Dry</p>
                <small>3 days</small>
            </div>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; background: rgba(255, 243, 224, 0.5); border-radius: 20px; box-shadow: inset 3px 3px 6px rgba(0,0,0,0.05), inset -3px -3px 6px rgba(255,255,255,0.9);">
            <div class="cleaning-icon" style="margin-bottom: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19.36,2.72L20.78,4.14L15.06,9.85C16.13,11.39 16.28,13.24 15.38,14.44L9.06,8.12C10.26,7.22 12.11,7.37 13.65,8.44L19.36,2.72M5.93,17.57C3.92,15.56 2.69,13.16 2.35,10.92L7.23,8.83L14.67,16.27L12.58,21.15C10.34,20.81 7.94,19.58 5.93,17.57Z"/>
                </svg>
            </div>
            <h3 style="color: #e65100;">Cleaning Mode Active</h3>
            <p style="color: #666;">Machine is undergoing maintenance cleaning</p>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="process-box">
        <div style="text-align: center; padding: 40px;">
            <div class="idle-icon" style="margin-bottom: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M7,9.5A1.5,1.5 0 0,0 5.5,11A1.5,1.5 0 0,0 7,12.5A1.5,1.5 0 0,0 8.5,11A1.5,1.5 0 0,0 7,9.5M17,9.5A1.5,1.5 0 0,0 15.5,11A1.5,1.5 0 0,0 17,12.5A1.5,1.5 0 0,0 18.5,11A1.5,1.5 0 0,0 17,9.5M12,17.23C10.25,17.23 8.71,16.5 7.81,15.42L9.23,14C9.68,14.72 10.75,15.23 12,15.23C13.25,15.23 14.32,14.72 14.77,14L16.19,15.42C15.29,16.5 13.75,17.23 12,17.23Z"/>
                </svg>
            </div>
            <p style="text-align: center; color: #666; font-size: 16px; margin-bottom: 20px;">Machine is idle - No batch currently running</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="<?php echo BASE_URL; ?>/pages/batches/add.php">
                    <button class="btn-primary btn-icon" style="padding: 12px 24px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                        </svg>
                        Start New Batch
                    </button>
                </a>
                <button class="control-btn cleaning-btn btn-icon" onclick="confirmCleaning()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19.36,2.72L20.78,4.14L15.06,9.85C16.13,11.39 16.28,13.24 15.38,14.44L9.06,8.12C10.26,7.22 12.11,7.37 13.65,8.44L19.36,2.72M5.93,17.57C3.92,15.56 2.69,13.16 2.35,10.92L7.23,8.83L14.67,16.27L12.58,21.15C10.34,20.81 7.94,19.58 5.93,17.57Z"/>
                    </svg>
                    Start Cleaning Mode
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <h3 style="margin-top: 30px;">Recent Batches</h3>
    <div class="table-scroll-container">
        <table>
            <thead>
                <tr>
                    <th>Batch Code</th>
                    <th>Status</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Fish Scale Type</th>
                    <th>Operator</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $batches = $conn->query("SELECT b.*, m.fish_scale_type, u.full_name 
                    FROM batches b 
                    LEFT JOIN materials m ON b.material_id = m.material_id 
                    LEFT JOIN users u ON b.user_id = u.user_id 
                    WHERE b.status NOT IN ('cleaning')
                    ORDER BY b.created_at DESC LIMIT 10");
                while ($batch = $batches->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $batch['batch_code']; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $batch['status']; ?>">
                            <?php echo ucfirst($batch['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y h:i A', strtotime($batch['start_time'])); ?></td>
                    <td>
                        <?php 
                        echo $batch['end_time'] 
                            ? date('M d, Y h:i A', strtotime($batch['end_time'])) 
                            : '<span style="color: #999;">—</span>';
                        ?>
                    </td>
                    <td><?php echo $batch['fish_scale_type'] ?? 'N/A'; ?></td>
                    <td><?php echo $batch['full_name']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>