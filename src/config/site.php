<?php

declare(strict_types=1);

/*
 * Site-wide settings. Fill these in from docs/brief.md at the start of each project.
 */
return [
    'name' => 'Client Name',
    'description' => '',
    'url' => rtrim(getenv('APP_URL') ?: 'http://localhost:8080', '/'),
    'locale' => 'en_GB',

    // Default Open Graph image (1200x630), relative to public/, e.g. '/images/og-default.jpg'.
    'og_image' => null,

    // Where contact form enquiries are sent.
    'contact_email' => getenv('CONTACT_EMAIL') ?: 'enquiries@example.com',

    // Companies Act: limited companies must show these on their website.
    'legal' => [
        'legal_name' => '',
        'company_number' => '',
        'registered_in' => 'England and Wales',
        'registered_office' => '',
    ],

    'nav' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Contact', 'href' => '/contact'],
    ],
];
