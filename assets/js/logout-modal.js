/**
 * Logout Modal Functionality
 * Add this to main.js or create logout-modal.js
 */

// Show logout confirmation modal
function showLogoutModal() {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.id = 'logoutModal';
    
    overlay.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">🐟</div>
                <h3>Confirm Logout</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to log out of BIO-FISH system?</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeLogoutModal()">
                    Cancel
                </button>
                <button class="btn btn-primary" onclick="confirmLogout()">
                    Logout
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Trigger animation
    setTimeout(() => {
        overlay.classList.add('active');
    }, 10);
    
    // Close on overlay click
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeLogoutModal();
        }
    });
}

// Close logout modal
function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

// Confirm logout and show success
function confirmLogout() {
    const modal = document.getElementById('logoutModal');
    const modalContent = modal.querySelector('.modal-content');
    
    // Show logging out state
    modalContent.innerHTML = `
        <div class="modal-success">
            <div class="modal-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="white">
                    <path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"/>
                </svg>
            </div>
            <h3>Logging Out</h3>
            <p>Please wait<span class="loading-dots"><span>.</span><span>.</span><span>.</span></span></p>
        </div>
    `;
    
    // Redirect after animation
    setTimeout(() => {
        window.location.href = '../../pages/auth/logout.php';
    }, 1500);
}

// Initialize logout button
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn && !logoutBtn.hasAttribute('onclick')) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            showLogoutModal();
        });
    }
});