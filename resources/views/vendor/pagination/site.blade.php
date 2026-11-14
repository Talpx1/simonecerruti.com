@if ($paginator->hasPages())
    @php
        $page_name = $paginator->getPageName();
        $scroll = "\$nextTick(() => document.getElementById('articoli')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))";
    @endphp

    <nav class="pager" aria-label="{{ __('Blog pages') }}">
        @if ($paginator->onFirstPage())
            <a class="pg-prev" aria-disabled="true">←</a>
        @else
            <a class="pg-prev" href="{{ $paginator->previousPageUrl() }}" aria-label="{{ __('Previous page') }}"
                wire:key="pager-prev" wire:click.prevent="gotoPage({{ $paginator->currentPage() - 1 }}, '{{ $page_name }}')"
                x-on:click="{{ $scroll }}">←</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <a aria-disabled="true">{{ $element }}</a>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page === $paginator->currentPage())
                        <a class="on" aria-current="page">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" wire:key="pager-{{ $page }}"
                            wire:click.prevent="gotoPage({{ $page }}, '{{ $page_name }}')"
                            x-on:click="{{ $scroll }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pg-next" href="{{ $paginator->nextPageUrl() }}" aria-label="{{ __('Next page') }}"
                wire:key="pager-next" wire:click.prevent="gotoPage({{ $paginator->currentPage() + 1 }}, '{{ $page_name }}')"
                x-on:click="{{ $scroll }}">→</a>
        @else
            <a class="pg-next" aria-disabled="true">→</a>
        @endif
    </nav>
@endif
