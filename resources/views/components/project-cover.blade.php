@props(['project', 'eager' => false])

<div class="project-cover">
    @if ($project->featured)
        <span class="project-cover-badge">★ {{ __('Featured') }}</span>
    @endif

    <div class="project-cover-inner">
        @if ($project->hasMedia('featured_image'))
            <img src="{{ $project->featured_image_url }}" alt="" loading="{{ $eager ? 'eager' : 'lazy' }}">
        @else
            <span class="before-after-screen-watermark" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($project->title, 0, 1)) }}</span>
        @endif
    </div>
</div>
