@props(['lines' => 3])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg animate-pulse">
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
        @if($lines > 2)
            <div class="mt-4 space-y-2">
                @for($i = 0; $i < $lines - 2; $i++)
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded {{ $i === $lines - 3 ? 'w-5/6' : 'w-full' }}"></div>
                @endfor
            </div>
        @endif
    </div>
</div>

