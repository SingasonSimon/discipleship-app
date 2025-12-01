<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-3 max-w-md w-full" style="pointer-events: none;">
    <!-- Notifications will be dynamically inserted here -->
</div>

<script>
    // Global notification system
    window.showNotification = function(type, message, duration = 5000) {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = 'pointer-events-auto';
        
        const icons = {
            success: `<svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            error: `<svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            info: `<svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            warning: `<svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>`
        };

        const colors = {
            success: 'bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 text-green-800 dark:text-green-200',
            error: 'bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-800 dark:text-red-200',
            info: 'bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 text-blue-800 dark:text-blue-200',
            warning: 'bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500 text-yellow-800 dark:text-yellow-200'
        };

        notification.innerHTML = `
            <div class="rounded-lg shadow-lg overflow-hidden ${colors[type] || colors.info} animate-slide-in-right">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            ${icons[type] || icons.info}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="this.closest('.pointer-events-auto').remove()" class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 hover:opacity-75">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gray-200 dark:bg-gray-700">
                    <div class="h-full bg-current transition-all ease-linear" style="width: 100%; animation: shrink ${duration}ms linear forwards;"></div>
                </div>
            </div>
        `;

        container.appendChild(notification);

        // Auto remove after duration
        setTimeout(() => {
            notification.style.animation = 'slide-out-right 0.3s ease-out forwards';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    };

    // Helper functions
    window.showSuccess = (message, duration) => window.showNotification('success', message, duration);
    window.showError = (message, duration) => window.showNotification('error', message, duration);
    window.showInfo = (message, duration) => window.showNotification('info', message, duration);
    window.showWarning = (message, duration) => window.showNotification('warning', message, duration);
</script>

<style>
    @keyframes slide-in-right {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slide-out-right {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    @keyframes shrink {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }

    .animate-slide-in-right {
        animation: slide-in-right 0.3s ease-out;
    }
</style>

