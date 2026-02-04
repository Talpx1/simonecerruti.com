@props(['seeAllRoute'])

<div class="grow flex flex-col">
    <div
        class="p-4 bg-light lg:bg-linear-[to_right,var(--color-light),transparent] flex flex-col lg:flex-row items-center justify-between">
        <h3 class="text-2xl 2xl:text-4xl uppercase text-dark">
            {{ $heading }}
        </h3>

        <a href="{{ $seeAllRoute }}" wire:navigate
            class="text-nowrap mt-2 lg:mt-0 text-dark lg:text-light lg:text-lg 2xl:text-2xl underline decoration-2 underline-offset-4 uppercase">
            {{ __('See all') }} 🡒
        </a>
    </div>
    <div class="grow grid grid-cols-1 lg:grid-cols-3 gap-16 lg:gap-8 p-4 2xl:p-8">
        @foreach (range(1, 3) as $i)
            <x-pages::home.components.article-card />
        @endforeach
    </div>
</div>
