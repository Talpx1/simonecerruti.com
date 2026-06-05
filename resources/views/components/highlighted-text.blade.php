{{-- Inverted highlight box for an emphasised word. Built to be injected into a
     translated heading via the :tag / :close_tag convention and compiled at
     runtime with Blade::render(), so the whole phrase stays one translation key:

     {!! Blade::render(__('The web that<br>:tag sells :close_tag and scales', [
         'tag' => '<x-highlighted-text>', 'close_tag' => '</x-highlighted-text>',
     ])) !!}

     The slot is trimmed because the :tag placeholder leaves surrounding spaces,
     which would otherwise show inside the inverted box. --}}
<span {{ $attributes->class(['bg-light text-dark px-[0.12em] box-decoration-clone']) }}>{{ trim($slot) }}</span>
