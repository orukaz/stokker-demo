<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import Menu from 'lucide-svelte/icons/menu';
    import Search from 'lucide-svelte/icons/search';
    import ShoppingCart from 'lucide-svelte/icons/shopping-cart';
    import UserRound from 'lucide-svelte/icons/user-round';
    import { onMount, type Snippet } from 'svelte';
    import FavoritesHeaderButton from '@/components/site/FavoritesHeaderButton.svelte';
    import { Input } from '@/components/ui/input';
    import stokkerFooterLogo from '../../images/stokker-logo-footer.svg';
    import stokkerLogo from '../../images/stokker-logo.svg';
    import facebookIcon from '../../images/stokker-social-facebook.svg';
    import instagramIcon from '../../images/stokker-social-instagram.svg';
    import linkedinIcon from '../../images/stokker-social-linkedin.svg';
    import youtubeIcon from '../../images/stokker-social-youtube.svg';
    import { cn, toUrl } from '@/lib/utils';
    import { home, login } from '@/routes';
    import { index as productsIndex } from '@/routes/products';
    import { edit as profileEdit } from '@/routes/profile';

    let {
        children,
        filters,
        pageHeader,
        showFilters = false,
    }: {
        children?: Snippet;
        filters?: Snippet;
        pageHeader?: Snippet;
        showFilters?: boolean;
    } = $props();

    let favoritesCount = $state(Number(page.props.favoritesCount ?? 0));

    const isAuthenticated = $derived(Boolean(page.props.auth.user));
    const accountRoute = $derived(isAuthenticated ? profileEdit() : login());
    const accountLabel = $derived(isAuthenticated ? 'Minu konto' : 'Sisenen');
    const disabledLinkClass =
        'inline-flex h-full cursor-not-allowed items-center px-4 py-2 text-white';
    const topLinkClass =
        'inline-flex h-full items-center bg-white px-4 py-2 font-bold text-stokker-primary transition-colors hover:text-stokker-primary';
    const footerLinkClass = 'cursor-not-allowed text-left';
    const footerIconLinkClass = 'cursor-not-allowed text-xl text-gray-400';

    $effect(() => {
        favoritesCount = Number(page.props.favoritesCount ?? 0);
    });

    onMount(() => {
        const handleFavoritesCountChange = (event: Event) => {
            const detail = (
                event as CustomEvent<{ count?: number; delta?: number }>
            ).detail;

            if (typeof detail?.count === 'number') {
                favoritesCount = Math.max(0, detail.count);

                return;
            }

            const delta = Number(detail?.delta ?? 0);

            favoritesCount = Math.max(0, favoritesCount + delta);
        };

        window.addEventListener(
            'stokker:favorites-count-change',
            handleFavoritesCountChange,
        );

        return () => {
            window.removeEventListener(
                'stokker:favorites-count-change',
                handleFavoritesCountChange,
            );
        };
    });
</script>

<svelte:head>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link
        href="https://fonts.googleapis.com/css2?family=Teko&display=swap"
        rel="stylesheet"
    />
</svelte:head>

