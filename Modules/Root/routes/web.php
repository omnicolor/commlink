<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('characters/root')
    ->name('root.')
    ->group(function (): void {
    });
