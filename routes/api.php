<?php

declare(strict_types=1);

use App\Http\Controllers\Shadowrun5E\AdeptPowersController;
use App\Http\Controllers\Shadowrun5E\ComplexFormsController;
use App\Http\Controllers\Shadowrun5E\ProgramsController;
use App\Http\Controllers\Shadowrun5E\SkillsController;
use App\Http\Controllers\Shadowrun5E\SkillGroupsController;
use App\Http\Controllers\Shadowrun5E\TraditionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('shadowrun5e')->name('shadowrun5e.')->group(function () {
    Route::resource('adept-powers', AdeptPowersController::class)
        ->only(['index', 'show']);
    Route::resource('complex-forms', ComplexFormsController::class)
        ->only(['index', 'show']);
    Route::resource('programs', ProgramsController::class)
        ->only(['index', 'show']);
    Route::resource('skills', SkillsController::class)
        ->only(['index', 'show']);
    Route::resource('skill-groups', SkillGroupsController::class)
        ->only(['index', 'show']);
    Route::resource('traditions', TraditionsController::class)
        ->only(['index', 'show']);
});
