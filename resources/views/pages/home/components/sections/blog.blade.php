<section class="p-4 flex gap-4 min-h-screen h-screen max-h-screen">
    <h2 class="text-8xl font-black uppercase text-center" style="writing-mode: sideways-lr; text-orientation: sideways;">
        {{ __('Blog') }}
    </h2>
    <div class="border border-light flex flex-col justify-between gap-16">
        <div class="h-1/2 max-h-1/2 flex flex-col">
            <div class="p-4 bg-linear-[to_right,var(--color-light),transparent] flex items-center justify-between">
                <h3 class="text-4xl uppercase text-dark">
                    {!! __(':tag Practical :close_tag Articles', [
                        'tag' => "<span class='font-black'>",
                        'close_tag' => '</span>',
                    ]) !!}
                </h3>

                <a href="" class="text-2xl underline decoration-2 underline-offset-4 uppercase">
                    {{ __('See all') }} 🡒
                </a>
            </div>
            <div class="grow grid grid-cols-3 gap-8 p-8">
                @foreach (range(1, 3) as $i)
                    <div class="relative">
                        <img src="https://picsum.photos/1920/1080"
                            class="w-2/3 h-full object-cover object-center brightness-50 border-[32px]">
                        <div class="absolute top-1/2 -translate-y-1/2 space-y-4 left-1/2 -translate-x-1/3">
                            <h4 class="uppercase font-bold text-4xl">
                                Lorem ipsum dolor sit amet.
                            </h4>
                            <p class="font-xl">
                                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestias eos earum saepe
                                consequuntur at ipsam, provident aliquam commodi fugiat adipisci.
                            </p>
                            <div
                                class="flex gap-4 items-center flex-wrap [&>a]:p-1 [&>a]:text-dark [&>a]:bg-light [&>a]:text-lg [&>a]:underline [&>a]:underline-offset-1">
                                <a href="">{{ __('#tag1') }}</a>
                                <a href="">{{ __('#tag2') }}</a>
                                <a href="">{{ __('#tag3') }}</a>
                                <a href="">{{ __('#tag4') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="h-1/2 max-h-1/2 flex flex-col">
            <div class="p-4 bg-linear-[to_right,var(--color-light),transparent] flex items-center justify-between">
                <h3 class="text-4xl uppercase text-dark">
                    {!! __(':tag Technical :close_tag Articles', [
                        'tag' => "<span class='font-black'>",
                        'close_tag' => '</span>',
                    ]) !!}
                </h3>

                <a href="" class="text-2xl underline decoration-2 underline-offset-4 uppercase">
                    {{ __('See all') }} 🡒
                </a>
            </div>
            <div class="grow grid grid-cols-3 gap-8 p-8">
                @foreach (range(1, 3) as $i)
                    <div class="relative">
                        <img src="https://picsum.photos/1920/1080"
                            class="w-2/3 h-full object-cover object-center brightness-50 border-[32px]">
                        <div class="absolute top-1/2 -translate-y-1/2 space-y-4 left-1/2 -translate-x-1/3">
                            <h4 class="uppercase font-bold text-4xl">
                                Lorem ipsum dolor sit amet.
                            </h4>
                            <p class="font-xl">
                                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestias eos earum saepe
                                consequuntur at ipsam, provident aliquam commodi fugiat adipisci.
                            </p>
                            <div
                                class="flex gap-4 items-center flex-wrap [&>a]:p-1 [&>a]:text-dark [&>a]:bg-light [&>a]:text-lg [&>a]:underline [&>a]:underline-offset-1">
                                <a href="">{{ __('#tag1') }}</a>
                                <a href="">{{ __('#tag2') }}</a>
                                <a href="">{{ __('#tag3') }}</a>
                                <a href="">{{ __('#tag4') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
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
