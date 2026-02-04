<section class="flex flex-col justify-around min-h-screen h-screen max-h-screen">
    <div class="space-y-4">
        <h2 class="container px-4 lg:px-0 mx-auto text-center lg:text-left text-5xl lg:text-8xl font-black uppercase">
            {{ __('How I work') }}</h2>
        <h3
            class="container px-4 lg:px-0 mx-auto text-center lg:text-left text-xl lg:text-3xl xl:text-4xl font-semibold">
            {!! __('I create software that adapts to your way of working, :tag not the other way around:close_tag.', [
                'tag' => "<span class='underline decoration-4 underline-offset-3'>",
                'close_tag' => '</span>',
            ]) !!}
        </h3>
        <div
            class="max-w-full overflow-x-clip bg-linear-[to_right,transparent,var(--color-light)_35%,var(--color-light)_65%,transparent]">
            <span
                class="text-dark flex w-full max-w-full flex-nowrap text-2xl lg:text-4xl 2xl:text-6xl font-black uppercase py-4">
                <span class="animate-marquee flex flex-nowrap gap-8 text-nowrap whitespace-nowrap">
                    <h4>{{ __('MANAGEMENT SOFTWARES') }}</h4>
                    /
                    <h4>{{ __('ERP') }}</h4>
                    /
                    <h4>{{ __('CRM') }}</h4>
                    /
                    <h4>{{ __('WEBSITES') }}</h4>
                    /
                    <h4>{{ __('E-COMMERCE') }}</h4>
                    /
                    <h4>{{ __('SAAS') }}</h4>
                    /
                    <h4>{{ __('WEB PLATFORMS') }}</h4>
                    /
                    <h4>{{ __('PWA') }}</h4>
                    /
                    <h4>{{ __('AND ANYTHING ELSE THAT COMES TO YOUR MIND') }}</h4>
                    <span class="mr-8">/</span>
                </span>
                <span class="animate-marquee flex flex-nowrap gap-8 text-nowrap whitespace-nowrap">
                    <span>{{ __('MANAGEMENT SOFTWARES') }}</span>
                    /
                    <span>{{ __('ERP') }}</span>
                    /
                    <span>{{ __('CRM') }}</span>
                    /
                    <span>{{ __('WEBSITES') }}</span>
                    /
                    <span>{{ __('E-COMMERCE') }}</span>
                    /
                    <span>{{ __('SAAS') }}</span>
                    /
                    <span>{{ __('WEB PLATFORMS') }}</span>
                    /
                    <span>{{ __('PWA') }}</span>
                    /
                    <span>{{ __('AND ANYTHING ELSE THAT COMES TO YOUR MIND') }}</span>
                    <span class="mr-8">/</span>
                </span>
            </span>
        </div>
    </div>

    <div class="mx-auto flex flex-col gap-4 text-center w-fit">
        <div class="flex flex-col gap-1">
            <div class="text-sm">{{ __('PRODUCTS THAT ARE') }}</div>
            <div class="flex flex-col lg:flex-row justify-center gap-1 lg:gap-8 text-4xl lg:text-5xl font-black">
                <span>{{ __('RELIABLE') }}</span> <span class="hidden lg:block">/</span>
                <span>{{ __('INTUITIVE') }}</span> <span class="hidden lg:block">/</span>
                <span>{{ __('TAILORED') }}</span>
            </div>
            <div class="text-sm font-extralight">
                {{ __("just the way you're looking for them, just the way you imagine them.") }}</div>
        </div>
        <a href="#" wire:navigate class="text-lg lg:text-2xl underline underline-offset-4">
            {{ __('Software can revolutionize your business') }} 🡒</a>
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
