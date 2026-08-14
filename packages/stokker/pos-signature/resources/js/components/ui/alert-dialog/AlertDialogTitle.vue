<script setup lang="ts">
import type { AlertDialogTitleProps } from 'reka-ui';
import { AlertDialogTitle } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

const props = defineProps<
    AlertDialogTitleProps & { class?: HTMLAttributes['class'] }
>();
</script>

<template>
    <AlertDialogTitle
        data-slot="alert-dialog-title"
        v-bind="props"
        :class="cn('text-lg font-semibold', props.class)"
    >
        <slot />
    </AlertDialogTitle>
</template>
