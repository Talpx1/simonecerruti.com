<section class="flex flex-col justify-around min-h-screen h-screen max-h-screen">
    <div class="space-y-4">
        <div class="container px-4 lg:px-0 mx-auto text-center lg:text-left space-y-4">
            <h2 class="text-5xl lg:text-8xl font-black uppercase">
                {{ __('How I work') }}
            </h2>

            <h3 class="text-xl lg:text-3xl xl:text-4xl font-semibold">
                {!! __('I create software that adapts to your way of working, :tag not the other way around:close_tag.', [
                    'tag' => "<span class='underline decoration-4 underline-offset-3'>",
                    'close_tag' => '</span>',
                ]) !!}
            </h3>

            <a href="{{ route('how_i_work') }}" wire:navigate
                class="text-lg lg:text-2xl underline decoration-2 underline-offset-4 font-semibold flex items-center gap-1">
                {{ __('Discover my method') }} <x-ri-arrow-right-long-line class="w-6" />
            </a>
        </div>
        <x-marquee :entries="[
            __('MANAGEMENT SOFTWARES'),
            __('ERP'),
            __('CRM'),
            __('WEBSITES'),
            __('E-COMMERCE'),
            __('SAAS'),
            __('WEB PLATFORMS'),
            __('PWA'),
            __('AND ANYTHING ELSE THAT COMES TO YOUR MIND'),
        ]" entriesTag="h4" />
    </div>

    <div class="mx-auto flex flex-col gap-4 text-center w-fit">
        <div class="flex flex-col gap-1">
            <div class="text-sm">{{ __('PRODUCTS THAT ARE') }}</div>
            <div class="flex flex-col lg:flex-row justify-center gap-1 lg:gap-8 text-4xl lg:text-5xl font-black">
                <span>{{ trans_choice('RELIABLE', 2) }}</span> <span class="hidden lg:block">/</span>
                <span>{{ __('INTUITIVE') }}</span> <span class="hidden lg:block">/</span>
                <span>{{ __('BESPOKE') }}</span>
            </div>
            <div class="text-sm font-extralight">
                {{ __("just the way you're looking for them, just the way you imagine them.") }}</div>
        </div>
        <a href="#" wire:navigate
            class="text-lg lg:text-2xl underline underline-offset-4 flex items-center gap-1 mx-auto">
            {{ __('Software can revolutionize your business') }} <x-ri-arrow-right-long-line class="w-6" /></a>
    </div>

    <div class="container mx-auto grid grid-cols-[1fr_auto_1fr] text-lg lg:text-3xl xl:text-4xl gap-8">
        <div class="text-right">{{ __('IF YOU CAN DREAM IT') }}</div>
        <div class="w-18 justify-self-center">
            <x-app-logo weight="bold" />
        </div>
        <div>{{ __('I CAN BUILD IT') }}</div>
    </div>

</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {


        }, {
            once: true
        })
    </script>
@endpush
