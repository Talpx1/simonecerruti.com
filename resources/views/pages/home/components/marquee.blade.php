@php
    $first_row = [__('Management software'), __('ERP'), __('CRM'), __('Websites'), __('E-commerce'), __('SaaS')];
    $second_row = [__('Web platforms'), __('PWA'), __('Apps'), __('Automations')];
@endphp

<section class="mq" data-sec="mq" aria-label="{{ __('What I build') }}">
    <div class="mq-row" data-dir="1">
        <div class="mq-in">
            @foreach (range(1, 2) as $copy)
                @foreach ($first_row as $word)
                    <span>{{ $word }}</span><i></i>
                @endforeach
            @endforeach
        </div>
    </div>
    <div class="mq-row o" data-dir="-1">
        <div class="mq-in">
            @foreach (range(1, 2) as $copy)
                @foreach ($second_row as $word)
                    <span>{{ $word }}</span><i></i>
                @endforeach
                <em>{{ __('and whatever you have in mind') }}</em><i></i>
            @endforeach
        </div>
    </div>
</section>
