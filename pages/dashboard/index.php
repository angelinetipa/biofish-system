<?php
/**
 * Dashboard Page
 * BIO-FISH Bioplastic Formation System
 */

require_once __DIR__ . '/../../config/init.php';
require_login();

// Get batch statistics
$stats = get_batch_statistics();
extract($stats); // $total_batches, $completed_batches, $success_rate, $avg_time

// Get low stock count
$low_stock_count = get_low_stock_count();

// Get machine status
$status_data = get_machine_status();
$machine_status = $status_data['status'];
$current_batch = $status_data['batch_code'];
$current_stage = $status_data['current_stage'];

// Get running batch details
$running_batch = get_running_batch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_page_title('Dashboard'); ?></title>
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
        <?php display_flash(); ?>
        
        <?php include __DIR__ . '/metrics.php'; ?>
        
        <!-- Tabs Section -->
        <div class="tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="showTab('monitoring')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z"/>
                    </svg>
                    Process Monitoring
                </button>
                <button class="tab-btn" onclick="showTab('inventory')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21,16.5C21,16.88 20.79,17.21 20.47,17.38L12.57,21.82C12.41,21.94 12.21,22 12,22C11.79,22 11.59,21.94 11.43,21.82L3.53,17.38C3.21,17.21 3,16.88 3,16.5V7.5C3,7.12 3.21,6.79 3.53,6.62L11.43,2.18C11.59,2.06 11.79,2 12,2C12.21,2 12.41,2.06 12.57,2.18L20.47,6.62C20.79,6.79 21,7.12 21,7.5V16.5M12,4.15L5,8.09V15.91L12,19.85L19,15.91V8.09L12,4.15Z"/>
                    </svg>
                    Material Inventory
                </button>
                <button class="tab-btn" onclick="showTab('feedback')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22H9Z"/>
                    </svg>
                    Feedback
                </button>
            </div>
            
            <?php include __DIR__ . '/tab_monitoring.php'; ?>
            <?php include __DIR__ . '/tab_inventory.php'; ?>
            <?php include __DIR__ . '/tab_feedback.php'; ?>
        </div>
    </div>
    
    <script src="<?php echo JS_URL; ?>/main.js"></script>
</body>
</html>