<script lang="ts">
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import AppHead from '@/components/AppHead.svelte';
    import JiraIssueBadge from '@/components/JiraIssueBadge.svelte';
    import JiraStatusBadge from '@/components/JiraStatusBadge.svelte';
    import JiraStatusFlow from '@/components/JiraStatusFlow.svelte';
    import MermaidDiagram from '@/components/MermaidDiagram.svelte';
    import SiteLayout from '@/layouts/SiteLayout.svelte';
    import itTeamHeaderImage from '../../../images/it-team-header.png';

    const tableOfContents = [
        { number: '1', label: 'Miks ja kuidas töötame?', href: '#miks' },
        { number: '2', label: 'Arendusprotsess', href: '#arendusprotsess' },
        { number: '3', label: 'Töö korraldamine Jiras', href: '#jira' },
        { number: '4', label: 'Portfolio ja Epic', href: '#portfolio' },
        { number: '5', label: 'Scrum ja sprindi rütm', href: '#scrum' },
        { number: '6', label: 'Igapäevased kokkulepped', href: '#kokkulepped' },
    ];

    const developmentProcessDiagram = `flowchart TB
        subgraph R1[" "]
            direction LR
            A["1. Vajaduse<br/>registreerimine"] --> B["2. Esmane<br/>ülevaatus"]
            B --> C["3. Analüüs ja<br/>Refinement"]
            C --> D["4. Kinnitamine ja<br/>prioriseerimine"]
            D --> E["5. Sprint<br/>Planning"]
        end

        subgraph R2[" "]
            direction RL
            F["6. Lahenduse<br/>kavandamine"] --> G["7. Arendus"]
            G --> H["8. Ülevaatus ja<br/>testimine"]
            H --> I["9. Release"]
            I --> J["10. Done ja<br/>järeltegevused"]
        end

        R1 --> R2`;

    const issueTypes = [
        {
            type: 'epic',
            description:
                'Suurem teema või projekt, mis koosneb seotud arendustöödest.',
        },
        {
            type: 'story',
            description:
                'Arendustöö, millega luuakse uus funktsionaalsus või parendus.',
        },
        {
            type: 'bug',
            description:
                'Arendustöö, millega parandatakse olemasoleva lahenduse viga.',
        },
        {
            type: 'task',
            description:
                'Analüüsi-, seadistus-, tehniline või muu sisemine töö.',
        },
        {
            type: 'subtask',
            description: 'Loo, ülesande või vea väiksem teostussamm.',
        },
    ] as const;

    const epicApprovalFlow = [
        'In Analysis',
        'Waiting for Approval',
        'Approved',
        'Lahendamisel',
        'Ready for Release',
        'Tehtud',
    ] as const;

    const statuses = [
        {
            key: 'epic',
            types: ['epic'],
            flow: ['Vaja teha', ...epicApprovalFlow],
        },
        {
            key: 'story-bug',
            types: ['story', 'bug'],
            flow: [
                'Vaja teha',
                'Lahendamisel',
                'In Review',
                'Testing',
                'Ready for Release',
                'Tehtud',
            ],
        },
        {
            key: 'task',
            types: ['task'],
            flow: ['Vaja teha', 'Lahendamisel', 'In Review', 'Tehtud'],
        },
        {
            key: 'subtask',
            types: ['subtask'],
            flow: ['Vaja teha', 'Lahendamisel', 'Tehtud'],
        },
    ] as const;

    const portfolioCategories = [
        [
            'Grouping',
            'Epic eri kategooriatega tööde koondamiseks',
            'Eraldi kinnitust ei ole.',
        ],
        ['IT Support', 'Kuni 2 MD / 10 h või alla 1 000 EUR', 'IT-tiim.'],
        [
            'IT Change',
            '2–6 MD / 10–30 h või 1 000–3 000 EUR',
            'IT-tiim ja sisemine tellija.',
        ],
        [
            'IT Improvement',
            '4–10 MD / 20–50 h või 2 000–5 000 EUR',
            'IT-komitee ja juhatus.',
        ],
        [
            'IT Project',
            'Üle 10 MD / 50 h või üle 5 000 EUR',
            'IT-komitee ja juhatus.',
        ],
    ];

    const meetings = [
        [
            'Sprint Review',
            'E 09.00–09.30',
            'Valminud tulemuse ülevaatus ja tagasiside.',
        ],
        [
            'Retrospective',
            'E 09.30–09.45',
            'Järgmise töökorralduse paranduse kokkuleppimine.',
        ],
        [
            'Sprint Planning + Stand-up',
            'E 09.45–10.15',
            'Sprint Goal ja realistlik töömaht.',
        ],
        [
            'Daily Stand-up',
            'T–R 09.00–09.15',
            'Edenemine, takistused ja järgmised tegevused.',
        ],
        [
            'Backlog Refinement',
            'K 14.00–14.45',
            'Tulevaste tööde täpsustamine ja hindamine.',
        ],
    ];

    const roles = [
        [
            'Tellija / ärivastutaja',
            'Kirjeldab vajaduse ja kinnitab soovitud tulemuse.',
        ],
        [
            'Product Owner',
            'Järjestab backlogi ja otsustab koos tiimiga, millal töö sprinti võtta.',
        ],
        [
            'Omanik ehk Assignee',
            'Vastutab töö järgmise aktiivse tegevuse ja ajakohase staatuse eest.',
        ],
    ];

    const bulletClass =
        'relative pl-5 before:absolute before:left-0 before:top-[0.7em] before:size-1.5 before:rounded-full before:bg-stokker-primary';
    const externalLinkClass =
        'inline-flex items-center gap-1.5 font-semibold text-stokker-primary underline decoration-stokker-primary/30 underline-offset-4 hover:decoration-stokker-primary';
    const sectionHeadingClass =
        'text-3xl! leading-tight! font-semibold! sm:text-4xl!';
    const subsectionHeadingClass =
        'pt-2 text-2xl! leading-tight! font-semibold! text-slate-900';
    const tableScrollClass =
        'overflow-x-auto overscroll-x-contain rounded-lg border border-slate-200';

    const openSection = (href: string): void => {
        const section = document.querySelector<HTMLDetailsElement>(href);

        if (section) {
            section.open = true;
        }
    };

    const scrollToSection = (event: MouseEvent): void => {
        const summary = event.currentTarget as HTMLElement;
        const section = summary.closest('details');

        requestAnimationFrame(() => {
            section?.scrollIntoView({ block: 'start' });
        });
    };
