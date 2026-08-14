<script setup lang="ts">
import type { AlertDialogDescriptionProps } from 'reka-ui';
import { AlertDialogDescription } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

const props = defineProps<
    AlertDialogDescriptionProps & { class?: HTMLAttributes['class'] }
>();
</script>

<template>
    <AlertDialogDescription
        data-slot="alert-dialog-description"
        v-bind="props"
        :class="cn('text-sm text-muted-foreground', props.class)"
    >
        <slot />
    </AlertDialogDescription>
</template>
