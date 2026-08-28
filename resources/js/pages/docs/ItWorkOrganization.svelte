<script lang="ts">
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import CalendarDays from 'lucide-svelte/icons/calendar-days';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import CircleCheckBig from 'lucide-svelte/icons/circle-check-big';
    import CirclePause from 'lucide-svelte/icons/circle-pause';
    import ClipboardCheck from 'lucide-svelte/icons/clipboard-check';
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import GitPullRequest from 'lucide-svelte/icons/git-pull-request';
    import Kanban from 'lucide-svelte/icons/kanban';
    import ListChecks from 'lucide-svelte/icons/list-checks';
    import PanelsTopLeft from 'lucide-svelte/icons/panels-top-left';
    import Route from 'lucide-svelte/icons/route';
    import Users from 'lucide-svelte/icons/users';
    import AppHead from '@/components/AppHead.svelte';
    import SiteLayout from '@/layouts/SiteLayout.svelte';

    const tableOfContents = [
        { number: '01', label: 'Miks ja kuidas töötame?', href: '#miks' },
        { number: '02', label: 'Arendusprotsess', href: '#arendusprotsess' },
        { number: '03', label: 'Töö korraldamine Jiras', href: '#jira' },
        { number: '04', label: 'Portfolio ja Epic', href: '#portfolio' },
        { number: '05', label: 'Scrum ja sprindi rütm', href: '#scrum' },
        {
            number: '06',
            label: 'Igapäevased kokkulepped',
            href: '#kokkulepped',
        },
    ];

    const developmentSteps = [
        'Vajaduse registreerimine',
        'Esmane ülevaatus',
        'Analüüs ja refinement',
        'Kinnitamine ja prioriseerimine',
        'Sprint planning',
        'Lahenduse kavandamine',
        'Arendus',
        'Ülevaatus ja testimine',
        'Release',
        'Done ja järeltegevused',
    ];

    const issueTypes = [
        {
            type: 'Epic',
            use: 'Suurem teema või projekt, mis koosneb seotud arendustöödest.',
        },
        {
            type: 'Story / Lugu',
            use: 'Uus funktsionaalsus või olemasoleva lahenduse parendus.',
        },
        {
            type: 'Bug / Viga',
            use: 'Olemasoleva lahenduse vea parandamine.',
        },
        {
            type: 'Task / Ülesanne',
            use: 'Analüüsi-, seadistus-, tehniline või muu sisemine töö.',
        },
        {
            type: 'Sub-task / Alamülesanne',
            use: 'Story, Taski või Bugi väiksem teostussamm.',
        },
    ];

    const statuses = [
        {
            type: 'Epic',
            flow: 'Vaja teha → In Analysis → Waiting for Approval → Approved → Lahendamisel → Ready for Release → Tehtud',
        },
        {
            type: 'Story / Lugu ja Bug / Viga',
            flow: 'Vaja teha → Lahendamisel → In Review → Testing → Ready for Release → Tehtud',
        },
        {
            type: 'Task / Ülesanne',
            flow: 'Vaja teha → Lahendamisel → In Review → Tehtud',
        },
        {
            type: 'Sub-task / Alamülesanne',
            flow: 'Vaja teha → Lahendamisel → Tehtud',
        },
    ];

    const portfolioCategories = [
        {
            category: 'Grouping',
            size: 'Epic eri kategooriatega tööde koondamiseks',
            approval: 'Eraldi kinnitust ei ole',
        },
        {
            category: 'IT Support',
            size: 'Kuni 2 MD / 10 h või alla 1 000 EUR',
            approval: 'IT-tiim',
        },
        {
            category: 'IT Change',
            size: '2–6 MD / 10–30 h või 1 000–3 000 EUR',
            approval: 'IT-tiim ja sisemine tellija',
        },
        {
            category: 'IT Improvement',
            size: '4–10 MD / 20–50 h või 2 000–5 000 EUR',
            approval: 'IT-komitee ja juhatus',
        },
        {
            category: 'IT Project',
            size: 'Üle 10 MD / 50 h või üle 5 000 EUR',
            approval: 'IT-komitee ja juhatus',
        },
    ];

    const meetings = [
        {
            event: 'Sprint Review',
            time: 'E 09.00–09.30',
            purpose: 'Valminud tulemuse ülevaatus ja tagasiside.',
        },
        {
            event: 'Retrospective',
            time: 'E 09.30–09.45',
            purpose: 'Järgmise töökorralduse paranduse kokkuleppimine.',
        },
        {
            event: 'Sprint Planning + Stand-up',
            time: 'E 09.45–10.15',
            purpose: 'Sprint Goal ja realistlik töömaht.',
        },
        {
            event: 'Daily Stand-up',
            time: 'T–R 09.00–09.15',
            purpose: 'Edenemine, takistused ja järgmised tegevused.',
        },
        {
            event: 'Backlog Refinement',
            time: 'K 14.00–14.45',
            purpose: 'Tulevaste tööde täpsustamine ja hindamine.',
        },
    ];

    const roles = [
        {
            role: 'Tellija / ärivastutaja',
            responsibility: 'Kirjeldab vajaduse ja kinnitab soovitud tulemuse.',
        },
        {
            role: 'Product Owner',
            responsibility:
                'Järjestab backlogi ja otsustab koos tiimiga, millal töö sprinti võtta.',
        },
        {
            role: 'Omanik ehk Assignee',
            responsibility:
                'Vastutab töö järgmise aktiivse tegevuse ja ajakohase staatuse eest.',
        },
    ];

    const bulletClass =
        'relative pl-6 before:absolute before:left-1 before:top-[0.65em] before:size-1.5 before:rounded-full before:bg-stokker-primary';
