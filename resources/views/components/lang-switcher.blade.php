<x-filament::dropdown class="dark">
    <x-slot name="trigger">
        <x-filament::button class="bg-transparent text-light backdrop-blur-md p-2 lg:p-4 rounded-2xl uppercase text-xl">
            {{ app()->getLocale() }}
            <x-heroicon-m-chevron-down class="w-5" />
        </x-filament::button>
    </x-slot>
    <x-filament::dropdown.list>
        @foreach (App::supportedLocales() as $locale_code => $properties)
            <a rel="alternate" hreflang="{{ $locale_code }}"
                href="{{ Route::localizedUrl(locale: $locale_code, force_default_location: true) }}" wire:navigate>
                <x-filament::dropdown.list.item>
                    {{ $properties['native'] }}
                </x-filament::dropdown.list.item>
            </a>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
