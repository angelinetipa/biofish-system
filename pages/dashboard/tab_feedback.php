<!-- Tab 3: Feedback -->
<div id="feedback" class="tab-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Quality Feedback & Assessment</h2>
        <a href="<?php echo BASE_URL; ?>/pages/feedback/add.php">
            <button class="btn-primary btn-icon" style="padding: 12px 24px; cursor: pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                </svg>
                Add Feedback
            </button>
        </a>
    </div>
    
    <?php
    $avg_rating = $conn->query("SELECT AVG(rating) as avg FROM feedback")->fetch_assoc()['avg'];
    $total_feedback = $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'];
    $total_comments = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE comments IS NOT NULL AND comments != ''")->fetch_assoc()['count'];
    $total_bugs = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE bug_report IS NOT NULL AND bug_report != ''")->fetch_assoc()['count'];
    $total_features = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE feature_request IS NOT NULL AND feature_request != ''")->fetch_assoc()['count'];
    ?>
    
    <div class="metrics-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 30px;">
        <div class="metric-card">
            <div class="metric-icon feedback-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffc107">
                    <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                </svg>
            </div>
            <div class="metric-label">Average Rating</div>
            <div class="metric-value"><?php echo $avg_rating ? number_format($avg_rating, 1) : '0.0'; ?>/5</div>
        </div>
        <div class="metric-card">
            <div class="metric-icon feedback-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4A90A4">
                    <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22H9Z"/>
                </svg>
            </div>
            <div class="metric-label">Total Responses</div>
            <div class="metric-value"><?php echo $total_feedback; ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-icon feedback-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#f44336">
                    <path d="M14,12H10V10H14M14,16H10V14H14M20,8H17.19C16.74,7.22 16.12,6.55 15.37,6.04L17,4.41L15.59,3L13.42,5.17C12.96,5.06 12.5,5 12,5C11.5,5 11.04,5.06 10.59,5.17L8.41,3L7,4.41L8.62,6.04C7.88,6.55 7.26,7.22 6.81,8H4V10H6.09C6.04,10.33 6,10.66 6,11V12H4V14H6V15C6,15.34 6.04,15.67 6.09,16H4V18H6.81C8.47,20.87 12.14,21.84 15,20.18C15.91,19.66 16.67,18.9 17.19,18H20V16H17.91C17.96,15.67 18,15.34 18,15V14H20V12H18V11C18,10.66 17.96,10.33 17.91,10H20V8Z"/>
                </svg>
            </div>
            <div class="metric-label">Bug Reports</div>
            <div class="metric-value"><?php echo $total_bugs; ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-icon feedback-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#9c27b0">
                    <path d="M7,10L12,15L17,10H7Z"/>
                </svg>
            </div>
            <div class="metric-label">Feature Requests</div>
            <div class="metric-value"><?php echo $total_features; ?></div>
        </div>
    </div>
    
    <h3>Feedback History</h3>
    <div class="table-scroll-container">
        <table>
            <thead>
                <tr>
                    <th>Batch Code</th>
                    <th>Rating</th>
                    <th>User</th>
                    <th>Feedback Types</th>
                    <th>Content Preview</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $feedbacks = $conn->query("SELECT f.*, b.batch_code FROM feedback f 
                    LEFT JOIN batches b ON f.batch_id = b.batch_id 
                    ORDER BY f.submitted_at DESC");
                $feedback_count = 0;
                while ($fb = $feedbacks->fetch_assoc()):
                    $feedback_count++;
                    
                    // Determine feedback types
                    $types = [];
                    if (!empty($fb['comments'])) $types[] = 'Comment';
                    if (!empty($fb['bug_report'])) $types[] = 'Bug';
                    if (!empty($fb['feature_request'])) $types[] = 'Feature';
                    
                    // Get preview text
                    $preview = '';
                    if (!empty($fb['comments'])) {
                        $preview = $fb['comments'];
                    } elseif (!empty($fb['bug_report'])) {
                        $preview = $fb['bug_report'];
                    } elseif (!empty($fb['feature_request'])) {
                        $preview = $fb['feature_request'];
                    }
                    $preview = strlen($preview) > 60 ? substr($preview, 0, 60) . '...' : $preview;
                ?>
                <tr>
                    <td><?php echo $fb['batch_code'] ?? 'N/A'; ?></td>
                    <td>
                        <span style="color: #ffc107; font-size: 16px;">
                            <?php echo str_repeat('★', $fb['rating']); ?>
                        </span>
                    </td>
                    <td><?php echo $fb['user_name']; ?></td>
                    <td>
                        <?php foreach ($types as $type): ?>
                            <span class="feedback-type-badge feedback-type-<?php echo strtolower($type); ?>">
                                <?php echo $type; ?>
                            </span>
                        <?php endforeach; ?>
                    </td>
                    <td style="font-size: 13px; color: #666;">
                        <?php echo htmlspecialchars($preview); ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($fb['submitted_at'])); ?></td>
                </tr>
                
                <!-- Expandable Details Row -->
                <?php if (!empty($fb['comments']) || !empty($fb['bug_report']) || !empty($fb['feature_request'])): ?>
                <tr class="feedback-details" style="display: none;" id="details-<?php echo $fb['feedback_id']; ?>">
                    <td colspan="6" style="background: #f8f9fa; padding: 20px;">
                        <?php if (!empty($fb['comments'])): ?>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #2C7873; display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#2C7873">
                                        <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22H9Z"/>
                                    </svg>
                                    General Comments:
                                </strong>
                                <p style="margin-top: 5px; color: #333;"><?php echo nl2br(htmlspecialchars($fb['comments'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($fb['bug_report'])): ?>
                            <div style="margin-bottom: 15px;">
                                <strong style="color: #f44336; display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#f44336">
                                        <path d="M14,12H10V10H14M14,16H10V14H14M20,8H17.19C16.74,7.22 16.12,6.55 15.37,6.04L17,4.41L15.59,3L13.42,5.17C12.96,5.06 12.5,5 12,5C11.5,5 11.04,5.06 10.59,5.17L8.41,3L7,4.41L8.62,6.04C7.88,6.55 7.26,7.22 6.81,8H4V10H6.09C6.04,10.33 6,10.66 6,11V12H4V14H6V15C6,15.34 6.04,15.67 6.09,16H4V18H6.81C8.47,20.87 12.14,21.84 15,20.18C15.91,19.66 16.67,18.9 17.19,18H20V16H17.91C17.96,15.67 18,15.34 18,15V14H20V12H18V11C18,10.66 17.96,10.33 17.91,10H20V8Z"/>
                                    </svg>
                                    Bug Report:
                                </strong>
                                <p style="margin-top: 5px; color: #333;"><?php echo nl2br(htmlspecialchars($fb['bug_report'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($fb['feature_request'])): ?>
                            <div>
                                <strong style="color: #2196f3; display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#2196f3">
                                        <path d="M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M13,7H11V11H7V13H11V17H13V13H17V11H13V7Z"/>
                                    </svg>
                                    Feature Request:
                                </strong>
                                <p style="margin-top: 5px; color: #333;"><?php echo nl2br(htmlspecialchars($fb['feature_request'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <button onclick="toggleDetails(<?php echo $fb['feedback_id']; ?>)" class="btn-secondary" style="margin-top: 10px; padding: 8px 16px; font-size: 14px;">
                            Close Details
                        </button>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endwhile; ?>
                
                <?php if ($feedback_count == 0): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; padding: 30px;">
                        No feedback submitted yet. Click "Add Feedback" to submit quality assessment.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($feedback_count > 5): ?>
    <div class="scroll-hint">
        <small>Scroll to see all <?php echo $feedback_count; ?> feedback entries →</small>
    </div>
    <?php endif; ?>
</div>