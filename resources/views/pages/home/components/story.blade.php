@php
    $chapters = [
        ['giant' => __('Chaos.'), 'index' => __('Chaos'), 'title' => __('Your work, today.'), 'text' => __('Scattered Excel files, lost emails, notes, phone calls. You call it management. It is chaos, and it costs you every day.')],
        ['giant' => __('Listening.'), 'index' => __('Listening'), 'title' => __('First I listen. To everything.'), 'text' => __('Every sheet, every email, every exception finds its place. I put in order what you really do, not what you think you do.')],
        ['giant' => __('Design.'), 'index' => __('Design'), 'title' => __('Then I design.'), 'text' => __('Modules, flows, rules, integrations. I engineer your process before writing a single line of code. Price and timing, in black and white.')],
        ['giant' => __('Structure.'), 'index' => __('Structure'), 'title' => __('Every screen has a reason.'), 'text' => __('I design the interface around the people who use it every day. No useless buttons, no extra steps.')],
        ['giant' => __('Construction.'), 'index' => __('Construction'), 'title' => __('Then I build it.'), 'text' => __('Screen after screen, with you inside the process. You watch it grow, try it, correct it. At delivery there are no surprises.')],
        ['giant' => __('Tailor-made.'), 'index' => __('Tailor-made'), 'title' => __('Here it is. Exactly as you imagined it.'), 'text' => __('It fills with your data, reads your emails, works in your place. Reliable, intuitive, yours. Your search ends here.')],
    ];
@endphp

<section class="hstory" id="method" data-sec="story">
    <div class="story-pin">
        <div class="giant" aria-hidden="true">
            @foreach ($chapters as $chapter)
                <span data-g="{{ $loop->index }}" @class(['fin' => $loop->last])>{{ $chapter['giant'] }}</span>
            @endforeach
        </div>

        <div class="ch-copy">
            @foreach ($chapters as $chapter)
                <div class="ch" data-c="{{ $loop->index }}">
                    <h2>{{ $chapter['title'] }}</h2>
                    <p>{{ $chapter['text'] }}</p>
                    @if ($loop->first)
                        <span class="hint">{{ __('Move the mouse between the pieces') }}</span>
                    @endif
                    @if ($loop->last)
                        <a class="more" style="margin-top: 22px" href="{{ route('how_i_work') }}" wire:navigate
                            data-pan="cta-home-method">{{ __('Find out how I work') }} <span>→</span></a>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="ch-index" role="group" aria-label="{{ __('Method chapters') }}">
            @foreach ($chapters as $chapter)
                <button type="button" data-go="{{ $loop->index }}"><i></i>{{ $chapter['index'] }}</button>
            @endforeach
        </div>
    </div>
</section>
