<script setup lang="ts">
import { X } from '@lucide/vue';
import { reactiveOmit } from '@vueuse/core';
import type { DialogContentEmits, DialogContentProps } from 'reka-ui';
import {
    DialogClose,
    DialogContent,
    DialogPortal,
    useForwardPropsEmits,
} from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';
import DialogOverlay from './DialogOverlay.vue';

defineOptions({
    inheritAttrs: false,
});

const props = withDefaults(
    defineProps<
        DialogContentProps & {
            class?: HTMLAttributes['class'];
            showCloseButton?: boolean;
        }
    >(),
    {
        showCloseButton: true,
    },
);
const emits = defineEmits<DialogContentEmits>();
const delegatedProps = reactiveOmit(props, 'class', 'showCloseButton');
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <DialogPortal>
        <DialogOverlay />
        <DialogContent
            data-slot="dialog-content"
            v-bind="{ ...$attrs, ...forwarded }"
            :class="
                cn(
                    'fixed inset-0 z-50 grid h-dvh w-screen max-w-none gap-0 overflow-hidden border-0 bg-background p-0 shadow-lg lg:inset-auto lg:top-1/2 lg:left-1/2 lg:h-[min(520px,calc(100dvh-3rem))] lg:w-[min(720px,calc(100vw-3rem))] lg:max-w-[720px] lg:-translate-x-1/2 lg:-translate-y-1/2 lg:rounded-xl lg:border',
                    props.class,
                )
            "
        >
            <slot />

            <DialogClose
                v-if="showCloseButton"
                data-slot="dialog-close"
                class="absolute top-[max(1rem,env(safe-area-inset-top))] right-[max(1rem,env(safe-area-inset-right))] z-20 grid size-9 place-items-center rounded-md bg-background/90 text-muted-foreground shadow-sm ring-1 ring-border transition-colors hover:bg-accent hover:text-foreground focus-visible:ring-3 focus-visible:ring-ring/50 focus-visible:outline-none"
            >
                <X />
                <span class="sr-only">Sulge</span>
            </DialogClose>
        </DialogContent>
    </DialogPortal>
</template>
