<section class="lg:px-4 py-16 flex flex-col lg:flex-row gap-4 min-h-screen lg:h-screen lg:max-h-screen">
    <h2 class="text-6xl 2xl:text-8xl font-black uppercase text-center" id="home-blog-heading">
        {{ __('Blog') }}
    </h2>
    <style>
        @media (min-width: 1024px) {
            #home-blog-heading {
                writing-mode: sideways-lr;
                text-orientation: sideways;
            }
        }
    </style>
    <div class="lg:border lg:border-light grid gris-cols-1 grid-rows-2 gap-8 lg:gap-0 lg:justify-between">
        <x-pages::home.components.articles-list :articles="$practical_blog_articles" see-all-route="">
            <x-slot:heading>
                {!! __(':tag Practical :close_tag Articles', [
                    'tag' => "<span class='font-black'>",
                    'close_tag' => '</span>',
                ]) !!}
            </x-slot:heading>
        </x-pages::home.components.articles-list>
        <x-pages::home.components.articles-list :articles="$technical_blog_articles" see-all-route="">
            <x-slot:heading>
                {!! __(':tag Technical :close_tag Articles', [
                    'tag' => "<span class='font-black'>",
                    'close_tag' => '</span>',
                ]) !!}
            </x-slot:heading>
        </x-pages::home.components.articles-list>
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
