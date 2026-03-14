<div class="min-h-full h-full max-h-full px-4 md:px-16 py-4" id="about">
    <h1 class="text-7xl lg:text-8xl text-center font-black">{{ __('ABOUT') }}</h1>
    <div class="grid grid-cols-1 lg:grid-cols-[3fr_1fr] mt-16 gap-y-16 lg:gap-y-0">
        <div
            class="space-y-16 lg:border-r lg:border-light pr-8 [&_h2]:text-4xl [&_h2]:mb-4 [&_h2]:uppercase [&_h2]:font-bold [&_p]:text-sm [&_p]:max-w-xl [&_p]:text-light/70 [&_p]:leading-loose">
            <div>
                <h2>{{ __('BIO') }}</h2>
                <div class="space-y-4">
                    <p>{{ __("I'm Simone, an independent software developer based in Biella, Italy.") }} </p>
                    <blockquote class="border-l-2 border-light pl-5 text-[15px] italic leading-relaxed font-light">
                        {{ __('I build software that quietly changes how businesses work.') }}
                    </blockquote>
                    <p>
                        {{ __('Not just websites, but above all complex management systems, ERPs, platforms, automation, AI-powered tools, and CRMs — software that sits at the core of a business and keeps everything running.') }}
                    </p>
                    <p>
                        {{ __("Over time, I've worked on large-scale, structured projects where performance, reliability, and scalability aren't optional — they're the foundation.") }}
                    </p>
                    <p>
                        {{ __("I'm not just a developer. I'm a technical partner. I step into your domain, truly understand how your business works, and guide you toward solutions that are often better than what you initially imagined.") }}
                    </p>
                </div>
                <x-chip-list class="mt-5" :entries="[
                    __('AI'),
                    __('ERP'),
                    __('CRM'),
                    __('WEB PLATFORMS'),
                    __('Automation'),
                    __('PWA'),
                    __('Laravel'),
                    __('Livewire'),
                    __('Filament'),
                    __('PHP'),
                    __('Vue'),
                    __('Docker'),
                    __('TypeScript'),
                    __('Tailwind'),
                ]" />
                <a wire:navigate href="{{ route('how_i_work') }}"
                    class="mt-4 font-semibold text-sm uppercase underline underline-offset-4 flex items gap-1">
                    {{ __('Discover how I work') }} <x-ri-arrow-right-long-line class="w-4" />
                </a>
            </div>

            <div id="about-how-i-started">
                <h2>{{ __('HOW I STARTED') }}</h2>
                <div class="space-y-4">
                    <p>{{ __("Here's the story: I was around 4 to 6 years old when my mom, while ironing, would let me mess around in Paint — yes, the doodling program that came on every Windows computer — on one of those old CRT monitors with a trackball mouse.") }}
                    </p>
                    <p>{{ __('I asked myself: "how does it know what I want to do? How does it make my drawing appear?"') }}
                    </p>
                    <p>{{ __("From that moment, I found myself choosing a technical high school focused on computer science. A choice I've never regretted — quite the opposite, one I've always loved and still do.") }}
                    </p>
                    <p>{{ __('What followed were various jobs: first at web agencies, then as a software developer for management systems, eventually becoming a Lead Developer.') }}
                    </p>
                    <p>{{ __("Now I'm following my dream: developing software as a freelancer, focusing on quality, on bespoke solutions, and on truly caring for the needs of each of my partners.") }}
                    </p>
                </div>
            </div>

            <div>
                <h2>{{ __('WHY I DO IT') }}</h2>
                <div class="space-y-4">
                    <p>{{ __('Out of passion.') }}</p>
                    <p>{{ __('Before being my job, software design and development are, first and foremost, my passion, my hobby, my genuine interest.') }}
                    </p>
                    <p>{{ __("The more skills I acquire, the more my passion grows. New technologies don't tire me — they excite me. What many call work, for me becomes more deeply rooted every day as a genuine interest, almost an instinct.") }}
                    </p>
                    <p>{{ __("That's why I'm a freelancer. Not by chance, but by deliberate choice: to work according to my own philosophies, to truly bring my vision to every project, to give the right attention to my partners and to the details of their software. The quality I pursue can't be improvised in an environment that doesn't allow for it.") }}
                    </p>
                </div>
            </div>

            <div>
                <h2>{{ __('ALWAYS LEARNING') }}</h2>
                <div class="space-y-4">
                    <p>{{ __("In software, evolution isn't linear — it's exponential. Keeping up isn't optional, it's part of the craft. And for me, it's also one of the most stimulating parts.") }}
                    </p>
                    <p>{{ __("Over the past few years I've integrated technologies that have profoundly changed the way I work — not to follow trends, but because the results I deliver to my partners must always match the moment we're living in.") }}
                    </p>
                    <p>{{ __("AI is the most significant phenomenon of this period, and it would be dishonest to ignore it. It's now an essential part not only of the industry, but of the developer's role itself. What once required a team of specialists can today be achieved by a single person — as long as they know how to use AI as a tool, not a shortcut. Someone who understands what it produces, evaluates its output, and maintains control.") }}
                    </p>
                    <p>{{ __("Over the past period, AI-assisted development has become one of my strengths. I've integrated these tools into my workflows deliberately, with concrete results: I work better, I work faster, and the final output is qualitatively superior.") }}
                    </p>
                </div>
            </div>

            <div>
                <h2>{{ __('PERSONAL INTERESTS') }}</h2>
                <div class="space-y-4">
                    <p>{{ __("I've talked at length about the professional side, and I'm proud of it. But I also want to dedicate a few lines to the person behind it all — because when you choose a partner to work with, you're ultimately choosing a person too.") }}
                    </p>
                    <p><b>{{ __('Friends.') }}</b>
                        {{ __("It's the first item because it's the one that brings me the most joy, and it's worth saying that clearly. A pizza, a board game night, an evening out dancing — the context doesn't matter much. What matters is being able to be myself, in the company of people with whom I can let my guard down and genuinely recharge.") }}
                    </p>
                    <p><b>{{ __('Motorcycle.') }}</b>
                        {{ __("Technically it's my friends' fault, but they didn't have to try very hard. In May 2025 I got my first motorcycle: a Kawasaki Z650, red and silver. I didn't know it yet, but it was already a passion just waiting to be discovered.") }}
                    </p>
                    <p><b>{{ __('Graphic design.') }}</b>
                        {{ __('A passion born alongside my love for code. As I was taking my first steps in tech, I was also experimenting with photo manipulation in Photoshop and motion design in After Effects. During high school I even seriously considered making it my career. In the end, software won — but the skills I built along the way have helped me enormously throughout my professional journey.') }}
                    </p>
                    <p><b>{{ __('Ethical hacking.') }}</b>
                        {{ __("To be honest, I'm far from an expert — and far from an intermediate level too. In ethical hacking, the distance between \"a few notions\" and \"expert\" is enormous. But the interest is genuine and goes back a long way: reverse engineering has always fascinated me, that backwards approach of taking something apart to understand how it was built. I've explored the subject through challenges on dedicated platforms, learned to think differently about software security, and those skills have proven genuinely useful in my work. If one day I were to change direction in tech, this would be a strong contender.") }}
                    </p>
                    <p><b>{{ __('Video games.') }}</b>
                        {{ __('From a NES-like console and Super Mario all the way to PC today — video games have been with me my whole life. Among the titles closest to my heart: Ratchet & Clank, Spyro, Horizon, The Division, Rocket League, Trackmania. And many others that would make this list far too long.') }}
                    </p>
                    <p><b>{{ __('Fitness.') }}</b>
                        {{ __("Gym and running — not out of declared passion, but out of conscious choice. When your work is entirely mental and sedentary, the physical counterbalance isn't optional.") }}
                    </p>
                </div>
            </div>
        </div>

        <div
            class="lg:sticky lg:top-8 lg:h-screen flex flex-col lg:overflow-hidden items-center gap-8 xl:gap-16 lg:pl-8">

            <x-app-logo class="max-w-72 2xl:max-w-96" />

            <x-ping-dot>
                {{ __('Available for new projects') }}
            </x-ping-dot>

            <div class="space-y-4">
                <a wire:navigate href="{{ route('how_i_work') }}" class="border border-light/[.15] p-5 block">
                    <p class="text-[9px] tracking-[.2em] uppercase text-light/35 mb-2">
                        {{ __('Process & Method') }}
                    </p>
                    <p
                        class="text-xl xl:text-2xl -tracking-tighter leading-none text-light uppercase flex items-center gap-1">
                        {{ __('How I work') }} <x-ri-arrow-right-long-line class="w-6" />
                    </p>
                    <p class="mt-1.5 text-xs text-light/40 leading-relaxed">
                        {{ __('Discover how I turn an idea into a product that truly works.') }}
                    </p>
                </a>

                <a wire:navigate href="{{ route('projects') }}" class="border border-light/[.15] p-5 block">
                    <p class="text-[9px] tracking-[.2em] uppercase text-light/35 mb-2">{{ __('Portfolio') }}</p>
                    <p
                        class="text-xl xl:text-2xl -tracking-tighter leading-none text-light uppercase flex items-center gap-1">
                        {{ __('Projects') }} <x-ri-arrow-right-long-line class="w-6" /></p>
                    <p class="mt-1.5 text-xs text-light/40 leading-relaxed">
                        {{ __('What I\'ve built, for whom, and with which technologies.') }}
                    </p>
                </a>

                <div class="border border-light/[.15] p-5">
                    <p class="text-[9px] -tracking-tight uppercase text-light/35 mb-3">{{ __('Direct contact') }}</p>
                    <a href="mailto:{{ config('company.contacts.email') }}"
                        class="block text-[16px] xl:text-lg tracking-wider text-light underline underline-offset-2 decoration-2 break-all hover:opacity-60 transition-opacity duration-200">
                        {{ config('company.contacts.email') }}
                    </a>
                </div>

                <a wire:navigate href="{{ route('contacts') }}"
                    class="flex items-center justify-between bg-light text-dark px-5 py-4">
                    <span
                        class="font-display text-sm xl:text-lg tracking-wider uppercase">{{ __('Don\'t wait, let\'s talk') }}</span>
                    <x-ri-arrow-right-long-line class="w-4" />
                </a>

                <div class="flex items-center gap-4 pt-4 border-t border-light/[.07]">
                    <a href="{{ config('company.socials.linkedin.link') }}" target="_blank" rel="noopener"
                        class="opacity-30 hover:opacity-100 text-light" title="LinkedIn">
                        <x-fab-linkedin class="w-4" />
                    </a>
                    <a href="{{ config('company.socials.github.link') }}" target="_blank" rel="noopener"
                        class="opacity-30 hover:opacity-100 text-light" title="GitHub">
                        <x-fab-github class="w-4" />
                    </a>
                    <a href="{{ config('company.socials.instagram.link') }}" target="_blank" rel="noopener"
                        class="opacity-30 hover:opacity-100 text-light" title="Instagram">
                        <x-fab-instagram class="w-4" />
                    </a>
                    <a href="{{ config('company.socials.x.link') }}" target="_blank" rel="noopener"
                        class="opacity-30 hover:opacity-100 text-light" title="X / Twitter">
                        <x-fab-x-twitter class="w-4" />
                    </a>
                    <a href="{{ config('company.socials.bluesky.link') }}" target="_blank" rel="noopener"
                        class="opacity-30 hover:opacity-100 text-light" title="BlueSky">
                        <x-fab-bluesky class="w-4" />
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush
