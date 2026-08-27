<script lang="ts">
    import CodeHighlighter from '@/components/CodeHighlighter.svelte';
    import {
        Sheet,
        SheetContent,
        SheetHeader,
        SheetTitle,
    } from '@/components/ui/sheet';
    import type { SourceCodeFile } from '@/types/source-code';

    type Props = {
        open: boolean;
        file: SourceCodeFile | null;
    };

    let { open = $bindable(), file }: Props = $props();
</script>

<Sheet bind:open>
    <SheetContent
        side="right"
        class="source-code-sheet data-[state=open]:animate-in data-[state=open]:slide-in-from-right-10 data-[state=open]:fade-in-0 data-[state=closed]:animate-out data-[state=closed]:slide-out-to-right-10 data-[state=closed]:fade-out-0 gap-0 overflow-hidden border-white/15 bg-black p-0 text-white"
    >
        <SheetHeader class="shrink-0 border-b border-white/15 p-4 pr-12">
            <SheetTitle
                class="truncate text-left font-mono text-sm font-normal text-white/80"
            >
                {file?.label ?? 'Lähtekood'}
            </SheetTitle>
        </SheetHeader>

        <div class="min-h-0 flex-1 overflow-auto bg-black">
            {#if file}
                <CodeHighlighter code={file.code} language={file.language} />
            {/if}
        </div>
    </SheetContent>
</Sheet>

<style>
    :global(.source-code-sheet) {
        width: 94vw !important;
        max-width: none !important;
    }

    @media (min-width: 768px) {
        :global(.source-code-sheet) {
            width: 40vw !important;
        }
    }
</style>
