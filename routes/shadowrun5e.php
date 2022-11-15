<?php

declare(strict_types=1);

use App\Http\Controllers\Shadowrun5e\AdeptPowersController;
use App\Http\Controllers\Shadowrun5e\AmmunitionController;
use App\Http\Controllers\Shadowrun5e\ArmorController;
use App\Http\Controllers\Shadowrun5e\ArmorModificationsController;
use App\Http\Controllers\Shadowrun5e\CharactersController;
use App\Http\Controllers\Shadowrun5e\ComplexFormsController;
use App\Http\Controllers\Shadowrun5e\CyberwareController;
use App\Http\Controllers\Shadowrun5e\GearController;
use App\Http\Controllers\Shadowrun5e\GearModificationsController;
use App\Http\Controllers\Shadowrun5e\LifestyleOptionsController;
use App\Http\Controllers\Shadowrun5e\LifestylesController;
use App\Http\Controllers\Shadowrun5e\LifestyleZonesController;
use App\Http\Controllers\Shadowrun5e\MartialArtsStylesController;
use App\Http\Controllers\Shadowrun5e\MartialArtsTechniquesController;
use App\Http\Controllers\Shadowrun5e\MentorSpiritsController;
use App\Http\Controllers\Shadowrun5e\MetamagicsController;
use App\Http\Controllers\Shadowrun5e\ProgramsController;
use App\Http\Controllers\Shadowrun5e\QualitiesController;
use App\Http\Controllers\Shadowrun5e\SkillGroupsController;
use App\Http\Controllers\Shadowrun5e\SkillsController;
use App\Http\Controllers\Shadowrun5e\SpellsController;
use App\Http\Controllers\Shadowrun5e\SpiritsController;
use App\Http\Controllers\Shadowrun5e\SpritesController;
use App\Http\Controllers\Shadowrun5e\TraditionsController;
use App\Http\Controllers\Shadowrun5e\VehicleModificationsController;
use App\Http\Controllers\Shadowrun5e\VehiclesController;
use App\Http\Controllers\Shadowrun5e\WeaponModificationsController;
use App\Http\Controllers\Shadowrun5e\WeaponsController;

Route::middleware('auth:sanctum')->prefix('shadowrun5e')->name('shadowrun5e.')->group(
    function (): void {
        Route::resource('adept-powers', AdeptPowersController::class)
            ->only(['index', 'show']);
        Route::resource('ammunition', AmmunitionController::class)
            ->only(['index', 'show']);
        Route::resource('armor', ArmorController::class)
            ->only(['index', 'show']);
        Route::resource('armor-modifications', ArmorModificationsController::class)
            ->only(['index', 'show']);
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show', 'update']);
        Route::resource('complex-forms', ComplexFormsController::class)
            ->only(['index', 'show']);
        Route::resource('cyberware', CyberwareController::class)
            ->only(['index', 'show']);
        Route::resource('gear', GearController::class)
            ->only(['index', 'show']);
        Route::resource('gear-modifications', GearModificationsController::class)
            ->only(['index', 'show']);
        Route::resource('lifestyles', LifestylesController::class)
            ->only(['index', 'show']);
        Route::resource('lifestyle-options', LifestyleOptionsController::class)
            ->only(['index', 'show']);
        Route::resource('lifestyle-zones', LifestyleZonesController::class)
            ->only(['index', 'show']);
        Route::resource('martial-arts-styles', MartialArtsStylesController::class)
            ->only(['index', 'show']);
        Route::resource('martial-arts-techniques', MartialArtsTechniquesController::class)
            ->only(['index', 'show']);
        Route::resource('mentor-spirits', MentorSpiritsController::class)
            ->only(['index', 'show']);
        Route::resource('metamagics', MetamagicsController::class)
            ->only(['index', 'show']);
        Route::resource('programs', ProgramsController::class)
            ->only(['index', 'show']);
        Route::resource('qualities', QualitiesController::class)
            ->only(['index', 'show']);
        Route::resource('skills', SkillsController::class)
            ->only(['index', 'show']);
        Route::resource('skill-groups', SkillGroupsController::class)
            ->only(['index', 'show']);
        Route::resource('spells', SpellsController::class)
            ->only(['index', 'show']);
        Route::resource('spirits', SpiritsController::class)
            ->only(['index', 'show']);
        Route::resource('sprites', SpritesController::class)
            ->only(['index', 'show']);
        Route::resource('traditions', TraditionsController::class)
            ->only(['index', 'show']);
        Route::resource('vehicles', VehiclesController::class)
            ->only(['index', 'show']);
        Route::resource('vehicle-modifications', VehicleModificationsController::class)
            ->only(['index', 'show']);
        Route::resource('weapons', WeaponsController::class)
            ->only(['index', 'show']);
        Route::resource('weapon-modifications', WeaponModificationsController::class)
            ->only(['index', 'show']);
    }
);
