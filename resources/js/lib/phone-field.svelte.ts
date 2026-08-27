import {
    formatPhoneNumber,
    getPhoneCountry,
    hasValidPhoneInputCharacters,
    isValidPhoneNumber,
    normalizePhoneNumber,
} from '@/lib/phone';
import type { NormalizedPhoneNumber } from '@/lib/phone';

type PhoneFieldStateOptions = {
    getId: () => string;
    getValue: () => string;
    setValue: (value: string) => void;
    getCountryIso: () => string;
    setCountryIso: (countryIso: string) => void;
    getShowCountryCode: () => boolean;
    getRequired: () => boolean;
    getError: () => string;
    getHelpText: () => string;
};

type PhoneFieldValues = {
    inputValue: string;
    touched: boolean;
};

export type PhoneFieldState = {
    values: PhoneFieldValues;
    readonly countrySelectId: string;
    readonly helpTextId: string;
    readonly errorId: string;
    readonly resolvedError: string;
    readonly describedBy: string | undefined;
    readonly placeholder: string;
    handleInput: () => void;
    handleBlur: () => void;
    handleCountryChange: (countryIso: string) => void;
    syncExternalValue: (
        value: string,
        countryIso: string,
        showCountryCode: boolean,
    ) => void;
};

export function phoneFieldState(
    options: PhoneFieldStateOptions,
): PhoneFieldState {
    const initialPhoneNumber = normalizePhoneNumber(
        options.getValue(),
        options.getCountryIso(),
    );
    const values = $state<PhoneFieldValues>({
        inputValue: formatPhoneNumber(
            initialPhoneNumber,
            options.getShowCountryCode(),
        ),
        touched: false,
    });
    let lastPublishedValue = initialPhoneNumber.canonical;
    let lastPublishedCountryIso = initialPhoneNumber.countryIso;
    let lastShowCountryCode = options.getShowCountryCode();

    options.setValue(initialPhoneNumber.canonical);
    options.setCountryIso(initialPhoneNumber.countryIso);

    const activeCountry = $derived.by(() =>
        getPhoneCountry(options.getCountryIso() || 'EE'),
    );
    const countrySelectId = $derived(`${options.getId()}-country`);
    const helpTextId = $derived(`${options.getId()}-help`);
    const errorId = $derived(`${options.getId()}-error`);
    const internalError = $derived.by(() => {
        if (!values.touched || options.getError()) {
            return '';
        }

        if (!hasValidPhoneInputCharacters(values.inputValue)) {
            return `Sisesta kehtiv telefoninumber, näiteks ${activeCountry.example}.`;
        }

        const phoneNumber = normalizePhoneNumber(
            values.inputValue,
            options.getCountryIso(),
        );

        if (!phoneNumber.nationalDigits) {
            return options.getRequired() ? 'Sisesta telefoninumber.' : '';
        }

        if (!isValidPhoneNumber(phoneNumber)) {
            return `Sisesta telefoninumber koos riigikoodiga, näiteks ${activeCountry.example}.`;
        }

        return '';
    });
    const resolvedError = $derived(options.getError() || internalError);
    const describedBy = $derived(
        [options.getHelpText() ? helpTextId : '', resolvedError ? errorId : '']
            .filter(Boolean)
            .join(' ') || undefined,
    );
    const placeholder = $derived.by(() =>
        options.getShowCountryCode()
            ? activeCountry.example.replace(`${activeCountry.dialCode} `, '')
            : activeCountry.example,
    );

    function publishPhoneNumber(
        phoneNumber: NormalizedPhoneNumber,
        formatInput: boolean,
    ): void {
        lastPublishedValue = phoneNumber.canonical;
        lastPublishedCountryIso = phoneNumber.countryIso;
        lastShowCountryCode = options.getShowCountryCode();
        options.setValue(phoneNumber.canonical);
        options.setCountryIso(phoneNumber.countryIso);

        if (formatInput) {
            values.inputValue = formatPhoneNumber(
                phoneNumber,
                options.getShowCountryCode(),
            );
        }
    }

    function syncCanonicalValue(formatInput: boolean): void {
        if (!hasValidPhoneInputCharacters(values.inputValue)) {
            lastPublishedValue = '';
            lastPublishedCountryIso = options.getCountryIso();
            options.setValue('');

            return;
        }

        publishPhoneNumber(
            normalizePhoneNumber(values.inputValue, options.getCountryIso()),
            formatInput,
        );
    }

    function handleInput(): void {
        syncCanonicalValue(false);
    }

    function handleBlur(): void {
        values.touched = true;
        syncCanonicalValue(true);
    }

    function handleCountryChange(nextCountryIso: string): void {
        const currentPhoneNumber = normalizePhoneNumber(
            values.inputValue,
            options.getCountryIso(),
        );

        options.setCountryIso(nextCountryIso);
        values.touched = false;

        if (!nextCountryIso) {
            lastPublishedCountryIso = '';

            return;
        }

        publishPhoneNumber(
            normalizePhoneNumber(
                currentPhoneNumber.nationalDigits,
                nextCountryIso,
            ),
            true,
        );
    }

    function syncExternalValue(
        nextValue: string,
        nextCountryIso: string,
        nextShowCountryCode: boolean,
    ): void {
        const normalizedCountryIso = nextCountryIso.trim().toUpperCase();

        if (
            nextValue === lastPublishedValue &&
            normalizedCountryIso === lastPublishedCountryIso &&
            nextShowCountryCode === lastShowCountryCode
        ) {
            return;
        }

        const phoneNumber = normalizePhoneNumber(
            nextValue,
            normalizedCountryIso,
        );

        lastPublishedValue = phoneNumber.canonical;
        lastPublishedCountryIso = phoneNumber.countryIso;
        lastShowCountryCode = nextShowCountryCode;
        options.setValue(phoneNumber.canonical);
        options.setCountryIso(phoneNumber.countryIso);
        values.inputValue = formatPhoneNumber(phoneNumber, nextShowCountryCode);
        values.touched = false;
    }

    return {
        values,
        get countrySelectId() {
            return countrySelectId;
        },
        get helpTextId() {
            return helpTextId;
        },
        get errorId() {
            return errorId;
        },
        get resolvedError() {
            return resolvedError;
        },
        get describedBy() {
            return describedBy;
        },
        get placeholder() {
            return placeholder;
        },
        handleInput,
        handleBlur,
        handleCountryChange,
        syncExternalValue,
    };
}
