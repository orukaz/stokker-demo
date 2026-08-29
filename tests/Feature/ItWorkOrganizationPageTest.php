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
        'juurde lisatakse esialgne MD-hinnang',
        '- **Töö tüüp - Component: Lühike',
        'kirjeldus** (N MD)',
    );
});

test('it work organization page matches the source guide details', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)
        ->toContain(
            'Sprint lõpetatakse',
            'Jiras ning Retrospective',
            'Logida võib kohe, päeva või',
            'Töö on peatatud; märgitakse',
            'järgmine tegevus.',
            "secondaryEmphasis: 'uus funktsionaalsus või parendus'",
            "secondaryEmphasis: 'olemasoleva lahenduse viga'",
            "secondaryEmphasis: 'ei ole arendustöö'",
            "secondaryEmphasis: 'väiksem teostussamm'",
            "'Stakeholderid'",
            "'Scrum Master'",
            "'Arendustiim'",
            "'Reporter'",
            'ei pruugi olla tellija',
            "'Assignee / omanik'",
            "text: 'järgmise tegevuse ja ajakohase staatuse'",
            "text: 'Jira töö esitaja või sisestaja', emphasis: true",
            "text: 'Kirjeldab vajaduse ja kinnitab soovitud tulemuse'",
            "text: 'sisendit ja tagasisidet', emphasis: true",
            "text: 'Hindab ja planeerib tööd', emphasis: true",
            "text: 'arenduse, ülevaatuse, testimise ja valmimise'",
            "text: 'Järjestab backlogi', emphasis: true",
            "text: 'millal töö sprinti võtta', emphasis: true",
            "text: 'Korraldab Scrum evente', emphasis: true",
            "text: 'töökorralduslikke takistusi', emphasis: true",
            '<strong',
            '>{part.text}</strong',
        )
        ->not->toContain('Töö ei ole valmis.', "'Omanik ehk Assignee'")
        ->toMatch(
            "/const roles = \[.*name: 'Assignee \/ omanik'.*name: 'Reporter'.*name: 'Tellija \/ ärivastutaja'.*name: 'Stakeholderid'.*name: 'Arendustiim'.*name: 'Product Owner'.*name: 'Scrum Master'.*\] as const;/s",
        );
});

test('it work organization page uses responsive headings and scrollable content', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        'mx-auto max-w-6xl',
        "'text-3xl! leading-tight! font-semibold! sm:text-4xl!'",
        "'pt-2 text-2xl! leading-tight! font-semibold! text-slate-900'",
        "'overflow-x-auto overscroll-x-contain rounded-lg border border-slate-200'",
        'snap-x scroll-px-2',
    );
});

test('portfolio estimates use scan friendly visual labels', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        'lucide-svelte/icons/calendar-days',
        'lucide-svelte/icons/clock-3',
        'lucide-svelte/icons/euro',
        'lucide-svelte/icons/layers',
        "item.size.kind === 'grouping'",
        'border-blue-200 bg-blue-50',
        'border-emerald-200/80 bg-emerald-50/70',
    );
});

test('it work organization page groups time logging guidance by topic', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)
        ->toContain(
            'Takistused ja seosed',
            'Ajaloogimine',
            '<ul class="space-y-2">',
            'Logi tegelik teostusaeg',
            'Logida võib kohe, päeva või',
            'Epicule teostustöödele logitud',
            'aega uuesti ei lisata.',
            'Hindamine ja planeerimine',
            '0,5 SP = 0,5 MD = 2,5 h',
            '1 SP = 1 MD = 5 h',
            'Ühe tunni töö hinnang võib olla',
            'Hinnangud ja töölogid aitavad',
        )
        ->and(substr_count($pageSource, '<dd class="mt-3">'))
        ->toBe(2);
});

test('sprint name guidance separates the template from its example', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        '<strong>Sprindi nimi:</strong>',
        'flex flex-wrap items-center gap-2',
        'border-slate-200 bg-slate-50 px-2.5 py-1 font-mono text-sm font-medium text-stokker-primary',
        "'IT Dev Sprint {YY}-W{ww}'",
        '>näiteks</span',
        'border-stokker-primary/25 bg-stokker-primary-50',
        'IT Dev Sprint 26-W35',
    );
});

test('jira setup values and backlog lists are highlighted', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        'bg-stokker-primary-50 px-2 py-0.5 font-mono text-sm font-semibold text-stokker-primary',
        '>Other</code',
        'inline-flex flex-wrap items-center gap-1.5 align-middle',
        'JiraStatusBadge',
        'status="Initial Review"',
        'status="In refinement"',
        'status="Ready for Development"',
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
            "task: 'Task / Ülesanne'",
            "story: 'Story / Lugu'",
            "bug: 'Bug / Viga'",
            "subtask: 'Sub-task / Alamülesanne'",
            'border-blue-200 bg-blue-50',
            'border-green-200 bg-green-50',
            'border-red-200 bg-red-50',
            'border-violet-200 bg-violet-50',
        );
});

