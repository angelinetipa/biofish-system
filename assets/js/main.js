/**
 * BIO-FISH Main JavaScript
 * Global functions and utilities
 */

// Tab management
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
    
    // Save to localStorage
    saveCurrentTab();
    
    // Start/stop auto-refresh based on tab
    checkAndStartRefresh();
}

// Save current tab to localStorage
function saveCurrentTab() {
    const activeTab = document.querySelector('.tab-btn.active');
    if (activeTab) {
        const tabName = activeTab.textContent.trim().split(' ')[1];
        localStorage.setItem('biofish_current_tab', tabName);
    }
}

// Restore tab on page load
window.addEventListener('DOMContentLoaded', function() {
    const savedTab = localStorage.getItem('biofish_current_tab');
    if (savedTab) {
        const tabs = {
            'Monitoring': 'monitoring',
            'Inventory': 'inventory',
            'Feedback': 'feedback'
        };
        const tabId = tabs[savedTab];
        if (tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            const tabElement = document.getElementById(tabId);
            const tabButton = document.querySelector(`[onclick="showTab('${tabId}')"]`);
            if (tabElement) tabElement.classList.add('active');
            if (tabButton) tabButton.classList.add('active');
        }
    }
    
    // Initialize timestamp
    updateTimestamp();
    checkAndStartRefresh();
});

// Update timestamp display
function updateTimestamp() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        hour12: true 
    });
    const element = document.getElementById('lastUpdate');
    if (element) {
        element.textContent = timeStr;
    }
}

// Countdown timer
let countdownSeconds = 30;
function updateCountdown() {
    const element = document.getElementById('countdown');
    if (element) {
        element.textContent = `(refreshing in ${countdownSeconds}s)`;
    }
    countdownSeconds--;
    if (countdownSeconds < 0) {
        countdownSeconds = 30;
    }
}

setInterval(updateCountdown, 1000);

// Auto-refresh with tab retention (every 30 seconds)
let refreshInterval;
function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        saveCurrentTab();
        countdownSeconds = 30;
        location.reload();
    }, 30000);
}

function checkAndStartRefresh() {
    const monitoringTab = document.getElementById('monitoring');
    if (monitoringTab && monitoringTab.classList.contains('active')) {
        if (!refreshInterval) {
            startAutoRefresh();
        }
    } else {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }
}

// Machine control functions
function controlMachine(action, batchId) {
    saveCurrentTab();
    
    fetch('/biofish/api/machine_control.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}&batch_id=${batchId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Connection error: ' + error);
    });
}

function confirmStop(batchId) {
    if (confirm('⚠️ Are you sure you want to STOP this batch?\n\nThis will terminate the current process and the batch cannot be resumed.\n\nClick OK to stop, or Cancel to go back.')) {
        saveCurrentTab();
        controlMachine('stop', batchId);
    }
}

function confirmCleaning() {
    if (confirm('Start cleaning mode?\n\nThis will activate the machine\'s cleaning cycle. Make sure the machine is empty before proceeding.')) {
        saveCurrentTab();
        controlMachine('cleaning', 0);
    }
}

// Feedback toggle
function toggleDetails(feedbackId) {
    const detailsRow = document.getElementById('details-' + feedbackId);
    if (detailsRow) {
        if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
            document.querySelectorAll('.feedback-details').forEach(row => {
                row.style.display = 'none';
            });
            detailsRow.style.display = 'table-row';
        } else {
            detailsRow.style.display = 'none';
        }
    }
}

// Click on feedback row to expand
document.addEventListener('DOMContentLoaded', function() {
    const feedbackRows = document.querySelectorAll('tbody tr:not(.feedback-details)');
    feedbackRows.forEach((row) => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button
            if (e.target.tagName === 'BUTTON') return;
            
            const feedbackId = this.nextElementSibling?.id?.replace('details-', '');
            if (feedbackId) {
                toggleDetails(feedbackId);
            }
        });
    });
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#f44336';
            isValid = false;
        } else {
            field.style.borderColor = '#e0e0e0';
        }
    });
    
    return isValid;
}

// Show loading spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner';
    spinner.id = 'globalSpinner';
    document.body.appendChild(spinner);
}

function hideLoading() {
    const spinner = document.getElementById('globalSpinner');
    if (spinner) {
        spinner.remove();
    }
}

// Confirm action
function confirmAction(message) {
    return confirm(message);
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `<span>${message}</span>`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideIn 0.3s ease-out';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add slide animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);