@props(['variant' => 'icons', 'placement' => null])

@php
    // The placement discriminator keeps `data-pan` names unique per location
    // (menu, footer, contacts page), so Pan stops warning about duplicate names
    // and can attribute social clicks to where they happened.
    $placement ??= $variant;
@endphp

@php
    /**
     * Single source of truth for the social links rendered across the site.
     * `link` and `username` are pulled from config('company.socials') by `key`.
     *
     * @var array<int, array{key: string, label: string, icon: string}> $socials
     */
    $socials = [
        ['key' => 'linkedin', 'label' => 'LinkedIn', 'icon' => 'fab-linkedin'],
        ['key' => 'instagram', 'label' => 'Instagram', 'icon' => 'fab-instagram'],
        ['key' => 'github', 'label' => 'GitHub', 'icon' => 'fab-github'],
        ['key' => 'bluesky', 'label' => 'BlueSky', 'icon' => 'fab-bluesky'],
        ['key' => 'x', 'label' => 'X / Twitter', 'icon' => 'fab-x-twitter'],
    ];
@endphp

@switch($variant)
    @case('icons')
        <div class="flex items-center gap-4">
            @foreach ($socials as $social)
                <a href="{{ config('company.socials.' . $social['key'] . '.link') }}" target="_blank" rel="noopener"
                    data-pan="cta-social-{{ $social['key'] }}-{{ $placement }}"
                    class="opacity-30 hover:opacity-100 text-light" title="{{ $social['label'] }}">
                    @svg($social['icon'], 'w-4')
                </a>
            @endforeach
        </div>
    @break

    @case('menu')
        <div class="flex flex-row lg:flex-col justify-between lg:gap-3">
            @foreach ($socials as $social)
                <div>
                    <a target="_blank" rel="noopener" data-pan="cta-social-{{ $social['key'] }}-{{ $placement }}"
                        href="{{ config('company.socials.' . $social['key'] . '.link') }}">
                        <div
                            class="flex gap-2 items-center text-xs uppercase tracking-widest text-light/40 hover:text-light hover:tracking-[.2em] transition-all duration-200">
                            @svg($social['icon'], 'w-5 lg:w-3')
                            <span class="hidden lg:inline">{{ $social['label'] }}</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @break

    @case('grid')
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 border-t border-l border-light/15">
            @foreach ($socials as $social)
                <a href="{{ config('company.socials.' . $social['key'] . '.link') }}" target="_blank" rel="noopener"
                    data-pan="cta-social-{{ $social['key'] }}-{{ $placement }}"
                    class="group flex flex-col border-r border-b border-light/15 p-6 lg:p-7 transition-colors duration-300 hover:bg-light/[0.03]">
                    @svg($social['icon'], 'w-7 mb-10 lg:mb-12 text-light/40 group-hover:text-light transition-colors duration-200')
                    <span class="flex items-center gap-2 font-bold leading-tight tracking-tight text-light">
                        {{ config('company.socials.' . $social['key'] . '.username') }}
                        <x-ri-arrow-right-up-line
                            class="w-3.5 shrink-0 text-light/40 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-light" />
                    </span>
                    <span class="mt-3 text-xs uppercase tracking-[0.18em] text-light/40">{{ $social['label'] }}</span>
                </a>
            @endforeach
        </div>
    @break
@endswitch
