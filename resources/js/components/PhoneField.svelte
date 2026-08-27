<script lang="ts">
    import { untrack } from 'svelte';
    import InputError from '@/components/InputError.svelte';
    import { Label } from '@/components/ui/label';
    import {
        NativeSelect,
        NativeSelectOption,
    } from '@/components/ui/native-select';
    import {
        formatPhoneNumber,
        getPhoneCountry,
        isValidPhoneNumber,
        normalizePhoneNumber,
        PHONE_COUNTRIES,
    } from '@/lib/phone';
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
        class: className = '',
    }: Props = $props();

    const initialPhoneNumber = normalizePhoneNumber(value, countryIso);

    value = initialPhoneNumber.canonical;
    countryIso = initialPhoneNumber.countryIso;

    let inputValue = $state(
        untrack(() => formatPhoneNumber(initialPhoneNumber, showCountryCode)),
    );
    let touched = $state(false);

    const countrySelectId = $derived(`${id}-country`);
    const helpTextId = $derived(`${id}-help`);
    const errorId = $derived(`${id}-error`);
    const activeCountry = $derived(getPhoneCountry(countryIso || 'EE'));
    const internalError = $derived.by(() => {
        if (!touched || error) {
            return '';
        }

        const phoneNumber = normalizePhoneNumber(
            inputValue,
            countryIso || 'EE',
        );

        if (!phoneNumber.nationalDigits) {
            return required ? 'Sisesta telefoninumber.' : '';
        }

        if (!isValidPhoneNumber(phoneNumber)) {
            return `Sisesta telefoninumber koos riigikoodiga, näiteks ${activeCountry.example}.`;
        }

        return '';
    });
    const resolvedError = $derived(error || internalError);
    const describedBy = $derived(
        [helpText ? helpTextId : '', resolvedError ? errorId : '']
            .filter(Boolean)
            .join(' ') || undefined,
    );

    function syncCanonicalValue(formatInput: boolean): void {
        const phoneNumber = normalizePhoneNumber(
            inputValue,
            countryIso || 'EE',
        );

        value = phoneNumber.canonical;
        countryIso = phoneNumber.countryIso;

        if (formatInput) {
            inputValue = formatPhoneNumber(phoneNumber, showCountryCode);
        }
    }

    function handleInput(): void {
        syncCanonicalValue(false);
    }

    function handleBlur(): void {
        touched = true;
        syncCanonicalValue(true);
    }

    function handleFocus(): void {
        if (!showCountryCode && !inputValue) {
            inputValue = `${activeCountry.dialCode} `;
        }
    }

    function handleCountryChange(event: Event): void {
        const nextCountryIso = (event.currentTarget as HTMLSelectElement).value;
        const currentPhoneNumber = normalizePhoneNumber(
            inputValue,
            countryIso || 'EE',
        );

        countryIso = nextCountryIso;
        touched = false;

        if (!nextCountryIso) {
            return;
        }

        const nextPhoneNumber = normalizePhoneNumber(
            currentPhoneNumber.nationalDigits,
            nextCountryIso,
        );

        value = nextPhoneNumber.canonical;
        inputValue = formatPhoneNumber(nextPhoneNumber, showCountryCode);
    }
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
                <label for={countrySelectId} class="sr-only">Riigikood</label>
                <NativeSelect
                    id={countrySelectId}
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
                    {#each PHONE_COUNTRIES as country (country.iso2)}
                        <NativeSelectOption value={country.iso2}>
                            {country.iso2}
                            {country.dialCode}
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
            bind:value={inputValue}
            oninput={handleInput}
            onblur={handleBlur}
            onfocus={handleFocus}
            aria-invalid={resolvedError ? 'true' : undefined}
            aria-describedby={describedBy}
            placeholder={showCountryCode
                ? activeCountry.example.replace(
                      `${activeCountry.dialCode} `,
                      '',
                  )
                : activeCountry.example}
            class={cn(
                'flex h-11 w-full rounded-md border border-input bg-white px-3 py-1 text-base shadow-none transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-stokker-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background',
                resolvedError &&
                    'border-destructive focus-visible:ring-destructive',
            )}
            {required}
            {disabled}
        />
    </div>

    {#if helpText}
        <p id={helpTextId} class="text-sm text-muted-foreground">
            {helpText}
        </p>
    {/if}

    <div id={errorId}>
        <InputError message={resolvedError} />
    </div>
</div>
