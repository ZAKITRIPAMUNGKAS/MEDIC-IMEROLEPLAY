
<div class="notification-container" id="notificationContainer"></div>

<script>
// Elegant Notification System
function showNotification(message, type = 'success', duration = 5000) {
    const container = document.getElementById('notificationContainer');
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // Get appropriate icon based on type
    let icon = '';
    switch(type) {
        case 'success':
            icon = 'fas fa-check-circle';
            break;
        case 'error':
            icon = 'fas fa-exclamation-circle';
            break;
        case 'warning':
            icon = 'fas fa-exclamation-triangle';
            break;
        case 'info':
            icon = 'fas fa-info-circle';
            break;
        default:
            icon = 'fas fa-bell';
    }
    
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-icon">
                <i class="${icon}"></i>
            </div>
            <div class="notification-text">${message}</div>
            <button class="notification-close" onclick="closeNotification(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to container
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            closeNotification(notification.querySelector('.notification-close'));
        }, duration);
    }
}

function closeNotification(closeBtn) {
    const notification = closeBtn.closest('.notification');
    notification.classList.remove('show');
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 400);
}

// Show notifications from Laravel session
<?php if(session('success')): ?>
    showNotification('<?php echo e(session("success")); ?>', 'success');
<?php endif; ?>

<?php if(session('error')): ?>
    showNotification('<?php echo e(session("error")); ?>', 'error');
<?php endif; ?>

<?php if(session('warning')): ?>
    showNotification('<?php echo e(session("warning")); ?>', 'warning');
<?php endif; ?>

<?php if(session('info')): ?>
    showNotification('<?php echo e(session("info")); ?>', 'info');
<?php endif; ?>
</script>
<?php /**PATH D:\website\EMS-IME\public_html\resources\views\components\notification.blade.php ENDPATH**/ ?>