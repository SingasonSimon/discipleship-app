/**
 * Modern Notification System
 * Provides consistent, modern toast notifications throughout the application
 */

class NotificationManager {
    constructor() {
        this.container = null;
        this.notifications = [];
        this.maxNotifications = 5;
        this.init();
    }

    init() {
        // Create container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-3 max-w-md w-full pointer-events-none';
            document.body.appendChild(container);
        }
        this.container = document.getElementById('notification-container');
    }

    show(type, message, duration = 5000) {
        const notification = this.createNotification(type, message, duration);
        this.container.appendChild(notification);
        this.notifications.push(notification);

        // Limit number of notifications
        if (this.notifications.length > this.maxNotifications) {
            const oldest = this.notifications.shift();
            this.removeNotification(oldest);
        }

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
        }

        return notification;
    }

    createNotification(type, message, duration) {
        const notification = document.createElement('div');
        notification.className = 'pointer-events-auto animate-slide-in-right';
        
        const config = this.getConfig(type);
        
        notification.innerHTML = `
            <div class="rounded-lg shadow-lg overflow-hidden ${config.bgClass}">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${config.icon}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium ${config.textClass}">${this.escapeHtml(message)}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="window.notificationManager.removeNotification(this.closest('.pointer-events-auto'))" 
                                    class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 hover:opacity-75 ${config.closeClass}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                ${duration > 0 ? `
                <div class="h-1 bg-gray-200 dark:bg-gray-700">
                    <div class="h-full ${config.progressClass} transition-all ease-linear" 
                         style="width: 100%; animation: shrink ${duration}ms linear forwards;"></div>
                </div>
                ` : ''}
            </div>
        `;

        return notification;
    }

    getConfig(type) {
        const configs = {
            success: {
                bgClass: 'bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500',
                textClass: 'text-green-800 dark:text-green-200',
                closeClass: 'text-green-500 hover:text-green-600 focus:ring-green-500',
                progressClass: 'bg-green-500',
                icon: `<svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`
            },
            error: {
                bgClass: 'bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500',
                textClass: 'text-red-800 dark:text-red-200',
                closeClass: 'text-red-500 hover:text-red-600 focus:ring-red-500',
                progressClass: 'bg-red-500',
                icon: `<svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`
            },
            info: {
                bgClass: 'bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500',
                textClass: 'text-blue-800 dark:text-blue-200',
                closeClass: 'text-blue-500 hover:text-blue-600 focus:ring-blue-500',
                progressClass: 'bg-blue-500',
                icon: `<svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`
            },
            warning: {
                bgClass: 'bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500',
                textClass: 'text-yellow-800 dark:text-yellow-200',
                closeClass: 'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500',
                progressClass: 'bg-yellow-500',
                icon: `<svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>`
            }
        };

        return configs[type] || configs.info;
    }

    removeNotification(notification) {
        if (!notification || !notification.parentNode) return;
        
        notification.style.animation = 'slide-out-right 0.3s ease-out forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
            const index = this.notifications.indexOf(notification);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }, 300);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Convenience methods
    success(message, duration = 5000) {
        return this.show('success', message, duration);
    }

    error(message, duration = 7000) {
        return this.show('error', message, duration);
    }

    info(message, duration = 5000) {
        return this.show('info', message, duration);
    }

    warning(message, duration = 6000) {
        return this.show('warning', message, duration);
    }
}

// Initialize notification manager
window.notificationManager = new NotificationManager();

// Global helper functions for easy access
window.showNotification = (type, message, duration) => window.notificationManager.show(type, message, duration);
window.showSuccess = (message, duration) => window.notificationManager.success(message, duration);
window.showError = (message, duration) => window.notificationManager.error(message, duration);
window.showInfo = (message, duration) => window.notificationManager.info(message, duration);
window.showWarning = (message, duration) => window.notificationManager.warning(message, duration);

// Auto-show flash messages from Laravel session
// This will be called from a Blade component that passes the session data
window.initFlashMessages = function(flashData) {
    if (!flashData) return;
    
    Object.keys(flashData).forEach(type => {
        if (flashData[type]) {
            const message = Array.isArray(flashData[type]) 
                ? flashData[type].join('<br>') 
                : flashData[type];
            window.notificationManager.show(type, message);
        }
    });
};

