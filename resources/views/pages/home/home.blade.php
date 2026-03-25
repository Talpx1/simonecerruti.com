<div class="h-full" id="homepage">
    @include('pages.home.components.sections.intro')
    @include('pages.home.components.sections.about')
    @include('pages.home.components.sections.how-i-work')

    @if ($projects->isNotEmpty())
        @include('pages.home.components.sections.projects')
    @endif

    @if ($blog_articles->isNotEmpty())
        @include('pages.home.components.sections.blog')
    @endif

    @include('pages.home.components.floating-contacts')
</div>

@push('scripts')
    <script>
        document.addEventListener('resize', () => window.snap.resize())

        document.addEventListener('livewire:navigated', () => {
            if (!window.isDesktop) {
                return
            }
            window.snap.addElements(document.querySelectorAll('#homepage>section'), {
                align: ['start', 'end']
            })
        }, {
            once: true
        })
    </script>
@endpush
