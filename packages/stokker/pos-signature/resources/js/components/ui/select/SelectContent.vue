<script setup lang="ts">
import { reactiveOmit } from '@vueuse/core';
import type { SelectContentEmits, SelectContentProps } from 'reka-ui';
import {
    SelectContent,
    SelectPortal,
    SelectViewport,
    useForwardPropsEmits,
} from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

defineOptions({
    inheritAttrs: false,
});

const props = withDefaults(
    defineProps<SelectContentProps & { class?: HTMLAttributes['class'] }>(),
    {
        position: 'popper',
    },
);
const emits = defineEmits<SelectContentEmits>();
const delegatedProps = reactiveOmit(props, 'class');
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <SelectPortal>
        <SelectContent
            data-slot="select-content"
            v-bind="{ ...$attrs, ...forwarded }"
            :class="
                cn(
                    'relative z-[70] min-w-20 overflow-hidden rounded-md border bg-background text-foreground shadow-md',
                    position === 'popper' &&
                        'data-[side=bottom]:translate-y-1 data-[side=top]:-translate-y-1',
                    props.class,
                )
            "
        >
            <SelectViewport class="p-1">
                <slot />
            </SelectViewport>
        </SelectContent>
    </SelectPortal>
</template>
