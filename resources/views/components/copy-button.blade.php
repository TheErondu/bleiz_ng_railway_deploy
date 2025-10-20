{{--
    Reusable Copy Button Component
    Usage: <x-copy-button text="Text to copy" size="sm" />
    Or: @include('components.copy-button', ['text' => 'Text to copy', 'size' => 'sm'])
--}}

@props([
    'text' => '',
    'size' => 'sm', // sm, md, lg
])

@php
    $uniqueId = 'copy-' . uniqid();

    $sizeClasses = [
        'sm' => 'text-sm p-1.5',
        'md' => 'text-sm p-2',
        'lg' => 'text-base p-2.5',
    ];

    $iconSizes = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];
@endphp

<button
    type="button"
    data-copy-text="{{ $text }}"
    class="copy-btn inline-flex items-center justify-center {{ $sizeClasses[$size] }} text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 transition-all duration-200"
    title="Copy to clipboard">

    {{-- Copy Icon --}}
    <svg class="{{ $iconSizes[$size] }} copy-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
        <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
    </svg>

    {{-- Checkmark Icon (hidden by default) --}}
    <svg class="{{ $iconSizes[$size] }} check-icon hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
    </svg>
</button>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event to all copy buttons
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-copy-text');
            const copyIcon = this.querySelector('.copy-icon');
            const checkIcon = this.querySelector('.check-icon');

            // Copy to clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showSuccess();
                }).catch(err => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }

            function showSuccess() {
                // Swap icons
                copyIcon.classList.add('hidden');
                checkIcon.classList.remove('hidden');

                // Change button color to green
                button.classList.add('text-green-600', 'dark:text-green-400', 'border-green-600', 'dark:border-green-400');
                button.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-gray-300', 'dark:border-gray-600');
                // Reset after 2 seconds
                setTimeout(() => {
                    copyIcon.classList.remove('hidden');
                    checkIcon.classList.add('hidden');
                    button.classList.remove('text-green-600', 'dark:text-green-400', 'border-green-600', 'dark:border-green-400');
                    button.classList.add('text-gray-500', 'dark:text-gray-400', 'border-gray-300', 'dark:border-gray-600');
                }, 2000);
            }

            function fallbackCopy(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    document.execCommand('copy');
                    showSuccess();
                } catch (err) {
                    console.error('Failed to copy text: ', err);
                    alert('Failed to copy to clipboard');
                }

                document.body.removeChild(textArea);
            }
        });
    });
});
</script>
@endpush
@endonce
