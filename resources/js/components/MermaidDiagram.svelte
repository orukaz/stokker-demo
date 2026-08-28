<script lang="ts">
    import { onMount } from 'svelte';
    import { replaceWithSvgMarkup } from '@/lib/svg';

    let {
        definition,
        label,
    }: {
        definition: string;
        label: string;
    } = $props();

    let diagramElement: HTMLDivElement;
    let rendered = $state(false);
    let failed = $state(false);

    onMount(() => {
        let isMounted = true;

        const renderDiagram = async (): Promise<void> => {
            try {
                await document.fonts.ready;

                const { default: mermaid } = await import('mermaid');

                mermaid.initialize({
                    startOnLoad: false,
                    securityLevel: 'strict',
                    theme: 'base',
                    fontFamily: 'Instrument Sans, sans-serif',
                    themeVariables: {
                        primaryColor: '#f4fafe',
                        primaryTextColor: '#0f172a',
                        primaryBorderColor: '#0075ba',
                        lineColor: '#64748b',
                        secondaryColor: '#ffffff',
                        tertiaryColor: '#ffffff',
                        clusterBkg: '#ffffff',
                        clusterBorder: '#cbd5e1',
                        fontSize: '15px',
                    },
                    flowchart: {
                        curve: 'linear',
                        htmlLabels: true,
                        nodeSpacing: 28,
                        rankSpacing: 36,
                        useMaxWidth: true,
                    },
                });

                const diagramId = `mermaid-${crypto.randomUUID()}`;
                const result = await mermaid.render(diagramId, definition);

                if (isMounted) {
                    replaceWithSvgMarkup(diagramElement, result.svg);
                    rendered = true;
                }
            } catch {
                if (isMounted) {
                    failed = true;
                }
            }
        };

        void renderDiagram();

        return () => {
            isMounted = false;
        };
    });
</script>

<div
    class="overflow-x-auto border-y border-slate-200 py-5"
    aria-busy={!rendered && !failed}
>
    <div
        bind:this={diagramElement}
        role="img"
        aria-label={label}
        class:hidden={!rendered}
        class="min-w-180 [&_svg]:mx-auto [&_svg]:h-auto [&_svg]:max-w-full"
    ></div>

    {#if failed}
        <p class="py-10 text-center text-sm text-slate-500">
            Diagrammi ei saanud kuvada.
        </p>
    {:else if !rendered}
        <div
            class="h-64 min-w-180 animate-pulse bg-slate-50"
            aria-hidden="true"
        ></div>
    {/if}
</div>
