<?php

declare(strict_types=1);

it('publishes a non-empty llms.txt at the public root', function () {
    expect(file_exists(public_path('llms.txt')))->toBeTrue()
        ->and(\Safe\file_get_contents(public_path('llms.txt')))->toStartWith('# Simone Cerruti');
});

it('stays in sync with the company contact channels', function () {
    $llms_txt = \Safe\file_get_contents(public_path('llms.txt'));
    $whatsapp = ltrim(config()->string('company.contacts.whatsapp.number'), '+');

    expect($llms_txt)
        ->toContain(config()->string('company.contacts.email'))
        ->toContain(config()->string('company.contacts.koalenda_url'))
        ->toContain("wa.me/{$whatsapp}");
});

it('lists every social profile from the company config', function () {
    $llms_txt = \Safe\file_get_contents(public_path('llms.txt'));

    /** @var array<string, array{username: string, link: string}> $socials */
    $socials = config()->array('company.socials');

    foreach ($socials as $social) {
        expect($llms_txt)->toContain($social['link']);
    }
});

it('links both the English and Italian versions of the site', function () {
    expect(\Safe\file_get_contents(public_path('llms.txt')))
        ->toContain('https://simonecerruti.com/en')
        ->toContain('https://simonecerruti.com/it')
        ->toContain('English')
        ->toContain('Italian');
});

it('covers the services with a bespoke, remote-friendly local positioning', function () {
    expect(\Safe\file_get_contents(public_path('llms.txt')))
        ->toContain('ERP')
        ->toContain('CRM')
        ->toContain('Automation')
        ->toContain('bespoke')
        ->toContain('tailor-made')
        ->toContain('Biella')
        ->toContain('remotely');
});
