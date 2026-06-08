{{-- Inverted (light-on-dark → dark-on-light) AI band shared by the Services hub
     and the Area landings. The heading goes in the default slot so callers can
     inject <br> or highlighted words; everything else comes through props. --}}
@props([
    'eyebrow',
    'lead',
    'points' => [],
])

<section {{ $attributes->class('py-12 lg:py-20') }}>
    <div class="max-w-7xl mx-auto px-8 lg:px-14">
        <div
            class="bg-light text-dark grid grid-cols-1 lg:grid-cols-[1.1fr_1fr] gap-8 lg:gap-14 items-center p-10 lg:p-16">
            <div>
                <x-eyebrow tone="dark">{{ $eyebrow }}</x-eyebrow>

                <h2 class="mt-5 font-black uppercase leading-none tracking-tighter text-dark text-4xl lg:text-6xl">
                    {{ $slot }}
                </h2>

                <p class="mt-5 text-dark/60 text-base lg:text-lg font-light leading-relaxed max-w-[40ch]">
                    {{ $lead }}
                </p>
            </div>

            <div class="flex flex-col gap-3.5">
                @foreach ($points as [$point_lead, $body])
                    <div class="flex gap-3 border border-dark/15 p-5 text-sm lg:text-base leading-relaxed">
                        <x-ri-arrow-right-long-line class="w-4 mt-1 shrink-0 text-dark" />
                        <span class="text-dark/80"><b class="font-bold text-dark">{{ $point_lead }}</b> {{ $body }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
