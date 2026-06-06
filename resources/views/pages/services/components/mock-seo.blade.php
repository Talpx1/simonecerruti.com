{{-- Monochrome CSS illustration: analytics / SEO (Area 03). Purely decorative. --}}
<x-pages::services.components.mock-frame :label="__('Analysis & SEO')">
    <div class="absolute inset-0 p-4 lg:p-6 flex flex-col gap-3 lg:gap-4">
        <div class="flex items-center justify-between">
            <span class="h-[9px] w-20 bg-light/15"></span>
            <span class="font-mono text-sm text-light">+38%</span>
        </div>

        {{-- chart — markers live inside the SVG (same coordinate space as the
             polyline) so they stay exactly on the line at any size --}}
        <div
            class="relative flex-1 min-h-0 overflow-hidden border border-light/15 bg-[repeating-linear-gradient(to_bottom,transparent_0_17px,rgba(255,255,255,0.045)_17px_18px)]">
            <svg viewBox="0 0 100 50" preserveAspectRatio="none" class="absolute inset-0 w-full h-full" aria-hidden="true">
                <polygon points="0,42 16,34 33,37 50,22 67,26 84,11 100,8 100,50 0,50" fill="rgba(255,255,255,0.06)" />
                <polyline points="0,42 16,34 33,37 50,22 67,26 84,11 100,8" fill="none" stroke="rgba(255,255,255,0.6)"
                    stroke-width="1.4" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                <rect x="83" y="10" width="2" height="2" fill="#fff" />
                <rect x="95" y="7.75" width="2" height="2" fill="#fff" />
            </svg>
        </div>

        {{-- ranking --}}
        <div class="flex flex-col gap-2.5">
            @foreach ([['1', 74, true], ['2', 54, true], ['3', 40, false]] as [$rank, $width, $up])
                <div class="flex items-center gap-2.5">
                    <span
                        class="w-5 h-5 shrink-0 border border-light/25 font-mono text-[10px] text-light/55 flex items-center justify-center">{{ $rank }}</span>
                    <span class="h-2 bg-light/25" style="width: {{ $width }}%"></span>
                    @if ($up)
                        <span class="ml-auto font-mono text-xs text-light">&uarr;</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</x-pages::services.components.mock-frame>
