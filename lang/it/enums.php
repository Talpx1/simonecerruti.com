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
];
