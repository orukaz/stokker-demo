<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import PhoneField from '@/components/PhoneField.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Label } from '@/components/ui/label';
    import {
        NativeSelect,
        NativeSelectOption,
    } from '@/components/ui/native-select';
    import SiteLayout from '@/layouts/SiteLayout.svelte';
    import { PHONE_COUNTRIES } from '@/lib/phone';

    let defaultCountryIso = $state('EE');
    let defaultPhoneValue = $state('51234567');
    let separateCountryIso = $state('EE');
    let separatePhoneValue = $state('51234567');
    let inlineCountryIso = $state('EE');
    let inlinePhoneValue = $state('51234567');
    let demoRevision = $state(0);

    function applyDefaults(event: SubmitEvent): void {
        event.preventDefault();

        separateCountryIso = defaultCountryIso;
        separatePhoneValue = defaultPhoneValue;
        inlineCountryIso = defaultCountryIso;
        inlinePhoneValue = defaultPhoneValue;
        demoRevision += 1;
    }
</script>

<AppHead title="DEV-193 telefoninumbri komponent">
    <meta
        name="description"
        content="Telefoninumbri komponendi demo riigikoodi, ISO-2 vaikeväärtuse ja E.164 normaliseerimisega."
    />
</AppHead>

<SiteLayout>
    <div class="mx-auto flex max-w-4xl flex-col gap-6 py-4 sm:py-8">
        <header class="flex flex-col gap-1">
            <a
                href="https://stokker-team-ojuicoeqcvdn.atlassian.net/browse/DEV-193"
                target="_blank"
                rel="noopener noreferrer"
                class="w-fit text-sm font-semibold text-stokker-primary underline-offset-4 hover:underline focus-visible:rounded-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stokker-primary"
            >
                DEV-193
            </a>
            <h1 class="text-3xl font-semibold tracking-tight">
                Telefonivälja demo
            </h1>
        </header>

        <Card>
            <CardHeader>
                <CardTitle>Vaikeväärtused</CardTitle>
            </CardHeader>
            <CardContent>
                <form
                    class="grid gap-4 md:grid-cols-[12rem_1fr_auto] md:items-end"
                    onsubmit={applyDefaults}
                >
                    <div class="grid gap-2">
                        <Label for="default-country-iso"
                            >Vaikeriik (ISO-2)</Label
                        >
                        <NativeSelect
                            id="default-country-iso"
                            name="default_country_iso"
                            bind:value={defaultCountryIso}
                            class="w-full [&_[data-slot=native-select]]:h-11 [&_[data-slot=native-select]]:bg-white [&_[data-slot=native-select]]:text-base dark:[&_[data-slot=native-select]]:bg-background"
                        >
                            {#each PHONE_COUNTRIES as country (country.iso2)}
                                <NativeSelectOption value={country.iso2}>
                                    {country.iso2} — {country.name}
                                </NativeSelectOption>
                            {/each}
                        </NativeSelect>
                    </div>

                    <div class="grid gap-2">
                        <Label for="default-phone-value">
                            Telefoni vaikeväärtus
                        </Label>
                        <input
                            id="default-phone-value"
                            name="default_phone_value"
                            type="tel"
                            bind:value={defaultPhoneValue}
                            class="flex h-11 w-full rounded-md border border-input bg-white px-3 py-1 text-base shadow-none transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-stokker-primary focus-visible:ring-offset-2 dark:bg-background"
                            placeholder="51234567 või +372 5123 4567"
                        />
                    </div>

                    <Button
                        type="submit"
                        size="lg"
                        class="h-11 bg-stokker-primary px-5 text-white hover:bg-stokker-primary-dark"
                    >
                        Rakenda
                    </Button>
                </form>
            </CardContent>
        </Card>

        <section class="grid gap-6 lg:grid-cols-2">
            <Card class="border-stokker-primary/30">
                <CardHeader>
                    <div class="flex items-center justify-between gap-3">
                        <CardTitle>Riigikood eraldi</CardTitle>
                        <span
                            class="rounded-full bg-stokker-primary px-2.5 py-1 text-xs font-semibold text-white"
                        >
                            Soovitatud
                        </span>
                    </div>
                </CardHeader>
                <CardContent class="grid gap-5">
                    {#key `${demoRevision}-separate`}
                        <PhoneField
                            id="phone-with-country-code"
                            name="phone_with_country_code"
                            label="Telefoninumber"
                            bind:value={separatePhoneValue}
                            bind:countryIso={separateCountryIso}
                            showCountryCode
                            required
                        />
                    {/key}

                    <div
                        class="flex items-center justify-between gap-4 rounded-lg bg-stokker-secondary p-4 text-sm"
                        aria-live="polite"
                    >
                        <span class="font-medium text-muted-foreground">
                            Salvestatav väärtus
                        </span>
                        <code class="break-all text-base font-semibold">
                            {separatePhoneValue || '—'}
                        </code>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Riigikood samas lahtris</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-5">
                    {#key `${demoRevision}-inline`}
                        <PhoneField
                            id="phone-with-inline-code"
                            name="phone_with_inline_code"
                            label="Telefoninumber"
                            bind:value={inlinePhoneValue}
                            bind:countryIso={inlineCountryIso}
                            showCountryCode={false}
                            required
                        />
                    {/key}

                    <div
                        class="flex items-center justify-between gap-4 rounded-lg bg-muted p-4 text-sm"
                        aria-live="polite"
                    >
                        <span class="font-medium text-muted-foreground">
                            Salvestatav väärtus
                        </span>
                        <code class="break-all text-base font-semibold">
                            {inlinePhoneValue || '—'}
                        </code>
                    </div>
                </CardContent>
            </Card>
        </section>
    </div>
</SiteLayout>
