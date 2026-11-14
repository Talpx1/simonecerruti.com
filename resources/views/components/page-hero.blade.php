@props(['fx' => null, 'label', 'rows'])

<section {{ $attributes->class('page-hero') }}>
    @if ($fx)
        <canvas id="{{ $fx }}" class="hero-bg" data-fx="{{ $fx }}" aria-hidden="true"></canvas>
    @endif

    {{ $eyebrow ?? '' }}

    <h1 class="ph1" aria-label="{{ $label }}">
        @foreach ($rows as $row)
            <span class="row" data-split>{{ $row }}</span>
        @endforeach
    </h1>

    <div class="ph-sub">
        {{ $slot }}

        <div class="actions">{{ $actions ?? '' }}</div>
    </div>
</section>
