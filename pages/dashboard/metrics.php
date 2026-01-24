<style>
.metric-icon svg,
.machine-status-card svg {
    filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
}
</style>

<!-- Key Metrics - CLAYMORPHISM LAYOUT -->
<div class="metrics-container">
    <!-- LARGE MACHINE STATUS CARD (Left Side) -->
    <div class="metric-card machine-status-card status-<?php echo $machine_status; ?> machine-status-large">
        <div class="metric-label">Machine Status</div>
        <div class="metric-value">
            <?php echo strtoupper($machine_status); ?>
        </div>
        <div class="machine-status-indicator">
            <div class="status-dot <?php echo $machine_status; ?>"></div>
            <div class="status-text">
                <?php 
                if ($current_batch) {
                    echo '<strong>' . $current_batch . '</strong>';
                    if ($current_stage && $machine_status == 'running') {
                        echo '<br><small style="color: #999;">' . ucwords(str_replace('_', ' ', $current_stage)) . '</small>';
                    }
                } else {
                    echo '<span style="color: #999;">Ready for operation</span>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- 4 METRICS GRID (Right Side) -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#4A90A4">
                    <path d="M21,16.5C21,16.88 20.79,17.21 20.47,17.38L12.57,21.82C12.41,21.94 12.21,22 12,22C11.79,22 11.59,21.94 11.43,21.82L3.53,17.38C3.21,17.21 3,16.88 3,16.5V7.5C3,7.12 3.21,6.79 3.53,6.62L11.43,2.18C11.59,2.06 11.79,2 12,2C12.21,2 12.41,2.06 12.57,2.18L20.47,6.62C20.79,6.79 21,7.12 21,7.5V16.5M12,4.15L5,8.09V15.91L12,19.85L19,15.91V8.09L12,4.15Z"/>
                </svg>
            </div>
            <div class="metric-label">Total Batches</div>
            <div class="metric-value"><?php echo $total_batches; ?></div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#4caf50">
                    <path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"/>
                </svg>
            </div>
            <div class="metric-label">Success Rate</div>
            <div class="metric-value"><?php echo $success_rate; ?>%</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#4A90A4">
                    <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                </svg>
            </div>
            <div class="metric-label">Avg Time</div>
            <div class="metric-value"><?php echo $avg_time; ?><small style="font-size: 16px;">min</small></div>
        </div>
        
        <div class="metric-card">
            <div class="metric-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="<?php echo $low_stock_count > 0 ? '#f44336' : '#4caf50'; ?>">
                    <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z"/>
                </svg>
            </div>
            <div class="metric-label">Low Stock</div>
            <div class="metric-value" style="color: <?php echo $low_stock_count > 0 ? '#f44336' : '#4caf50'; ?>">
                <?php echo $low_stock_count; ?>
            </div>
        </div>
    </div>
</div>