<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
    ->prefix('transformers')
    ->name('transformers.')
    ->group(
        function (): void {
        }
    );
