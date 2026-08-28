<script lang="ts">
    import Bookmark from 'lucide-svelte/icons/bookmark';
    import Bug from 'lucide-svelte/icons/bug';
    import GitBranch from 'lucide-svelte/icons/git-branch';
    import SquareCheck from 'lucide-svelte/icons/square-check';
    import Zap from 'lucide-svelte/icons/zap';
    import { Badge } from '@/components/ui/badge';

    type JiraIssueType = 'task' | 'story' | 'bug' | 'epic' | 'subtask';

    const labels: Record<JiraIssueType, string> = {
        task: 'Ülesanne',
        story: 'Lugu',
        bug: 'Viga',
        epic: 'Epic',
        subtask: 'Alamülesanne',
    };

    const badgeClasses: Record<JiraIssueType, string> = {
        task: 'border-blue-200 bg-blue-50',
        story: 'border-green-200 bg-green-50',
        bug: 'border-red-200 bg-red-50',
        epic: 'border-violet-200 bg-violet-50',
        subtask: 'border-cyan-200 bg-cyan-50',
    };

    let {
        type,
        label,
    }: {
        type: JiraIssueType;
        label?: string;
    } = $props();

    const badgeClass = $derived(badgeClasses[type]);
    const visibleLabel = $derived(label ?? labels[type]);
</script>

<Badge
    variant="outline"
    class="gap-1.5 rounded-md px-2.5 py-1 text-sm font-semibold text-slate-800 [&>svg]:size-3.5 {badgeClass}"
>
    {#if type === 'task'}
        <SquareCheck
            class="fill-blue-500 text-white"
            strokeWidth={2.25}
            aria-hidden="true"
        />
    {:else if type === 'story'}
        <Bookmark
            class="text-green-600"
            strokeWidth={2.25}
            aria-hidden="true"
        />
    {:else if type === 'bug'}
        <Bug class="text-red-500" strokeWidth={2.25} aria-hidden="true" />
    {:else if type === 'epic'}
        <Zap class="text-violet-500" strokeWidth={2.25} aria-hidden="true" />
    {:else}
        <GitBranch
            class="text-cyan-600"
            strokeWidth={2.25}
            aria-hidden="true"
        />
    {/if}

    <span>{visibleLabel}</span>
</Badge>
