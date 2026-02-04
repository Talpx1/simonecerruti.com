<section
    class="p-4 2xl:p-16 grid grid-cols-1 lg:grid-cols-3 lg:grid-rows-2 gap-4 2xl:gap-16 min-h-screen lg:h-screen lg:max-h-screen">
    <div class="space-y-4 py-16">
        <h2 class="text-5xl lg:text-6xl 2xl:text-8xl font-black uppercase">{{ __('Projects') }}</h2>
        <p class="text-xl lg:text-2xl 2xl:text-4xl font-semibold uppercase">
            {{ __('Discover what I\'m working on') }}
        </p>
        <div class="text-xl lg:text-2xl underline underline-offset-4">
            {{ __('See all') }} 🡒</div>
        <div
            class="flex gap-4 items-center flex-wrap [&>a]:p-1 [&>a]:text-dark [&>a]:bg-light [&>a]:text-sm 2xl:[&>a]:text-lg [&>a]:underline [&>a]:underline-offset-1">
            <a href="">{{ __('#tag1') }}</a>
            <a href="">{{ __('#tag2') }}</a>
            <a href="">{{ __('#tag3') }}</a>
            <a href="">{{ __('#tag4') }}</a>
        </div>
    </div>
    @foreach (range(1, 5) as $i)
        <x-pages::home.components.project-card />
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
