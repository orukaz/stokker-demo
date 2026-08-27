import parsePhoneNumber, {
    AsYouType,
    getCountries,
    getCountryCallingCode,
    getExampleNumber,
    isSupportedCountry,
} from 'libphonenumber-js';
import type { CountryCode } from 'libphonenumber-js';
import phoneExamples from 'libphonenumber-js/mobile/examples';

export type PhoneCountry = {
    iso2: CountryCode;
    name: string;
    dialCode: string;
    example: string;
};

export type NormalizedPhoneNumber = {
    canonical: string;
    countryIso: string;
    nationalDigits: string;
};

const countryDisplayNames = new Intl.DisplayNames(['et'], {
    type: 'region',
});

function getCountryName(countryIso: CountryCode): string {
    const countryName = countryDisplayNames.of(countryIso);

    return countryName && countryName !== countryIso ? countryName : countryIso;
}

export const PHONE_COUNTRIES: PhoneCountry[] = getCountries()
    .map((countryIso) => {
        const dialCode = `+${getCountryCallingCode(countryIso)}`;
        const example = getExampleNumber(countryIso, phoneExamples);

        return {
            iso2: countryIso,
            name: getCountryName(countryIso),
            dialCode,
            example: example?.formatInternational() ?? dialCode,
        };
    })
    .sort((first, second) => first.name.localeCompare(second.name, 'et'));

const fallbackCountry = PHONE_COUNTRIES.find(
    (country) => country.iso2 === 'EE',
) as PhoneCountry;

export function hasValidPhoneInputCharacters(input: string): boolean {
    return /^\+?[\d\s().-]*$/.test(input.trim());
}

export function findPhoneCountry(countryIso: string): PhoneCountry | undefined {
    const normalizedCountryIso = countryIso.trim().toUpperCase();

    return PHONE_COUNTRIES.find(
        (country) => country.iso2 === normalizedCountryIso,
    );
}

export function getPhoneCountry(countryIso: string): PhoneCountry {
    return findPhoneCountry(countryIso) ?? fallbackCountry;
}

export function getPhoneCountries(countryIsos: string[]): PhoneCountry[] {
    return countryIsos
        .map(findPhoneCountry)
        .filter((country): country is PhoneCountry => country !== undefined);
}

export function orderPhoneCountries(
    preferredCountryIsos: string[],
): PhoneCountry[] {
    const preferredCountries = getPhoneCountries(preferredCountryIsos);
    const preferredCountrySet = new Set(
        preferredCountries.map((country) => country.iso2),
    );

    return [
        ...preferredCountries,
        ...PHONE_COUNTRIES.filter(
            (country) => !preferredCountrySet.has(country.iso2),
        ),
    ];
}

export function normalizePhoneNumber(
    input: string,
    defaultCountryIso: string,
): NormalizedPhoneNumber {
    const trimmedInput = input.trim();
    const normalizedDefaultCountryIso = defaultCountryIso.trim().toUpperCase();
    const defaultCountry = isSupportedCountry(normalizedDefaultCountryIso)
        ? normalizedDefaultCountryIso
        : undefined;

    if (!trimmedInput) {
        return {
            canonical: '',
            countryIso: defaultCountry ?? normalizedDefaultCountryIso,
            nationalDigits: '',
        };
    }

    if (!hasValidPhoneInputCharacters(trimmedInput)) {
        return {
            canonical: '',
            countryIso: defaultCountry ?? normalizedDefaultCountryIso,
            nationalDigits: '',
        };
    }

    const normalizedInput = trimmedInput.startsWith('00')
        ? `+${trimmedInput.slice(2)}`
        : trimmedInput;
    const isInternational = normalizedInput.startsWith('+');
    const phoneNumber = parsePhoneNumber(normalizedInput, {
        defaultCountry,
        extract: false,
    });

    if (!phoneNumber) {
        return {
            canonical: '',
            countryIso: isInternational ? '' : (defaultCountry ?? ''),
            nationalDigits: normalizedInput.replace(/\D/g, ''),
        };
    }

    return {
        canonical: phoneNumber.number,
        countryIso:
            phoneNumber.country ??
            (isInternational ? '' : (defaultCountry ?? '')),
        nationalDigits: phoneNumber.nationalNumber,
    };
}

export function formatPhoneNumber(
    phoneNumber: NormalizedPhoneNumber,
    separateCountryCode = false,
): string {
    if (!phoneNumber.canonical) {
        return '';
    }

    const formattedPhoneNumber = new AsYouType().input(phoneNumber.canonical);

    if (!separateCountryCode) {
        return formattedPhoneNumber;
    }

    const country = findPhoneCountry(phoneNumber.countryIso);

    if (!country) {
        return formattedPhoneNumber;
    }

    return formattedPhoneNumber
        .replace(new RegExp(`^\\${country.dialCode}\\s?`), '')
        .trimStart();
}

export function isValidPhoneNumber(
    phoneNumber: NormalizedPhoneNumber,
): boolean {
    if (!phoneNumber.canonical) {
        return false;
    }

    return (
        parsePhoneNumber(phoneNumber.canonical, {
            extract: false,
        })?.isPossible() ?? false
    );
}
