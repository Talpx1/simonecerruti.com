{{--
    x-cookie-toggle
    ───────────────
    Accessible toggle switch for cookie preference controls.
    Usage:  <x-cookie-toggle wire:model="analytics" id="toggle-analytics" />
--}}
@props(['id'])

<label for="{{ $id }}" class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5"
    aria-label="{{ __('Toggle') }}">
    <input type="checkbox" id="{{ $id }}" {{ $attributes->whereStartsWith('wire:model') }} class="sr-only peer">
    <div
        class="
            w-9 h-5 rounded-full
            bg-gray-200 dark:bg-gray-700
            peer-checked:bg-blue-600 dark:peer-checked:bg-blue-500
            peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-400 peer-focus:ring-offset-1
            after:content-[''] after:absolute after:top-0.5 after:left-0.5
            after:bg-white after:rounded-full after:h-4 after:w-4
            after:transition-all
            peer-checked:after:translate-x-4
            transition-colors
        ">
    </div>
</label>
