@props(['project', 'eager' => false])

<div class="cover">
    @if ($project->featured)
        <span class="badge">★ {{ __('Featured') }}</span>
    @endif

    <div class="in">
        @if ($project->hasMedia('featured_image'))
            <img src="{{ $project->featured_image_url }}" alt="" loading="{{ $eager ? 'eager' : 'lazy' }}">
        @else
            <span class="ghost" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($project->title, 0, 1)) }}</span>
        @endif
    </div>
</div>
