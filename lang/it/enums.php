<?php

declare(strict_types=1);

return [
    'blog_article_statuses' => [
        'draft' => [
            'label' => 'Bozza',
            'description' => 'L\'articolo non è ancora pronto alla pubblicazione. Non è mostrato in alcun posto nel sito, non è accessibile dal link e non è visibile ai motori di ricerca.',
        ],
        'published' => [
            'label' => 'Pubblicato',
            'description' => 'L\'articolo è pubblico e visibile sul sito e ai motori di ricerca.',
        ],
        'archived' => [
            'label' => 'Archiviato',
            'description' => 'L\'articolo non è mostrato sul sito (ricerche, lista articoli, ...). È comunque possibile accederci dal link ed è visibile ai motori di ricerca.',
        ],
        'hidden' => [
            'label' => 'Nascosto',
            'description' => 'L\'articolo non è mostrato sul sito (ricerche, lista articoli, ...), non è accessibile dal link e non è visibile ai motori di ricerca.',
        ],
    ],

    'lead_read_status' => [
        'read' => ['label' => 'Letto'],
        'unread' => ['label' => 'Non letto'],
    ],

    'blog_categories' => [
        'practical' => ['label' => 'pratico'],
        'technical' => ['label' => 'tecnico'],
    ],

    'visit_source_type' => [
        'direct' => ['label' => 'Diretto'],
        'internal' => ['label' => 'Interno'],
        'unknown' => ['label' => 'Sconosciuto'],
    ],

    'visit_medium_type' => [
        'social' => ['label' => 'Social'],
        'email' => ['label' => 'Email'],
        'organic' => ['label' => 'Organica'],
        'paid' => ['label' => 'A pagamento'],
        'display' => ['label' => 'Display'],
        'referral' => ['label' => 'Referral'],
        'affiliate' => ['label' => 'Affiliate'],
        'print' => ['label' => 'Stampa'],
        'physical' => ['label' => 'Fisico'],
    ],

    'device_type' => [
        'mobile' => ['label' => 'Mobile'],
        'tablet' => ['label' => 'Tablet'],
        'desktop' => ['label' => 'Desktop'],
        'bot' => ['label' => 'Bot'],
        'unknown' => ['label' => 'Sconosciuto'],
    ],
];
