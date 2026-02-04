<section
    class="min-h-screen h-screen max-h-screen grid content-center relative perspective-midrange -mt-[100px] gap-8 lg:gap-0">
    <x-app-logo weight="bold"
        class="order-1 lg:order-0 w-full lg:w-1/2 lg:absolute opacity-20 lg:top-1/2 lg:left-1/2 lg:-z-10 perspective-origin-center transform-3d"
        id="home-intro-logo" />

    <div class="order-0 lg:order-1 px-5 lg:px-0 mx-auto relative">
        <p class="text:xs lg:text-lg 2xl:text-xl lg:absolute lg:left-0 lg:-translate-x-full lg:pr-4 lg:bottom-2">
            {{ __("Hi, I'm Simone and...") }}
        </p>
        <h1 class="text-2xl lg:text-4xl 2xl:text-6xl font-semibold tracking-tighter uppercase mx-auto">
            {!! __("I create the :tag software:close_tag you're looking for...", [
                'tag' => "<span class='bg-light text-dark'>",
                'close_tag' => '</span>',
            ]) !!}<br>
            {!! __("And what you didn't even know you :tag wished for :close_tag", [
                'tag' => "<span class='underline decoration-3 lg:decoration-5 underline-offset-3 lg:underline-offset-5'>",
                'close_tag' => '</span>',
            ]) !!}
        </h1>
    </div>

    <div class="to-light mt-1 flex w-full justify-end bg-linear-to-r from-transparent to-60% py-4 order-2">
        <a href="{{ route('contacts') }}" wire:navigate>
            <h2 class="text-dark px-4 text-xl lg:text-4xl 2xl:text-5xl font-black uppercase interactable">
                {{ __("Don't wait, let's talk") }} 🡒
            </h2>
        </a>
    </div>
    <x-filament-actions::modals />
</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            if (window.isMobile) {
                return
            }

            const el = document.querySelector("#home-intro-logo");

            const centerElement = () => {
                gsap.set(el, {
                    xPercent: -50,
                    yPercent: -50,
                    rotationX: 0,
                    rotationY: 0,
                    transformPerspective: 800,
                    transformOrigin: "center center",
                    force3D: true
                });
            };

            centerElement();

            const setRotX = gsap.quickTo(el, "rotationX", {
                duration: 0.4,
                ease: "power3.out"
            });
            const setRotY = gsap.quickTo(el, "rotationY", {
                duration: 0.4,
                ease: "power3.out"
            });

            const onMove = e => {
                const x = e.clientX / window.innerWidth - 0.5;
                const y = e.clientY / window.innerHeight - 0.5;
                const max = 12;

                setRotY(x * max);
                setRotX(-y * max);
            };

            document.addEventListener("mousemove", onMove);
            window.listenersToRemove.push(["mousemove", onMove]);

            document.addEventListener("mouseleave", () => {
                gsap.to(el, {
                    rotationX: 0,
                    rotationY: 0,
                    duration: 0.6,
                    ease: "power3.out"
                });
            });


        }, {
            once: true
        })
    </script>
@endpush
