<div class="grid grid-cols-4 gap-2">
    @foreach ($images as $image)
        <figure>
            <img src="{{ $image['url'] }}" alt="{{ $image['caption'] }}" loading="lazy">
        </figure>
    @endforeach
</div>
