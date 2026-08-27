export type PhoneCountry = {
    iso2: string;
    name: string;
    dialCode: string;
    example: string;
    formatGroups: number[];
    minNationalLength: number;
    maxNationalLength: number;
    trunkPrefix?: string;
};

export type NormalizedPhoneNumber = {
    canonical: string;
    countryIso: string;
    nationalDigits: string;
};

export const PHONE_COUNTRIES: PhoneCountry[] = [
    {
        iso2: 'EE',
        name: 'Eesti',
        dialCode: '+372',
        example: '+372 5123 4567',
        formatGroups: [4, 4],
        minNationalLength: 7,
        maxNationalLength: 8,
    },
    {
        iso2: 'LV',
        name: 'Läti',
        dialCode: '+371',
        example: '+371 20 123 456',
        formatGroups: [2, 3, 3],
        minNationalLength: 8,
        maxNationalLength: 8,
    },
    {
        iso2: 'LT',
        name: 'Leedu',
        dialCode: '+370',
        example: '+370 612 34567',
        formatGroups: [3, 5],
        minNationalLength: 8,
        maxNationalLength: 8,
        trunkPrefix: '0',
    },
    {
        iso2: 'FI',
        name: 'Soome',
        dialCode: '+358',
        example: '+358 40 123 4567',
        formatGroups: [2, 3, 4],
        minNationalLength: 5,
        maxNationalLength: 12,
        trunkPrefix: '0',
    },
    {
        iso2: 'SE',
        name: 'Rootsi',
        dialCode: '+46',
        example: '+46 70 123 45 67',
        formatGroups: [2, 3, 2, 2],
        minNationalLength: 7,
        maxNationalLength: 10,
        trunkPrefix: '0',
    },
];

const fallbackCountry = PHONE_COUNTRIES[0];

function countryForCallingCode(digits: string): PhoneCountry | undefined {
    return [...PHONE_COUNTRIES]
        .sort((first, second) => second.dialCode.length - first.dialCode.length)
        .find((country) => digits.startsWith(country.dialCode.slice(1)));
}

function hasValidNationalLength(
    country: PhoneCountry,
    nationalDigits: string,
): boolean {
    return (
        nationalDigits.length >= country.minNationalLength &&
        nationalDigits.length <= country.maxNationalLength
    );
}

function stripTrunkPrefix(
    nationalDigits: string,
    country: PhoneCountry,
): string {
    if (country.trunkPrefix && nationalDigits.startsWith(country.trunkPrefix)) {
        return nationalDigits.slice(country.trunkPrefix.length);
    }

    return nationalDigits;
}

function formatDigitGroups(digits: string, groups: number[]): string {
    if (!digits) {
        return '';
    }

    const expectedLength = groups.reduce((sum, group) => sum + group, 0);
    const adjustedGroups = [...groups];

    if (digits.length < expectedLength) {
        adjustedGroups[0] = Math.max(
            1,
            adjustedGroups[0] - (expectedLength - digits.length),
        );
    }

    const formattedGroups: string[] = [];
    let offset = 0;

    for (const groupLength of adjustedGroups) {
        const group = digits.slice(offset, offset + groupLength);

        if (!group) {
            break;
        }

        formattedGroups.push(group);
        offset += groupLength;
    }

    if (offset < digits.length) {
        formattedGroups.push(digits.slice(offset));
    }

    return formattedGroups.join(' ');
}

export function getPhoneCountry(countryIso: string): PhoneCountry {
    const normalizedCountryIso = countryIso.trim().toUpperCase();

    return (
        PHONE_COUNTRIES.find(
            (country) => country.iso2 === normalizedCountryIso,
        ) ?? fallbackCountry
    );
}

export function normalizePhoneNumber(
    input: string,
    defaultCountryIso: string,
): NormalizedPhoneNumber {
    const trimmedInput = input.trim();
    const defaultCountry = getPhoneCountry(defaultCountryIso);

    if (!trimmedInput) {
        return {
            canonical: '',
            countryIso: defaultCountry.iso2,
            nationalDigits: '',
        };
    }

    const startsWithInternationalPrefix =
        trimmedInput.startsWith('+') || trimmedInput.startsWith('00');
    let digits = trimmedInput.replace(/\D/g, '');

    if (trimmedInput.startsWith('00')) {
        digits = digits.slice(2);
    }

    let detectedCountry = countryForCallingCode(digits);

    if (detectedCountry && !startsWithInternationalPrefix) {
        const possibleNationalDigits = digits.slice(
            detectedCountry.dialCode.length - 1,
        );

        if (!hasValidNationalLength(detectedCountry, possibleNationalDigits)) {
            detectedCountry = undefined;
        }
    }

    if (startsWithInternationalPrefix || detectedCountry) {
        const nationalDigits = detectedCountry
            ? digits.slice(detectedCountry.dialCode.length - 1)
            : digits;

        return {
            canonical: nationalDigits ? `+${digits}` : '',
            countryIso: detectedCountry?.iso2 ?? '',
            nationalDigits,
        };
    }

    const nationalDigits = stripTrunkPrefix(digits, defaultCountry);

    return {
        canonical: nationalDigits
            ? `${defaultCountry.dialCode}${nationalDigits}`
            : '',
        countryIso: defaultCountry.iso2,
        nationalDigits,
    };
}

export function formatPhoneNumber(
    phoneNumber: NormalizedPhoneNumber,
    separateCountryCode = false,
): string {
    if (!phoneNumber.canonical) {
        return '';
    }

    const country = PHONE_COUNTRIES.find(
        (option) => option.iso2 === phoneNumber.countryIso,
    );

    if (!country) {
        return phoneNumber.canonical;
    }

    const formattedNationalNumber = formatDigitGroups(
        phoneNumber.nationalDigits,
        country.formatGroups,
    );

    return separateCountryCode
        ? formattedNationalNumber
        : `${country.dialCode} ${formattedNationalNumber}`;
}

export function isValidPhoneNumber(
    phoneNumber: NormalizedPhoneNumber,
): boolean {
    if (!phoneNumber.canonical) {
        return false;
    }

    const totalDigits = phoneNumber.canonical.replace(/\D/g, '').length;

    if (totalDigits < 8 || totalDigits > 15) {
        return false;
    }

    const country = PHONE_COUNTRIES.find(
        (option) => option.iso2 === phoneNumber.countryIso,
    );

    return country
        ? hasValidNationalLength(country, phoneNumber.nationalDigits)
        : true;
}
