<?php

return [
    'allowed_roots' => [
        'app',
        'config',
        'resources/js',
        'routes',
        'tests',
    ],

    'allowed_extensions' => [
        'css',
        'js',
        'json',
        'php',
        'svelte',
        'ts',
    ],

    'allowed_languages' => [
        'css',
        'javascript',
        'json',
        'php',
        'svelte',
        'text',
        'typescript',
    ],

    'max_file_size' => 512 * 1024,

    'source_sets' => [
        'phone-field' => [
            'title' => 'Telefonivälja lähtekood',
            'files' => [
                'demo-page' => [
                    'label' => 'resources/js/pages/demos/PhoneField.svelte',
                    'language' => 'svelte',
                    'path' => 'resources/js/pages/demos/PhoneField.svelte',
                ],
                'component' => [
                    'label' => 'resources/js/components/PhoneField.svelte',
                    'language' => 'svelte',
                    'path' => 'resources/js/components/PhoneField.svelte',
                ],
                'field-state' => [
                    'label' => 'resources/js/lib/phone-field.svelte.ts',
                    'language' => 'typescript',
                    'path' => 'resources/js/lib/phone-field.svelte.ts',
                ],
                'phone-helpers' => [
                    'label' => 'resources/js/lib/phone.ts',
                    'language' => 'typescript',
                    'path' => 'resources/js/lib/phone.ts',
                ],
                'phone-config' => [
                    'label' => 'config/phone.php',
                    'language' => 'php',
                    'path' => 'config/phone.php',
                ],
                'normalization-test' => [
                    'label' => 'tests/Unit/PhoneNormalizationTest.php',
                    'language' => 'php',
                    'path' => 'tests/Unit/PhoneNormalizationTest.php',
                ],
            ],
        ],
    ],
];
