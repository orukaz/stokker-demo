<script lang="ts" module>
    export type SearchableSelectOption = {
        value: string;
        label: string;
        searchText?: string;
        disabled?: boolean;
    };

    export type SearchableSelectProps = {
        id?: string;
        name?: string;
        value?: string;
        options: SearchableSelectOption[];
        placeholder?: string;
        searchPlaceholder?: string;
        emptyText?: string;
        ariaLabel?: string;
        required?: boolean;
        disabled?: boolean;
        class?: string;
        onValueChange?: (value: string) => void;
    };
</script>

<script lang="ts">
    import { Combobox } from 'bits-ui';
    import Check from 'lucide-svelte/icons/check';
    import ChevronsUpDown from 'lucide-svelte/icons/chevrons-up-down';
    import Search from 'lucide-svelte/icons/search';
    import { tick } from 'svelte';
    import { cn } from '@/lib/utils';

    let {
        id,
        name,
        value = $bindable(''),
        options,
        placeholder = 'Vali',
        searchPlaceholder = 'Otsi...',
        emptyText = 'Vasteid ei leitud.',
        ariaLabel,
        required = false,
        disabled = false,
        class: className,
        onValueChange,
    }: SearchableSelectProps = $props();

    let open = $state(false);
    let searchValue = $state('');
    let inputElement = $state<HTMLInputElement | null>(null);
    let triggerElement = $state<HTMLButtonElement | null>(null);

    const comboboxItems = $derived(
        options.map(({ value: optionValue, label, disabled: isDisabled }) => ({
            value: optionValue,
            label,
            disabled: isDisabled,
        })),
    );
    const selectedOption = $derived(
        options.find((option) => option.value === value),
    );
    const inputValue = $derived(
        open ? searchValue : (selectedOption?.label ?? ''),
    );
    const normalizedSearchValue = $derived(normalizeSearchText(searchValue));
    const filteredOptions = $derived(
        normalizedSearchValue
            ? options.filter((option) =>
                  normalizeSearchText(
                      `${option.label} ${option.value} ${option.searchText ?? ''}`,
                  ).includes(normalizedSearchValue),
              )
            : options,
    );

    function normalizeSearchText(searchText: string): string {
        return searchText
            .normalize('NFD')
            .replace(/\p{Diacritic}/gu, '')
            .toLocaleLowerCase('et')
            .trim();
    }

    function handleInput(
        event: Event & { currentTarget: HTMLInputElement },
    ): void {
        searchValue = event.currentTarget.value;
    }

    function handleOpenChange(nextOpen: boolean): void {
        open = nextOpen;

        if (!nextOpen) {
            searchValue = '';
        }
    }

    async function handleOpenChangeComplete(nextOpen: boolean): Promise<void> {
        if (!nextOpen) {
            return;
        }

        await tick();
        inputElement?.focus();
    }

    function handleValueChange(nextValue: string): void {
        value = nextValue;
        onValueChange?.(nextValue);
    }

</script>

<Combobox.Root
    type="single"
    {name}
    {required}
    {disabled}
    items={comboboxItems}
    {value}
    {inputValue}
    {open}
    allowDeselect={false}
    loop
    onValueChange={handleValueChange}
    onOpenChange={handleOpenChange}
    onOpenChangeComplete={handleOpenChangeComplete}
>
    <Combobox.Trigger
        bind:ref={triggerElement}
        {id}
        data-slot="searchable-select"
        aria-label={ariaLabel}
        class={cn(
            'flex h-11 w-full items-center justify-between gap-2 rounded-md border border-input bg-white px-3 text-left text-base shadow-none transition-colors outline-none focus-visible:ring-2 focus-visible:ring-stokker-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background',
            className,
        )}
    >
        <span
            class={cn(
                'truncate',
                !selectedOption && 'text-muted-foreground',
            )}
        >
            {selectedOption?.label ?? placeholder}
        </span>
        <ChevronsUpDown
            class="size-4 shrink-0 text-muted-foreground"
            aria-hidden="true"
        />
    </Combobox.Trigger>

    <Combobox.Portal>
        <Combobox.Content
            sideOffset={4}
            customAnchor={triggerElement}
            preventScroll
            class="z-50 w-72 max-w-[calc(100vw-2rem)] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95"
        >
            <div class="relative border-b">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <Combobox.Input
                    bind:ref={inputElement}
                    id={id ? `${id}-search` : undefined}
                    autocomplete="off"
                    aria-label={searchPlaceholder}
                    class="h-11 w-full bg-transparent pr-3 pl-9 text-sm outline-none placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder={searchPlaceholder}
                    oninput={handleInput}
                />
            </div>
            <Combobox.Viewport class="max-h-72 overflow-y-auto p-1">
                {#each filteredOptions as option (option.value)}
                    <Combobox.Item
                        value={option.value}
                        label={option.label}
                        disabled={option.disabled}
                        class="relative flex w-full cursor-default items-center rounded-sm py-2 pr-8 pl-2 text-sm outline-none select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground"
                    >
                        {#snippet children({ selected })}
                            <span class="truncate">{option.label}</span>
                            {#if selected}
                                <Check
                                    class="absolute right-2 size-4"
                                    aria-hidden="true"
                                />
                            {/if}
                        {/snippet}
                    </Combobox.Item>
                {:else}
                    <p class="px-3 py-6 text-center text-sm text-muted-foreground">
                        {emptyText}
                    </p>
                {/each}
            </Combobox.Viewport>
        </Combobox.Content>
    </Combobox.Portal>
</Combobox.Root>
