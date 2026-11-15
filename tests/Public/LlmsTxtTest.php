<?php

declare(strict_types=1);

use function Safe\file_get_contents;

beforeEach(function (): void {
    $this->llms_txt = file_get_contents(public_path('llms.txt'));
});

describe('llms.txt', function (): void {
    it('publishes a non-empty llms.txt at the public root', function (): void {
        expect(\file_exists(public_path('llms.txt')))->toBeTrue()
            ->and($this->llms_txt)->toStartWith('# Simone Cerruti');
    });

    it('stays in sync with the company contact channels', function (): void {
        $whatsapp = \ltrim(config()->string('company.contacts.whatsapp.number'), '+');

        expect($this->llms_txt)
            ->toContain(config()->string('company.contacts.email'))
            ->toContain(config()->string('company.contacts.koalenda_url'))
            ->toContain("wa.me/{$whatsapp}");
    });

    it('lists every social profile from the company config', function (): void {
        /** @var array<string, array{username: string, link: string}> $socials */
        $socials = config()->array('company.socials');

        expect($this->llms_txt)->toContain(...\array_column($socials, 'link'));
    });

    it('links both the English and Italian versions of the site', function (string $expected): void {
        expect($this->llms_txt)->toContain($expected);
    })->with([
        'English url' => 'https://simonecerruti.com/en',
        'Italian url' => 'https://simonecerruti.com/it',
        'English label' => 'English',
        'Italian label' => 'Italian',
    ]);

    it('covers the services with a bespoke, remote-friendly local positioning', function (string $expected): void {
        expect($this->llms_txt)->toContain($expected);
    })->with(['ERP', 'CRM', 'Automation', 'bespoke', 'tailor-made', 'Biella', 'remotely']);
});
