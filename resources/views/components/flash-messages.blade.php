@php
    $flashMessages = [
        'success' => session('success'),
        'error' => session('error'),
        'info' => session('info'),
        'warning' => session('warning'),
    ];
    
    $validationErrors = $errors->any() ? $errors->all() : [];
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show flash messages
        const flashData = @json($flashMessages);
        if (window.initFlashMessages) {
            window.initFlashMessages(flashData);
        }

        // Show validation errors
        @if($errors->any())
            const errors = @json($validationErrors);
            if (errors && errors.length > 0) {
                const errorMessage = errors.length === 1 
                    ? errors[0] 
                    : '<ul class="list-disc list-inside mt-1">' + errors.map(e => `<li>${e}</li>`).join('') + '</ul>';
                if (window.notificationManager) {
                    window.notificationManager.error(errorMessage, 8000);
                }
            }
        @endif
    });
</script>

