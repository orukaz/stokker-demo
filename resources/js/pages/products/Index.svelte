<script lang="ts">
    import { InfiniteScroll, Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import ProductCard from '@/components/site/ProductCard.svelte';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import SiteLayout from '@/layouts/SiteLayout.svelte';
    import { home } from '@/routes';
    import { index as productsIndex } from '@/routes/products';
    import type { InfiniteScrollCollection, ProductSummary } from '@/types';

    const filterInputClass =
        'h-9 w-20 grow rounded-md border border-stokker-secondary-400 bg-white px-2 py-1 text-base shadow-none focus-visible:ring-1 focus-visible:ring-stokker-primary focus-visible:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-100';

    let {
        products,
    }: {
        products: InfiniteScrollCollection<ProductSummary>;
    } = $props();
</script>

<AppHead title="Mootorsaed">
    <meta
        name="description"
        content="Stokkeri mootorsaagide tootenimekiri koos lemmikutesse lisamise võimalusega."
    />
</AppHead>

<SiteLayout showFilters>
    {#snippet pageHeader()}
        <div class="bg-stokker-secondary px-4 lg:px-8">
            <div class="mx-auto space-y-4 py-4 xl:container">
                <nav class="overflow-hidden" aria-label="Breadcrumb">
                    <ol class="flex items-center text-sm sm:hidden">
                        <li
                            class="flex min-w-0 items-center gap-2 overflow-hidden font-bold"
                        >
                            <span aria-hidden="true">&lt;</span>
                            <span
                                class="truncate transition-colors duration-150"
                            >
                                Metsatehnika
                            </span>
                        </li>
                    </ol>

                    <ol class="hidden min-w-0 items-center text-sm sm:flex">
                        <li class="flex min-w-0 items-center">
                            <Link
                                href={home()}
                                class="truncate transition-colors duration-150 hover:text-gray-500"
                            >
                                Avaleht
                            </Link>
                            <span
                                class="mx-2 size-1 shrink-0 rounded-full bg-stokker-primary"
                            ></span>
                        </li>
                        <li class="flex min-w-0 items-center">
                            <span
                                class="truncate transition-colors duration-150"
                            >
                                Metsatehnika
                            </span>
                            <span
                                class="mx-2 size-1 shrink-0 rounded-full bg-stokker-primary"
                            ></span>
                        </li>
                        <li class="flex min-w-0 items-center overflow-hidden">
                            <Link
                                href={productsIndex()}
                                class="truncate font-bold transition-colors duration-150 hover:text-gray-500"
                            >
                                Mootorsaed
                            </Link>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="bg-stokker-secondary px-4 lg:px-8">
            <div class="mx-auto flex flex-col gap-4 py-4 xl:container">
                <div class="flex flex-col gap-2">
                    <h1>Mootorsaed</h1>
                    <p class="text-base leading-6 font-normal text-black">
                        Kõik vajalik metsatöödeks. Kogenud oma ala asjatundjad
                        oskavad soovitada parimat sobivat lahendust. Nõustame,
                        finantseerime, hooldame! Osta mugavalt e-poest.
                    </p>
                </div>
            </div>
        </div>
    {/snippet}

    {#snippet filters()}
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <span class="m-0 font-semibold">Hinnavahemik</span>
                <div class="flex items-center gap-2">
                    <label for="price-min" class="sr-only">Miinimumhind</label>
                    <Input
                        id="price-min"
                        type="number"
                        min="0"
                        class={filterInputClass}
                        disabled
                    />
                    <span>-</span>
                    <label for="price-max" class="sr-only">Maksimumhind</label>
                    <Input
                        id="price-max"
                        type="number"
                        min="0"
                        class={filterInputClass}
                        disabled
                    />
                </div>
            </div>

            <hr class="border-gray-300" />

            <div class="flex flex-col gap-2">
                <span class="m-0 font-semibold">Toote alamgrupp</span>
                <div class="flex flex-col gap-1">
                    {#each [['Akuga kettsaed', 15], ['Bensiinimootoriga kettsaed', 7], ['Elektrilised kettsaed', 1]] as [label, count]}
                        <label
                            class="flex cursor-not-allowed items-center justify-between pr-3 pl-1"
                        >
                            <span class="flex items-center gap-2">
                                <Checkbox
                                    class="cursor-not-allowed rounded border-gray-300 data-[state=checked]:bg-stokker-primary data-[state=checked]:border-stokker-primary disabled:opacity-100"
                                    disabled
                                />
                                <span>{label}</span>
                            </span>
                            <span>{count}</span>
                        </label>
                    {/each}
                </div>
            </div>

            <hr class="border-gray-300" />

            <div class="flex flex-col gap-2">
                <span class="m-0 font-semibold">Kaubamärgid</span>
                <div class="flex flex-col gap-1">
                    {#each [['Cramer', 5], ['Echo', 8], ['Metabo', 2], ['Milwaukee', 3], ['Scheppach', 5]] as [label, count]}
                        <label
                            class="flex cursor-not-allowed items-center justify-between pr-3 pl-1"
                        >
                            <span class="flex items-center gap-2">
                                <Checkbox
                                    class="cursor-not-allowed rounded border-gray-300 data-[state=checked]:bg-stokker-primary data-[state=checked]:border-stokker-primary disabled:opacity-100"
                                    disabled
                                />
                                <span>{label}</span>
                            </span>
                            <span>{count}</span>
                        </label>
                    {/each}
                </div>
            </div>

            <hr class="border-gray-300" />
        </div>
    {/snippet}

    <div
        class="sticky top-[var(--sticky-navbar-height,0px)] z-20 bg-white pt-2"
    >
        <div class="flex flex-wrap items-center justify-between gap-4">
            <label for="change-order" class="sr-only">Muuda järjestust</label>
            <div class="relative w-full max-w-fit">
                <select
                    id="change-order"
                    name="order"
                    class="w-full cursor-not-allowed appearance-none truncate rounded-md border border-stokker-secondary-400 bg-white py-2 pr-10 pl-3 text-base disabled:opacity-100"
                    disabled
                >
                    <option selected>Populaarsemad eespool</option>
                    <option>Odavamad eespool</option>
                </select>
                <ChevronDown
                    class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-gray-500"
                    strokeWidth={1.8}
                />
            </div>

            <div class="md:flex-1"></div>

            <div class="hidden gap-4 md:flex">
                {#each ['Laos', 'Kampaania'] as label}
                    <div class="relative flex items-center gap-2">
                        <span class="text-sm">{label}</span>
                        <span
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-not-allowed rounded-full bg-gray-300"
                            aria-hidden="true"
                        >
                            <span
                                class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow-sm"
                            ></span>
                        </span>
                    </div>
                {/each}
            </div>

            <button
                type="button"
                class="inline-flex cursor-not-allowed items-center rounded-lg bg-stokker-primary-dark px-4 py-2 text-white opacity-70 md:hidden"
                disabled
            >
                Filtrid
            </button>
        </div>

        <hr class="mt-3 border-gray-300" />
    </div>

    <InfiniteScroll data="products">
        <div
            class="products grid grid-flow-dense grid-cols-1 overflow-hidden border-gray-300 sm:grid-cols-2 lg:grid-cols-5"
        >
            {#each products.data as product (product.id)}
                <ProductCard {product} />
            {/each}
        </div>
    </InfiniteScroll>
</SiteLayout>
