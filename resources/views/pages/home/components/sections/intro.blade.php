<section class="min-h-screen h-screen max-h-screen grid content-center relative perspective-midrange -mt-[100px]">
    <x-app-logo weight="bold"
        class="w-1/2 absolute opacity-20 top-1/2 left-1/2 -translate-1/2 -z-10 perspective-origin-center transform-3d"
        id="first-section-logo" />

    <div class="px-5 lg:px-0 mx-auto relative">
        <p class="text-xl absolute left-0 -translate-x-full pr-4 bottom-2">{{ __("Hi, I'm Simone and...") }}</p>
        <h1 class=" text-6xl font-semibold tracking-tighter uppercase mx-auto">
            {!! __("I create the :tag software:close_tag you're looking for...", [
                'tag' => "<span class='bg-light text-dark'>",
                'close_tag' => '</span>',
            ]) !!}<br>
            {!! __("And what you didn't even know you :tag wished for :close_tag", [
                'tag' => "<span class='underline decoration-5 underline-offset-5'>",
                'close_tag' => '</span>',
            ]) !!}
        </h1>
    </div>

    <div class="to-light mt-1 flex w-full justify-end bg-linear-to-r from-transparent to-60% py-4">
        <h2 class="text-dark px-4 text-5xl font-black uppercase interactable">
            {{ __("Don't wait, let's talk") }} 🡒
        </h2>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            const el = document.querySelector("#first-section-logo");

            gsap.set(el, {
                rotationX: 0,
                rotationY: 0,
                transformPerspective: 800,
                transformOrigin: "center center",
                force3D: true
            });

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
