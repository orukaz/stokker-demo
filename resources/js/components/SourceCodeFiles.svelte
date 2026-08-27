<script lang="ts">
    import ChevronDown from '@lucide/svelte/icons/chevron-down';
    import ChevronRight from '@lucide/svelte/icons/chevron-right';
    import CodeXml from '@lucide/svelte/icons/code-xml';
    import { show } from '@/actions/App/Http/Controllers/SourceCodeController';
    import SourceCodeSheet from '@/components/SourceCodeSheet.svelte';
    import type {
        SourceCodeFile,
        SourceCodeResponse,
    } from '@/types/source-code';

    type Props = {
        sourceSet: string;
    };

    let { sourceSet }: Props = $props();
    let sourceCode = $state<SourceCodeResponse | null>(null);
    let selectedFile = $state<SourceCodeFile | null>(null);
    let listOpen = $state(false);
    let sheetOpen = $state(false);
    let isLoading = $state(true);
    let loadFailed = $state(false);

    $effect(() => {
        const requestedSourceSet = sourceSet;
        const abortController = new AbortController();

        sourceCode = null;
        selectedFile = null;
        isLoading = true;
        loadFailed = false;

        fetch(show.url(requestedSourceSet), {
            headers: {
                Accept: 'application/json',
            },
            signal: abortController.signal,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Source code could not be loaded.');
                }

                return response.json() as Promise<SourceCodeResponse>;
            })
            .then((response) => {
                sourceCode = response;
            })
            .catch((error: unknown) => {
                if (
                    !(
                        error instanceof DOMException &&
                        error.name === 'AbortError'
                    )
                ) {
                    loadFailed = true;
                }
            })
            .finally(() => {
                if (!abortController.signal.aborted) {
                    isLoading = false;
                }
            });

        return () => {
            abortController.abort();
        };
    });

    function openFile(file: SourceCodeFile): void {
        selectedFile = file;
        sheetOpen = true;
    }
</script>

<section class="overflow-hidden rounded-lg border bg-card">
    <button
        type="button"
        class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium transition-colors hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-ring"
        aria-expanded={listOpen}
        onclick={() => (listOpen = !listOpen)}
    >
        <CodeXml class="size-4 shrink-0 text-muted-foreground" />
        <span class="flex-1">Vaata lähtekoodi</span>
        <ChevronDown
            class={listOpen
                ? 'size-4 shrink-0 rotate-180 text-muted-foreground transition-transform'
                : 'size-4 shrink-0 text-muted-foreground transition-transform'}
        />
    </button>

    {#if listOpen}
        <div class="border-t">
            {#if isLoading}
                <div class="space-y-2 p-3" aria-label="Lähtekoodi laadimine">
                    {#each Array(4) as _, index (index)}
                        <div
                            class="h-11 animate-pulse rounded-lg bg-muted"
                        ></div>
                    {/each}
                </div>
            {:else if loadFailed}
                <p class="p-4 text-sm text-destructive">
                    Lähtekoodi ei saanud laadida.
                </p>
            {:else if sourceCode}
                <ul class="divide-y">
                    {#each sourceCode.files as file (file.id)}
                        <li>
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm transition-colors hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-ring"
                                onclick={() => openFile(file)}
                            >
                                <span class="min-w-0 flex-1 truncate font-mono">
                                    {file.label}
                                </span>
                                <ChevronRight
                                    class="size-4 shrink-0 text-muted-foreground"
                                />
                            </button>
                        </li>
                    {/each}
                </ul>
            {/if}
        </div>
    {/if}
</section>

<SourceCodeSheet bind:open={sheetOpen} file={selectedFile} />
