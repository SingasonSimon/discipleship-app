@props(['rows' => 5, 'columns' => 4])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg animate-pulse">
    <div class="p-6">
        <!-- Table Header -->
        <div class="grid gap-4 mb-4" style="grid-template-columns: repeat({{ $columns }}, 1fr);">
            @for($i = 0; $i < $columns; $i++)
                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
            @endfor
        </div>
        
        <!-- Table Rows -->
        <div class="space-y-3">
            @for($row = 0; $row < $rows; $row++)
                <div class="grid gap-4" style="grid-template-columns: repeat({{ $columns }}, 1fr);">
                    @for($col = 0; $col < $columns; $col++)
                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded {{ $col === 0 ? 'w-3/4' : 'w-full' }}"></div>
                    @endfor
                </div>
            @endfor
        </div>
    </div>
</div>

