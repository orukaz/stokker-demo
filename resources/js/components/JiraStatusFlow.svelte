<script lang="ts">
    import JiraStatusBadge from '@/components/JiraStatusBadge.svelte';

    let {
        statuses,
        class: className = '',
    }: {
        statuses: readonly string[];
        class?: string;
    } = $props();
</script>

<div class="flex flex-wrap items-center gap-1.5 {className}">
    {#each statuses as status, index (status)}
        {#if index === 0}
            <JiraStatusBadge {status} />
        {:else}
            <span class="inline-flex items-center gap-1.5">
                <span class="text-xs text-slate-300" aria-hidden="true">→</span>
                <JiraStatusBadge {status} />
            </span>
        {/if}
    {/each}
</div>
