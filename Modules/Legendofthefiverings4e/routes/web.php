<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('characters/legendofthefiverings4e')
    ->name('legendofthefiverings4e.')
    ->group(function (): void {
    });
