<script setup lang="ts">
import type { AlertDialogCancelProps } from 'reka-ui';
import { AlertDialogCancel } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';
import { buttonVariants } from '../button';

const props = defineProps<
    AlertDialogCancelProps & { class?: HTMLAttributes['class'] }
>();
</script>

<template>
    <AlertDialogCancel
        data-slot="alert-dialog-cancel"
        v-bind="props"
        :class="cn(buttonVariants({ variant: 'outline' }), props.class)"
    >
        <slot />
    </AlertDialogCancel>
</template>
