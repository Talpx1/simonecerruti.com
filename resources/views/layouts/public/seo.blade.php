{{-- Renders the resolved SEO metadata. Only the sections present on $seo_data are emitted. --}}
@php
    /** @var \App\DataTransferObjects\SeoData|null $seo_data */
@endphp
@if ($seo_data)
    {{-- A page must always carry a title: fall back to the app name when none was resolved. --}}
    <title>{{ $seo_data->title ?: config('app.name') }}</title>

    @if ($seo_data->description)
        <meta name="description" content="{{ $seo_data->description }}">
    @endif

    @if ($seo_data->robots)
        <meta name="robots" content="{{ $seo_data->robots }}">
    @endif

    @if ($seo_data->canonical)
        <link rel="canonical" href="{{ $seo_data->canonical }}">
    @endif

    @foreach ($seo_data->alternates as $alternate)
        <link rel="alternate" hreflang="{{ $alternate['hreflang'] }}" href="{{ $alternate['href'] }}">
    @endforeach

    @foreach ($seo_data->open_graph as $property => $content)
        <meta property="{{ $property }}" content="{{ $content }}">
    @endforeach

    @foreach ($seo_data->twitter as $name => $content)
        <meta name="{{ $name }}" content="{{ $content }}">
    @endforeach

    @if ($seo_data->json_ld)
        {{-- @@context is escaped to a literal "@context" so Blade doesn't treat it as a directive. --}}
        <script type="application/ld+json">{!! \Safe\json_encode(['@@context' => 'https://schema.org', '@graph' => $seo_data->json_ld], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endif
@endif
