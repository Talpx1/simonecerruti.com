{{-- Monochrome CSS illustration: combined ERP / CRM workspace (Area 01 hero).
     Purely decorative — a tab bar, a KPI bar chart and a pipeline list. --}}
<x-pages::services.components.mock-frame :label="__('Management software · ERP · CRM')">
    <div class="absolute inset-0 p-4 lg:p-6 flex flex-col gap-3 lg:gap-4">
        {{-- top bar: logo · tabs · avatar --}}
        <div class="flex items-center gap-2.5 border-b border-light/15 pb-3 lg:pb-3.5">
            <span class="w-5 h-5 shrink-0 border border-light/25"></span>
            <span class="ml-1 flex gap-2">
                <i class="block w-[34px] h-2 bg-light"></i>
                <i class="block w-[34px] h-2 bg-light/15"></i>
                <i class="block w-[34px] h-2 bg-light/15"></i>
                <i class="block w-[34px] h-2 bg-light/15"></i>
            </span>
            <span class="ml-auto w-5 h-5 shrink-0 border border-light/25 bg-light/15"></span>
        </div>

        {{-- body: KPI/chart column + pipeline column --}}
        <div class="grid grid-cols-[1.3fr_1fr] gap-3 lg:gap-4 flex-1 min-h-0">
            <div class="flex flex-col gap-2.5 lg:gap-3 min-h-0">
                <div class="border border-light/15 p-2.5 lg:p-3 flex flex-col gap-2.5">
                    <div class="flex items-center justify-between">
                        <span class="h-[5px] w-[58%] bg-light/25"></span>
                        <span class="font-mono text-base lg:text-2xl leading-none tracking-tight text-light">+38%</span>
                    </div>
                    <div class="flex items-end gap-1.5 h-14">
                        @foreach ([42, 66, 50, 84, 60, 100, 72] as $bar)
                            <i class="flex-1 block bg-gradient-to-t from-light/[0.06] to-light/30"
                                style="height: {{ $bar }}%"></i>
                        @endforeach
                    </div>
                </div>
                <div class="mt-auto flex flex-col gap-2">
                    <span class="h-2 bg-light/15"></span>
                    <span class="h-2 w-[88%] bg-light/15"></span>
                    <span class="h-2 w-[72%] bg-light/15"></span>
                </div>
            </div>

            <div class="border border-light/15 p-2.5 lg:p-3 flex flex-col gap-2.5">
                <div class="flex items-center justify-between">
                    <span class="h-[5px] w-[58%] bg-light/25"></span>
                    <span class="font-mono text-base lg:text-2xl leading-none tracking-tight text-light">207</span>
                </div>
                <div class="flex flex-col gap-2.5">
                    @foreach ([['on', 80], ['on', 62], ['off', 46], ['off', 70]] as [$state, $width])
                        <div class="flex items-center gap-2.5">
                            <span @class([
                                'w-2 h-2 shrink-0',
                                'bg-light' => $state === 'on',
                                'bg-light/25' => $state === 'off',
                            ])></span>
                            <span class="h-2 bg-light/15" style="width: {{ $width }}%"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-pages::services.components.mock-frame>
