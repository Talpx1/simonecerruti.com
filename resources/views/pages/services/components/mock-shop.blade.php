{{-- Monochrome CSS illustration: e-commerce / web app (Area 02). Purely decorative. --}}
<div class="relative overflow-hidden aspect-[4/3] border border-light/15 bg-gradient-to-br from-light/5 to-transparent">
    <div class="absolute inset-0 p-4 lg:p-6 flex flex-col gap-2.5 lg:gap-3">
        {{-- top nav --}}
        <div class="flex items-center gap-3 pb-2.5 lg:pb-3 border-b border-light/15">
            <span class="w-5 h-5 shrink-0 border border-light/25"></span>
            <span class="flex gap-2">
                <span class="w-6 h-[7px] bg-light/15"></span>
                <span class="w-6 h-[7px] bg-light/15"></span>
                <span class="w-6 h-[7px] bg-light/15"></span>
            </span>
            <span class="ml-auto relative shrink-0 text-light">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                    stroke-linejoin="round" class="w-5 h-5" aria-hidden="true">
                    <path d="M2.5 3h2.2l2 11h10l1.8-8H6" />
                    <circle cx="9" cy="19" r="1.5" />
                    <circle cx="17" cy="19" r="1.5" />
                </svg>
                <span class="absolute -top-1 -right-1.5 w-[11px] h-[11px] bg-light"></span>
            </span>
        </div>

        {{-- hero banner --}}
        <div
            class="h-[19%] min-h-[44px] border border-light/15 bg-[repeating-linear-gradient(135deg,rgba(255,255,255,0.05)_0_1px,transparent_1px_10px)]">
        </div>

        {{-- product grid --}}
        <div class="grid grid-cols-3 gap-3">
            @foreach (range(1, 3) as $product)
                <div class="flex flex-col gap-1.5">
                    <div
                        class="aspect-[1/0.82] border border-light/15 bg-[repeating-linear-gradient(135deg,rgba(255,255,255,0.05)_0_1px,transparent_1px_9px)]">
                    </div>
                    <span class="h-1.5 w-[55%] bg-light/25"></span>
                    <span class="h-3.5 w-full border border-light/25"></span>
                </div>
            @endforeach
        </div>

        {{-- checkout bar --}}
        <div class="mt-auto border border-light/15 px-3.5 py-2.5 flex items-center justify-between gap-3">
            <span class="h-[7px] flex-1 max-w-[130px] bg-light/15"></span>
            <span class="w-[90px] h-[22px] shrink-0 bg-light"></span>
        </div>
    </div>

    <span
        class="absolute left-3 bottom-3 z-10 font-mono text-[10px] tracking-[0.14em] uppercase text-light/55 bg-dark/40 border border-light/15 px-2.5 py-1 backdrop-blur-sm">{{ __('E-commerce / web app') }}</span>
</div>
