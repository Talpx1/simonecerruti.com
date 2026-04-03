<div x-data="{ open: true }" x-show="open" x-transition.opacity.duration.300ms x-init="setTimeout(() => open = false, 5000)"
    class="fixed lg:right-6 bottom-20 lg:bottom-6 z-[99999] w-full max-w-fit px-4">
    <div class="relative flex gap-2 bg-light text-dark p-2">
        {{ __('This website is still work-in-progress, some features and content may be missing. It\'ll be done soon, I\'m on it!') }}
        <button type="button" @click="open = false" aria-label="{{ __('Close') }}" class="relative p-1">

            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                <rect x="1" y="1" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-dasharray="132" stroke-dashoffset="0" class="opacity-40"
                    style="animation: timer-drain 5s linear forwards;">
                </rect>
            </svg>

            <x-heroicon-m-x-mark class="w-5 relative z-10" />
        </button>

        <style>
            @keyframes timer-drain {
                from {
                    stroke-dashoffset: 0;
                }

                to {
                    stroke-dashoffset: 128;
                }
            }
        </style>
    </div>
</div>
