<div class="fixed bottom-2 left-1/2 -translate-x-1/2 shadow-2xl z-50">
    <div
        class="relative bg-light rounded-full p-1 flex items-center text-dark
            [&_*]:transition-all
            [&_*]:duration-500

            [&>.floating-contact]:p-2
            [&>.floating-contact]:hover:bg-dark
            [&>.floating-contact]:hover:text-light

            [&_.floating-contact-text]:backdrop-blur-xl
            [&_.floating-contact-text]:p-2
            [&_.floating-contact-text]:rounded-full
            [&_.floating-contact-text]:absolute
            [&_.floating-contact-text]:-top-8
            [&_.floating-contact-text]:-translate-1/2
            [&_.floating-contact-text]:w-max
            [&_.floating-contact-text]:uppercase
            [&_.floating-contact-text]:font-black
            [&_.floating-contact-text]:text-light
            [&_.floating-contact-text]:text-center
            [&_.floating-contact-text]:left-1/2
            [&_.floating-contact-text]:text-2xl
            [&_.floating-contact-text]:opacity-0
            [&_.floating-contact-text]:invisible
    ">
        <a href="mailto:{{ config('company.contacts.email') }}"
            class="floating-contact hover:[&_.floating-contact-text]:opacity-100 hover:[&_.floating-contact-text]:visible rounded-l-full block">
            <div>
                <span class="floating-contact-text">{{ config('company.contacts.email') }}</span>
                <x-heroicon-o-envelope class="w-5" />
            </div>
        </a>

        <a href="{{ route('contacts') }}" class="floating-contact rounded-r-full block font-black">
            HIRE
        </a>
    </div>
</div>
