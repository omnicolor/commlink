<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Expanse\Http\Controllers\CharactersController;

Route::get(
    '/characters/expanse/{character}',
    [CharactersController::class, 'view']
)->name('expanse.character');
