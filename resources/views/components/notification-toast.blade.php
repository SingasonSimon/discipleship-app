@props(['type' => 'success', 'message' => '', 'autoClose' => true, 'duration' => 5000])

<div x-data="{
    show: true,
    type: '{{ $type }}',
    message: '{{ $message }}',
    autoClose: {{ $autoClose ? 'true' : 'false' }},
    duration: {{ $duration }},
    init() {
        if (this.autoClose) {
            setTimeout(() => {
                this.close();
            }, this.duration);
        }
    },
    close() {
        this.show = false;
        setTimeout(() => {
            this.$el.remove();
        }, 300);
    }
}" 
x-show="show"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 transform translate-x-full"
x-transition:enter-end="opacity-100 transform translate-x-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 transform translate-x-0"
x-transition:leave-end="opacity-0 transform translate-x-full"
class="fixed top-4 right-4 z-50 max-w-md w-full"
style="display: none;">
    <div class="rounded-lg shadow-lg overflow-hidden"
         :class="{
             'bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500': type === 'success',
             'bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500': type === 'error',
             'bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500': type === 'info',
             'bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500': type === 'warning'
         }">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg x-show="type === 'success'" class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg x-show="type === 'error'" class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg x-show="type === 'info'" class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg x-show="type === 'warning'" class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium"
                       :class="{
                           'text-green-800 dark:text-green-200': type === 'success',
                           'text-red-800 dark:text-red-200': type === 'error',
                           'text-blue-800 dark:text-blue-200': type === 'info',
                           'text-yellow-800 dark:text-yellow-200': type === 'warning'
                       }"
                       x-text="message"></p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="close()" class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="{
                                'text-green-500 hover:text-green-600 focus:ring-green-500': type === 'success',
                                'text-red-500 hover:text-red-600 focus:ring-red-500': type === 'error',
                                'text-blue-500 hover:text-blue-600 focus:ring-blue-500': type === 'info',
                                'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': type === 'warning'
                            }">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="autoClose" class="h-1 bg-gray-200 dark:bg-gray-700">
            <div class="h-full bg-current transition-all duration-{{ $duration }}ms"
                 :class="{
                     'text-green-500': type === 'success',
                     'text-red-500': type === 'error',
                     'text-blue-500': type === 'info',
                     'text-yellow-500': type === 'warning'
                 }"
                 x-bind:style="'width: 0%'"
                 x-init="setTimeout(() => { $el.style.width = '0%' }, 100); setTimeout(() => { $el.style.width = '100%' }, 200);"></div>
        </div>
    </div>
</div>

