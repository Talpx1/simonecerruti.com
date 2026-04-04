@php
    $missing_translations_locale = request()->query('missing_translations');
    $is_valid_locale = $missing_translations_locale
        ? in_array($missing_translations_locale, array_keys(app()->supportedLocales()))
        : true;
@endphp

@if ($missing_translations_locale)
    <div x-data="{ open: true }" x-show="open" x-transition:leave="transition duration-300 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[99999]">
        <div
            class="flex items-center gap-6 border border-light/20 bg-dark/90 backdrop-blur-sm px-6 py-4 text-light/70 text-xs font-semibold uppercase tracking-widest">
            <span>
                {!! __(
                    'This content is not yet translated in :language',
                    [
                        'language' =>
                            '<span class="text-light">' .
                            config()->string(
                                "laravellocalization.supportedLocales.{$missing_translations_locale}.native",
                                strtoupper($missing_translations_locale),
                            ) .
                            '</span>',
                    ],
                    $is_valid_locale ? $missing_translations_locale : 'en',
                ) !!}
            </span>

            <button type="button" @click="open = false" aria-label="{{ __('Close') }}"
                class="text-light/30 hover:text-light transition-colors duration-200">
                <x-heroicon-m-x-mark class="w-4" />
            </button>
        </div>
    </div>
@endif
