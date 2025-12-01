@props(['items' => 6, 'columns' => 3])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $columns }} gap-6 animate-pulse">
    @for($i = 0; $i < $items; $i++)
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Image/Icon placeholder -->
                <div class="w-full h-32 bg-gray-300 dark:bg-gray-600 rounded mb-4"></div>
                
                <!-- Title -->
                <div class="h-5 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-3"></div>
                
                <!-- Description lines -->
                <div class="space-y-2 mb-4">
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-full"></div>
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-4/6"></div>
                </div>
                
                <!-- Footer -->
                <div class="flex items-center justify-between mt-4">
                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/3"></div>
                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/4"></div>
                </div>
            </div>
        </div>
    @endfor
</div>

