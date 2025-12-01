/**
 * Skeleton Loading Utilities
 * For showing loading skeletons during async operations
 */

window.SkeletonManager = {
    /**
     * Show skeleton for a container
     */
    show(containerId, type = 'default') {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Hide existing content
        const existingContent = container.querySelectorAll(':not(.skeleton-placeholder)');
        existingContent.forEach(el => {
            el.style.display = 'none';
        });

        // Create or show skeleton
        let skeleton = container.querySelector('.skeleton-placeholder');
        if (!skeleton) {
            skeleton = document.createElement('div');
            skeleton.className = 'skeleton-placeholder';
            container.appendChild(skeleton);
        }

        skeleton.innerHTML = this.getSkeletonHTML(type);
        skeleton.style.display = 'block';
    },

    /**
     * Hide skeleton and show content
     */
    hide(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const skeleton = container.querySelector('.skeleton-placeholder');
        if (skeleton) {
            skeleton.style.display = 'none';
        }

        // Show existing content
        const existingContent = container.querySelectorAll(':not(.skeleton-placeholder)');
        existingContent.forEach(el => {
            el.style.display = '';
        });
    },

    /**
     * Get skeleton HTML based on type
     */
    getSkeletonHTML(type) {
        const skeletons = {
            table: `
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg animate-pulse">
                    <div class="p-6">
                        <div class="grid grid-cols-5 gap-4 mb-4">
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                        </div>
                        ${Array(5).fill(0).map(() => `
                            <div class="grid grid-cols-5 gap-4 mb-3">
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `,
            grid: `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
                    ${Array(6).fill(0).map(() => `
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="w-full h-32 bg-gray-300 dark:bg-gray-600 rounded mb-4"></div>
                                <div class="h-5 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-3"></div>
                                <div class="space-y-2 mb-4">
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-full"></div>
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-4/6"></div>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/3"></div>
                                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/4"></div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `,
            cards: `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-pulse">
                    ${Array(4).fill(0).map(() => `
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-md"></div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-24 mb-2"></div>
                                        <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `,
            default: `
                <div class="space-y-6 animate-pulse">
                    ${Array(3).fill(0).map(() => `
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                                    </div>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-full"></div>
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `
        };

        return skeletons[type] || skeletons.default;
    }
};

