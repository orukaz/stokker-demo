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
        'saved-filters' => [
            'title' => 'Salvestatud filtrite lähtekood',
            'files' => [
                'demo-page' => [
                    'label' => 'resources/js/pages/demos/SavedFilters.svelte',
                    'language' => 'svelte',
                    'path' => 'resources/js/pages/demos/SavedFilters.svelte',
                ],
                'controller' => [
                    'label' => 'app/Http/Controllers/SavedFilterController.php',
                    'language' => 'php',
                    'path' => 'app/Http/Controllers/SavedFilterController.php',
                ],
                'model' => [
                    'label' => 'app/Models/SavedFilter.php',
                    'language' => 'php',
                    'path' => 'app/Models/SavedFilter.php',
                ],
                'store-request' => [
                    'label' => 'app/Http/Requests/StoreSavedFilterRequest.php',
                    'language' => 'php',
                    'path' => 'app/Http/Requests/StoreSavedFilterRequest.php',
                ],
                'feature-test' => [
                    'label' => 'tests/Feature/Dev238SavedFilterTest.php',
                    'language' => 'php',
                    'path' => 'tests/Feature/Dev238SavedFilterTest.php',
                ],
            ],
        ],
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
