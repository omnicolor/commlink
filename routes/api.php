<?php

declare(strict_types=1);

use App\Http\Controllers\DiceRollerController;
use App\Http\Controllers\Shadowrun5E\AdeptPowersController;
use App\Http\Controllers\Shadowrun5E\ArmorController;
use App\Http\Controllers\Shadowrun5E\ArmorModificationsController;
use App\Http\Controllers\Shadowrun5E\CharactersController;
use App\Http\Controllers\Shadowrun5E\ComplexFormsController;
use App\Http\Controllers\Shadowrun5E\CyberwareController;
use App\Http\Controllers\Shadowrun5E\GearModificationsController;
use App\Http\Controllers\Shadowrun5E\ProgramsController;
use App\Http\Controllers\Shadowrun5E\QualitiesController;
use App\Http\Controllers\Shadowrun5E\SkillsController;
use App\Http\Controllers\Shadowrun5E\SkillGroupsController;
use App\Http\Controllers\Shadowrun5E\SpritesController;
use App\Http\Controllers\Shadowrun5E\TraditionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::options('/roll', [DiceRollerController::class, 'options'])
    ->name('roll-options');
Route::post('/roll', [DiceRollerController::class, 'post'])->name('roll');

Route::middleware('auth:sanctum')->prefix('shadowrun5e')->name('shadowrun5e.')->group(function () {
    Route::resource('adept-powers', AdeptPowersController::class)
        ->only(['index', 'show']);
    Route::resource('armor', ArmorController::class)
        ->only(['index', 'show']);
    Route::resource('armor-modifications', ArmorModificationsController::class)
        ->only(['index', 'show']);
    Route::resource('characters', CharactersController::class)
        ->only(['index', 'show']);
    Route::resource('complex-forms', ComplexFormsController::class)
        ->only(['index', 'show']);
    Route::resource('cyberware', CyberwareController::class)
        ->only(['index', 'show']);
    Route::resource('gear-modifications', GearModificationsController::class)
        ->only(['index', 'show']);
    Route::resource('programs', ProgramsController::class)
        ->only(['index', 'show']);
    Route::resource('qualities', QualitiesController::class)
        ->only(['index', 'show']);
    Route::resource('skills', SkillsController::class)
        ->only(['index', 'show']);
    Route::resource('skill-groups', SkillGroupsController::class)
        ->only(['index', 'show']);
    Route::resource('sprites', SpritesController::class)
        ->only(['index', 'show']);
    Route::resource('traditions', TraditionsController::class)
        ->only(['index', 'show']);
});
