<script setup lang="ts">
import { reactiveOmit } from '@vueuse/core';
import type { AlertDialogContentEmits, AlertDialogContentProps } from 'reka-ui';
import {
    AlertDialogContent,
    AlertDialogOverlay,
    AlertDialogPortal,
    useForwardPropsEmits,
} from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps<
    AlertDialogContentProps & { class?: HTMLAttributes['class'] }
>();
const emits = defineEmits<AlertDialogContentEmits>();
const delegatedProps = reactiveOmit(props, 'class');
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <AlertDialogPortal>
        <AlertDialogOverlay
            class="fixed inset-0 z-[60] bg-black/70"
            data-slot="alert-dialog-overlay"
        />
        <AlertDialogContent
            data-slot="alert-dialog-content"
            v-bind="{ ...$attrs, ...forwarded }"
            :class="
                cn(
                    'fixed top-1/2 left-1/2 z-[60] grid w-[calc(100%-2rem)] max-w-md -translate-x-1/2 -translate-y-1/2 gap-4 rounded-lg border bg-background p-5 shadow-lg',
                    props.class,
                )
            "
        >
            <slot />
        </AlertDialogContent>
    </AlertDialogPortal>
</template>
