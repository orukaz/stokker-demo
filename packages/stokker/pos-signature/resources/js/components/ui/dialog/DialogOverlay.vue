<script setup lang="ts">
import { reactiveOmit } from '@vueuse/core';
import type { DialogOverlayProps } from 'reka-ui';
import { DialogOverlay } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

const props = defineProps<
    DialogOverlayProps & { class?: HTMLAttributes['class'] }
>();
const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
    <DialogOverlay
        data-slot="dialog-overlay"
        v-bind="delegatedProps"
        :class="cn('fixed inset-0 z-50 bg-black/65', props.class)"
    >
        <slot />
    </DialogOverlay>
</template>
