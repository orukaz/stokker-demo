<script setup lang="ts">
import { reactiveOmit } from '@vueuse/core';
import type { SelectItemProps } from 'reka-ui';
import { SelectItem, SelectItemText, useForwardProps } from 'reka-ui';
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
                'relative flex h-10 w-full cursor-default items-center justify-center rounded-sm px-3 py-1.5 outline-none select-none focus:bg-accent data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
                props.class,
            )
        "
    >
        <SelectItemText data-slot="select-item-text">
            <slot />
        </SelectItemText>
    </SelectItem>
</template>
