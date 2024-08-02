<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
    ->prefix('shadowrunanarchy')
    ->name('shadowrunanarch.')
    ->group(function (): void {
    });
