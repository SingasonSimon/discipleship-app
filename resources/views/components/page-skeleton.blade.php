@props(['type' => 'default'])

<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($type === 'table')
            <!-- Table skeleton -->
            <div class="mb-6">
                <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-48 mb-4 animate-pulse"></div>
                <x-skeleton-table :rows="8" :columns="5" />
            </div>
        @elseif($type === 'grid')
            <!-- Grid skeleton -->
            <div class="mb-6">
                <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-48 mb-4 animate-pulse"></div>
                <x-skeleton-grid :items="6" :columns="3" />
            </div>
        @elseif($type === 'dashboard')
            <!-- Dashboard skeleton -->
            <div class="space-y-6">
                <!-- Stats cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @for($i = 0; $i < 4; $i++)
                        <x-skeleton-stat-card />
                    @endfor
                </div>
                
                <!-- Content cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @for($i = 0; $i < 2; $i++)
                        <x-skeleton-card :lines="5" />
                    @endfor
                </div>
            </div>
        @else
            <!-- Default skeleton -->
            <div class="space-y-6">
                <x-skeleton-card :lines="4" />
                <x-skeleton-card :lines="4" />
                <x-skeleton-card :lines="4" />
            </div>
        @endif
    </div>
</div>

