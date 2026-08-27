<script lang="ts">
    import { untrack } from 'svelte';
    import InputError from '@/components/InputError.svelte';
    import { Label } from '@/components/ui/label';
    import {
        NativeSelect,
        NativeSelectOption,
    } from '@/components/ui/native-select';
    import { orderPhoneCountries } from '@/lib/phone';
    import { phoneFieldState } from '@/lib/phone-field.svelte';
    import { cn } from '@/lib/utils';

    type Props = {
        id?: string;
        name?: string;
        label?: string;
        value?: string;
        countryIso?: string;
        showCountryCode?: boolean;
        required?: boolean;
        disabled?: boolean;
        error?: string;
        helpText?: string;
        preferredCountryIsos?: string[];
        class?: string;
    };

    let {
        id = 'phone',
        name = 'phone',
        label = 'Telefoninumber',
        value = $bindable(''),
        countryIso = $bindable('EE'),
        showCountryCode = true,
        required = false,
        disabled = false,
        error = '',
        helpText = '',
        preferredCountryIsos = [],
        class: className = '',
    }: Props = $props();

    const phoneCountries = $derived(orderPhoneCountries(preferredCountryIsos));

    const phoneField = phoneFieldState({
        getId: () => id,
        getValue: () => value,
        setValue: (nextValue) => (value = nextValue),
        getCountryIso: () => countryIso,
        setCountryIso: (nextCountryIso) => (countryIso = nextCountryIso),
        getShowCountryCode: () => showCountryCode,
        getRequired: () => required,
        getError: () => error,
        getHelpText: () => helpText,
    });

    function handleCountryChange(event: Event): void {
        phoneField.handleCountryChange(
            (event.currentTarget as HTMLSelectElement).value,
        );
    }

    $effect(() => {
        const nextValue = value;
        const nextCountryIso = countryIso;
        const nextShowCountryCode = showCountryCode;

        untrack(() =>
            phoneField.syncExternalValue(
                nextValue,
                nextCountryIso,
                nextShowCountryCode,
            ),
        );
    });
</script>

<div class={cn('grid gap-2', className)}>
    <Label for={id}>
        {label}
        {#if required}
            <span class="text-destructive" aria-hidden="true">*</span>
        {/if}
    </Label>

    <div
        class={cn(
            'flex flex-col gap-2 sm:flex-row',
            !showCountryCode && 'block',
        )}
    >
        {#if showCountryCode}
            <div class="sm:w-40 sm:shrink-0">
                <label for={phoneField.countrySelectId} class="sr-only">
                    Riigikood
                </label>
                <NativeSelect
                    id={phoneField.countrySelectId}
                    name={`${name}_country`}
                    value={countryIso}
                    onchange={handleCountryChange}
                    class="w-full [&_[data-slot=native-select]]:h-11 [&_[data-slot=native-select]]:bg-white [&_[data-slot=native-select]]:text-base dark:[&_[data-slot=native-select]]:bg-background"
                    aria-label="Riigikood"
                    {disabled}
                >
                    {#if !countryIso}
                        <NativeSelectOption value="">
                            Muu riik
                        </NativeSelectOption>
                    {/if}
                    {#each phoneCountries as country (country.iso2)}
                        <NativeSelectOption value={country.iso2}>
                            {country.iso2}
                            {country.dialCode}
                            - {country.name}
                        </NativeSelectOption>
                    {/each}
                </NativeSelect>
            </div>
        {/if}

        <input
            {id}
            {name}
            type="tel"
            inputmode="tel"
            autocomplete="tel"
            bind:value={phoneField.values.inputValue}
            oninput={phoneField.handleInput}
            onblur={phoneField.handleBlur}
            aria-invalid={phoneField.resolvedError ? 'true' : undefined}
            aria-describedby={phoneField.describedBy}
            placeholder={phoneField.placeholder}
            class={cn(
                'flex h-11 w-full rounded-md border border-input bg-white px-3 py-1 text-base shadow-none transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-stokker-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background',
                phoneField.resolvedError &&
                    'border-destructive focus-visible:ring-destructive',
            )}
            {required}
            {disabled}
        />
    </div>

    {#if helpText}
        <p id={phoneField.helpTextId} class="text-sm text-muted-foreground">
            {helpText}
        </p>
    {/if}

    <div id={phoneField.errorId}>
        <InputError message={phoneField.resolvedError} />
    </div>
</div>
