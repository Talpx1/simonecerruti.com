@props([
    'flip' => false,
    'area',
    'lead',
    'bullets' => [],
    'buttonLabel',
    'buttonHref',
])

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
    <div @class(['lg:order-2' => $flip])>
        {{ $media }}
    </div>

    <div @class(['lg:order-1' => $flip])>
        <p class="font-mono text-xs lg:text-sm tracking-[0.2em] text-light/40 mb-4 lg:mb-5">{{ $area }}</p>

        <h2 class="font-black uppercase leading-none tracking-tighter text-light text-4xl lg:text-6xl mb-5">
            {{ $heading }}
        </h2>

        <p class="text-light/55 text-base lg:text-lg font-light leading-relaxed max-w-[46ch]">{{ $lead }}</p>

        <ul class="mt-6 mb-8 flex flex-col gap-3.5">
            @foreach ($bullets as $bullet)
                <li class="flex gap-3 text-base leading-relaxed">
                    <x-ri-arrow-right-long-line class="w-3.5 mt-1.5 shrink-0 text-light" />
                    <span class="text-light/55">{{ $bullet }}</span>
                </li>
            @endforeach
        </ul>

        <div class="flex flex-wrap items-center gap-x-6 gap-y-4">
            <x-button :href="$buttonHref">{{ $buttonLabel }}</x-button>
            {{ $contextual ?? '' }}
        </div>
    </div>
</div>
