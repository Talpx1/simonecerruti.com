<div class="article-gallery">
    @foreach ($images as $image)
        <figure>
            <img src="{{ $image['url'] }}" alt="{{ $image['caption'] }}" loading="lazy">
            @if ($image['caption'])
                <figcaption>{{ $image['caption'] }}</figcaption>
            @endif
        </figure>
    @endforeach
</div>
