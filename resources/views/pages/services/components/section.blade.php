@props(['padding' => 'py-20 lg:py-28'])

<section data-reveal {{ $attributes->class(['border-t border-light/15', $padding]) }}>
    {{ $slot }}
</section>
