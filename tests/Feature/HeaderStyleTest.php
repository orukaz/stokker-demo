<?php

test('site header product categories control uses the source reference color', function () {
    $siteCss = file_get_contents(resource_path('css/site.css'));
    $layout = file_get_contents(resource_path('js/layouts/SiteLayout.svelte'));

    expect($siteCss)
        ->toContain('--color-stokker-header-action: #1484c7;')
        ->and($layout)
        ->toContain('text-stokker-header-action')
        ->toContain('Tootekategooriad');
});

test('site header account action changes when authenticated', function () {
    $layout = file_get_contents(resource_path('js/layouts/SiteLayout.svelte'));

    expect($layout)
        ->toContain('const isAuthenticated = $derived(Boolean(page.props.auth.user));')
        ->toContain('const accountRoute = $derived(isAuthenticated ? profileEdit() : login());')
        ->toContain("const accountLabel = \$derived(isAuthenticated ? 'Minu konto' : 'Sisenen');")
        ->toContain('href={toUrl(accountRoute)}')
        ->toContain('aria-label={accountLabel}');
});
