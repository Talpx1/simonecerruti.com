@php
    // Inverted "highlight box" applied to key words across the page.
    $hl = 'bg-light text-dark px-[0.12em] box-decoration-clone';
@endphp

<div>
    {{-- ============================== HERO ============================== --}}
    <section data-pan="section-impression-services"
        class="relative overflow-hidden px-8 lg:px-14 pt-16 lg:pt-24 pb-16 lg:pb-20">

        <x-app-logo weight="thin"
            class="pointer-events-none absolute right-[-8%] top-1/2 -translate-y-1/2 w-[min(70vw,900px)] opacity-5 hidden sm:block" />

        <div class="relative z-10">
            <x-eyebrow>{{ __('Services') }}</x-eyebrow>

            <h1
                class="mt-6 font-black uppercase leading-none tracking-tighter text-light text-6xl sm:text-7xl lg:text-8xl xl:text-9xl max-w-[14ch]">
                {!! __('What do you want to<br>:solve', [
                    'solve' => '<span class="' . $hl . '">' . __('solve?') . '</span>',
                ]) !!}
            </h1>

            <p class="mt-7 text-light/55 text-lg lg:text-xl font-light leading-relaxed max-w-2xl">
                {{ __('Pick your goal: I\'ll take you to the right service, explained in plain words. Software, web and AI — tailor-made, built around your processes.') }}
            </p>

            @php
                $goals = [
                    ['01', __('Manage the company better'), '#aree'],
                    ['02', __('Sell online'), '#aree'],
                    ['03', __('Digitize a process'), '#aree'],
                    ['04', __('Build or rebuild a website'), '#aree'],
                    ['05', __('Build a platform'), '#aree'],
                    ['06', __("I don't know yet"), route('contacts')],
                ];
            @endphp

            <div class="mt-12 lg:mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($goals as [$number, $label, $href])
                    <a href="{{ $href }}" @unless (str_starts_with($href, '#')) wire:navigate @endunless
                        class="group flex items-center justify-between gap-4 border border-light/15 p-6 lg:p-7 min-h-[132px] transition-colors duration-300 hover:bg-light hover:text-dark hover:border-light">
                        <span class="block">
                            <span
                                class="block font-mono text-xs text-light/40 transition-colors group-hover:text-dark/50">{{ $number }}</span>
                            <span class="mt-4 block font-bold uppercase text-xl leading-tight tracking-tight">{{ $label }}</span>
                        </span>
                        <x-ri-arrow-right-long-line
                            class="w-5 shrink-0 opacity-50 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-1.5" />
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
