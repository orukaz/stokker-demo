<?php

test('a DEV-160 demo page is available', function (
    string $routeName,
    string $asset,
    string $title,
    string $interactiveElement,
) {
    $this->get(route($routeName))
        ->assertSuccessful()
        ->assertHeader('content-type', 'text/html; charset=UTF-8');

    expect(file_get_contents(public_path($asset)))
        ->toContain($title)
        ->toContain($interactiveElement);
})->with([
    'discount reason modal' => [
        'demos.dev_160.discount_reason_modal',
        'dev-160-discount-reason-modal.html',
        'DEV-160 – allahindluse põhjendamine',
        'id="discount-form"',
    ],
    'discount reason dropdown' => [
        'demos.dev_160.discount_reason_dropdown',
        'dev-160-discount-reason-dropdown.html',
        'DEV-160 – allahindluse põhjenduse dropdown',
        'id="reason-form"',
    ],
    'discount report' => [
        'demos.dev_160.discount_report',
        'dev-160-discount-report.html',
        'DEV-160 – põhjendatud allahindluste raport',
        'id="report-body"',
    ],
]);
