<script lang="ts">
    import CalendarDays from 'lucide-svelte/icons/calendar-days';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import Clock3 from 'lucide-svelte/icons/clock-3';
    import Euro from 'lucide-svelte/icons/euro';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import Layers from 'lucide-svelte/icons/layers';
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
            primaryEmphasis: 'Suurem teema või projekt',
            middle: ', mis koosneb seotud arendustöödest.',
            secondaryEmphasis: '',
            suffix: '',
        },
        {
            type: 'story',
            primaryEmphasis: 'Arendustöö',
            middle: ', millega luuakse ',
            secondaryEmphasis: 'uus funktsionaalsus või parendus',
            suffix: '.',
        },
        {
            type: 'bug',
            primaryEmphasis: 'Arendustöö',
            middle: ', millega parandatakse ',
            secondaryEmphasis: 'olemasoleva lahenduse viga',
            suffix: '.',
        },
        {
            type: 'task',
            primaryEmphasis: '',
            middle: 'Analüüsi-, seadistus- või muu töö, mis ',
            secondaryEmphasis: 'ei ole arendustöö',
            suffix: '.',
        },
        {
            type: 'subtask',
            primaryEmphasis: '',
            middle: 'Story, Taski või Bugi ',
            secondaryEmphasis: 'väiksem teostussamm',
            suffix: '.',
        },
    ] as const;

    const epicApprovalFlow = [
        'To Do',
        'In Analysis',
        'Waiting for Approval',
        'Approved',
        'In Progress',
        'Ready for Release',
        'Done',
    ] as const;

    const statuses = [
        {
            key: 'epic',
            types: ['epic'],
            flow: epicApprovalFlow,
        },
        {
            key: 'story-bug',
            types: ['story', 'bug'],
            flow: [
                'To Do',
                'In Progress',
                'In Review',
                'Testing',
                'Ready for Release',
                'Done',
            ],
        },
        {
            key: 'task',
            types: ['task'],
            flow: ['To Do', 'In Progress', 'In Review', 'Done'],
        },
        {
            key: 'subtask',
            types: ['subtask'],
            flow: ['To Do', 'In Progress', 'Done'],
        },
    ] as const;

    const portfolioCategories = [
        {
            name: 'Grouping',
            size: {
                kind: 'grouping',
                label: 'Epic eri kategooriatega tööde koondamiseks',
            },
            approval: 'Eraldi kinnitust ei ole.',
        },
        {
            name: 'IT Support',
            size: {
                kind: 'estimate',
                days: 'Kuni 2 MD',
                hours: '10 h',
                cost: 'alla 1 000 EUR',
            },
            approval: 'IT-tiim.',
        },
        {
            name: 'IT Change',
            size: {
                kind: 'estimate',
                days: '2–6 MD',
                hours: '10–30 h',
                cost: '1 000–3 000 EUR',
            },
            approval: 'IT-tiim ja sisemine tellija.',
        },
        {
            name: 'IT Improvement',
            size: {
                kind: 'estimate',
                days: '4–10 MD',
                hours: '20–50 h',
                cost: '2 000–5 000 EUR',
            },
            approval: 'IT-komitee ja juhatus.',
        },
        {
            name: 'IT Project',
            size: {
                kind: 'estimate',
                days: 'Üle 10 MD',
                hours: '50 h',
                cost: 'üle 5 000 EUR',
            },
            approval: 'IT-komitee ja juhatus.',
        },
    ] as const;

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
        {
            name: 'Assignee / omanik',
            responsibility: [
                { text: 'Tegeleb tööga ning vastutab ' },
                {
                    text: 'järgmise tegevuse ja ajakohase staatuse',
                    emphasis: true,
                },
                { text: ' eest.' },
            ],
        },
        {
            name: 'Reporter',
            responsibility: [
                { text: 'Jira töö esitaja või sisestaja', emphasis: true },
                { text: ', kellelt saab vajaduse kohta lisainfot; ' },
                { text: 'ei pruugi olla tellija', emphasis: true },
                { text: '.' },
            ],
        },
        {
            name: 'Tellija / ärivastutaja',
            responsibility: [
                {
                    text: 'Kirjeldab vajaduse ja kinnitab soovitud tulemuse',
                    emphasis: true,
                },
                { text: '.' },
            ],
        },
        {
            name: 'Stakeholderid',
            responsibility: [
                { text: 'Annavad tööks vajalikku ' },
                { text: 'sisendit ja tagasisidet', emphasis: true },
                { text: ' ning osalevad neid puudutavates otsustes.' },
            ],
        },
        {
            name: 'Arendustiim',
            responsibility: [
                { text: 'Hindab ja planeerib tööd', emphasis: true },
                { text: ' ning vastutab ' },
                {
                    text: 'arenduse, ülevaatuse, testimise ja valmimise',
                    emphasis: true,
                },
                { text: ' eest.' },
            ],
        },
        {
            name: 'Product Owner',
            responsibility: [
                { text: 'Järjestab backlogi', emphasis: true },
                { text: ' ja otsustab koos tiimiga, ' },
                { text: 'millal töö sprinti võtta', emphasis: true },
                { text: '.' },
            ],
        },
        {
            name: 'Scrum Master',
            responsibility: [
                { text: 'Korraldab Scrum evente', emphasis: true },
                { text: ', aitab eemaldada ' },
                { text: 'töökorralduslikke takistusi', emphasis: true },
                { text: ' ja järgida kokkuleppeid.' },
            ],
        },
    ] as const;

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
                        <section>
                            <h3 class={subsectionHeadingClass}>
                                Uus töökorraldus aitab
                            </h3>
                            <ul class="mt-4 space-y-2">
                                <li class={bulletClass}>
                                    <strong>Fookus:</strong> suunata tiimi võimekus
                                    kõige olulisematele töödele;
                                </li>
                                <li class={bulletClass}>
                                    <strong>Vähem pooleliolevat:</strong> vähendada
                                    korraga pooleliolevate tööde hulka;
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
                        </section>

                        <section>
                            <h3 class={subsectionHeadingClass}>Tööviis</h3>
                            <ul class="mt-4 space-y-2">
                                <li class={bulletClass}>
                                    <strong>Arendus:</strong> tööd tehakse
                                    ühenädalaste ühise eesmärgiga sprintidena (<strong
                                        >Scrum</strong
                                    >).
                                </li>
                                <li class={bulletClass}>
                                    <strong>IT Ops ja IT-tugi:</strong> tööd
                                    liiguvad järjestatud nimekirja alusel pideva
                                    voona (<strong>Kanban</strong>).
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
                            <strong>Sprinti võetakse:</strong> selge, hinnatud ja
                            vajaliku kinnituse saanud töö.
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
                                    <strong class="block">Töö asukoht:</strong>
                                    <ul
                                        class="mt-2 list-disc space-y-1.5 pl-5 marker:text-stokker-primary/70"
                                    >
                                        <li>
                                            <a
                                                href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class={externalLinkClass}
                                                >IT Development ehk DEV space</a
                                            > – arendustööde peamine Jira space.
                                        </li>
                                        <li>
                                            <a
                                                href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/boards/112"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class={externalLinkClass}
                                                >IT Team - DEV - Board (Scrum)</a
                                            > – arendustööde sprindid.
                                        </li>
                                        <li>
                                            <a
                                                href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/boards/145"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class={externalLinkClass}
                                                >IT Team - Epics - Board
                                                (Kanban)</a
                                            > – Epicute üldvaade.
                                        </li>
                                    </ul>
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/components"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}>Component</a
                                    >: kohustuslik väli, mis näitab seotud
                                    süsteemi. Kui komponent pole veel teada,
                                    kasutatakse ajutiselt väärtust
                                    <code
                                        class="inline-flex rounded-md border border-stokker-primary/25 bg-stokker-primary-50 px-2 py-0.5 font-mono text-sm font-semibold text-stokker-primary"
                                        >Other</code
                                    >.
                                </li>
                                <li class={bulletClass}>
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <strong>Sprindi nimi:</strong>
                                        <code
                                            class="inline-flex rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 font-mono text-sm font-medium text-stokker-primary"
                                            >{'IT Dev Sprint {YY}-W{ww}'}</code
                                        >
                                        <span
                                            class="text-sm font-medium text-slate-400"
                                            >näiteks</span
                                        >
                                        <code
                                            class="inline-flex rounded-md border border-stokker-primary/25 bg-stokker-primary-50 px-2.5 py-1 font-mono text-sm font-semibold text-stokker-primary"
                                            >IT Dev Sprint 26-W35</code
                                        >
                                    </div>
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/boards/112/backlog"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}>Backlog</a
                                    >: planeerimata tööd paiknevad nimekirjades
                                    <span
                                        class="inline-flex flex-wrap items-center gap-1.5 align-middle"
                                    >
                                        <JiraStatusBadge
                                            status="Initial Review"
                                        />
                                        <JiraStatusBadge
                                            status="In refinement"
                                        />
                                        <JiraStatusBadge
                                            status="Ready for Development"
                                        />
                                    </span>.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint ja tähtaeg:</strong> sprint
                                    näitab planeeritud nädalat.
                                    <code
                                        class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm font-medium text-stokker-primary"
                                        >Due date</code
                                    >'i kasutatakse ainult kindla tähtaja
                                    korral.
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
                                                <td class="px-4 py-3">
                                                    {#if issue.primaryEmphasis}<strong
                                                            >{issue.primaryEmphasis}</strong
                                                        >{/if}{issue.middle}{#if issue.secondaryEmphasis}<strong
                                                            >{issue.secondaryEmphasis}</strong
                                                        >{/if}{issue.suffix}
                                                </td>
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
                                    <div
                                        class="flex flex-wrap items-center gap-x-2 gap-y-1.5"
                                    >
                                        <strong>Parent:</strong>
                                        <span>Sub-taski otsene parent on</span>
                                        <JiraIssueBadge type="story" />
                                        <JiraIssueBadge type="task" />
                                        <span>või</span>
                                        <JiraIssueBadge type="bug" />
                                        <span>— mitte</span>
                                        <JiraIssueBadge type="epic" />
                                    </div>
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint:</strong> Sub-taski ei planeerita
                                    eraldi sprinti; see valmib koos põhitööga samas
                                    sprindis.
                                </li>
                                <li class={bulletClass}>
                                    <div
                                        class="flex flex-wrap items-center gap-x-2 gap-y-1.5"
                                    >
                                        <strong>Iseseisev töö:</strong>
                                        <span
                                            >kui töö on iseseisev või vajab
                                            eraldi sprinti, luuakse see eraldi
                                            tööna:</span
                                        >
                                        <JiraIssueBadge type="story" />
                                        <JiraIssueBadge type="task" />
                                        <span>või</span>
                                        <JiraIssueBadge type="bug" />
                                    </div>
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
                                        Töö on peatatud; märgitakse põhjus ja
                                        järgmine tegevus.
                                    </span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <JiraStatusBadge status="Cancelled" />
                                    <span>
                                        Tööga ei jätkata; märgitakse tühistamise
                                        põhjus.
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
                                        <th
                                            class="w-[52%] px-4 py-3 font-semibold"
                                            >Orienteeruv suurus</th
                                        >
                                        <th class="px-4 py-3 font-semibold"
                                            >Kinnitamine</th
                                        >
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    {#each portfolioCategories as item (item.name)}
                                        <tr
                                            class="transition-colors hover:bg-slate-50/60"
                                        >
                                            <td
                                                class="px-4 py-4 font-semibold text-slate-900"
                                                >{item.name}</td
                                            >
                                            <td class="px-4 py-4">
                                                {#if item.size.kind === 'grouping'}
                                                    <span
                                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-sm font-medium text-slate-600"
                                                    >
                                                        <Layers
                                                            class="size-4 shrink-0 text-slate-400"
                                                            aria-hidden="true"
                                                        />
                                                        {item.size.label}
                                                    </span>
                                                {:else}
                                                    <div
                                                        class="flex flex-wrap items-center gap-1.5"
                                                    >
                                                        <span
                                                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-sm font-semibold text-blue-700"
                                                        >
                                                            <CalendarDays
                                                                class="size-3.5"
                                                                aria-hidden="true"
                                                            />
                                                            {item.size.days}
                                                        </span>
                                                        <span
                                                            class="text-xs font-medium text-slate-300"
                                                            aria-hidden="true"
                                                            >/</span
                                                        >
                                                        <span
                                                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-sm font-medium text-slate-600"
                                                        >
                                                            <Clock3
                                                                class="size-3.5 text-slate-400"
                                                                aria-hidden="true"
                                                            />
                                                            {item.size.hours}
                                                        </span>
                                                        <span
                                                            class="px-0.5 text-xs font-medium text-slate-400"
                                                            >või</span
                                                        >
                                                        <span
                                                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-md border border-emerald-200/80 bg-emerald-50/70 px-2.5 py-1 text-sm font-medium text-emerald-700"
                                                        >
                                                            <Euro
                                                                class="size-3.5"
                                                                aria-hidden="true"
                                                            />
                                                            {item.size.cost}
                                                        </span>
                                                    </div>
                                                {/if}
                                            </td>
                                            <td class="px-4 py-4"
                                                >{item.approval}</td
                                            >
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
                                    <strong>Epic:</strong> Epicut ennast eelanalüüsiks
                                    sprinti ei lisata.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Eelanalüüsi Task:</strong> Epicu alla
                                    luuakse sprinti planeeritav Task, milles täpsustatakse
                                    eesmärk, skoop, sõltuvused, riskid ja võimalikud
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
                                    <strong>Loendi tähendus:</strong> loend on tööplaan,
                                    mitte Jira backlog.
                                </li>
                                <li class={bulletClass}>
                                    <strong
                                        >Pärast ülevaatust ja kinnitamist:</strong
                                    >
                                    loendis kirjeldatud Jira tööd luuakse pärast IT-komitee
                                    ülevaatust ja vajaliku kinnituse saamist ning
                                    täpsustatakse Refinementis.
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
                                    <strong>Sprint:</strong> kinnitamata IT Improvementi
                                    või IT Projecti algatust sprinti ei võeta.
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
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/boards/112"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >Aktiivse sprindi töölaud</a
                                    > – igapäevane töö ja Stand-up.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/boards/112/backlog"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}>Backlog</a
                                    > – täpsustamine, järjestamine ja planeerimine.
                                </li>
                                <li class={bulletClass}>
                                    <a
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/boards/145"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class={externalLinkClass}
                                        >Epicute töölaud</a
                                    > – Epicute ülevaade ja kinnitamise voog.
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
                                        href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/software/c/projects/DEV/components"
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
                                    saadavust ja sõltuvusi.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint Planning:</strong> lepitakse kokku
                                    Sprint Goal ja valitakse realistlik töömaht. Tööd
                                    lisatakse uude sprinti ning sprint käivitatakse
                                    Jiras.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Sprint Goal:</strong> 1–3 lühikest tulemust,
                                    mida sprint peab saavutama. See ei ole Jira tööde
                                    loetelu.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Lõpetamine:</strong> Sprint Review'l vaadatakse
                                    tulemus üle. Done-staatusesse liigub ainult kokkulepitud
                                    tulemustele ja kvaliteedinõuetele vastav töö.
                                    Sprint lõpetatakse Jiras ning Retrospective'il
                                    lepitakse kokku vähemalt üks parandus.
                                </li>
                                <li class={bulletClass}>
                                    <strong>Lõpetamata töö:</strong> uuendatakse staatus,
                                    takistus, töölogid ja järelejäänud maht. Product
                                    Owner ja tiim otsustavad, kas töö läheb backlogi
                                    või järgmisse sprinti.
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
                                        {#each roles as role (role.name)}
                                            <tr>
                                                <td
                                                    class="px-4 py-3 font-semibold text-slate-900"
                                                    >{role.name}</td
                                                >
                                                <td class="px-4 py-3">
                                                    {#each role.responsibility as part (part.text)}
                                                        {#if 'emphasis' in part && part.emphasis}
                                                            <strong
                                                                >{part.text}</strong
                                                            >
                                                        {:else}
                                                            {part.text}
                                                        {/if}
                                                    {/each}
                                                </td>
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
                                        Takistuse korral märgitakse selle põhjus
                                        ja järgmine tegevus. Jira tööga seotakse
                                        vajalik branch, commit'id ja Pull
                                        Request.
                                    </dd>
                                </div>

                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-5"
                                >
                                    <dt class="font-semibold text-slate-900">
                                        Ajaloogimine
                                    </dt>
                                    <dd class="mt-3">
                                        <ul class="space-y-2">
                                            <li class={bulletClass}>
                                                Logi tegelik teostusaeg
                                                ligikaudsete täistundidena,
                                                näiteks 1 h, 2 h või 5 h.
                                            </li>
                                            <li class={bulletClass}>
                                                Logida võib kohe, päeva või
                                                nädala lõpus, kuid töölogid
                                                peavad olema kuu lõpuks korras.
                                            </li>
                                            <li class={bulletClass}>
                                                Epicule teostustöödele logitud
                                                aega uuesti ei lisata.
                                            </li>
                                        </ul>
                                    </dd>
                                </div>

                                <div
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-5 md:col-span-2"
                                >
                                    <dt class="font-semibold text-slate-900">
                                        Hindamine ja planeerimine
                                    </dt>
                                    <dd class="mt-3">
                                        <ul class="space-y-2">
                                            <li class={bulletClass}>
                                                <code
                                                    class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm font-medium text-stokker-primary"
                                                    >Story Points</code
                                                >i hinnang sisestatakse 0,5 MD
                                                sammuga:
                                                <ul
                                                    class="mt-2 list-disc space-y-1 pl-5 marker:text-stokker-primary/70"
                                                >
                                                    <li>
                                                        <strong
                                                            >0,5 SP = 0,5 MD =
                                                            2,5 h</strong
                                                        >
                                                    </li>
                                                    <li>
                                                        <strong
                                                            >1 SP = 1 MD = 5 h</strong
                                                        >
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class={bulletClass}>
                                                Ühe tunni töö hinnang võib olla
                                                <code
                                                    class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm font-medium text-stokker-primary"
                                                    >0,5 MD</code
                                                >, tegelik töölogi aga
                                                <code
                                                    class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-sm font-medium text-stokker-primary"
                                                    >1 h</code
                                                >.
                                            </li>
                                            <li class={bulletClass}>
                                                Hinnangud ja töölogid aitavad
                                                sprinte planeerida; neid ei
                                                kasutata arendaja tulemuslikkuse
                                                mõõtmiseks.
                                            </li>
                                        </ul>
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
