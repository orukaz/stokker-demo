<script setup lang="ts">
import { reactiveOmit } from '@vueuse/core';
import type { TooltipContentEmits, TooltipContentProps } from 'reka-ui';
import { TooltipContent, TooltipPortal, useForwardPropsEmits } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

const props = withDefaults(
    defineProps<TooltipContentProps & { class?: HTMLAttributes['class'] }>(),
    {
        sideOffset: 6,
    },
);
const emits = defineEmits<TooltipContentEmits>();
const delegatedProps = reactiveOmit(props, 'class');
const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>
    <TooltipPortal>
        <TooltipContent
            data-slot="tooltip-content"
            v-bind="forwarded"
            :class="
                cn(
                    'z-[70] rounded-md bg-foreground px-2.5 py-1.5 text-xs text-background shadow-md',
                    props.class,
                )
            "
        >
            <slot />
        </TooltipContent>
    </TooltipPortal>
</template>
