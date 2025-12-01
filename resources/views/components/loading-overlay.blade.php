@props(['show' => false, 'message' => 'Loading...'])

<div 
    x-data="{ show: {{ $show ? 'true' : 'false' }} }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 dark:bg-opacity-70 backdrop-blur-sm transition-opacity duration-300"
    style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-sm w-full mx-4">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-12 w-12 text-indigo-600 dark:text-indigo-400 mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $message }}</p>
        </div>
    </div>
</div>

