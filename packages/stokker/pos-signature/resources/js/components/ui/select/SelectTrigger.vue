<script setup lang="ts">
import { ChevronDown } from '@lucide/vue';
import { reactiveOmit } from '@vueuse/core';
import type { SelectTriggerProps } from 'reka-ui';
import { SelectIcon, SelectTrigger, useForwardProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

const props = defineProps<
    SelectTriggerProps & { class?: HTMLAttributes['class'] }
>();
const delegatedProps = reactiveOmit(props, 'class');
const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <SelectTrigger
        data-slot="select-trigger"
        v-bind="forwardedProps"
        :class="
            cn(
                'flex size-10 shrink-0 items-center justify-between gap-2 rounded-md border bg-background px-3 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50 sm:size-11',
                props.class,
            )
        "
    >
        <slot />
        <SelectIcon as-child>
            <ChevronDown class="size-4 opacity-50" />
        </SelectIcon>
    </SelectTrigger>
</template>
