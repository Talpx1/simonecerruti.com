@php
    $pains = [
        __('You have a website, but nobody contacts you through it.'),
        __('On Google they find your competitors, not you.'),
        __('You only sell in person, while others sell at night too.'),
        __('On a phone your website is slow and awkward.'),
        __('Website, inventory and invoices do not talk to each other.'),
    ];

    $offers = [
        [
            __('Websites and landing pages'),
            __('Designed for your audience, written to convert.'),
            [
                __('Tailor-made design, no themes'),
                __('Structure built for SEO'),
                __('Fast on a phone too'),
                __('Content you update yourself'),
            ],
        ],
        [
            __('E-commerce'),
            __('Your online shop, with your own selling rules.'),
            [
                __('Catalog, variants and price lists'),
                __('Integrated payments and shipping'),
                __('Connected to inventory and invoices'),
                __('B2B areas and reserved price lists'),
            ],
        ],
        [
            __('Platforms and web apps'),
            __('Portals and tools that work for you, right in the browser.'),
            [
                __('Private areas and roles'),
                __('Online bookings and case handling'),
                __('Portals for customers and suppliers'),
                __('Multilingual from top to bottom'),
            ],
        ],
    ];

    $faqs = [
        [
            __('How much does a tailor-made website or e-commerce cost?'),
            __('It depends on what it has to do. After a free call you get a precise quote. You can start with the essentials and add the rest when needed.'),
        ],
        [
            __('How long does it take?'),
            __('A first version goes online in a few weeks. Then it improves with the real data of the people using it.'),
        ],
        [
            __('Will people find me on Google?'),
            __('The website is born ready for Google: fast, well structured, with structured data and a sitemap. To climb the rankings you also need a content strategy, and I can follow it for you.'),
        ],
        [
            __('Can I update the content myself?'),
            __('Yes. Texts, images, products and articles are managed from a simple panel, without touching code.'),
        ],
        [
            __('Does it connect to management software, payments and shipping?'),
            __('Yes. The website talks to your management software, to payment systems and to couriers. No more orders copied by hand.'),
        ],
        [
            __('Does it work well on a phone?'),
            __('It is designed for the phone first, because that is where most of your customers come from.'),
        ],
        [
            __('Who owns the website in the end?'),
            __('You do. Code and content are yours, no lifelong subscriptions.'),
        ],
    ];
@endphp

<x-service-landing :area="\App\Enums\ServiceAreas::WEB_ECOMMERCE_PLATFORMS" pan="web-development" mode="flow"
    :name="__('Tailor-made websites, e-commerce and web apps')" :label="__('Tailor-made websites, e-commerce and web apps')"
    :rows="[__('Websites, e-commerce'), __('and web apps.')]"
    :schema_description="__('Tailor-made websites, e-commerce and platforms for SMEs: fast, ready for Google, connected to your tools and with code you own.')"
    :$pains :$offers
    :scrub="__('Good looking is the bare minimum. The rest is speed, Google and sales.')"
    :stack="['Laravel', 'PHP', 'Livewire', 'Vue', 'Tailwind', 'GSAP', __('Technical SEO'), __('Online payments'), 'Docker']"
    :ai="__('Search that understands what the customer is really looking for, personalized suggestions, assistants that answer at three in the morning too.')"
    :$projects :$faqs>
    <x-slot:intro>
        <p>{!! __('Not a showcase to look at: <em>a tool that brings customers and sales.</em>') !!}</p>
    </x-slot:intro>
</x-service-landing>
