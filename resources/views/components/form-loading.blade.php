@props(['show' => false])

<div 
    x-data="{ show: {{ $show ? 'true' : 'false' }} }"
    x-show="show"
    x-cloak
    class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 dark:bg-opacity-75 backdrop-blur-sm rounded-lg flex items-center justify-center z-10"
    style="display: none;">
    <div class="flex flex-col items-center">
        <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400 mb-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-gray-600 dark:text-gray-400">Processing...</p>
    </div>
</div>

