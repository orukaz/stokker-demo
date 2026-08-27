<?php

use Symfony\Component\Process\Process;

test('phone numbers are normalized for supported countries', function () {
    $script = <<<'JS'
        const {
            PHONE_COUNTRIES,
            formatPhoneNumber,
            hasValidPhoneInputCharacters,
            normalizePhoneNumber,
            orderPhoneCountries,
        } = await import('./resources/js/lib/phone.ts');

        const estonianPhoneNumber = normalizePhoneNumber('51234567', 'EE');

        console.log(JSON.stringify({
            eeLocal: estonianPhoneNumber,
            fiWithTrunkPrefix: normalizePhoneNumber('0401234567', 'FI'),
            international: normalizePhoneNumber('+447700900123', 'EE'),
            unsupportedLocal: normalizePhoneNumber('07700900123', 'GB'),
            formattedSeparate: formatPhoneNumber(estonianPhoneNumber, true),
            formattedInline: formatPhoneNumber(estonianPhoneNumber),
            formattedCharactersAreValid: hasValidPhoneInputCharacters('+372 5123-4567'),
            lettersAreValid: hasValidPhoneInputCharacters('abc51234567'),
            lettersAreNormalized: normalizePhoneNumber('abc51234567', 'EE'),
            hasAllCountries: PHONE_COUNTRIES.length > 240,
            preferredCountries: orderPhoneCountries(['EE', 'LV', 'LT', 'FI', 'SE'])
                .slice(0, 5)
                .map((country) => country.iso2),
        }));
        JS;
    $process = new Process([
        'node',
        '--experimental-strip-types',
        '--input-type=module',
        '--eval',
        $script,
    ], base_path());

    $process->mustRun();

    $result = json_decode(
        trim($process->getOutput()),
        true,
        flags: JSON_THROW_ON_ERROR,
    );

    expect($result)->toBe([
        'eeLocal' => [
            'canonical' => '+37251234567',
            'countryIso' => 'EE',
            'nationalDigits' => '51234567',
        ],
        'fiWithTrunkPrefix' => [
            'canonical' => '+358401234567',
            'countryIso' => 'FI',
            'nationalDigits' => '401234567',
        ],
        'international' => [
            'canonical' => '+447700900123',
            'countryIso' => '',
            'nationalDigits' => '7700900123',
        ],
        'unsupportedLocal' => [
            'canonical' => '+447700900123',
            'countryIso' => 'GB',
            'nationalDigits' => '7700900123',
        ],
        'formattedSeparate' => '5123 4567',
        'formattedInline' => '+372 5123 4567',
        'formattedCharactersAreValid' => true,
        'lettersAreValid' => false,
        'lettersAreNormalized' => [
            'canonical' => '',
            'countryIso' => 'EE',
            'nationalDigits' => '',
        ],
        'hasAllCountries' => true,
        'preferredCountries' => ['EE', 'LV', 'LT', 'FI', 'SE'],
    ]);
});
