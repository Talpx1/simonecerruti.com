@props(['current'])

@php
    $documents = [
        'privacy' => [__('Privacy'), route('privacy_policy')],
        'cookies' => [__('Cookies'), route('cookie_policy')],
        'terms' => [__('Terms'), route('terms_and_conditions')],
    ];
@endphp

<nav class="legal-switcher" aria-label="{{ __('Legal documents') }}">
    @foreach ($documents as $key => [$label, $url])
        <a href="{{ $url }}" wire:navigate @if ($key === $current) class="is-current" aria-current="page"@endif>{{ $label }}</a>
    @endforeach
</nav>
