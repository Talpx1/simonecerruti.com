<div class="relative">
    <img src="https://picsum.photos/1920/1080"
        class="aspect-square 2xl:max-w-2/3 h-full object-cover object-center brightness-50 border-[16px] 2xl:border-[32px]">
    <div
        class="absolute top-0 2xl:top-1/2 2xl:-translate-y-1/2 space-y-2 2xl:space-y-4 left-2 2xl:left-1/2 2xl:-translate-x-1/3">
        <h4 class="uppercase font-bold text-2xl 2xl:text-4xl">
            Lorem ipsum dolor sit amet.
        </h4>
        <p class="text-lg 2xl:text-xl">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Molestias eos earum saepe
            consequuntur at ipsam, provident aliquam commodi fugiat adipisci.
        </p>
        <x-post-tag-list :tags="[
            __('#tag1') => '#',
            __('#tag2') => '#',
            __('#tag3') => '#',
            __('#tag4') => '#',
        ]" />
    </div>
</div>
