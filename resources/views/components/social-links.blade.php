@props(['variant' => 'icons'])

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
                    data-pan="cta-social-{{ $social['key'] }}"
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
                    <a target="_blank" rel="noopener" data-pan="cta-social-{{ $social['key'] }}"
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

    @case('detailed')
        <div
            class="flex flex-col lg:flex-row w-fit lg:w-full md:w-auto mx-auto md:flex-row gap-8 md:gap-0 md:justify-between items-start md:items-center">
            @foreach ($socials as $social)
                <div>
                    <a target="_blank" rel="noopener" data-pan="cta-social-{{ $social['key'] }}"
                        href="{{ config('company.socials.' . $social['key'] . '.link') }}">
                        <div class="flex gap-2 items-center">
                            @svg($social['icon'], 'w-7')
                            <h2 class="text-xl font-semibold underline">
                                {{ config('company.socials.' . $social['key'] . '.username') }}
                            </h2>
                            @svg('fas-external-link-alt', 'w-3 self-start')
                        </div>
                        <h3 class="mt-1">{{ $social['label'] }}</h3>
                    </a>
                </div>
            @endforeach
        </div>
    @break
@endswitch
