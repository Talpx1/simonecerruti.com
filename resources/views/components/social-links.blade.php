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

    $line_icons = [
        'linkedin' => '<rect x="3" y="3" width="18" height="18"/><path d="M8 10v7M8 7v.01M12 17v-7M12 13c0-2 1.5-3 3-3s2 1 2 3v4"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5v.01"/>',
        'github' => '<path d="M9 19c-4 1.3-4-2-6-2.5M15 21v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 0 0-1.3-3.2 4.2 4.2 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12 12 0 0 0-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.2 4.2 0 0 0-.1 3.2A4.6 4.6 0 0 0 4 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>',
        'bluesky' => '<path d="M12 11.5C10.6 8.8 6.8 3.8 4 3.5c-1.8-.2-1.5 2.6-1 5 .5 2.3 2.4 4 5.5 3.5-3 .7-4.3 2.3-2.5 4.3 3.1 3.4 5 .2 6-2.3 1 2.5 2.9 5.7 6 2.3 1.8-2 .5-3.6-2.5-4.3 3.1.5 5-1.2 5.5-3.5.5-2.4.8-5.2-1-5-2.8.3-6.6 5.3-8 8z"/>',
        'x' => '<path d="M4 4l16 16M20 4L4 20" transform="scale(.82) translate(2.6 2.6)"/>',
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

    @case('foot')
        <nav class="foot-soc" aria-label="{{ __('Social') }}">
            @foreach ($socials as $social)
                <a class="ico mag" href="{{ config('company.socials.' . $social['key'] . '.link') }}" target="_blank"
                    rel="noopener" aria-label="{{ $social['label'] }}" data-cursor="{{ $social['label'] }}"
                    data-pan="cta-social-{{ $social['key'] }}-{{ $placement }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">{!! $line_icons[$social['key']] !!}</svg>
                </a>
            @endforeach
        </nav>
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
