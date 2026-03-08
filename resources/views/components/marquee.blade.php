@props(['entries', 'entriesTag' => 'span', 'separator' => '/'])

<div
    class="max-w-full overflow-x-clip bg-linear-[to_right,transparent,var(--color-light)_35%,var(--color-light)_65%,transparent]">
    <span
        class="text-dark flex w-full max-w-full flex-nowrap text-2xl lg:text-4xl 2xl:text-6xl font-black uppercase py-4">
        <span class="animate-marquee flex flex-nowrap gap-8 text-nowrap whitespace-nowrap">
            @foreach ($entries as $entry)
                <{{ $entriesTag }}>{{ $entry }}</{{ $entriesTag }}>

                    @if ($separator && !$loop->last)
                        {{ $separator }}
                    @endif
            @endforeach
            <span class="mr-8">
                @if ($separator)
                    {{ $separator }}
                @endif
            </span>
        </span>
        <span class="animate-marquee flex flex-nowrap gap-8 text-nowrap whitespace-nowrap">
            @foreach ($entries as $entry)
                <span>{{ $entry }}</span>

                @if ($separator && !$loop->last)
                    {{ $separator }}
                @endif
            @endforeach
            <span class="mr-8">
                @if ($separator)
                    {{ $separator }}
                @endif
            </span>
        </span>
    </span>
</div>
