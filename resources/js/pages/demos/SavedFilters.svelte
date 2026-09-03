<script lang="ts">
    import BookmarkCheck from 'lucide-svelte/icons/bookmark-check';
    import Check from 'lucide-svelte/icons/check';
    import ChevronDown from 'lucide-svelte/icons/chevron-down';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Plus from 'lucide-svelte/icons/plus';
    import Search from 'lucide-svelte/icons/search';
    import Star from 'lucide-svelte/icons/star';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import X from 'lucide-svelte/icons/x';
    import { onMount, untrack } from 'svelte';
    import { toast } from 'svelte-sonner';
    import {
        destroy,
        makeDefault,
        store,
        update,
    } from '@/actions/App/Http/Controllers/SavedFilterController';
    import AppHead from '@/components/AppHead.svelte';
    import JiraIssueBadge from '@/components/JiraIssueBadge.svelte';
    import SourceCodeFiles from '@/components/SourceCodeFiles.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import {
        Dialog,
        DialogContent,
        DialogFooter,
        DialogTitle,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Toaster } from '@/components/ui/sonner';
    import { forceLightTheme } from '@/lib/theme.svelte';
    import type { OrderFilterCriteria, SavedFilter } from '@/types';

    type OrderStatus = 'new' | 'in_progress' | 'ready' | 'completed';

    type Order = {
        id: string;
        customer: string;
        branch: string;
        status: OrderStatus;
        assignee: string;
        createdAt: string;
        total: number;
    };

    type RouteDefinition = {
        url: string;
        method: string;
    };

    type SavedFilterResponse = {
        savedFilter: SavedFilter;
    };

    type ValidationErrors = Record<string, string[]>;

    class RequestError extends Error {
        errors: ValidationErrors;

        constructor(message: string, errors: ValidationErrors = {}) {
            super(message);
            this.errors = errors;
        }
    }

    const orders: Order[] = [
        {
            id: 'MT-10512',
            customer: 'Nordhaus OÜ',
            branch: 'Tallinn',
            status: 'in_progress',
            assignee: 'Mari Maasikas',
            createdAt: '2026-09-03',
            total: 1840.5,
        },
        {
            id: 'MT-10511',
            customer: 'Lõuna Ehitus AS',
            branch: 'Tartu',
            status: 'ready',
            assignee: 'Karl Kask',
            createdAt: '2026-09-03',
            total: 629.9,
        },
        {
            id: 'MT-10508',
            customer: 'Värvimeister OÜ',
            branch: 'Pärnu',
            status: 'new',
            assignee: 'Anna Tamm',
            createdAt: '2026-09-03',
            total: 312.0,
        },
        {
            id: 'MT-10497',
            customer: 'Tartu Hooldus OÜ',
            branch: 'Tartu',
            status: 'ready',
            assignee: 'Mari Maasikas',
            createdAt: '2026-09-02',
            total: 2440.75,
        },
        {
            id: 'MT-10491',
            customer: 'Rakvere Tehnika AS',
            branch: 'Rakvere',
            status: 'in_progress',
            assignee: 'Karl Kask',
            createdAt: '2026-09-02',
            total: 989.4,
        },
        {
            id: 'MT-10482',
            customer: 'Kodu & Aed OÜ',
            branch: 'Tallinn',
            status: 'completed',
            assignee: 'Anna Tamm',
            createdAt: '2026-09-01',
            total: 157.9,
        },
        {
            id: 'MT-10476',
            customer: 'Pärnu Partnerid OÜ',
            branch: 'Pärnu',
            status: 'in_progress',
            assignee: 'Mari Maasikas',
            createdAt: '2026-09-01',
            total: 3760.0,
        },
    ];

    const statusLabels: Record<OrderStatus, string> = {
        new: 'Uus',
        in_progress: 'Töös',
        ready: 'Valmis väljastamiseks',
        completed: 'Lõpetatud',
    };

    const statusClasses: Record<OrderStatus, string> = {
        new: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        in_progress: 'bg-amber-50 text-amber-800 ring-amber-600/20',
        ready: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        completed: 'bg-slate-100 text-slate-600 ring-slate-500/20',
    };

    const selectClass =
        'h-9 w-full appearance-none rounded-lg border border-slate-300 bg-white py-1 pr-9 pl-3 text-sm text-slate-900 shadow-sm outline-none transition-colors focus:border-stokker-primary focus:ring-2 focus:ring-stokker-primary/20';

    let { savedFilters: initialSavedFilters }: { savedFilters: SavedFilter[] } =
        $props();

    const startingSavedFilters = untrack(() =>
        $state.snapshot(initialSavedFilters),
    );
    const initialDefault = startingSavedFilters.find(
        (savedFilter) => savedFilter.isDefault,
    );

    let savedFilters = $state<SavedFilter[]>(
        sortSavedFilters(startingSavedFilters),
    );
    let filters = $state<OrderFilterCriteria>(
        cloneFilters(initialDefault?.filters ?? emptyFilters()),
    );
    let selectedFilterId = $state<number | null>(initialDefault?.id ?? null);
    let saveDialogOpen = $state(false);
    let renameDialogOpen = $state(false);
    let deleteDialogOpen = $state(false);
    let savedFiltersMenuOpen = $state(false);
    let pendingFilter = $state<SavedFilter | null>(null);
    let filterName = $state('');
    let saveAsDefault = $state(false);
    let nameError = $state('');
    let isSaving = $state(false);
    let isDeleting = $state(false);
    let pendingDefaultId = $state<number | null>(null);

    const selectedFilter = $derived(
        savedFilters.find(
            (savedFilter) => savedFilter.id === selectedFilterId,
        ) ?? null,
    );
    const hasUnsavedChanges = $derived(
        selectedFilter !== null &&
            !filtersMatch(filters, selectedFilter.filters),
    );
    const activeFilterCount = $derived(
        Object.values(filters).filter((value) => value !== '').length,
    );
    const filteredOrders = $derived.by(() => {
        const searchValue = filters.search.trim().toLocaleLowerCase('et');

        return orders.filter((order) => {
            const searchableText =
                `${order.id} ${order.customer}`.toLocaleLowerCase('et');

            return (
                (!searchValue || searchableText.includes(searchValue)) &&
                (!filters.status || order.status === filters.status) &&
                (!filters.branch || order.branch === filters.branch) &&
                (!filters.assignee || order.assignee === filters.assignee) &&
                (!filters.date_from || order.createdAt >= filters.date_from) &&
                (!filters.date_to || order.createdAt <= filters.date_to)
            );
        });
    });

    onMount(forceLightTheme);

    function emptyFilters(): OrderFilterCriteria {
        return {
            search: '',
            status: '',
            branch: '',
            assignee: '',
            date_from: '',
            date_to: '',
        };
    }

    function cloneFilters(criteria: OrderFilterCriteria): OrderFilterCriteria {
        return { ...emptyFilters(), ...criteria };
    }

    function filtersMatch(
        left: OrderFilterCriteria,
        right: OrderFilterCriteria,
    ): boolean {
        return (
            JSON.stringify(cloneFilters(left)) ===
            JSON.stringify(cloneFilters(right))
        );
    }

    function sortSavedFilters(filtersToSort: SavedFilter[]): SavedFilter[] {
        return [...filtersToSort].sort((left, right) => {
            if (left.isDefault !== right.isDefault) {
                return Number(right.isDefault) - Number(left.isDefault);
            }

            return right.updatedAt.localeCompare(left.updatedAt);
        });
    }

    function applySavedFilter(savedFilter: SavedFilter): void {
        filters = cloneFilters(savedFilter.filters);
        selectedFilterId = savedFilter.id;
        savedFiltersMenuOpen = false;
    }

    function clearFilters(): void {
        filters = emptyFilters();
        selectedFilterId = null;
    }

    function openSaveDialog(): void {
        savedFiltersMenuOpen = false;
        pendingFilter = null;
        filterName = `Minu filter ${savedFilters.length + 1}`;
        saveAsDefault = savedFilters.length === 0;
        nameError = '';
        saveDialogOpen = true;
    }

    function openRenameDialog(savedFilter: SavedFilter): void {
        savedFiltersMenuOpen = false;
        pendingFilter = savedFilter;
        filterName = savedFilter.name;
        nameError = '';
        renameDialogOpen = true;
    }

    function openDeleteDialog(savedFilter: SavedFilter): void {
        savedFiltersMenuOpen = false;
        pendingFilter = savedFilter;
        deleteDialogOpen = true;
    }

    function mergeSavedFilter(updatedFilter: SavedFilter): void {
        savedFilters = sortSavedFilters(
            savedFilters.map((savedFilter) =>
                savedFilter.id === updatedFilter.id
                    ? updatedFilter
                    : savedFilter,
            ),
        );
    }

    function csrfToken(): string {
        return (
            document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
                ?.content ?? ''
        );
    }

    async function requestJson<T>(
        route: RouteDefinition,
        body?: Record<string, unknown>,
    ): Promise<T> {
        const response = await fetch(route.url, {
            method: route.method.toUpperCase(),
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body ? JSON.stringify(body) : undefined,
        });

        if (!response.ok) {
            const payload = (await response.json().catch(() => ({}))) as {
                message?: string;
                errors?: ValidationErrors;
            };

            throw new RequestError(
                payload.message ?? 'Salvestamine ebaõnnestus.',
                payload.errors,
            );
        }

        return (
            response.status === 204 ? undefined : await response.json()
        ) as T;
    }

    async function saveNewFilter(event: SubmitEvent): Promise<void> {
        event.preventDefault();
        nameError = '';
        isSaving = true;

        try {
            const response = await requestJson<SavedFilterResponse>(store(), {
                view: 'orders',
                name: filterName.trim(),
                filters: cloneFilters(filters),
                is_default: saveAsDefault,
            });

            if (response.savedFilter.isDefault) {
                savedFilters = savedFilters.map((savedFilter) => ({
                    ...savedFilter,
                    isDefault: false,
                }));
            }

            savedFilters = sortSavedFilters([
                response.savedFilter,
                ...savedFilters,
            ]);
            selectedFilterId = response.savedFilter.id;
            saveDialogOpen = false;
            toast.success('Filter salvestatud');
        } catch (error) {
            if (error instanceof RequestError) {
                nameError = error.errors.name?.[0] ?? error.message;
            }
        } finally {
            isSaving = false;
        }
    }

    async function overwriteSelectedFilter(): Promise<void> {
        if (!selectedFilter) {
            return;
        }

        isSaving = true;

        try {
            const response = await requestJson<SavedFilterResponse>(
                update(selectedFilter.id),
                {
                    view: selectedFilter.view,
                    name: selectedFilter.name,
                    filters: cloneFilters(filters),
                },
            );

            mergeSavedFilter(response.savedFilter);
            savedFiltersMenuOpen = false;
            toast.success('Filtri muudatused salvestatud');
        } catch {
            toast.error('Filtri muudatusi ei saanud salvestada');
        } finally {
            isSaving = false;
        }
    }

    async function renameFilter(event: SubmitEvent): Promise<void> {
        event.preventDefault();

        if (!pendingFilter) {
            return;
        }

        nameError = '';
        isSaving = true;

        try {
            const response = await requestJson<SavedFilterResponse>(
                update(pendingFilter.id),
                {
                    view: pendingFilter.view,
                    name: filterName.trim(),
                    filters: cloneFilters(pendingFilter.filters),
                },
            );

            mergeSavedFilter(response.savedFilter);
            renameDialogOpen = false;
            toast.success('Filter ümber nimetatud');
        } catch (error) {
            if (error instanceof RequestError) {
                nameError = error.errors.name?.[0] ?? error.message;
            }
        } finally {
            isSaving = false;
        }
    }

    async function setDefault(savedFilter: SavedFilter): Promise<void> {
        if (savedFilter.isDefault || pendingDefaultId !== null) {
            return;
        }

        pendingDefaultId = savedFilter.id;

        try {
            const response = await requestJson<SavedFilterResponse>(
                makeDefault(savedFilter.id),
            );

            savedFilters = sortSavedFilters(
                savedFilters.map((currentFilter) => ({
                    ...currentFilter,
                    isDefault: currentFilter.id === response.savedFilter.id,
                })),
            );
            toast.success('Vaikefilter määratud');
        } catch {
            toast.error('Vaikefiltrit ei saanud muuta');
        } finally {
            pendingDefaultId = null;
        }
    }

    async function deleteFilter(): Promise<void> {
        if (!pendingFilter) {
            return;
        }

        const filterToDelete = pendingFilter;
        isDeleting = true;

        try {
            await requestJson<void>(destroy(filterToDelete.id));
            savedFilters = savedFilters.filter(
                (savedFilter) => savedFilter.id !== filterToDelete.id,
            );

            if (selectedFilterId === filterToDelete.id) {
                selectedFilterId = null;
            }

            deleteDialogOpen = false;
            pendingFilter = null;
            toast.success('Filter kustutatud');
        } catch {
            toast.error('Filtrit ei saanud kustutada');
        } finally {
            isDeleting = false;
        }
    }

    function filterSummary(savedFilter: SavedFilter): string {
        const parts: string[] = [];

        if (savedFilter.filters.status) {
            parts.push(
                statusLabels[savedFilter.filters.status as OrderStatus] ??
                    savedFilter.filters.status,
            );
        }

        if (savedFilter.filters.branch) {
            parts.push(savedFilter.filters.branch);
        }

        if (savedFilter.filters.assignee) {
            parts.push(savedFilter.filters.assignee);
        }

        if (savedFilter.filters.date_from || savedFilter.filters.date_to) {
            parts.push('Kuupäev');
        }

        if (savedFilter.filters.search) {
            parts.push(`“${savedFilter.filters.search}”`);
        }

        return parts.join(' · ') || 'Kõik tellimused';
    }

    function formatCurrency(amount: number): string {
        return new Intl.NumberFormat('et-EE', {
            style: 'currency',
            currency: 'EUR',
        }).format(amount);
    }

    function formatDate(value: string): string {
        return new Intl.DateTimeFormat('et-EE', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(new Date(`${value}T12:00:00`));
    }
</script>

<AppHead title="DEV-238 filtrite salvestamine">
    <meta
        name="description"
        content="Andmebaasi salvestatavate tabelifiltrite ja nende halduse demo."
    />
</AppHead>

<main
    class="min-h-screen bg-slate-50 px-4 py-8 text-slate-950 sm:px-6 lg:py-12"
>
    <div class="mx-auto flex max-w-7xl flex-col gap-5">
        <header
            class="flex flex-col gap-3 sm:flex-row-reverse sm:items-start sm:justify-between"
        >
            <a
                href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV-238"
                target="_blank"
                rel="noopener noreferrer"
                class="w-fit rounded-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stokker-primary"
                aria-label="Ava Jira ülesanne DEV-238 uuel vahelehel"
            >
                <JiraIssueBadge type="story" label="DEV-238 · Lugu" />
            </a>
            <h1 class="text-2xl font-semibold tracking-tight">Tellimused</h1>
        </header>

        <section
            class="relative rounded-xl border border-slate-200 bg-white"
            aria-label="POS tellimuste demo"
        >
            <div class="relative">
                <div class="min-w-0">
                    <Card class="gap-0 rounded-none border-0 py-0 shadow-none">
                        <CardHeader class="gap-3 border-b px-4 py-3 sm:px-5">
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <CardTitle class="text-base">
                                        Filtrid
                                    </CardTitle>
                                    {#if activeFilterCount > 0}
                                        <span class="text-xs text-slate-500">
                                            {activeFilterCount} aktiivset
                                        </span>
                                    {/if}
                                    {#if selectedFilter}
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 ring-1 ring-sky-600/15"
                                        >
                                            <BookmarkCheck class="size-3.5" />
                                            {selectedFilter.name}
                                        </span>
                                    {/if}
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-slate-600"
                                        disabled={activeFilterCount === 0}
                                        onclick={clearFilters}
                                    >
                                        <X class="size-4" />
                                        Tühjenda
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="icon-sm"
                                        class="relative"
                                        aria-label="Salvestatud filtrid"
                                        aria-expanded={savedFiltersMenuOpen}
                                        title="Salvestatud filtrid"
                                        onclick={() =>
                                            (savedFiltersMenuOpen =
                                                !savedFiltersMenuOpen)}
                                    >
                                        <BookmarkCheck class="size-4" />
                                        {#if savedFilters.length > 0}
                                            <span
                                                class="absolute -top-1.5 -right-1.5 inline-flex min-w-4 items-center justify-center rounded-full bg-slate-900 px-1 text-[10px] leading-4 font-semibold text-white"
                                            >
                                                {savedFilters.length}
                                            </span>
                                        {/if}
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent class="px-4 py-3 sm:px-5">
                            <div
                                class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
                            >
                                <div
                                    class="grid gap-1.5 sm:col-span-2 lg:col-span-3 xl:col-span-2"
                                >
                                    <Label for="order-search">Otsing</Label>
                                    <div class="relative">
                                        <Search
                                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400"
                                        />
                                        <Input
                                            id="order-search"
                                            type="search"
                                            bind:value={filters.search}
                                            class="border-slate-300 bg-white pl-9 focus-visible:ring-stokker-primary/30"
                                            placeholder="Tellimuse nr või klient"
                                        />
                                    </div>
                                </div>

                                <div class="grid gap-1.5">
                                    <Label for="status-filter">Olek</Label>
                                    <div class="relative">
                                        <select
                                            id="status-filter"
                                            bind:value={filters.status}
                                            class={selectClass}
                                        >
                                            <option value="">Kõik olekud</option
                                            >
                                            <option value="new">Uus</option>
                                            <option value="in_progress"
                                                >Töös</option
                                            >
                                            <option value="ready"
                                                >Valmis väljastamiseks</option
                                            >
                                            <option value="completed"
                                                >Lõpetatud</option
                                            >
                                        </select>
                                        <ChevronDown
                                            class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-slate-400"
                                        />
                                    </div>
                                </div>

                                <div class="grid gap-1.5">
                                    <Label for="branch-filter">Esindus</Label>
                                    <div class="relative">
                                        <select
                                            id="branch-filter"
                                            bind:value={filters.branch}
                                            class={selectClass}
                                        >
                                            <option value=""
                                                >Kõik esindused</option
                                            >
                                            <option>Tallinn</option>
                                            <option>Tartu</option>
                                            <option>Pärnu</option>
                                            <option>Rakvere</option>
                                        </select>
                                        <ChevronDown
                                            class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-slate-400"
                                        />
                                    </div>
                                </div>

                                <div class="grid gap-1.5">
                                    <Label for="assignee-filter"
                                        >Vastutaja</Label
                                    >
                                    <div class="relative">
                                        <select
                                            id="assignee-filter"
                                            bind:value={filters.assignee}
                                            class={selectClass}
                                        >
                                            <option value=""
                                                >Kõik vastutajad</option
                                            >
                                            <option>Mari Maasikas</option>
                                            <option>Karl Kask</option>
                                            <option>Anna Tamm</option>
                                        </select>
                                        <ChevronDown
                                            class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-slate-400"
                                        />
                                    </div>
                                </div>

                                <div class="grid gap-1.5">
                                    <Label for="date-from-filter">Alates</Label>
                                    <Input
                                        id="date-from-filter"
                                        type="date"
                                        bind:value={filters.date_from}
                                        max={filters.date_to || undefined}
                                        class="border-slate-300 bg-white focus-visible:ring-stokker-primary/30"
                                    />
                                </div>

                                <div class="grid gap-1.5">
                                    <Label for="date-to-filter">Kuni</Label>
                                    <Input
                                        id="date-to-filter"
                                        type="date"
                                        bind:value={filters.date_to}
                                        min={filters.date_from || undefined}
                                        class="border-slate-300 bg-white focus-visible:ring-stokker-primary/30"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card
                        class="gap-0 overflow-hidden rounded-none border-0 border-t py-0 shadow-none"
                    >
                        <div
                            class="flex items-center justify-between gap-4 border-b px-4 py-3 sm:px-5"
                        >
                            <div>
                                <h3
                                    class="text-base font-semibold text-slate-900"
                                >
                                    Tellimuste nimekiri
                                </h3>
                                <p
                                    class="text-sm text-slate-500"
                                    aria-live="polite"
                                >
                                    Kuvatud {filteredOrders.length} / {orders.length}
                                </p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table
                                class="w-full min-w-[48rem] text-left text-sm"
                            >
                                <thead
                                    class="bg-slate-50 text-xs font-semibold tracking-wide text-slate-500 uppercase"
                                >
                                    <tr>
                                        <th class="px-5 py-3">Tellimus</th>
                                        <th class="px-4 py-3">Klient</th>
                                        <th class="px-4 py-3">Esindus</th>
                                        <th class="px-4 py-3">Olek</th>
                                        <th class="px-4 py-3">Vastutaja</th>
                                        <th class="px-4 py-3">Loodud</th>
                                        <th class="px-5 py-3 text-right"
                                            >Summa</th
                                        >
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-slate-100 bg-white"
                                >
                                    {#each filteredOrders as order (order.id)}
                                        <tr
                                            class="transition-colors hover:bg-sky-50/40"
                                        >
                                            <td
                                                class="px-5 py-3.5 font-semibold text-stokker-primary"
                                            >
                                                {order.id}
                                            </td>
                                            <td
                                                class="px-4 py-3.5 font-medium text-slate-900"
                                            >
                                                {order.customer}
                                            </td>
                                            <td
                                                class="px-4 py-3.5 text-slate-600"
                                            >
                                                {order.branch}
                                            </td>
                                            <td class="px-4 py-3.5">
                                                <span
                                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {statusClasses[
                                                        order.status
                                                    ]}"
                                                >
                                                    {statusLabels[order.status]}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-3.5 text-slate-600"
                                            >
                                                {order.assignee}
                                            </td>
                                            <td
                                                class="px-4 py-3.5 whitespace-nowrap text-slate-600"
                                            >
                                                {formatDate(order.createdAt)}
                                            </td>
                                            <td
                                                class="px-5 py-3.5 text-right font-semibold whitespace-nowrap text-slate-900"
                                            >
                                                {formatCurrency(order.total)}
                                            </td>
                                        </tr>
                                    {:else}
                                        <tr>
                                            <td
                                                colspan="7"
                                                class="px-5 py-14 text-center"
                                            >
                                                <Search
                                                    class="mx-auto size-6 text-slate-300"
                                                />
                                                <p
                                                    class="mt-2 font-medium text-slate-700"
                                                >
                                                    Sobivaid tellimusi ei leitud
                                                </p>
                                                <button
                                                    type="button"
                                                    class="mt-1 text-sm font-semibold text-stokker-primary hover:underline"
                                                    onclick={clearFilters}
                                                >
                                                    Tühjenda filtrid
                                                </button>
                                            </td>
                                        </tr>
                                    {/each}
                                </tbody>
                            </table>
                        </div>
                    </Card>
                </div>

                {#if savedFiltersMenuOpen}
                    <button
                        type="button"
                        class="fixed inset-0 z-40 cursor-default"
                        aria-label="Sulge salvestatud filtrite menüü"
                        onclick={() => (savedFiltersMenuOpen = false)}
                    ></button>

                    <aside
                        class="absolute top-3 right-3 z-50 w-[min(22rem,calc(100%-1.5rem))]"
                        aria-labelledby="saved-filters-heading"
                    >
                        <Card
                            class="gap-0 overflow-hidden rounded-xl border-slate-200 py-0 shadow-xl"
                        >
                            <CardHeader class="border-b px-4 py-3">
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <div id="saved-filters-heading">
                                        <CardTitle class="text-base">
                                            Salvestatud filtrid
                                        </CardTitle>
                                    </div>
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        aria-label="Sulge menüü"
                                        title="Sulge"
                                        onclick={() =>
                                            (savedFiltersMenuOpen = false)}
                                    >
                                        <X class="size-4" />
                                    </Button>
                                </div>
                            </CardHeader>

                            <CardContent class="p-2">
                                {#if hasUnsavedChanges}
                                    <button
                                        type="button"
                                        class="flex w-full items-center gap-3 rounded-lg bg-amber-50 px-3 py-2.5 text-left text-amber-950 transition-colors hover:bg-amber-100 focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-amber-600 disabled:opacity-60"
                                        disabled={isSaving}
                                        onclick={overwriteSelectedFilter}
                                    >
                                        <BookmarkCheck
                                            class="size-4 shrink-0 text-amber-700"
                                        />
                                        <span class="min-w-0 flex-1">
                                            <span
                                                class="block text-sm font-semibold"
                                            >
                                                {isSaving
                                                    ? 'Salvestan…'
                                                    : 'Salvesta muudatused'}
                                            </span>
                                            <span
                                                class="block truncate text-xs"
                                            >
                                                {selectedFilter?.name}
                                            </span>
                                        </span>
                                    </button>
                                    <div class="my-2 h-px bg-slate-100"></div>
                                {/if}

                                {#if savedFilters.length > 0}
                                    <ul class="grid gap-1">
                                        {#each savedFilters as savedFilter (savedFilter.id)}
                                            <li
                                                class:selected-filter={selectedFilterId ===
                                                    savedFilter.id}
                                                class="group rounded-lg border border-transparent transition-colors hover:bg-slate-50 [&.selected-filter]:border-sky-200 [&.selected-filter]:bg-sky-50"
                                            >
                                                <div
                                                    class="flex items-start gap-1 p-1.5"
                                                >
                                                    <button
                                                        type="button"
                                                        class="min-w-0 flex-1 rounded-md px-2 py-1.5 text-left focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-stokker-primary"
                                                        onclick={() =>
                                                            applySavedFilter(
                                                                savedFilter,
                                                            )}
                                                    >
                                                        <span
                                                            class="flex items-center gap-2"
                                                        >
                                                            <span
                                                                class="truncate text-sm font-semibold text-slate-900"
                                                            >
                                                                {savedFilter.name}
                                                            </span>
                                                            {#if selectedFilterId === savedFilter.id}
                                                                <Check
                                                                    class="size-3.5 shrink-0 text-stokker-primary"
                                                                />
                                                            {/if}
                                                        </span>
                                                        <span
                                                            class="mt-0.5 block truncate text-xs text-slate-500"
                                                        >
                                                            {filterSummary(
                                                                savedFilter,
                                                            )}
                                                        </span>
                                                    </button>

                                                    <div
                                                        class="flex shrink-0 items-center gap-0.5 pt-1"
                                                    >
                                                        <button
                                                            type="button"
                                                            class="inline-flex size-7 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-white hover:text-amber-600 focus-visible:outline-2 focus-visible:outline-stokker-primary disabled:opacity-60"
                                                            class:text-amber-500={savedFilter.isDefault}
                                                            aria-label={savedFilter.isDefault
                                                                ? `${savedFilter.name} on vaikefilter`
                                                                : `Määra ${savedFilter.name} vaikefiltriks`}
                                                            title={savedFilter.isDefault
                                                                ? 'Vaikefilter'
                                                                : 'Määra vaikefiltriks'}
                                                            disabled={pendingDefaultId !==
                                                                null}
                                                            onclick={() =>
                                                                setDefault(
                                                                    savedFilter,
                                                                )}
                                                        >
                                                            <Star
                                                                class="size-4"
                                                                fill={savedFilter.isDefault
                                                                    ? 'currentColor'
                                                                    : 'none'}
                                                            />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="inline-flex size-7 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-white hover:text-slate-700 focus-visible:outline-2 focus-visible:outline-stokker-primary"
                                                            aria-label={`Nimeta ${savedFilter.name} ümber`}
                                                            title="Nimeta ümber"
                                                            onclick={() =>
                                                                openRenameDialog(
                                                                    savedFilter,
                                                                )}
                                                        >
                                                            <Pencil
                                                                class="size-3.5"
                                                            />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="inline-flex size-7 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600 focus-visible:outline-2 focus-visible:outline-red-500"
                                                            aria-label={`Kustuta ${savedFilter.name}`}
                                                            title="Kustuta"
                                                            onclick={() =>
                                                                openDeleteDialog(
                                                                    savedFilter,
                                                                )}
                                                        >
                                                            <Trash2
                                                                class="size-3.5"
                                                            />
                                                        </button>
                                                    </div>
                                                </div>
                                            </li>
                                        {/each}
                                    </ul>
                                {:else}
                                    <div class="px-3 py-6 text-center">
                                        <BookmarkCheck
                                            class="mx-auto size-7 text-slate-300"
                                        />
                                        <p
                                            class="mt-2 text-sm font-semibold text-slate-800"
                                        >
                                            Salvestatud filtreid pole
                                        </p>
                                    </div>
                                {/if}

                                <div class="my-2 h-px bg-slate-100"></div>
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-stokker-primary"
                                    onclick={openSaveDialog}
                                >
                                    <Plus class="size-4" />
                                    Salvesta uue filtrina
                                </button>
                            </CardContent>
                        </Card>
                    </aside>
                {/if}
            </div>
        </section>

        <SourceCodeFiles sourceSet="saved-filters" />
    </div>
</main>

<Dialog bind:open={saveDialogOpen}>
    <DialogContent class="max-w-md">
        <form class="grid gap-5" onsubmit={saveNewFilter}>
            <div class="grid gap-1.5">
                <DialogTitle>Salvesta uus filter</DialogTitle>
            </div>

            <div class="grid gap-2">
                <Label for="new-filter-name">Filtri nimi</Label>
                <Input
                    id="new-filter-name"
                    bind:value={filterName}
                    maxlength={60}
                    aria-invalid={Boolean(nameError)}
                    aria-describedby={nameError
                        ? 'new-filter-name-error'
                        : undefined}
                    autofocus
                />
                {#if nameError}
                    <p
                        id="new-filter-name-error"
                        class="text-sm text-destructive"
                    >
                        {nameError}
                    </p>
                {/if}
            </div>

            <label
                class="flex cursor-pointer items-start gap-3 rounded-lg border bg-slate-50 p-3"
            >
                <input
                    type="checkbox"
                    bind:checked={saveAsDefault}
                    class="mt-0.5 size-4 rounded border-slate-300 text-stokker-primary accent-stokker-primary"
                />
                <span>
                    <span class="block text-sm font-semibold text-slate-900">
                        Määra vaikefiltriks
                    </span>
                </span>
            </label>

            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    onclick={() => (saveDialogOpen = false)}
                >
                    Loobu
                </Button>
                <Button
                    type="submit"
                    class="bg-stokker-primary text-white hover:bg-stokker-primary-dark"
                    disabled={isSaving || filterName.trim() === ''}
                >
                    {isSaving ? 'Salvestan…' : 'Salvesta filter'}
                </Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<Dialog bind:open={renameDialogOpen}>
    <DialogContent class="max-w-md">
        <form class="grid gap-5" onsubmit={renameFilter}>
            <div class="grid gap-1.5">
                <DialogTitle>Nimeta filter ümber</DialogTitle>
            </div>

            <div class="grid gap-2">
                <Label for="rename-filter-name">Filtri nimi</Label>
                <Input
                    id="rename-filter-name"
                    bind:value={filterName}
                    maxlength={60}
                    aria-invalid={Boolean(nameError)}
                    aria-describedby={nameError
                        ? 'rename-filter-name-error'
                        : undefined}
                    autofocus
                />
                {#if nameError}
                    <p
                        id="rename-filter-name-error"
                        class="text-sm text-destructive"
                    >
                        {nameError}
                    </p>
                {/if}
            </div>

            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    onclick={() => (renameDialogOpen = false)}
                >
                    Loobu
                </Button>
                <Button
                    type="submit"
                    class="bg-stokker-primary text-white hover:bg-stokker-primary-dark"
                    disabled={isSaving || filterName.trim() === ''}
                >
                    {isSaving ? 'Salvestan…' : 'Salvesta nimi'}
                </Button>
            </DialogFooter>
        </form>
    </DialogContent>
</Dialog>

<Dialog bind:open={deleteDialogOpen}>
    <DialogContent class="max-w-md">
        <div class="grid gap-5">
            <div class="grid gap-1.5">
                <DialogTitle>Kustuta salvestatud filter?</DialogTitle>
                <p class="text-sm leading-6 text-muted-foreground">
                    Filter <strong class="text-foreground"
                        >{pendingFilter?.name}</strong
                    >
                    eemaldatakse jäädavalt.
                </p>
            </div>

            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    onclick={() => (deleteDialogOpen = false)}
                >
                    Loobu
                </Button>
                <Button
                    type="button"
                    variant="destructive"
                    disabled={isDeleting}
                    onclick={deleteFilter}
                >
                    <Trash2 class="size-4" />
                    {isDeleting ? 'Kustutan…' : 'Kustuta filter'}
                </Button>
            </DialogFooter>
        </div>
    </DialogContent>
</Dialog>

<Toaster />
