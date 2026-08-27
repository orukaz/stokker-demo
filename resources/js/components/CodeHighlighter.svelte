<script lang="ts">
    import type { ThemedToken } from 'shiki/types';
    import { highlightCode } from '@/lib/code-highlighter';
    import type { SourceCodeLanguage } from '@/types/source-code';

    type Props = {
        code: string;
        language: SourceCodeLanguage;
    };

    let { code, language }: Props = $props();
    let tokenLines = $state<ThemedToken[][]>([]);
    let isLoading = $state(true);
    let highlightingFailed = $state(false);

    $effect(() => {
        const currentCode = code;
        const currentLanguage = language;
        let isCancelled = false;

        isLoading = true;
        highlightingFailed = false;

        highlightCode(currentCode, currentLanguage)
            .then((lines) => {
                if (!isCancelled) {
                    tokenLines = lines;
                }
            })
            .catch(() => {
                if (!isCancelled) {
                    highlightingFailed = true;
                }
            })
            .finally(() => {
                if (!isCancelled) {
                    isLoading = false;
                }
            });

        return () => {
            isCancelled = true;
        };
    });

    function tokenStyle(token: ThemedToken): string {
        const fontStyle = token.fontStyle ?? 0;
        const textDecorations: string[] = [];

        if (fontStyle & 4) {
            textDecorations.push('underline');
        }

        if (fontStyle & 8) {
            textDecorations.push('line-through');
        }

        return [
            `color: ${token.color ?? '#e6edf3'}`,
            fontStyle & 1 ? 'font-style: italic' : '',
            fontStyle & 2 ? 'font-weight: 700' : '',
            textDecorations.length > 0
                ? `text-decoration: ${textDecorations.join(' ')}`
                : '',
        ]
            .filter(Boolean)
            .join('; ');
    }
</script>

<div class="min-h-full min-w-full bg-black text-[#e6edf3]">
    {#if isLoading}
        <div class="space-y-3 p-6" aria-label="Koodi värvimine">
            {#each Array(8) as _, index (index)}
                <div class="h-3 animate-pulse rounded bg-white/10"></div>
            {/each}
        </div>
    {:else if highlightingFailed}
        <pre class="min-w-max p-5 font-mono text-[13px] leading-6"><code
                >{code}</code
            ></pre>
    {:else}
        <div class="min-w-max py-5 font-mono text-[13px] leading-6">
            {#each tokenLines as tokens, lineIndex (lineIndex)}
                <div class="table-row">
                    <span
                        class="table-cell w-12 select-none border-r border-white/10 pr-3 text-right text-white/35"
                        aria-hidden="true"
                    >
                        {lineIndex + 1}
                    </span>
                    <code class="table-cell whitespace-pre px-4">
                        {#each tokens as token, tokenIndex (tokenIndex)}
                            <span style={tokenStyle(token)}
                                >{token.content}</span
                            >
                        {/each}
                    </code>
                </div>
            {/each}
        </div>
    {/if}
</div>