test('subtask rules highlight the allowed parent issue types', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)
        ->toContain(
            'Sub-taski eripära',
            'flex flex-wrap items-center gap-x-2 gap-y-1.5',
            '<span>— mitte</span>',
            'kui töö on iseseisev või vajab',
            'luuakse see eraldi',
            'tööna:</span',
        )
        ->and(substr_count($pageSource, '<JiraIssueBadge type="story" />'))
        ->toBe(2)
        ->and(substr_count($pageSource, '<JiraIssueBadge type="task" />'))
        ->toBe(2)
        ->and(substr_count($pageSource, '<JiraIssueBadge type="bug" />'))
        ->toBe(2)
        ->and(substr_count($pageSource, '<JiraIssueBadge type="epic" />'))
        ->toBe(1);
});

test('it work organization page uses subtle status badges', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );
    $badgeSource = file_get_contents(
        resource_path('js/components/JiraStatusBadge.svelte'),
    );
    $flowSource = file_get_contents(
        resource_path('js/components/JiraStatusFlow.svelte'),
    );

    expect($pageSource)
        ->toContain(
            'JiraStatusFlow',
            'statuses={status.flow}',
            'JiraStatusBadge status="On Hold"',
            'JiraStatusBadge status="Cancelled"',
        )
        ->and($badgeSource)
        ->toContain(
            'border-blue-200/70 bg-blue-50/70',
            'border-green-200/70 bg-green-50/70',
            'border-slate-200 bg-slate-50',
        )
        ->and($flowSource)
        ->toContain(
            'flex flex-wrap items-center',
            'inline-flex items-center',
            'aria-hidden="true">→',
        );
});

test('it work organization page uses English Jira statuses', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );
    $badgeSource = file_get_contents(
        resource_path('js/components/JiraStatusBadge.svelte'),
    );

    expect($pageSource)
        ->toContain(
            "'To Do'",
            "'In Progress'",
            "'Done'",
            'Done-staatusesse',
        )
        ->not->toContain("'Vaja teha'", "'Lahendamisel'", "'Tehtud'")
        ->and($badgeSource)
        ->toContain("'In Progress'", "new Set(['Done', 'Cancelled'])")
        ->not->toContain("'Lahendamisel'", "'Tehtud'");
});

test('it work organization guidance uses scan friendly labels', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)->toContain(
        'Uus töökorraldus aitab',
        '<strong>Fookus:</strong>',
        '>Tööviis</h3>',
        '<strong>Arendus:</strong>',
        '<strong>IT Ops ja IT-tugi:</strong>',
        '>Scrum</strong',
        '>Kanban</strong>',
        '<strong>Sprinti võetakse:</strong>',
        '<strong>Mida tuuakse:</strong>',
        '<strong>Eelanalüüsi Task:</strong>',
        'Pärast ülevaatust ja kinnitamist:',
        '<strong>Pärimine:</strong>',
        "primaryEmphasis: 'Suurem teema või projekt'",
        "primaryEmphasis: 'Arendustöö'",
        "secondaryEmphasis: 'ei ole arendustöö'",
        "secondaryEmphasis: 'väiksem teostussamm'",
        '<strong',
        '>{issue.primaryEmphasis}</strong',
        '>{issue.secondaryEmphasis}</strong',
    )->not->toContain('Scrum ja Kanban', '<strong>Ühine põhimõte:</strong>');
});

test('it work organization page includes the latest source guide revisions', function () {
    $pageSource = file_get_contents(
        resource_path('js/pages/docs/ItWorkOrganization.svelte'),
    );

    expect($pageSource)
        ->toContain(
            'R1 --> R2`;',
            "const epicApprovalFlow = [\n        'To Do',",
            '<strong>Sprinti võetakse:</strong> selge, hinnatud ja',
            'Kui komponent pole veel teada',
            'planeerimata tööd paiknevad nimekirjades',
            'Sub-taski ei planeerita',
            'see valmib koos põhitööga samas',
            '<strong>Loendi tähendus:</strong> loend on tööplaan,',
            'loendis kirjeldatud Jira tööd luuakse pärast IT-komitee',
            'Takistuse korral märgitakse selle põhjus',
            'Epicule teostustöödele logitud',
            'jira/software/c/projects/DEV/boards/112',
            'jira/software/c/projects/DEV/boards/112/backlog',
            'jira/software/c/projects/DEV/boards/145',
            'IT Team - DEV - Board (Scrum)',
            'IT Team - Epics - Board (Kanban)',
            'Epicute töölaud',
        )
        ->not->toContain(
            'E --> F`;',
            '/boards/45',
            'Acceptance Criteria, sõltuvused, töömaht, vastutus',
            'eelmise sprindi lõpetamata',
            'backlogi või teadlikult järgmisse sprinti',
            'Epicule sama aega uuesti ei',
        );
});
