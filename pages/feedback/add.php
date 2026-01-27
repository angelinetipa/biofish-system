<?php
/**
 * Add Feedback Page
 * BIO-FISH Bioplastic Formation System
 */

require_once __DIR__ . '/../../config/init.php';
require_login();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $batch_id = clean_input($_POST['batch_id']);
    $rating = clean_input($_POST['rating']);
    $user_name = clean_input($_POST['user_name']);
    $comments = clean_input($_POST['comments']);
    $bug_report = clean_input($_POST['bug_report']);
    $feature_request = clean_input($_POST['feature_request']);
    
    // Validation
    if (empty($batch_id)) {
        $error = "Please select a batch to provide feedback for.";
    } elseif (empty($rating) || $rating < 1 || $rating > 5) {
        $error = "Please provide a valid rating (1-5 stars).";
    } elseif (empty($user_name)) {
        $error = "Please enter your name.";
    } elseif (empty($comments) && empty($bug_report) && empty($feature_request)) {
        $error = "Please provide at least one type of feedback (comment, bug report, or feature request).";
    } else {
        if (submit_feedback($batch_id, $rating, $user_name, $comments, $bug_report, $feature_request)) {
            set_flash('success', '✓ Thank you! Your feedback has been submitted successfully.');
            redirect(BASE_URL . '/pages/dashboard/index.php');
        } else {
            $error = "Error submitting feedback. Please try again.";
        }
    }
}

// Get completed batches for selection
$completed_batches = $conn->query("SELECT batch_id, batch_code, DATE_FORMAT(end_time, '%M %d, %Y') as completed_date 
    FROM batches WHERE status = 'completed' ORDER BY end_time DESC LIMIT 20");

$page_title = 'Submit Feedback';
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
            <h2>💬 <?php echo $page_title; ?></h2>
            <p class="subtitle">Help us improve the BIO-FISH system with your valuable feedback</p>
            
            <?php if ($success): ?>
                <?php echo show_alert('success', $success); ?>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <?php echo show_alert('error', $error); ?>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Select Batch <span class="required">*</span></label>
                    <select name="batch_id" required>
                        <option value="">-- Select a completed batch --</option>
                        <?php while ($batch = $completed_batches->fetch_assoc()): ?>
                            <option value="<?php echo $batch['batch_id']; ?>">
                                <?php echo $batch['batch_code']; ?> (Completed: <?php echo $batch['completed_date']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="help-text">Choose the batch you want to provide feedback for</div>
                </div>
                
                <div class="form-group">
                    <label>Your Name <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="user_name" 
                        placeholder="Enter your name"
                        value="<?php echo get_user_name(); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label>Overall Quality Rating <span class="required">*</span></label>
                    <div class="star-rating">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5">⭐</label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4">⭐</label>
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3">⭐</label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2">⭐</label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1">⭐</label>
                    </div>
                    <div class="help-text">Click to rate the bioplastic film quality (1 = Poor, 5 = Excellent)</div>
                </div>
                
                <div class="feedback-sections">
                    <h3>📝 Provide Detailed Feedback (At least one required)</h3>
                    
                    <div class="form-group">
                        <label>General Comments & Observations</label>
                        <textarea 
                            name="comments" 
                            placeholder="e.g., The bioplastic sheet has good transparency and flexibility. The texture is smooth and uniform..."
                        ></textarea>
                        <div class="help-text">Share your observations about film quality, appearance, or performance</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Bug Reports / Issues</label>
                        <textarea 
                            name="bug_report" 
                            placeholder="e.g., The filtration stage took longer than expected. There were air bubbles in the final film..."
                        ></textarea>
                        <div class="help-text">Report any problems, errors, or unexpected behavior during production</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Feature Requests / Suggestions</label>
                        <textarea 
                            name="feature_request" 
                            placeholder="e.g., It would be helpful to have temperature alerts during the extraction phase..."
                        ></textarea>
                        <div class="help-text">Suggest improvements or new features for the system</div>
                    </div>
                </div>
                
                <div class="btn-group" style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">📤 Submit Feedback</button>
                    <a href="<?php echo BASE_URL; ?>/pages/dashboard/index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo JS_URL; ?>/main.js"></script>
</body>
</html>