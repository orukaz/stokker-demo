<script setup lang="ts">
import { Check } from '@lucide/vue';
import { reactiveOmit } from '@vueuse/core';
import type { SelectItemProps } from 'reka-ui';
import {
    SelectItem,
    SelectItemIndicator,
    SelectItemText,
    useForwardProps,
} from 'reka-ui';
import type { HTMLAttributes } from 'vue';

import { cn } from '../../../lib/utils';

const props = defineProps<
    SelectItemProps & { class?: HTMLAttributes['class'] }
>();
const delegatedProps = reactiveOmit(props, 'class');
const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <SelectItem
        data-slot="select-item"
        v-bind="forwardedProps"
        :class="
            cn(
                'relative flex h-10 w-full cursor-default items-center rounded-sm py-1.5 pr-8 pl-3 outline-none select-none focus:bg-accent data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
                props.class,
            )
        "
    >
        <span class="absolute right-2 flex size-4 items-center justify-center">
            <SelectItemIndicator>
                <Check class="size-4" />
            </SelectItemIndicator>
        </span>

        <SelectItemText>
            <slot />
        </SelectItemText>
    </SelectItem>
</template>
