@if ($this->categories->isNotEmpty())
    <div class="filter-bar" role="group" aria-label="{{ __('Categories') }}">
        <button type="button" wire:click="filterByCategory(null)" @class(['is-active' => $this->active_category === null])>{{ __('All') }}</button>

        @foreach ($this->categories as $category)
            <button type="button" wire:key="category-{{ $category->id }}" wire:click="filterByCategory('{{ $category->slug }}')"
                @class(['is-active' => $this->active_category === $category->slug])>{{ $category->name }}</button>
        @endforeach
    </div>
@endif
