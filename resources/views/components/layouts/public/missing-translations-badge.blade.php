@php
    $missing_translations_locale = request()->query('missing_translations');
    $is_valid_locale = $missing_translations_locale
        ? in_array($missing_translations_locale, array_keys(app()->supportedLocales()))
        : true;
@endphp

@if ($missing_translations_locale)
    <div x-data="{ open: true }" x-show="open" x-transition.opacity.duration.300ms
        class="fixed bottom-6 z-[99999] w-full max-w-fit px-4">
        <div class="relative flex gap-2 bg-yellow-400/75 border-2 border-yellow-700 text-yellow-950 rounded-lg p-2">
            {{ __(
                'This content is not yet translated in :language',
                [
                    'language' => config()->string(
                        "laravellocalization.supportedLocales.{$missing_translations_locale}.native",
                        strtoupper($missing_translations_locale),
                    ),
                ],
                $is_valid_locale ? $missing_translations_locale : 'en',
            ) }}

            <button type="button" @click="open = false" aria-label="{{ __('Close') }}">
                <x-heroicon-m-x-mark class="w-5" />
            </button>
        </div>
    </div>
@endif
