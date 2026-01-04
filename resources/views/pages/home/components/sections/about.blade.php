<section class="container mx-auto flex items-center justify-center min-h-screen h-screen max-h-screen">
    <img src="{{ asset('images/about_protrait.webp') }}" alt="Simone Cerruti protrait"
        class="translate-x-32 object-contain z-0">
    <div class="grid place-content-center gap-16 z-10 mix-blend-difference">
        <h2 class="text-8xl font-black uppercase">{{ __('About Simone') }}</h2>
        <p class="text-2xl translate-x-16">
            {{ __('Appassionato di informatica da... beh, da quando ho memoria.') }} <small
                class="text-sm underline decoration-1 underline-offset-2">{{ __("Scopri l'aneddoto") }}
                🡒</small>
            <br>
            {!! __(
                'Adesso ho :current_age anni e ADORO creare software che aiutano le aziende nelle loro attività quotidiane.',
                [
                    'current_age' => floor(now()->diffInYears('03/04/2001', true)),
                ],
            ) !!}
        </p>
        <p class="translate-x-16 text-xl underline decoration-3 underline-offset-2 font-bold uppercase">Ti spiego come lo
            faccio 🡒
        </p>
    </div>
</section>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {


        }, {
            once: true
        })
    </script>
@endpush
