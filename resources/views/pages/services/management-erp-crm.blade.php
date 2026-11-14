@php
    $pains = [
        __('Excel files everywhere, each with its own version of the truth.'),
        __('Departments that do not talk to each other and information lost along the way.'),
        __('Hours spent copying data from one program to another.'),
        __('Figures updated at the end of the month, when it is already too late.'),
        __('A bought management system that forces you to work its way.'),
    ];

    $offers = [
        [
            __('Tailor-made management software'),
            __('The operational heart of the company, designed around your processes.'),
            [
                __('Orders, inventory, customers and suppliers'),
                __('Workflows and permissions for each role'),
                __('Automations for deadlines and notifications'),
                __('Always up-to-date reports'),
            ],
        ],
        [
            __('ERP'),
            __('The whole company in a single system, from purchasing to invoicing.'),
            [
                __('Planning and production'),
                __('Purchasing, sales and logistics'),
                __('Integrated electronic invoicing'),
                __('One piece of data, valid for everyone'),
            ],
        ],
        [
            __('CRM'),
            __('Customers, deals and communications in a single place.'),
            [
                __('Complete history of every customer'),
                __('Tailor-made sales pipeline'),
                __('Automatic reminders and follow-up'),
                __('Email and WhatsApp connected'),
            ],
        ],
    ];

    $faqs = [
        [
            __('How much does tailor-made management software cost?'),
            __('It depends on what it has to do. After a free 15-minute call you get a precise quote, not a range. And you can start from a first module, to spread the investment over time.'),
        ],
        [
            __('How long does it take to get it?'),
            __('A first usable version arrives in a few weeks. Then the software grows one piece at a time, while you are already using it.'),
        ],
        [
            __('Management software, ERP or CRM: what is the difference?'),
            __('A CRM manages customers and sales. An ERP holds the whole company together: orders, inventory, production, accounting. Management software is the umbrella term. With tailor-made you do not have to choose: I build exactly what you need.'),
        ],
        [
            __('Does it connect to the programs I already use?'),
            __('Yes. Electronic invoicing, e-commerce, portals, external services: if it has an interface, I connect it. If it does not, we find a way.'),
        ],
        [
            __('What happens to the data I have now?'),
            __('I import it myself: Excel, CSV, exports from old management systems. And I clean it up along the way, so you start with tidy data.'),
        ],
        [
            __('Who owns the software in the end?'),
            __('You do. 100% of the code is yours, no lifelong fees, no ties to me or to other providers.'),
        ],
    ];
@endphp

<x-service-landing :area="\App\Enums\ServiceAreas::MANAGEMENT_ERP_CRM" pan="custom-software-development" mode="cells"
    :name="__('Management software, ERP and CRM')" :label="__('Tailor-made management software, ERP and CRM')"
    :rows="[__('Management software, ERP'), __('and tailor-made CRM.')]"
    :schema_description="__('Tailor-made management software, ERP and CRM for SMEs: built on your processes, with automations, real-time data and code you own.')"
    :$pains :$offers
    :scrub="__('Tailor-made management software costs less than you think. The wrong one costs more, every single day.')"
    :stack="['Laravel', 'PHP', 'Filament', 'Livewire', 'Vue', 'PostgreSQL', __('REST APIs'), 'Webhook', 'Docker']"
    :ai="__('Assistants that answer on your data, automatic extraction from documents and PDFs, smart search in the archive. AI works inside the software, not instead of you.')"
    :$projects :$faqs>
    <x-slot:intro>
        <p>{!! __('The software that runs your company, built around how you really work. <em>Not the other way around.</em>') !!}</p>
    </x-slot:intro>
</x-service-landing>
