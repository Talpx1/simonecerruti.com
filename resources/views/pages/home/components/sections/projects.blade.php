<section class="p-4 grid grid-cols-3 grid-rows-2 gap-16 min-h-screen h-screen max-h-screen">
    <div class="space-y-4 p-16">
        <h2 class="text-8xl font-black uppercase">{{ __('Projects') }}</h2>
        <p class="text-4xl font-semibold uppercase">
            {{ __('Discover what I\'m working on') }}
        </p>
        <div class="text-2xl underline underline-offset-4">
            {{ __('See all') }} 🡒</div>
        <div
            class="flex gap-4 items-center flex-wrap [&>a]:p-1 [&>a]:text-dark [&>a]:bg-light [&>a]:text-lg [&>a]:underline [&>a]:underline-offset-1">
            <a href="">{{ __('#tag1') }}</a>
            <a href="">{{ __('#tag2') }}</a>
            <a href="">{{ __('#tag3') }}</a>
            <a href="">{{ __('#tag4') }}</a>
        </div>
    </div>
    @foreach (range(1, 5) as $i)
        <div class="border border-light p-4 flex flex-col">
            <img src="https://picsum.photos/1920/1080"
                class="max-h-1/2 h-1/2 min-h-1/2 object-cover object-center mask-b-from-60%">
            <div class="gap-4 flex flex-col justify-between grow">
                <h3 class="text-4xl font-bold uppercase -mt-[1em]">
                    Lorem ipsum dolor sit amet.
                </h3>

                <h4 class="text-xl">
                    Lorem ipsum dolor sit, amet consectetur adipisicing elit. Nesciunt officiis nam odio tempora quo,
                    provident aperiam quos quis impedit consequatur!
                </h4>
                <div class="flex justify-between items-center [&>a]:underline [&>a]:underline-offset-4 [&>a]:text-lg">
                    <a href="">{{ __('Discover more') }}</a>
                    <a href="">{{ __('GitHub') }}</a>
                    <a href="">{{ __('Website') }}</a>
                </div>

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
</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {


        }, {
            once: true
        })
    </script>
@endpush
