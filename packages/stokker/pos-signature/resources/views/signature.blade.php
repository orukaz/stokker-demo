<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Stokker') }}</title>

        @vite('packages/stokker/pos-signature/resources/js/app.ts')
    </head>
    <body>
        <div id="pos-signature-app"></div>
    </body>
</html>
