{{-- Monochrome CSS illustration: management dashboard (Area 01). Purely decorative. --}}
<x-pages::services.components.mock-frame :label="__('Management dashboard')">
    <div class="absolute inset-0 p-4 lg:p-6 flex flex-row gap-3 lg:gap-4">
        {{-- sidebar --}}
        <div class="w-1/4 max-w-[116px] shrink-0 border-r border-light/15 pr-3 lg:pr-4 flex flex-col gap-2">
            <span class="w-[22px] h-[22px] border border-light/25 mb-1"></span>
            <span class="h-2 w-[78%] bg-light"></span>
            <span class="h-2 bg-light/15"></span>
            <span class="h-2 bg-light/15"></span>
            <span class="h-2 bg-light/15"></span>
        </div>

        {{-- main --}}
        <div class="flex-1 min-w-0 flex flex-col gap-3 lg:gap-4">
            <div class="flex items-center justify-between">
                <span class="h-[9px] w-24 bg-light/15"></span>
                <span class="w-[22px] h-[22px] shrink-0 border border-light/25 bg-light/15"></span>
            </div>

            <div class="grid grid-cols-3 gap-2.5">
                @foreach (['207', '8377', '98%'] as $stat)
                    <div class="border border-light/15 p-2.5 flex flex-col gap-2">
                        <span class="font-mono text-sm lg:text-xl leading-none text-light">{{ $stat }}</span>
                        <span class="h-[5px] w-[64%] bg-light/25"></span>
                    </div>
                @endforeach
            </div>

            <div class="flex items-end gap-1.5 h-[22%] min-h-[34px]">
                @foreach ([46, 70, 38, 88, 60, 100, 54] as $bar)
                    <span class="flex-1 bg-gradient-to-t from-light/5 to-light/30" style="height: {{ $bar }}%"></span>
                @endforeach
            </div>

            <div class="mt-auto flex flex-col gap-2">
                <span class="h-[9px] bg-light/15"></span>
                <span class="h-[9px] w-[92%] bg-light/15"></span>
                <span class="h-[9px] w-[80%] bg-light/15"></span>
            </div>
        </div>
    </div>

</x-pages::services.components.mock-frame>
