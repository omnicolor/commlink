<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('characters/shadowrunanarchy')
    ->name('shadowrunanarchy.')
    ->group(function (): void {
    });