</script>

<AppHead title="IT-tiimi uus töökorraldus">
    <meta
        name="description"
        content="Stokkeri IT-tiimi uue töökorralduse lühiülevaade: arendusprotsess, Jira, Portfolio, Scrum ja igapäevased kokkulepped."
    />
</AppHead>

<SiteLayout>
    <div class="min-h-screen scroll-smooth bg-slate-50 text-slate-950">
        <main>
            <section
                class="relative isolate overflow-hidden bg-stokker-primary-dark"
            >
                <div
                    class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_15%_20%,rgba(0,122,194,0.75),transparent_32%),radial-gradient(circle_at_90%_75%,rgba(89,158,117,0.35),transparent_30%)]"
                ></div>
                <div
                    class="absolute -top-24 -right-20 -z-10 size-80 rounded-full border border-white/10"
                ></div>
                <div
                    class="absolute -right-4 -bottom-48 -z-10 size-96 rounded-full border border-white/10"
                ></div>

                <div
                    class="mx-auto grid max-w-7xl gap-12 px-5 py-16 lg:grid-cols-[minmax(0,1fr)_22rem] lg:px-8 lg:py-24"
                >
                    <div class="max-w-3xl">
                        <div
                            class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-sm font-medium text-sky-50"
                        >
                            <CircleCheckBig class="size-4" aria-hidden="true" />
                            Lühiülevaade · 6 teemat · 10 protsessisammu
                        </div>
                        <h1
                            class="max-w-3xl text-4xl leading-[1.05] font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl"
                        >
                            IT-tiimi uus töökorraldus
                        </h1>
                        <p
                            class="mt-6 max-w-2xl text-lg leading-8 text-sky-50/85 sm:text-xl"
                        >
                            Ühine viis tööde vastuvõtmiseks, prioriseerimiseks
                            ja lõpetamiseks — vähem korraga pooleli, selgem
                            vastutus ja varem nähtavad takistused.
                        </p>

                        <div class="mt-9 flex flex-wrap gap-3">
                            <a
                                href="https://stokker365.sharepoint.com/sites/IRONMANEE2/SitePages/ITHelpdeskHome.aspx"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-stokker-primary-dark shadow-sm transition-transform hover:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                Registreeri vajadus Iron Manis
                                <ArrowRight class="size-4" aria-hidden="true" />
                            </a>
                            <a
                                href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                            >
                                Ava Jira DEV space
                                <ExternalLink
                                    class="size-4"
                                    aria-hidden="true"
                                />
                            </a>
                        </div>
                    </div>

                    <div
                        class="self-end rounded-2xl border border-white/15 bg-white/10 p-5 text-white shadow-2xl shadow-slate-950/20 backdrop-blur"
                    >
                        <p class="text-sm font-semibold text-sky-100">
                            Põhiprintsiip
                        </p>
                        <p class="mt-3 text-2xl leading-8 font-semibold">
                            Järgmine töö algab siis, kui selle eesmärk ja
                            oodatav tulemus on piisavalt selged.
                        </p>
                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-white/10 p-3">
                                <span class="block text-2xl font-semibold"
                                    >1 nädal</span
                                >
                                <span class="text-sky-100">Scrumi sprint</span>
                            </div>
                            <div class="rounded-xl bg-white/10 p-3">
                                <span class="block text-2xl font-semibold"
                                    >0,5 MD</span
                                >
                                <span class="text-sky-100">hindamise samm</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="border-b border-slate-200 bg-white">
                <div
                    class="mx-auto grid max-w-7xl divide-y divide-slate-200 px-5 sm:grid-cols-3 sm:divide-x sm:divide-y-0 lg:px-8"
                >
                    <div class="flex gap-4 py-6 sm:px-6 sm:first:pl-0">
                        <GitPullRequest
                            class="mt-0.5 size-6 shrink-0 text-stokker-primary"
                            aria-hidden="true"
                        />
                        <div>
                            <p class="font-semibold">Scrum</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Arendustööd ühe nädala kaupa, ühise Sprint
                                Goaliga.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4 py-6 sm:px-6">
                        <Kanban
                            class="mt-0.5 size-6 shrink-0 text-stokker-primary"
                            aria-hidden="true"
                        />
                        <div>
                            <p class="font-semibold">Kanban</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Opsi ja toe järgmine töö tuleb järjestatud
                                nimekirjast.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4 py-6 sm:px-6 sm:last:pr-0">
                        <PanelsTopLeft
                            class="mt-0.5 size-6 shrink-0 text-stokker-primary"
                            aria-hidden="true"
                        />
                        <div>
                            <p class="font-semibold">Üks nähtav töövoog</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Prioriteet, omanik, seis ja takistused on Jiras
                                nähtavad.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <div
                class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-12 lg:grid-cols-[17rem_minmax(0,1fr)] lg:px-8 lg:py-16"
            >
                <aside class="min-w-0 lg:sticky lg:top-24 lg:self-start">
                    <p
                        class="mb-4 text-xs font-semibold tracking-[0.16em] text-slate-500 uppercase"
                    >
                        Sellel lehel
                    </p>
                    <nav aria-label="Lehe sisukord">
                        <ol
                            class="flex gap-2 overflow-x-auto pb-3 lg:flex-col lg:pb-0"
                        >
                            {#each tableOfContents as item (item.href)}
                                <li class="shrink-0">
                                    <a
                                        href={item.href}
                                        class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-600 transition-colors hover:bg-white hover:text-stokker-primary-dark"
                                    >
                                        <span
                                            class="text-xs font-semibold text-stokker-primary"
                                            >{item.number}</span
                                        >
                                        <span>{item.label}</span>
                                    </a>
                                </li>
                            {/each}
                        </ol>
                    </nav>
                    <div
                        class="mt-7 hidden rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-600 lg:block"
                    >
                        <p class="font-semibold text-slate-900">
                            Kuidas kasutada?
                        </p>
                        <p class="mt-1">
                            Ava teema pealkirjale vajutades. Esimesed kaks
                            peatükki on alustuseks lahti.
                        </p>
                    </div>
                </aside>

                <div class="min-w-0 space-y-4">
                    <details
                        id="miks"
                        class="group scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm open:shadow-md"
                        open
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-4 px-5 py-5 marker:hidden sm:px-7"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-stokker-primary-soft font-semibold text-stokker-primary-dark"
                                >01</span
                            >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-xl font-semibold sm:text-2xl"
                                    >Miks ja kuidas töötame?</span
                                >
                                <span
                                    class="mt-1 block text-sm leading-5 text-slate-500"
                                    >Fookus, läbipaistvus ja vähem pooleliolevat
                                    tööd</span
                                >
                            </span>
                            <ChevronDown
                                class="size-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="border-t border-slate-100 px-5 py-6 sm:px-7 sm:py-8"
                        >
                            <p class="max-w-3xl leading-7 text-slate-700">
                                Stokkeri IT-tiim toetab paljusid süsteeme ja
                                ärivajadusi ning kõiki töid ei ole võimalik
                                korraga ette võtta. Uus töökorraldus aitab:
                            </p>
                            <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                                {#each ['suunata tiimi võimekuse kõige olulisematele töödele;', 'vähendada korraga pooleliolevate tööde hulka;', 'muuta prioriteedid, vastutus ja tööde seis arusaadavaks;', 'tuua takistused, sõltuvused ja riskid varem nähtavale.'] as benefit (benefit)}
                                    <li
                                        class="flex gap-3 rounded-xl bg-slate-50 p-4 leading-6 text-slate-700"
                                    >
                                        <CircleCheckBig
                                            class="mt-0.5 size-5 shrink-0 text-stokker-green"
                                            aria-hidden="true"
                                        />
                                        <span>{benefit}</span>
                                    </li>
                                {/each}
                            </ul>

                            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                                <div
                                    class="rounded-xl border border-slate-200 p-5"
                                >
                                    <div class="flex items-center gap-3">
                                        <GitPullRequest
                                            class="size-5 text-stokker-primary"
                                            aria-hidden="true"
                                        />
                                        <h3 class="text-base font-semibold">
                                            Scrum
                                        </h3>
                                    </div>
                                    <p class="mt-3 leading-7 text-slate-600">
                                        Arendustööd tehakse ühenädalaste
                                        sprintidena, millel on valitud tööd ja
                                        ühine eesmärk.
                                    </p>
                                </div>
                                <div
                                    class="rounded-xl border border-slate-200 p-5"
                                >
                                    <div class="flex items-center gap-3">
                                        <Kanban
                                            class="size-5 text-stokker-primary"
                                            aria-hidden="true"
                                        />
                                        <h3 class="text-base font-semibold">
                                            Kanban
                                        </h3>
                                    </div>
                                    <p class="mt-3 leading-7 text-slate-600">
                                        IT Opsi ja IT-toe järgmine töö võetakse
                                        järjestatud nimekirjast vaba võimekuse
                                        tekkimisel.
                                    </p>
                                </div>
                            </div>

                            <p
                                class="mt-6 rounded-xl border-l-4 border-stokker-primary bg-stokker-primary-50 p-4 leading-7 text-slate-700"
                            >
                                Mõlemas hoitakse tööde seis, prioriteedid,
                                vastutajad ja takistused nähtavana. Vajadus
                                esitatakse üldjuhul
                                <a
                                    href="https://stokker365.sharepoint.com/sites/IRONMANEE2/SitePages/ITHelpdeskHome.aspx"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-semibold text-stokker-primary underline decoration-stokker-primary/30 underline-offset-4 hover:decoration-stokker-primary"
                                    >Iron Manis</a
                                >. Vajaduse korral luuakse või seotakse sellega
                                Jira arendustöö, mis järjestatakse backlogis.
                            </p>
                        </div>
                    </details>

                    <details
                        id="arendusprotsess"
                        class="group scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm open:shadow-md"
                        open
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-4 px-5 py-5 marker:hidden sm:px-7"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-stokker-primary-soft font-semibold text-stokker-primary-dark"
                                >02</span
                            >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-xl font-semibold sm:text-2xl"
                                    >Arendusprotsess</span
                                >
                                <span
                                    class="mt-1 block text-sm leading-5 text-slate-500"
                                    >Vajadusest valminud lahenduse ja
                                    järeltegevusteni</span
                                >
                            </span>
                            <ChevronDown
                                class="size-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="border-t border-slate-100 px-5 py-6 sm:px-7 sm:py-8"
                        >
                            <div
                                class="mb-7 flex items-start gap-3 rounded-xl bg-amber-50 p-4 text-amber-950"
                            >
                                <ClipboardCheck
                                    class="mt-0.5 size-5 shrink-0"
                                    aria-hidden="true"
                                />
                                <p class="leading-7">
                                    Sprinti võetakse töö, mille soovitud
                                    tulemus, Acceptance Criteria, sõltuvused,
                                    töömaht, vastutus ja vajalik kinnitus on
                                    piisavalt selged.
                                </p>
                            </div>

                            <ol
                                class="grid gap-x-3 gap-y-6 sm:grid-cols-2 xl:grid-cols-5"
                            >
                                {#each developmentSteps as step, index (step)}
                                    <li class="relative min-w-0">
                                        <div
                                            class="h-full rounded-xl border border-slate-200 bg-slate-50 p-4"
                                        >
                                            <span
                                                class="text-xs font-semibold tracking-wider text-stokker-primary"
                                                >SAMM {String(
                                                    index + 1,
                                                ).padStart(2, '0')}</span
                                            >
                                            <p
                                                class="mt-2 text-sm leading-6 font-semibold text-slate-800"
                                            >
                                                {step}
                                            </p>
                                        </div>
                                        {#if index < developmentSteps.length - 1}
                                            <ArrowRight
                                                class="absolute top-1/2 -right-2.5 z-10 hidden size-5 -translate-y-1/2 rounded-full bg-white p-1 text-slate-400 xl:block"
                                                aria-hidden="true"
                                            />
                                        {/if}
                                    </li>
                                {/each}
                            </ol>
                        </div>
                    </details>

                    <details
                        id="jira"
                        class="group scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm open:shadow-md"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-4 px-5 py-5 marker:hidden sm:px-7"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-stokker-primary-soft font-semibold text-stokker-primary-dark"
                                >03</span
                            >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-xl font-semibold sm:text-2xl"
                                    >Töö korraldamine Jiras</span
                                >
                                <span
                                    class="mt-1 block text-sm leading-5 text-slate-500"
                                    >DEV space, backlog, töö tüübid ja staatused</span
                                >
                            </span>
                            <ChevronDown
                                class="size-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="border-t border-slate-100 px-5 py-6 sm:px-7 sm:py-8"
                        >
                            <div class="grid gap-4 lg:grid-cols-2">
                                <a
                                    href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group/link flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-5 transition-colors hover:border-stokker-primary/40 hover:bg-stokker-primary-50"
                                >
                                    <span>
                                        <span class="block font-semibold"
                                            >IT Development ehk DEV space</span
                                        >
                                        <span
                                            class="mt-1 block text-sm text-slate-500"
                                            >Kõik arendustööd</span
                                        >
                                    </span>
                                    <ExternalLink
                                        class="size-5 shrink-0 text-stokker-primary transition-transform group-hover/link:-translate-y-0.5"
                                        aria-hidden="true"
                                    />
                                </a>
                                <a
                                    href="https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45/backlog"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group/link flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-5 transition-colors hover:border-stokker-primary/40 hover:bg-stokker-primary-50"
                                >
                                    <span>
                                        <span class="block font-semibold"
                                            >Development backlog</span
                                        >
                                        <span
                                            class="mt-1 block text-sm text-slate-500"
                                            >Täpsustamine ja planeerimine</span
                                        >
                                    </span>
                                    <ExternalLink
                                        class="size-5 shrink-0 text-stokker-primary transition-transform group-hover/link:-translate-y-0.5"
                                        aria-hidden="true"
                                    />
                                </a>
                            </div>

                            <div class="mt-7 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase"
                                    >
                                        Component
                                    </p>
                                    <p class="mt-2 leading-6 text-slate-700">
                                        Kohustuslik süsteemi väli. Kui pole veel
                                        teada, kasutatakse ajutiselt väärtust
                                        <code class="font-semibold">Other</code
                                        >.
                                    </p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase"
                                    >
                                        Sprindi nimi
                                    </p>
                                    <p class="mt-2 leading-6 text-slate-700">
                                        <code class="font-semibold"
                                            >IT Dev Sprint YY-Www</code
                                        >, näiteks
                                        <code class="font-semibold"
                                            >IT Dev Sprint 26-W35</code
                                        >.
                                    </p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p
                                        class="text-xs font-semibold text-slate-500 uppercase"
                                    >
                                        Sprint ja tähtaeg
                                    </p>
                                    <p class="mt-2 leading-6 text-slate-700">
                                        Sprint näitab planeeritud nädalat. Due
                                        date on ainult kindla tähtaja korral.
                                    </p>
                                </div>
                            </div>

                            <p class="mt-6 leading-7 text-slate-700">
                                Planeerimata tööd paiknevad nimekirjades
                                <strong>Initial Review</strong>,
                                <strong>In refinement</strong> ja
                                <strong>Ready for Development</strong>. Teistest
                                space'idest tuuakse DEV-i korrastatud tööd
                                jooksvalt; säilitatakse töö hierarhia, staatus,
                                lingid ja ajalugu.
                            </p>

                            <h3 class="mt-9 text-lg font-semibold">
                                Tööde tüübid
                            </h3>
                            <div
                                class="mt-4 overflow-x-auto rounded-xl border border-slate-200"
                            >
                                <table
                                    class="w-full min-w-150 text-left text-sm"
                                >
                                    <thead class="bg-slate-50 text-slate-600">
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
                                                <td
                                                    class="px-4 py-3 font-semibold text-slate-900"
                                                    >{issue.type}</td
                                                >
                                                <td
                                                    class="px-4 py-3 leading-6 text-slate-600"
                                                    >{issue.use}</td
                                                >
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>

                            <div
                                class="mt-7 rounded-xl border border-sky-200 bg-sky-50 p-5"
                            >
                                <h3
                                    class="font-semibold text-stokker-primary-dark"
                                >
                                    Sub-taski eripära
                                </h3>
                                <ul
                                    class="mt-3 space-y-2 leading-7 text-slate-700"
                                >
                                    <li class={bulletClass}>
                                        Otsene parent on Story, Task või Bug,
                                        mitte Epic.
                                    </li>
                                    <li class={bulletClass}>
                                        Sub-task järgib põhitöö sprinti ja peab
                                        valmima koos põhitööga samas sprindis.
                                    </li>
                                    <li class={bulletClass}>
                                        Iseseisva või eraldi sprinti vajava töö
                                        jaoks luuakse Story, Task või Bug.
                                    </li>
                                </ul>
                            </div>

                            <h3 class="mt-9 text-lg font-semibold">
                                Tööde staatused
                            </h3>
                            <div class="mt-4 space-y-3">
                                {#each statuses as status (status.type)}
                                    <div
                                        class="rounded-xl border border-slate-200 px-4 py-4 sm:grid sm:grid-cols-[13rem_1fr] sm:gap-4"
                                    >
                                        <p class="font-semibold text-slate-900">
                                            {status.type}
                                        </p>
                                        <p
                                            class="mt-2 text-sm leading-6 text-slate-600 sm:mt-0"
                                        >
                                            {status.flow}
                                        </p>
                                    </div>
                                {/each}
                            </div>
                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <div
                                    class="flex gap-3 rounded-xl bg-amber-50 p-4 text-amber-950"
                                >
                                    <CirclePause
                                        class="mt-0.5 size-5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    <p class="text-sm leading-6">
                                        <strong>On Hold:</strong> ajutiselt peatatud;
                                        juurde märgitakse põhjus ja järgmine tegevus.
                                        Töö ei ole valmis.
                                    </p>
                                </div>
                                <div
                                    class="flex gap-3 rounded-xl bg-rose-50 p-4 text-rose-950"
                                >
                                    <CirclePause
                                        class="mt-0.5 size-5 shrink-0"
                                        aria-hidden="true"
                                    />
                                    <p class="text-sm leading-6">
                                        <strong>Cancelled:</strong> tööga ei jätkata;
                                        juurde märgitakse tühistamise põhjus. Töö
                                        ei ole valmis.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </details>

                    <details
                        id="portfolio"
                        class="group scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm open:shadow-md"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-4 px-5 py-5 marker:hidden sm:px-7"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-stokker-primary-soft font-semibold text-stokker-primary-dark"
                                >04</span
                            >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-xl font-semibold sm:text-2xl"
                                    >IT Portfolio Category ja Epicu protsess</span
                                >
                                <span
                                    class="mt-1 block text-sm leading-5 text-slate-500"
                                    >Algatuse suurus, otsustajad ja kinnitamine</span
                                >
                            </span>
                            <ChevronDown
                                class="size-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="border-t border-slate-100 px-5 py-6 sm:px-7 sm:py-8"
                        >
                            <p class="leading-7 text-slate-700">
                                Töö tüüp näitab, <strong>mida tehakse</strong>.
                                IT Portfolio Category näitab,
                                <strong
                                    >kui suure algatusega on tegemist ja kes
                                    selle kinnitab</strong
                                >. Kui töömaht, maksumus või mõju viitavad
                                erinevale tasemele, kasutatakse kõrgemat
                                põhjendatud kategooriat.
                            </p>

                            <div
                                class="mt-6 overflow-x-auto rounded-xl border border-slate-200"
                            >
                                <table
                                    class="w-full min-w-170 text-left text-sm"
                                >
                                    <thead class="bg-slate-50 text-slate-600">
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
                                        {#each portfolioCategories as item (item.category)}
                                            <tr>
                                                <td
                                                    class="px-4 py-3 font-semibold text-slate-900"
                                                    >{item.category}</td
                                                >
                                                <td
                                                    class="px-4 py-3 leading-6 text-slate-600"
                                                    >{item.size}</td
                                                >
                                                <td
                                                    class="px-4 py-3 leading-6 text-slate-600"
                                                    >{item.approval}</td
                                                >
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                                <div class="rounded-xl bg-slate-50 p-5">
                                    <div class="flex items-center gap-3">
                                        <ListChecks
                                            class="size-5 text-stokker-primary"
                                            aria-hidden="true"
                                        />
                                        <h3 class="font-semibold">
                                            Epicu eelanalüüs ja tööplaan
                                        </h3>
                                    </div>
                                    <ul
                                        class="mt-4 space-y-3 leading-7 text-slate-700"
                                    >
                                        <li class={bulletClass}>
                                            Kinnitamist vajavat Epicut ennast ei
                                            lisata eelanalüüsiks sprinti.
                                        </li>
                                        <li class={bulletClass}>
                                            Eelanalüüsiks luuakse Epicu alla
                                            Task, mis täpsustab eesmärgi,
                                            skoobi, sõltuvused, riskid ja
                                            võimalikud arendustööd.
                                        </li>
                                        <li class={bulletClass}>
                                            Võimalikud tööd lisatakse Epicu
                                            kirjelduse jaotisesse
                                            <code class="font-semibold"
                                                >## Võimalikud arendustööd</code
                                            >
                                            kujul
                                            <code class="font-semibold"
                                                >- **Töö tüüp - Component:
                                                Lühike kirjeldus** (N MD)</code
                                            >.
                                        </li>
                                        <li class={bulletClass}>
                                            See on esialgne tööplaan, mitte Jira
                                            backlog. Tööd luuakse pärast
                                            IT-komitee ülevaatust ja vajalikku
                                            kinnitust.
                                        </li>
                                    </ul>
                                </div>

                                <div
                                    class="rounded-xl bg-stokker-primary-50 p-5"
                                >
                                    <div class="flex items-center gap-3">
                                        <Route
                                            class="size-5 text-stokker-primary"
                                            aria-hidden="true"
                                        />
                                        <h3 class="font-semibold">
                                            Epicu kinnitamine ja arendusvoog
                                        </h3>
                                    </div>
                                    <p
                                        class="mt-4 rounded-lg bg-white p-3 text-sm leading-6 font-semibold text-stokker-primary-dark"
                                    >
                                        In Analysis → Waiting for Approval →
                                        Approved → Lahendamisel → Ready for
                                        Release → Tehtud
                                    </p>
                                    <ul
                                        class="mt-4 space-y-3 leading-7 text-slate-700"
                                    >
                                        <li class={bulletClass}>
                                            Otsuseks on vaja eesmärki, kasu,
                                            ärivastutajat, komponenti, mahtu,
                                            kulu ja eeldatavat valmimisaega.
                                        </li>
                                        <li class={bulletClass}>
                                            Kinnitamata IT Improvement või IT
                                            Project algatust sprinti ei võeta.
                                        </li>
                                        <li class={bulletClass}>
                                            Tagasi lükatud Epic liigub Cancelled
                                            olekusse ja Jira Resolution on Won't
                                            do.
                                        </li>
                                        <li class={bulletClass}>
                                            Epicu kategooria pärivad Storyd,
                                            Taskid ja Bugid; Sub-task pärib
                                            põhitöölt. Grouping'u all
                                            liigitatakse tööd eraldi.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </details>

                    <details
                        id="scrum"
                        class="group scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm open:shadow-md"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-4 px-5 py-5 marker:hidden sm:px-7"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-stokker-primary-soft font-semibold text-stokker-primary-dark"
                                >05</span
                            >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-xl font-semibold sm:text-2xl"
                                    >Scrum: töölaud ja sprindi rütm</span
                                >
                                <span
                                    class="mt-1 block text-sm leading-5 text-slate-500"
                                    >Töövaated, kohtumised ja nädala lõpetamine</span
                                >
                            </span>
                            <ChevronDown
                                class="size-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="border-t border-slate-100 px-5 py-6 sm:px-7 sm:py-8"
                        >
                            <div
                                class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                            >
                                {#each [{ label: 'Aktiivse sprindi töölaud', note: 'Igapäevane töö ja Stand-up', href: 'https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45' }, { label: 'Backlog', note: 'Täpsustamine ja planeerimine', href: 'https://stokker-team-ojuicoeqcvdn.atlassian.net/jira/people/712020:f3032e30-90f2-497b-81ea-7be97174f69d/boards/45/backlog' }, { label: 'Jira DEV space', note: 'Kõik arendustööd', href: 'https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV' }, { label: 'Täielik juhend', note: 'Detailid SharePointis', href: 'https://stokker365.sharepoint.com/sites/ITTeam/SitePages/IT-tiimi-uue-t%C3%B6%C3%B6korralduse-kiir%C3%BClevaade.aspx' }] as workView (workView.href)}
                                    <a
                                        href={workView.href}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group/link rounded-xl border border-slate-200 p-4 transition-colors hover:border-stokker-primary/40 hover:bg-stokker-primary-50"
                                    >
                                        <span
                                            class="flex items-center justify-between gap-3"
                                        >
                                            <span class="font-semibold"
                                                >{workView.label}</span
                                            >
                                            <ExternalLink
                                                class="size-4 shrink-0 text-stokker-primary"
                                                aria-hidden="true"
                                            />
                                        </span>
                                        <span
                                            class="mt-1 block text-sm text-slate-500"
                                            >{workView.note}</span
                                        >
                                    </a>
                                {/each}
                            </div>

                            <div class="mt-9 flex items-center gap-3">
                                <CalendarDays
                                    class="size-5 text-stokker-primary"
                                    aria-hidden="true"
                                />
                                <h3 class="text-lg font-semibold">
                                    Kohtumiste rütm
                                </h3>
                            </div>
                            <div
                                class="mt-4 overflow-x-auto rounded-xl border border-slate-200"
                            >
                                <table
                                    class="w-full min-w-170 text-left text-sm"
                                >
                                    <thead class="bg-slate-50 text-slate-600">
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
                                        {#each meetings as meeting (meeting.event)}
                                            <tr>
                                                <td
                                                    class="px-4 py-3 font-semibold text-slate-900"
                                                    >{meeting.event}</td
                                                >
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-slate-600"
                                                    >{meeting.time}</td
                                                >
                                                <td
                                                    class="px-4 py-3 leading-6 text-slate-600"
                                                    >{meeting.purpose}</td
                                                >
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>

                            <h3 class="mt-9 text-lg font-semibold">
                                Sprindi alustamine ja lõpetamine
                            </h3>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                {#each [{ label: 'Ettevalmistus', text: 'Product Owner järjestab Ready for Development tööd. Tiim kontrollib saadavust, sõltuvusi ja eelmise sprindi lõpetamata töid.' }, { label: 'Sprint Planning', text: 'Lepitakse kokku Sprint Goal ja valitakse realistlik töömaht. Tööd lisatakse uude sprinti olekuga Vaja teha ning sprint käivitatakse Jiras.' }, { label: 'Sprint Goal', text: '1–3 lühikest tulemust, mida sprint peab saavutama. See ei ole Jira tööde loetelu.' }, { label: 'Lõpetamine', text: 'Review’l vaadatakse tulemus üle. Tehtud on ainult kvaliteedinõuetele vastav töö. Retrospective’il lepitakse kokku vähemalt üks parandus.' }, { label: 'Lõpetamata töö', text: 'Uuendatakse staatus, takistuse põhjus, töölogid ja järelejäänud maht. Product Owner ja tiim otsustavad backlogi või järgmise sprindi.' }] as sprintStep (sprintStep.label)}
                                    <div class="rounded-xl bg-slate-50 p-5">
                                        <p
                                            class="font-semibold text-stokker-primary-dark"
                                        >
                                            {sprintStep.label}
                                        </p>
                                        <p
                                            class="mt-2 text-sm leading-6 text-slate-600"
                                        >
                                            {sprintStep.text}
                                        </p>
                                    </div>
                                {/each}
                            </div>
                        </div>
                    </details>

                    <details
                        id="kokkulepped"
                        class="group scroll-mt-28 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm open:shadow-md"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center gap-4 px-5 py-5 marker:hidden sm:px-7"
                        >
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-stokker-primary-soft font-semibold text-stokker-primary-dark"
                                >06</span
                            >
                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-xl font-semibold sm:text-2xl"
                                    >Igapäevased kokkulepped</span
                                >
                                <span
                                    class="mt-1 block text-sm leading-5 text-slate-500"
                                    >Vastutus, töö tegemine, ajalogimine ja
                                    hinnangud</span
                                >
                            </span>
                            <ChevronDown
                                class="size-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                aria-hidden="true"
                            />
                        </summary>
                        <div
                            class="border-t border-slate-100 px-5 py-6 sm:px-7 sm:py-8"
                        >
                            <div class="flex items-center gap-3">
                                <Users
                                    class="size-5 text-stokker-primary"
                                    aria-hidden="true"
                                />
                                <h3 class="text-lg font-semibold">Vastutus</h3>
                            </div>
                            <div class="mt-4 grid gap-4 lg:grid-cols-3">
                                {#each roles as item (item.role)}
                                    <div
                                        class="rounded-xl border border-slate-200 p-5"
                                    >
                                        <p
                                            class="font-semibold text-stokker-primary-dark"
                                        >
                                            {item.role}
                                        </p>
                                        <p
                                            class="mt-2 text-sm leading-6 text-slate-600"
                                        >
                                            {item.responsibility}
                                        </p>
                                    </div>
                                {/each}
                            </div>

                            <div
                                class="mt-8 rounded-2xl bg-slate-900 p-6 text-white sm:p-7"
                            >
                                <h3 class="text-lg font-semibold">
                                    Töö tegemine ja ajalogimine
                                </h3>
                                <ul class="mt-5 grid gap-4 lg:grid-cols-2">
                                    {#each ['Takistuse korral märgitakse selle põhjus ja järgmine tegevus. Jira tööga seotakse vajalik branch, commit’id ja Pull Request.', 'Tegelik aeg logitakse teostustööle ligikaudu täistundides, näiteks 1h, 2h või 5h. Kuu töölogid peavad olema korras kuu lõpuks.', 'Epicule teostustöödele logitud aega uuesti ei lisata.', 'Hinnang sisestatakse Story Pointsi 0,5 MD sammuga: 0,5 SP = 0,5 MD = 2,5 h ja 1 SP = 1 MD = 5 h.', 'Ühe tunni töö hinnang võib olla 0,5 MD, kuid tegelik töölogi 1h.', 'Hinnanguid ja tööloge kasutatakse sprintide planeerimiseks, mitte arendaja tulemuslikkuse mõõtmiseks.'] as agreement (agreement)}
                                        <li
                                            class="flex gap-3 text-sm leading-6 text-slate-200"
                                        >
                                            <CircleCheckBig
                                                class="mt-0.5 size-5 shrink-0 text-emerald-400"
                                                aria-hidden="true"
                                            />
                                            <span>{agreement}</span>
                                        </li>
                                    {/each}
                                </ul>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </main>
    </div>
</SiteLayout>
