@props(['entries', 'size' => '2xs'])

<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-2']) }}>
    @foreach ($entries as $entry)
        <x-chip :$size>{{ __($entry) }}</x-chip>
    @endforeach
</div>