<div class="site-layout flex min-h-screen flex-col bg-white text-black">
    <header class="relative z-40 bg-white will-change-transform">
        <div class="hidden bg-stokker-primary text-white lg:block">
            <div class="px-4 lg:px-8">
                <div class="mx-auto xl:container">
                    <nav class="flex justify-between">
                        <ul class="flex">
                            <li>
                                <Link
                                    href={productsIndex()}
                                    class={topLinkClass}
                                    prefetch
                                >
                                    E-pood
                                </Link>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    class={disabledLinkClass}
                                    disabled
                                >
                                    Põllumajandus
                                </button>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    class={disabledLinkClass}
                                    disabled
                                >
                                    Ehitus
                                </button>
                            </li>
                            <li>
                                <a
                                    href="https://github.com/orukaz/stokker-demo"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex h-full items-center px-4 py-2 text-sky-200 transition-colors hover:text-sky-100"
                                >
                                    GitHub
                                </a>
                            </li>
                        </ul>
                        <ul class="flex">
                            {#each ['Meist', 'Blogi', 'Kontaktid', 'Iseteenindus'] as item}
                                <li>
                                    <button
                                        type="button"
                                        class={disabledLinkClass}
                                        disabled
                                    >
                                        {item}
                                    </button>
                                </li>
                            {/each}
                            <li class="hidden lg:list-item">
                                <button
                                    type="button"
                                    class="flex h-full cursor-not-allowed items-center gap-2 bg-stokker-primary px-4 py-2 text-white"
                                    disabled
                                >
                                    <span>Eesti</span>
                                    <span class="size-1 rounded-full bg-white"
                                    ></span>
                                    <span>KM-ga</span>
                                    <ChevronDown class="size-6" />
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <div class="bg-white">
            <div class="px-4 lg:px-8">
                <div
                    class="mx-auto flex items-center justify-between py-4 xl:container"
                >
                    <Link href={home()} class="flex min-w-30 items-center">
                        <img
                            src={stokkerLogo}
                            alt="Stokker"
                            class="h-10 w-43.5 max-w-full min-w-30"
                        />
                    </Link>

                    <div
                        class="hidden flex-1 items-center gap-2 lg:mx-4 lg:flex"
                    >
                        <div
                            class="flex justify-center rounded-lg hover:bg-gray-200"
                        >
                            <button
                                type="button"
                                class="inline-flex cursor-not-allowed items-center gap-1 rounded-lg px-4 py-2 text-stokker-header-action disabled:opacity-100"
                                disabled
                            >
                                <span class="text-black">Tootekategooriad</span>
                                <ChevronDown class="size-6" strokeWidth={2} />
                            </button>
                        </div>

                        <div class="flex-1 p-2">
                            <div
                                class="relative flex w-full items-center overflow-hidden rounded-lg ring-1 ring-stokker-primary transition-all focus-within:ring-2 focus-within:ring-blue-400"
                            >
                                <Search
                                    class="pointer-events-none absolute left-4 z-10 size-6 text-stokker-primary"
                                    strokeWidth={1.5}
                                />
                                <Input
                                    value=""
                                    class="h-auto w-full min-w-40 cursor-not-allowed border-none bg-transparent py-2 pr-4 pl-12 text-base shadow-none outline-none placeholder:text-gray-500 focus-visible:ring-0 focus-visible:ring-offset-0"
                                    placeholder="Otsi tooteid"
                                    autocomplete="off"
                                    type="search"
                                    enterkeyhint="search"
                                    list="search-list"
                                    readonly
                                    tabindex={-1}
                                />
                                <datalist id="search-list"></datalist>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="cursor-not-allowed rounded-md p-2 px-4 text-center hover:bg-gray-200"
                            disabled
                        >
                            Brändid
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <FavoritesHeaderButton count={favoritesCount} />

                        <button
                            type="button"
                            title="Ostukorv"
                            class="inline-flex cursor-not-allowed items-center gap-1 rounded-md px-2 py-3 text-stokker-primary transition-colors hover:bg-gray-100"
                            disabled
                        >
                            <span class="relative inline-flex">
                                <ShoppingCart
                                    class="size-6"
                                    strokeWidth={1.5}
                                />
                            </span>
                        </button>

                        <button
                            type="button"
                            aria-label="Otsing"
                            class="inline-flex cursor-not-allowed items-center gap-1 rounded-md px-2 py-3 text-stokker-primary transition-colors hover:bg-gray-100 lg:hidden"
                            disabled
                        >
                            <span class="relative inline-flex">
                                <Search class="size-6" strokeWidth={1.5} />
                            </span>
                        </button>

                        <button
                            type="button"
                            aria-label="Menüü"
                            class="inline-flex cursor-not-allowed items-center gap-1 rounded-md px-2 py-3 text-stokker-primary transition-colors hover:bg-gray-100 lg:hidden"
                            disabled
                        >
                            <span class="relative inline-flex">
                                <Menu class="size-6" strokeWidth={1.5} />
                            </span>
                        </button>

                        <div class="z-31 ml-2 hidden items-center lg:flex">
                            <Link
                                href={toUrl(accountRoute)}
                                class="inline-flex items-center gap-1 rounded-lg bg-stokker-primary px-4 py-2 text-white transition-colors hover:bg-stokker-primary-dark"
                            >
                                <span>{accountLabel}</span>
                                {#if isAuthenticated}
                                    <UserRound class="size-[18px]" />
                                {:else}
                                    <ArrowRight class="size-[18px]" />
                                {/if}
                            </Link>
                        </div>

                        <Link
                            href={toUrl(accountRoute)}
                            class="inline-flex items-center gap-1 rounded-md px-2 py-3 text-stokker-primary transition-colors hover:bg-gray-100 lg:hidden"
                            aria-label={accountLabel}
                        >
                            <UserRound class="size-5" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="grow">
        {#if pageHeader}
            {@render pageHeader()}
        {/if}

        {#if showFilters}
            <div class="px-4 lg:px-8">
                <div class="mx-auto pt-4 pb-16 xl:container">
                    <div
                        class="filter-container grid gap-8 md:grid-cols-3 lg:grid-cols-4"
                    >
                        <aside
                            class={cn(
                                'hidden self-start overflow-y-auto px-1 md:sticky md:top-[var(--sticky-navbar-height,0px)] md:block md:max-h-[calc(100vh-var(--sticky-navbar-height,0px))] md:-mx-1',
                                !filters && 'pointer-events-none opacity-0',
                            )}
                        >
                            {#if filters}
                                {@render filters()}
                            {/if}
                        </aside>

                        <section
                            class="flex min-w-0 flex-col gap-3 md:col-span-2 lg:col-span-3"
                        >
                            {#if children}
                                {@render children()}
                            {/if}
                        </section>
                    </div>
                </div>
            </div>
        {:else}
            <div class="px-4 lg:px-8">
                <div class="mx-auto py-6 xl:container">
                    {#if children}
                        {@render children()}
                    {/if}
                </div>
            </div>
        {/if}
    </main>

    <footer class="border-t border-gray-300">
        <div class="px-4 lg:px-8">
            <div
                class="mx-auto flex flex-col pt-6 md:pt-11 lg:gap-6 xl:container"
            >
                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-5"
                >
                    <div class="flex flex-col gap-1">
                        <span>Klienditeenindus</span>
                        <button
                            type="button"
                            class="cursor-not-allowed text-left"
                            disabled
                        >
                            (+372) 655 55 11
                        </button>
                        <button
                            type="button"
                            class="cursor-not-allowed text-left"
                            disabled
                        >
                            klient@stokker.com
                        </button>
                    </div>

                    <div class="flex flex-col gap-1">
                        <button type="button" class={footerLinkClass} disabled>
                            Üldtingimused&nbsp;»
                        </button>
                        <button type="button" class={footerLinkClass} disabled>
                            Privaatsuspoliitika&nbsp;»
                        </button>
                        <button type="button" class={footerLinkClass} disabled>
                            StokkerPRO boonus&nbsp;»
                        </button>
                    </div>

                    <div class="flex flex-col gap-1">
                        <button type="button" class={footerLinkClass} disabled>
                            Tule tööle&nbsp;»
                        </button>
                        <button type="button" class={footerLinkClass} disabled>
                            Abikeskus&nbsp;»
                        </button>
                    </div>

                    <p
                        class="text-stokker-primary sm:col-span-3 lg:col-span-2 lg:w-10/12"
                    >
                        Stokker on Baltikumi juhtiv professionaalsete
                        tööriistade ja masinate müügi- ja teenindusettevõte.
                    </p>
                </div>

                <div class="flex flex-col max-lg:gap-4 lg:grid lg:grid-cols-3">
                    <div></div>
                    <button
                        type="button"
                        class="flex cursor-not-allowed justify-center max-lg:order-2"
                        disabled
                    >
                        <img
                            src={stokkerFooterLogo}
                            alt="Stokker"
                            class="h-20 w-auto"
                        />
                    </button>
                    <div
                        class="flex items-center justify-center gap-8 max-lg:order-1 lg:justify-start"
                    >
                        <button
                            type="button"
                            aria-label="Instagram"
                            class={footerIconLinkClass}
                            disabled
                        >
                            <img
                                src={instagramIcon}
                                alt=""
                                class="size-[23px]"
                            />
                        </button>
                        <button
                            type="button"
                            aria-label="Facebook"
                            class={footerIconLinkClass}
                            disabled
                        >
                            <img
                                src={facebookIcon}
                                alt=""
                                class="size-[23px]"
                            />
                        </button>
                        <button
                            type="button"
                            aria-label="YouTube"
                            class={footerIconLinkClass}
                            disabled
                        >
                            <img src={youtubeIcon} alt="" class="size-[23px]" />
                        </button>
                        <button
                            type="button"
                            aria-label="LinkedIn"
                            class={footerIconLinkClass}
                            disabled
                        >
                            <img
                                src={linkedinIcon}
                                alt=""
                                class="size-[23px]"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
