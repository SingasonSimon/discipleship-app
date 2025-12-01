@props(['action', 'method' => 'POST', 'hasFiles' => false])

<form 
    action="{{ $action }}" 
    method="{{ $method === 'GET' ? 'GET' : 'POST' }}"
    @if($hasFiles) enctype="multipart/form-data" @endif
    x-data="{ loading: false }"
    @submit="loading = true"
    class="relative">
    @if($method !== 'GET')
        @csrf
        @if(in_array($method, ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif
    @endif

    <x-form-loading :show="false" x-show="loading" />

    <div class="space-y-6">
        {{ $slot }}
    </div>
</form>

