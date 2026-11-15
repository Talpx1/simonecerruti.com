@php
    $pains = [
        __('You know you need digital, but not where to start.'),
        __('You already spent on websites and software, without results.'),
        __('On Google you are on page three. When things go well.'),
        __('You have three quotes and no way to compare them.'),
        __('Your internal team needs direction.'),
    ];

    $offers = [
        [
            __('Strategic consulting'),
            __('Goals, priorities and technologies, decided calmly and with numbers.'),
            [
                __('Analysis of processes and goals'),
                __('Roadmap with clear priorities'),
                __('Independent technology choices'),
                __('Review of the quotes you receive'),
            ],
        ],
        [
            __('SEO and ranking'),
            __('Get found by the people who are already looking for you.'),
            [
                __('Keywords and competitors'),
                __('Technical SEO: speed, structure, data'),
                __('Content architecture'),
                __('Clear reports, no jargon'),
            ],
        ],
        [
            __('Audit and support'),
            __('An outside look at what you have, and a hand for the people working on it.'),
            [
                __('Audit of existing websites and software'),
                __('Security and dependency audit'),
                __('Code review and mentoring'),
                __('Support for the internal team'),
            ],
        ],
    ];

    $faqs = [
        [
            __('How does a consultation work?'),
            __('It starts with a free 15-minute call. Then I analyze your situation and you receive a concrete roadmap: what to do, in what order, with what budget.'),
        ],
        [
            __('If I book a consultation, do I then have to let you build everything?'),
            __('No. The roadmap is yours and you can hand it to anyone you like. Technology choices are independent exactly for this reason.'),
        ],
        [
            __('How long until I see SEO results?'),
            __('The first signs usually come in a few weeks, solid results in a few months. Be wary of anyone promising the first page in seven days.'),
        ],
        [
            __('Can you help me compare the quotes I received?'),
            __('Yes. I read them, explain what is inside and what is missing, and tell you which one makes sense for you. Even if it is not mine.'),
        ],
        [
            __('How much does it cost?'),
            __('The first call is free. Then you receive a tailor-made proposal, based on what is really needed.'),
        ],
        [
            __('Do you also work with our in-house team?'),
            __('Yes: support, code review, audits and training. I also teach this craft at a web academy, so I know how to pass it on.'),
        ],
    ];
@endphp

<x-service-landing :area="\App\Enums\ServiceAreas::CONSULTING_AND_SEO" analytics-slug="consulting-and-seo" effect-mode="rank"
    :name="__('Digital consulting and SEO')" :label="__('Digital consulting and SEO')"
    :rows="[__('Consulting'), __('and SEO.')]"
    :schema_description="__('Digital and SEO consulting for SMEs: strategy before code, independent technology choices, Google ranking and support for your team.')"
    :$pains :$offers
    :closing-statement="__('Sometimes the most useful advice is to build nothing. If that is the case, I will tell you.')"
    :technology-stack="[__('SEO audit'), 'Search Console', __('Keyword analysis'), 'Core Web Vitals', __('Structured data'), __('Security audit'), 'SBOM']"
    :ai-description="__('AI quickly analyzes large amounts of data: searches, competitors, content. The decisions stay with the two of us.')"
    :$projects :$faqs>
    <x-slot:intro>
        <p>{!! __('Strategy first, then code. <em>That way you do not pay twice.</em>') !!}</p>
    </x-slot:intro>
</x-service-landing>