</script>

<AppHead title="IT-tiimi uus töökorraldus">
    <meta
        name="description"
        content="Stokkeri IT-tiimi uue töökorralduse lühiülevaade."
    />
</AppHead>

<SiteLayout>
    <main class="bg-white text-slate-900">
        <article
            class="mx-auto max-w-6xl px-5 pt-6 pb-32 sm:pt-10 lg:px-8 lg:pt-16 lg:pb-32"
        >
            <header class="border-b border-slate-200 pb-10">
                <img
                    src={itTeamHeaderImage}
                    alt="Stokkeri IT-tiim"
                    class="mb-8 aspect-[2560/533] w-full rounded-lg object-cover sm:mb-10"
                />
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                    IT-tiimi uus töökorraldus
                </h1>
                <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-600">
                    Stokkeri IT-tiim toetab paljusid süsteeme ja ärivajadusi
                    ning kõiki töid ei ole võimalik korraga ette võtta. Uus
                    töökorraldus aitab suunata tiimi võimekuse kõige
                    olulisematele töödele ning muuta prioriteedid, vastutus ja
                    tööde seis arusaadavaks.
                </p>
                <div class="mt-6 flex flex-wrap gap-x-6 gap-y-3 text-sm">
                    <a
                        href="https://stokker365.sharepoint.com/sites/IRONMANEE2/SitePages/ITHelpdeskHome.aspx"
                        target="_blank"
                        rel="noopener noreferrer"
                        class={externalLinkClass}
                    >
                        Iron Man
                        <ExternalLink class="size-4" aria-hidden="true" />
                    </a>
                    <a
                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV"
                        target="_blank"
                        rel="noopener noreferrer"
                        class={externalLinkClass}
                    >
                        Jira DEV
                        <ExternalLink class="size-4" aria-hidden="true" />
                    </a>
                    <a
                        href="https://stokker365.sharepoint.com/sites/ITTeam/SitePages/IT-tiimi-uue-t%C3%B6%C3%B6korralduse-kiir%C3%BClevaade.aspx"
                        target="_blank"
                        rel="noopener noreferrer"
                        class={externalLinkClass}
                    >
                        Täielik juhend
                        <ExternalLink class="size-4" aria-hidden="true" />
                    </a>
                </div>
            </header>

            <nav
                aria-label="Lehe sisukord"
                class="fixed inset-x-4 bottom-4 z-50 mx-auto max-w-6xl rounded-2xl border border-stokker-primary bg-stokker-primary p-2 shadow-lg shadow-slate-900/15"
            >
                <ol
                    class="flex snap-x scroll-px-2 gap-1 overflow-x-auto overscroll-x-contain"
                >
                    {#each tableOfContents as item (item.href)}
                        <li class="min-w-max flex-1 snap-start">
                            <a
                                href={item.href}
                                onclick={() => openSection(item.href)}
                                class="flex items-center justify-center gap-2 rounded-xl px-2 py-2.5 text-sm whitespace-nowrap text-white transition-colors hover:bg-white/15 hover:text-white sm:px-3"
                            >
                                <span class="font-semibold text-white/70"
                                    >{item.number}</span
                                >
                                <span class="font-medium">{item.label}</span>
                            </a>
                        </li>
                    {/each}
                </ol>
            </nav>

            <div class="divide-y divide-slate-200">
                <details id="miks" class="group scroll-mt-6">
                    <summary
                        onclick={scrollToSection}
                        class="flex cursor-pointer list-none items-center justify-between gap-6 py-7 marker:hidden"
                    >
                        <h2 class={sectionHeadingClass}>
                            1. Miks ja kuidas töötame?
                        </h2>
                        <ChevronDown
                            class="size-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180"
                            aria-hidden="true"
                        />
                    </summary>
                    <div class="space-y-6 pb-10 leading-7 text-slate-700">
                        <p>Uus töökorraldus aitab:</p>
                        <ul class="space-y-2">
                            <li class={bulletClass}>
                                <strong>Fookus:</strong> suunata tiimi võimekus kõige
                                olulisematele töödele;
                            </li>
                            <li class={bulletClass}>
                                <strong>Vähem pooleliolevat:</strong> vähendada korraga
                                pooleliolevate tööde hulka;
                            </li>
                            <li class={bulletClass}>
                                <strong>Selgus:</strong> muuta prioriteedid, vastutus
                                ja tööde seis arusaadavaks;
                            </li>
                            <li class={bulletClass}>
                                <strong>Riskid nähtavaks:</strong> tuua takistused,
                                sõltuvused ja riskid varem nähtavale.
                            </li>
                        </ul>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Scrum ja Kanban
                            </h3>
                            <ul class="mt-4 space-y-2">
                                <li class={bulletClass}>
                                    <strong>Scrum:</strong> arendustööd tehakse ühenädalaste
                                    sprintidena, millel on valitud tööd ja ühine eesmärk.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Kanban:</strong> IT Opsi ja IT-toe järgmine
                                    töö võetakse järjestatud nimekirjast vaba võimekuse
                                    tekkimisel.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Ühine põhimõte:</strong> mõlemas hoitakse
                                    tööde seis, prioriteedid, vastutajad ja takistused
                                    nähtavana.
                                </li>
                            </ul>
                        </section>

                        <p>
                            <strong>Vajaduse esitamine:</strong> vajadus
                            esitatakse üldjuhul
                            <a
                                href="https://stokker365.sharepoint.com/sites/IRONMANEE2/SitePages/ITHelpdeskHome.aspx"
                                target="_blank"
                                rel="noopener noreferrer"
                                class={externalLinkClass}>Iron Manis</a
                            >. Vajaduse korral luuakse või seotakse sellega Jira
                            arendustöö, mis järjestatakse backlogis.
                        </p>
                    </div>
                </details>

                <details id="arendusprotsess" class="group scroll-mt-6">
                    <summary
                        onclick={scrollToSection}
                        class="flex cursor-pointer list-none items-center justify-between gap-6 py-7 marker:hidden"
                    >
                        <h2 class={sectionHeadingClass}>2. Arendusprotsess</h2>
                        <ChevronDown
                            class="size-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180"
                            aria-hidden="true"
                        />
                    </summary>
                    <div class="space-y-7 pb-10 leading-7 text-slate-700">
                        <MermaidDiagram
                            definition={developmentProcessDiagram}
                            label="Arendusprotsessi kümme sammu"
                        />
                        <p
                            class="rounded-xl border border-slate-200 bg-slate-50 p-5"
                        >
                            <strong>Sprinti valmis töö:</strong> soovitud tulemus,
                            Acceptance Criteria, sõltuvused, töömaht, vastutus ja
                            vajalik kinnitus on piisavalt selged.
                        </p>
                    </div>
                </details>

                <details id="jira" class="group scroll-mt-6">
                    <summary
                        onclick={scrollToSection}
                        class="flex cursor-pointer list-none items-center justify-between gap-6 py-7 marker:hidden"
                    >
                        <h2 class={sectionHeadingClass}>
                            3. Töö korraldamine Jiras
                        </h2>
                        <ChevronDown
                            class="size-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180"
                            aria-hidden="true"
                        />
                    </summary>
                    <div class="space-y-8 pb-10 leading-7 text-slate-700">
                        <section>
                            <h3 class={subsectionHeadingClass}>
                                DEV space ja backlog
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <strong>Töö asukoht:</strong>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >IT Development ehk DEV</a
                                    >
                                    ja
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >Development Board</a
                                    >.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/plugins/servlet/project-config/DEV/components"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}>Component</a
                                    >: kohustuslik väli, mis näitab seotud
                                    süsteemi. Kui komponent pole teada,
                                    kasutatakse ajutiselt väärtust
                                    <code>Other</code>.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprindi nimi:</strong>
                                    <code>IT Dev Sprint YY-Www</code>, näiteks
                                    <code>IT Dev Sprint 26-W35</code>.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45/backlog"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}>Backlog</a
                                    >: planeerimata tööd on nimekirjades Initial
                                    Review, In refinement ja Ready for
                                    Development.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint ja tähtaeg:</strong> sprint näitab
                                    planeeritud nädalat. Due date'i kasutatakse ainult
                                    kindla tähtaja korral.
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Tööde ületoomine teistest space'idest
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <strong>Mida tuuakse:</strong> DEV space'i tuuakse
                                    korrastatud tööd, millega jätkatakse.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Millal:</strong> töid tuuakse üle jooksvalt
                                    ning eraldi üleviimise tähtaega ei ole.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Mis säilib:</strong> töö hierarhia, staatus,
                                    lingid ja ajalugu.
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>Tööde tüübid</h3>
                            <div class="mt-4 {tableScrollClass}">
                                <table
                                    class="w-full min-w-150 text-left text-sm"
                                >
                                    <thead
                                        class="border-b border-slate-200 bg-slate-50"
                                    >
                                        <tr>
                                            <th class="px-4 py-3 font-semibold"
                                                >Tüüp</th
                                            >
                                            <th class="px-4 py-3 font-semibold"
                                                >Kasutus</th
                                            >
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        {#each issueTypes as issue (issue.type)}
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <JiraIssueBadge
                                                        type={issue.type}
                                                    />
                                                </td>
                                                <td class="px-4 py-3"
                                                    >{issue.description}</td
                                                >
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Sub-taski eripära
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <strong>Parent:</strong> Sub-taski otsene parent
                                    on Story, Task või Bug, mitte Epic.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint:</strong> Sub-taski ei lisata eraldi
                                    sprinti; see järgib oma põhitöö sprinti ja peab
                                    valmima koos põhitööga.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Iseseisev töö:</strong> kui töö vajab
                                    eraldi sprinti, luuakse see Story, Taski või Bugina.
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Tööde staatused
                            </h3>
                            <div class="mt-4 {tableScrollClass}">
                                <table
                                    class="w-full min-w-180 text-left text-sm"
                                >
                                    <thead
                                        class="border-b border-slate-200 bg-slate-50"
                                    >
                                        <tr>
                                            <th class="px-4 py-3 font-semibold"
                                                >Tüüp</th
                                            >
                                            <th class="px-4 py-3 font-semibold"
                                                >Tavapärane põhivoog Jiras</th
                                            >
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        {#each statuses as status (status.key)}
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div
                                                        class="flex flex-wrap gap-2"
                                                    >
                                                        {#each status.types as type (type)}
                                                            <JiraIssueBadge
                                                                {type}
                                                            />
                                                        {/each}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <JiraStatusFlow
                                                        statuses={status.flow}
                                                    />
                                                </td>
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                            <ul class="mt-4 space-y-3">
                                <li class="flex items-start gap-2.5">
                                    <JiraStatusBadge status="On Hold" />
                                    <span>
                                        Töö on peatatud; juurde märgitakse
                                        põhjus ja järgmine tegevus.
                                    </span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <JiraStatusBadge status="Cancelled" />
                                    <span>
                                        Tööga ei jätkata; juurde märgitakse
                                        tühistamise põhjus.
                                    </span>
                                </li>
                            </ul>
                        </section>
                    </div>
                </details>

                <details id="portfolio" class="group scroll-mt-6">
                    <summary
                        onclick={scrollToSection}
                        class="flex cursor-pointer list-none items-center justify-between gap-6 py-7 marker:hidden"
                    >
                        <h2 class={sectionHeadingClass}>
                            4. IT Portfolio Category ja Epicu protsess
                        </h2>
                        <ChevronDown
                            class="size-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180"
                            aria-hidden="true"
                        />
                    </summary>
                    <div class="space-y-8 pb-10 leading-7 text-slate-700">
                        <p>
                            Töö tüüp näitab, <strong>mida tehakse</strong>. IT
                            Portfolio Category näitab,
                            <strong
                                >kui suure algatusega on tegemist ja kes selle
                                kinnitab</strong
                            >.
                        </p>

                        <div class={tableScrollClass}>
                            <table class="w-full min-w-180 text-left text-sm">
                                <thead
                                    class="border-b border-slate-200 bg-slate-50"
                                >
                                    <tr>
                                        <th class="px-4 py-3 font-semibold"
                                            >Kategooria</th
                                        >
                                        <th class="px-4 py-3 font-semibold"
                                            >Orienteeruv suurus</th
                                        >
                                        <th class="px-4 py-3 font-semibold"
                                            >Kinnitamine</th
                                        >
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    {#each portfolioCategories as item (item[0])}
                                        <tr>
                                            <td
                                                class="px-4 py-3 font-semibold text-slate-900"
                                                >{item[0]}</td
                                            >
                                            <td class="px-4 py-3">{item[1]}</td>
                                            <td class="px-4 py-3">{item[2]}</td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                        </div>

                        <p>
                            Kui töömaht, maksumus või mõju viitavad erinevale
                            tasemele, kasutatakse kõrgemat põhjendatud
                            kategooriat.
                        </p>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Epicu eelanalüüs ja tööplaan
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <strong>Epic:</strong> kinnitamist vajavat Epicut
                                    ennast eelanalüüsi tegemiseks sprinti ei lisata.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Eelanalüüsi Task:</strong> luuakse Epicu
                                    alla ja planeeritakse sprinti. Selle käigus täpsustatakse
                                    Epicu eesmärk, skoop, sõltuvused, riskid ja võimalikud
                                    arendustööd.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Tööplaan:</strong> võimalikud
                                    arendustööd lisatakse Epicu kirjelduse lõppu
                                    jaotisesse „Võimalikud arendustööd“. Iga töö
                                    juurde lisatakse esialgne MD-hinnang
                                    järgmise malli järgi:
                                    <div
                                        class="mt-3 overflow-x-auto rounded-lg border border-slate-200 bg-slate-50 p-4 font-mono text-sm leading-6"
                                    >
                                        <code
                                            class="block whitespace-nowrap text-slate-600"
                                            >## Võimalikud arendustööd</code
                                        >
                                        <code
                                            class="mt-2 block whitespace-nowrap text-slate-900"
                                            >- **Töö tüüp - Component: Lühike
                                            kirjeldus** (N MD)</code
                                        >
                                    </div>
                                </li>
                                <li class={bulletClass}>
                                    <strong>Enne kinnitamist:</strong> loend on esialgne
                                    tööplaan, mitte Jira backlog. Epicu eelanalüüsi
                                    Task on erand; teisi töid veel ei looda.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Pärast kinnitamist:</strong> Jira tööd
                                    luuakse pärast IT-komitee ülevaatust ja Epicule
                                    vajaliku kinnituse saamist. Seejärel täpsustatakse
                                    need Refinementis ning planeeritakse backlogi
                                    või sprinti.
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Epicu kinnitamine ja arendusvoog
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <strong>Voog:</strong>
                                    <JiraStatusFlow
                                        statuses={epicApprovalFlow}
                                        class="mt-2"
                                    />
                                </li>
                                <li class={bulletClass}>
                                    <strong>Otsustamiseks vajalik:</strong> eesmärk,
                                    kasu, ärivastutaja, komponent, maht, kulu ja eeldatav
                                    valmimisaeg.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint:</strong> kinnitamata IT Improvement
                                    või IT Project algatust sprinti ei võeta.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Tagasilükkamine:</strong> Epic liigub
                                    olekusse Cancelled ja selle Jira Resolution on
                                    Won't do.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Pärimine:</strong> Epicu kategooria pärivad
                                    selle Storyd, Taskid ja Bugid; Sub-task pärib
                                    kategooria põhitöölt. Grouping'u alla kuuluvad
                                    tööd liigitatakse eraldi.
                                </li>
                            </ul>
                        </section>
                    </div>
                </details>

                <details id="scrum" class="group scroll-mt-6">
                    <summary
                        onclick={scrollToSection}
                        class="flex cursor-pointer list-none items-center justify-between gap-6 py-7 marker:hidden"
                    >
                        <h2 class={sectionHeadingClass}>
                            5. Scrum: töölaud ja sprindi rütm
                        </h2>
                        <ChevronDown
                            class="size-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180"
                            aria-hidden="true"
                        />
                    </summary>
                    <div class="space-y-8 pb-10 leading-7 text-slate-700">
                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Töövaated ja juhendid
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >Aktiivse sprindi töölaud</a
                                    > – igapäevane töö ja Stand-up.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45/backlog"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}>Backlog</a
                                    > – täpsustamine, järjestamine ja planeerimine.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >Jira DEV space</a
                                    > – kõik arendustööd.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/plugins/servlet/project-config/DEV/components"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >DEV komponendid</a
                                    > – süsteemide ja rakenduste loend.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker365.sharepoint.com/sites/ITTeam/SitePages/IT-tiimi-uue-t%C3%B6%C3%B6korralduse-kiir%C3%BClevaade.aspx"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >Täielik juhend SharePointis</a
                                    > – töökorralduse detailid.
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Kohtumiste rütm
                            </h3>
                            <div class="mt-4 {tableScrollClass}">
                                <table
                                    class="w-full min-w-180 text-left text-sm"
                                >
                                    <thead
                                        class="border-b border-slate-200 bg-slate-50"
                                    >
                                        <tr>
                                            <th class="px-4 py-3 font-semibold"
                                                >Sündmus</th
                                            >
                                            <th class="px-4 py-3 font-semibold"
                                                >Aeg</th
                                            >
                                            <th class="px-4 py-3 font-semibold"
                                                >Eesmärk</th
                                            >
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        {#each meetings as meeting (meeting[0])}
                                            <tr>
                                                <td
                                                    class="px-4 py-3 font-semibold text-slate-900"
                                                    >{meeting[0]}</td
                                                >
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap"
                                                    >{meeting[1]}</td
                                                >
                                                <td class="px-4 py-3"
                                                    >{meeting[2]}</td
                                                >
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Sprindi alustamine ja lõpetamine
                            </h3>
                            <ul class="mt-4 space-y-3">
                                <li class={bulletClass}>
                                    <strong>Ettevalmistus:</strong> Product Owner
                                    järjestab Ready for Development tööd. Tiim kontrollib
                                    saadavust, sõltuvusi ja eelmise sprindi lõpetamata
                                    töid.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint Planning:</strong> lepitakse kokku
                                    Sprint Goal ja valitakse realistlik töömaht. Tööd
                                    lisatakse uude sprinti olekuga Vaja teha ning
                                    sprint käivitatakse Jiras.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint Goal:</strong> 1–3 lühikest tulemust,
                                    mida sprint peab saavutama. See ei ole Jira tööde
                                    loetelu.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Lõpetamine:</strong> Sprint Review'l vaadatakse
                                    tulemus üle. Tehtud on ainult kokkulepitud tulemustele
                                    ja kvaliteedinõuetele vastav töö. Sprint lõpetatakse
                                    Jiras ning Retrospective'il lepitakse kokku vähemalt
                                    üks parandus.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Lõpetamata töö:</strong> uuendatakse staatus,
                                    takistuse põhjus, töölogid ja järelejäänud maht.
                                    Product Owner ja tiim otsustavad, kas töö läheb
                                    backlogi või teadlikult järgmisse sprinti.
                                </li>
                            </ul>
                        </section>
                    </div>
                </details>

                <details id="kokkulepped" class="group scroll-mt-6">
                    <summary
                        onclick={scrollToSection}
                        class="flex cursor-pointer list-none items-center justify-between gap-6 py-7 marker:hidden"
                    >
                        <h2 class={sectionHeadingClass}>
                            6. Igapäevased kokkulepped
                        </h2>
                        <ChevronDown
                            class="size-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180"
                            aria-hidden="true"
                        />
                    </summary>
                    <div class="space-y-8 pb-10 leading-7 text-slate-700">
                        <section>
                            <h3 class={subsectionHeadingClass}>Vastutus</h3>
                            <div class="mt-4 {tableScrollClass}">
                                <table
                                    class="w-full min-w-150 text-left text-sm"
                                >
                                    <thead
                                        class="border-b border-slate-200 bg-slate-50"
                                    >
                                        <tr>
                                            <th class="px-4 py-3 font-semibold"
                                                >Roll või väli</th
                                            >
                                            <th class="px-4 py-3 font-semibold"
                                                >Vastutus</th
                                            >
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        {#each roles as role (role[0])}
                                            <tr>
                                                <td
                                                    class="px-4 py-3 font-semibold text-slate-900"
                                                    >{role[0]}</td
                                                >
                                                <td class="px-4 py-3"
                                                    >{role[1]}</td
                                                >
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Töö tegemine ja ajalogimine
                            </h3>
                            <dl class="mt-5 grid gap-4 md:grid-cols-2">
                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-5"
                                >
                                    <dt class="font-semibold text-slate-900">
                                        Takistused ja seosed
                                    </dt>
                                    <dd class="mt-2">
                                        Kui töö takerdub, märgi Jiras takistuse
                                        põhjus ja järgmine tegevus. Seo töö
                                        vastava branchi, commit'ide ja Pull
                                        Requestiga.
                                    </dd>
                                </div>

                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-5"
                                >
                                    <dt class="font-semibold text-slate-900">
                                        Ajaloogimine
                                    </dt>
                                    <dd class="mt-2">
                                        Logi tegelik teostusaeg ligikaudsete
                                        täistundidena, näiteks 1 h, 2 h või 5 h.
                                        Logida võib kohe, päeva või nädala
                                        lõpus, kuid töölogid peavad olema kuu
                                        lõpuks korras. Epicule sama aega uuesti
                                        ei logita.
                                    </dd>
                                </div>

                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-5 md:col-span-2"
                                >
                                    <dt class="font-semibold text-slate-900">
                                        Hindamine ja planeerimine
                                    </dt>
                                    <dd class="mt-2">
                                        Story Pointsi hinnang sisestatakse 0,5
                                        MD sammuga:
                                        <strong>0,5 SP = 0,5 MD = 2,5 h</strong>
                                        ja <strong>1 SP = 1 MD = 5 h</strong>.
                                        Ühe tunni töö hinnang võib olla 0,5 MD,
                                        tegelik töölogi aga 1 h. Hinnangud ja
                                        töölogid aitavad sprinte planeerida;
                                        neid ei kasutata arendaja tulemuslikkuse
                                        mõõtmiseks.
                                    </dd>
                                </div>
                            </dl>
                        </section>
                    </div>
                </details>
            </div>
        </article>
    </main>
</SiteLayout>
