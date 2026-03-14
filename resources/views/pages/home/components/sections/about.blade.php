<section
    class="container mx-auto flex flex-col lg:flex-row items-center justify-center min-h-screen h-screen max-h-screen px-4 lg:px-0">
    <img src="{{ asset('images/about_protrait.webp') }}" alt="Simone Cerruti protrait" id="home-about-protrait-img"
        class="w-2/3 lg:w-auto lg:translate-x-32 object-contain z-0">
    <div class="grid place-content-center gap-8 lg:gap-16 z-10 mix-blend-difference -mt-14">
        <h2 class="text-center lg:text-left text-6xl lg:text-8xl font-black uppercase">
            {{ __('About Simone') }}</h2>
        <div class="text-xl lg:text-2xl lg:translate-x-16 space-y-4 lg:space-y-0">
            <p>
                {{ __("I've been passionate about IT since... well, since I can remember.") }}
                <a href="{{ route('about') }}#about-how-i-started" wire:navigate
                    class="text-sm underline decoration-1 underline-offset-2 inline-flex gap-1 items-center">
                    {{ __('Discover the anecdote') }} <x-ri-arrow-right-long-line class="w-4" />
                </a>
            </p>

            <p>
                {!! __(
                    'I\'m now :current_age years old and I LOVE creating software that helps businesses in their everyday work.',
                    [
                        'current_age' => floor(
                            now()->diffInYears(\Illuminate\Support\Carbon::createFromFormat('d/m/Y', '03/04/2001'), true),
                        ),
                    ],
                ) !!}
            </p>
        </div>
        <div
            class="lg:translate-x-16 text-xl underline decoration-3 underline-offset-2 font-bold uppercase flex flex-col xl:flex-row justify-between gap-4 xl:gap-0">
            <a href="{{ route('how_i_work') }}" wire:navigate class="flex gap-1 items-center">
                {{ __('Let me explain how I do it') }} <x-ri-arrow-right-long-line class="w-6" />
            </a>
            <a href="{{ route('about') }}" wire:navigate class="flex gap-1 items-center">
                {{ __('Find out more about me') }} <x-ri-arrow-right-long-line class="w-6" />
            </a>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            if (window.isMobile) {
                return
            }

            const img = document.getElementById('home-about-protrait-img');

            if (!img) return;

            const strength = 0.005;

            const handleMouseMove = (e) => {
                const rect = img.getBoundingClientRect();
                const imgCenterX = rect.left + rect.width / 2;
                const imgCenterY = rect.top + rect.height / 2;

                const deltaX = (e.clientX - imgCenterX) * 0.02;
                const deltaY = (e.clientY - imgCenterY) * 0.02;

                const maxMove = 15;
                const clampedX = Math.max(-maxMove, Math.min(maxMove, deltaX));
                const clampedY = Math.max(-maxMove, Math.min(maxMove, deltaY));

                gsap.to(img, {
                    x: 128 + clampedX,
                    y: clampedY,
                    duration: 0.6,
                    ease: 'power2.out'
                });
            };

            document.addEventListener('mousemove', handleMouseMove);

            window.listenersToRemove.push(['mousemove', handleMouseMove]);
        }, {
            once: true
        })
    </script>
@endpush
