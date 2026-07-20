// Notification system
class NotificationManager {
    constructor() {
        this.container = $('#notification-container');
        if (!this.container.length) {
            $('body').append('<div id="notification-container"></div>');
            this.container = $('#notification-container');
        }
    }
    
    show(message, type = 'info', duration = 5000) {
        const notification = $(`
            <div class="notification notification-${type}">
                <i class="fas fa-${this.getIcon(type)}"></i>
                ${message}
                <button class="close-btn" onclick="$(this).parent().remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `);
        
        this.container.append(notification);
        
        if (duration > 0) {
            setTimeout(() => {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, duration);
        }
    }
    
    getIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            info: 'info-circle',
            warning: 'exclamation-triangle'
        };
        return icons[type] || icons.info;
    }
}

// Global notification instance
const notify = new NotificationManager();

// Auto show notifications from session
$(document).ready(function() {
    if (typeof session_notifications !== 'undefined') {
        session_notifications.forEach(function(notification) {
            notify.show(notification.message, notification.type);
        });
    }
});