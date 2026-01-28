<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <div class="footer-logo">
                <div class="footer-logo-icon">🐟</div>
                <div>
                    <h4>BIO-FISH</h4>
                    <p>Bioplastic Sheet Production</p>
                </div>
            </div>
            <p class="footer-tagline">
                Sustainable innovation from fish scales to eco-friendly bioplastic materials.
            </p>
        </div>
        
        <div class="footer-section">
            <h5>Quick Links</h5>
            <ul class="footer-links">
                <li><a href="<?php echo BASE_URL; ?>/pages/dashboard/index.php">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/batches/add.php">Start Batch</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/materials/add.php">Add Material</a></li>
                <li><a href="<?php echo BASE_URL; ?>/pages/feedback/add.php">Submit Feedback</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h5>System Info</h5>
            <ul class="footer-info">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                    </svg>
                    <span>Version 1.0.0</span>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,17V16H9V14H13V13H10A1,1 0 0,1 9,12V10A1,1 0 0,1 10,9H14V10H16V12H12V13H15A1,1 0 0,1 16,14V16A1,1 0 0,1 15,17H11Z"/>
                    </svg>
                    <span>User: <?php echo get_user_name(); ?></span>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                    </svg>
                    <span><?php echo date('F d, Y'); ?></span>
                </li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h5>Contact & Support</h5>
            <ul class="footer-info">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                    </svg>
                    <span>biofish@example.com</span>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                    </svg>
                    <span>Bacoor, Calabarzon, PH</span>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> BIO-FISH System. All rights reserved.</p>
        <p class="footer-credits">
            Sustainable bioplastic innovation • Made with 🌊 for the environment
        </p>
    </div>
</footer>