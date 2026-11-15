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

    'service_areas' => [
        'management_erp_crm' => ['label' => 'Gestionali, ERP e CRM'],
        'web_ecommerce_platforms' => ['label' => 'Siti web e piattaforme e-commerce'],
        'consulting_and_seo' => ['label' => 'Consulenza e SEO'],
    ],

    'lead_read_status' => [
        'read' => ['label' => 'Letto'],
        'unread' => ['label' => 'Non letto'],
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

    'generatable_fields' => [
        'blog_article_title' => ['label' => 'Titolo articolo del blog'],
        'blog_article_summary' => ['label' => 'Riassunto articolo del blog'],
        'blog_article_content' => ['label' => 'Contenuto articolo del blog'],
        'project_title' => ['label' => 'Titolo progetto'],
        'project_short_description' => ['label' => 'Descrizione breve progetto'],
        'project_description' => ['label' => 'Descrizione progetto'],
        'project_starting_point' => ['label' => 'Punto di partenza del progetto'],
        'project_goal' => ['label' => 'Obiettivo del progetto'],
        'project_outcome' => ['label' => 'Risultato del progetto'],
        'project_feature_title' => ['label' => 'Titolo funzionalità del progetto'],
        'project_feature_description' => ['label' => 'Descrizione funzionalità del progetto'],
        'project_home_summary' => ['label' => 'Riassunto progetto per la home'],
        'client_description' => ['label' => 'Descrizione cliente'],
        'tag_description' => ['label' => 'Descrizione tag'],
        'campaign_description' => ['label' => 'Descrizione campagna'],
        'campaign_notes' => ['label' => 'Note campagna'],
        'seo_title' => ['label' => 'Meta title'],
        'seo_description' => ['label' => 'Meta description'],
        'seo_og_title' => ['label' => 'Titolo Open Graph'],
        'seo_og_description' => ['label' => 'Descrizione Open Graph'],
        'seo_twitter_title' => ['label' => 'Titolo Twitter'],
        'seo_twitter_description' => ['label' => 'Descrizione Twitter'],
    ],
];
