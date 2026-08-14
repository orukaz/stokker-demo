<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::view('/pos/signature', 'pos-signature::signature')
        ->name('pos-signature.show');
});
