<?php

use Inertia\Testing\AssertableInertia as Assert;

test('it work organization overview page is publicly available', function () {
    $this->get(route('docs.it_work_organization'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('docs/ItWorkOrganization'),
        );
});

test('it work organization sections start collapsed and scroll when toggled', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)
        ->not->toMatch('/<details\b[^>]*\bopen\b[^>]*>/')
        ->and(substr_count($pageSource, 'onclick={scrollToSection}'))
        ->toBe(6);
});

test('it work organization page includes the Epic work template', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        '## Võimalikud arendustööd',
        '- **Töö tüüp - Component: Lühike',
        'kirjeldus** (N MD)',
    );
});

test('it work organization page uses responsive headings and scrollable content', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        "'text-3xl! leading-tight! font-semibold! sm:text-4xl!'",
        "'pt-2 text-2xl! leading-tight! font-semibold! text-slate-900'",
        "'overflow-x-auto overscroll-x-contain rounded-lg border border-slate-200'",
        'snap-x scroll-px-2',
    );
});

test('it work organization page groups time logging guidance by topic', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        'Takistused ja seosed',
        'Ajaloogimine',
        'Hindamine ja planeerimine',
        '0,5 SP = 0,5 MD = 2,5 h',
        '1 SP = 1 MD = 5 h',
    );
});

test('it work organization page uses Jira style issue badges', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );
    $badgeSource = file_get_contents(
        resource_path('js/components/JiraIssueBadge.svelte'),
    );

    expect($pageSource)
        ->toContain('JiraIssueBadge', 'status.types as type')
        ->and($badgeSource)
        ->toContain(
            'lucide-svelte/icons/square-check',
            'lucide-svelte/icons/bookmark',
            'lucide-svelte/icons/bug',
            'lucide-svelte/icons/zap',
            'border-blue-200 bg-blue-50',
            'border-green-200 bg-green-50',
            'border-red-200 bg-red-50',
            'border-violet-200 bg-violet-50',
        );
});

test('it work organization guidance uses scan friendly labels', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        '<strong>Fookus:</strong>',
        '<strong>Sprinti valmis töö:</strong>',
        '<strong>Mida tuuakse:</strong>',
        '<strong>Eelanalüüsi Task:</strong>',
        '<strong>Pärast kinnitamist:</strong>',
        '<strong>Pärimine:</strong>',
    );
});
