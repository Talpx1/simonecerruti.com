@props(['effect' => null, 'label', 'rows'])

<section {{ $attributes->class('page-hero') }}>
    @if ($effect)
        <canvas id="{{ $effect }}" class="page-hero-background" data-effect="{{ $effect }}" aria-hidden="true"></canvas>
    @endif

    {{ $eyebrow ?? '' }}

    <h1 class="page-hero-title" aria-label="{{ $label }}">
        @foreach ($rows as $row)
            <span class="page-hero-title-line" data-variable-letters>{{ $row }}</span>
        @endforeach
    </h1>

    <div class="page-hero-subtitle">
        {{ $slot }}

        <div class="button-group">{{ $actions ?? '' }}</div>
    </div>
</section>
