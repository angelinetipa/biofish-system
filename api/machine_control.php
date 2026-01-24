<?php
/**
 * Machine Control API
 * BIO-FISH Bioplastic Formation System
 */

require_once __DIR__ . '/../config/init.php';
require_login();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$batch_id = isset($_POST['batch_id']) ? intval($_POST['batch_id']) : 0;

$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'pause':
            if (update_batch_status($batch_id, STATUS_PAUSED)) {
                log_activity('batch_paused', "Batch ID: $batch_id paused");
                $response['success'] = true;
                $response['message'] = 'Batch paused successfully';
            } else {
                $response['message'] = 'Failed to pause batch';
            }
            break;
            
        case 'continue':
            if (update_batch_status($batch_id, STATUS_RUNNING)) {
                log_activity('batch_resumed', "Batch ID: $batch_id resumed");
                $response['success'] = true;
                $response['message'] = 'Batch resumed successfully';
            } else {
                $response['message'] = 'Failed to resume batch';
            }
            break;
            
        case 'stop':
            if (update_batch_status($batch_id, STATUS_STOPPED, date('Y-m-d H:i:s'))) {
                log_activity('batch_stopped', "Batch ID: $batch_id stopped - Emergency stop");
                $response['success'] = true;
                $response['message'] = 'Batch stopped. Machine is now idle.';
            } else {
                $response['message'] = 'Failed to stop batch';
            }
            break;
            
        case 'cleaning':
            if (is_machine_busy()) {
                $response['message'] = 'Cannot start cleaning mode while a batch is active. Please stop the batch first.';
            } else {
                $cleaning_code = generate_batch_code() . '-CLEAN';
                $user_id = get_user_id();
                
                $sql = "INSERT INTO batches (batch_code, start_time, status, current_stage, user_id) 
                        VALUES (?, NOW(), 'cleaning', 'cleaning_mode', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $cleaning_code, $user_id);
                
                if ($stmt->execute()) {
                    $new_batch_id = $stmt->insert_id;
                    log_activity('cleaning_started', "Cleaning mode activated");
                    $response['success'] = true;
                    $response['message'] = 'Cleaning mode activated';
                    $response['batch_id'] = $new_batch_id;
                } else {
                    $response['message'] = 'Failed to start cleaning mode';
                }
            }
            break;
            
        case 'end_cleaning':
            if (update_batch_status($batch_id, STATUS_COMPLETED, date('Y-m-d H:i:s'))) {
                log_activity('cleaning_completed', "Cleaning mode completed");
                $response['success'] = true;
                $response['message'] = 'Cleaning mode completed. Machine is now idle.';
            } else {
                $response['message'] = 'Failed to end cleaning mode';
            }
            break;
            
        default:
            $response['message'] = 'Invalid action';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>