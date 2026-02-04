<header id="main-header"
    {{ $attributes->merge(['class' => 'p-2 lg:p-4 flex justify-between items-center sticky top-0 z-[999] h-[100px] min-h-[100px] max-h-[100px] overflow-x-clip']) }}>

    <a class="flex items-center h-full max-w-[180px] p-4 backdrop-blur-md rounded-2xl" id="header-logo"
        href="{{ route('home') }}" wire:navigate>
        <x-app-logo alt="{{ config('app.name') }} logo" class="aspect-auto h-1/2 lg:h-full" />
    </a>



    <div class="flex items-center gap-2 lg:gap-8" id="header-tools">
        <x-lang-switcher />

        <button x-on:click="isMenuOpen=true" id="open-menu"
            class="backdrop-blur-md p-2 lg:p-4 rounded-2xl uppercase text-xl cursor-pointer">MENU</button>
    </div>
</header>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            const logo = document.querySelector("#header-logo");
            const tools = document.querySelector("#header-tools");

            let lastScrollY = window.scrollY;
            let isHidden = false;

            window.addEventListener("scroll", () => {
                const currentScrollY = window.scrollY;

                if (currentScrollY > lastScrollY && !isHidden && currentScrollY > 50) {
                    isHidden = true;
                    gsap.to(logo, {
                        x: "-100%",
                        opacity: 0,
                        duration: 0.5,
                        ease: "power2.out"
                    });
                    gsap.to(tools, {
                        x: "100%",
                        opacity: 0,
                        duration: 0.5,
                        ease: "power2.out"
                    });
                }

                if (currentScrollY < lastScrollY && isHidden) {
                    isHidden = false;
                    gsap.to(logo, {
                        x: "0%",
                        opacity: 1,
                        duration: 0.5,
                        ease: "power2.out"
                    });
                    gsap.to(tools, {
                        x: "0%",
                        opacity: 1,
                        duration: 0.5,
                        ease: "power2.out"
                    });
                }

                lastScrollY = currentScrollY;
            });
        }, {
            once: true
        })
    </script>
@endpush
