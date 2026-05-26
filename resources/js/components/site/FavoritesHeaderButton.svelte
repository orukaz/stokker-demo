<script lang="ts">
    import Heart from 'lucide-svelte/icons/heart';

    let {
        count = 0,
        disabled = true,
    }: {
        count?: number;
        disabled?: boolean;
    } = $props();
</script>

<button
    type="button"
    title="Lemmikud"
    class="inline-flex cursor-not-allowed items-center gap-1 rounded-md px-2 py-3 text-stokker-primary transition-colors hover:bg-gray-100"
    {disabled}
>
    <span class="relative inline-flex">
        <Heart class="size-6" />
        {#if count > 0}
            <span class="absolute -top-1.5 -right-1.5">
                <span
                    class="flex size-5 items-center justify-center rounded-full bg-stokker-green text-xs text-white"
                >
                    {count}
                </span>
            </span>
        {/if}
    </span>
</button>
