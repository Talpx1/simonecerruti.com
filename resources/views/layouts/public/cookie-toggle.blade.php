@props(['id', 'label' => null])

@php
    $property = $attributes->whereStartsWith('wire:model')->first();
@endphp

<button type="button" id="{{ $id }}" class="ck-sw" role="switch"
    aria-label="{{ $label ?? __('Toggle') }}" x-data
    x-bind:aria-checked="$wire.{{ $property }} ? 'true' : 'false'"
    x-on:click="$wire.{{ $property }} = ! $wire.{{ $property }}"></button>
