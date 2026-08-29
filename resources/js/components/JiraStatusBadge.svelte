<script lang="ts">
    import { Badge } from '@/components/ui/badge';

    const activeStatuses = new Set([
        'In Analysis',
        'Waiting for Approval',
        'In Progress',
        'In Review',
        'Testing',
        'Ready for Release',
    ]);
    const completedStatuses = new Set(['Done', 'Cancelled']);

    let { status }: { status: string } = $props();

    const badgeClass = $derived(
        completedStatuses.has(status)
            ? 'border-green-200/70 bg-green-50/70 text-green-700/90'
            : activeStatuses.has(status)
              ? 'border-blue-200/70 bg-blue-50/70 text-blue-700/90'
              : 'border-slate-200 bg-slate-50 text-slate-600',
    );
</script>

<Badge
    variant="outline"
    class="rounded-md px-2 py-0.5 text-xs font-medium {badgeClass}"
>
    {status}
</Badge>
