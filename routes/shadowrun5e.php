<?php

declare(strict_types=1);

use App\Http\Controllers\Shadowrun5E\AdeptPowersController;
use App\Http\Controllers\Shadowrun5E\AmmunitionController;
use App\Http\Controllers\Shadowrun5E\ArmorController;
use App\Http\Controllers\Shadowrun5E\ArmorModificationsController;
use App\Http\Controllers\Shadowrun5E\CharactersController;
use App\Http\Controllers\Shadowrun5E\ComplexFormsController;
use App\Http\Controllers\Shadowrun5E\CyberwareController;
use App\Http\Controllers\Shadowrun5E\GearController;
use App\Http\Controllers\Shadowrun5E\GearModificationsController;
use App\Http\Controllers\Shadowrun5E\LifestyleOptionsController;
use App\Http\Controllers\Shadowrun5E\LifestylesController;
use App\Http\Controllers\Shadowrun5E\LifestyleZonesController;
use App\Http\Controllers\Shadowrun5E\MartialArtsStylesController;
use App\Http\Controllers\Shadowrun5E\MartialArtsTechniquesController;
use App\Http\Controllers\Shadowrun5E\MentorSpiritsController;
use App\Http\Controllers\Shadowrun5E\MetamagicsController;
use App\Http\Controllers\Shadowrun5E\ProgramsController;
use App\Http\Controllers\Shadowrun5E\QualitiesController;
use App\Http\Controllers\Shadowrun5E\SkillGroupsController;
use App\Http\Controllers\Shadowrun5E\SkillsController;
use App\Http\Controllers\Shadowrun5E\SpellsController;
use App\Http\Controllers\Shadowrun5E\SpiritsController;
use App\Http\Controllers\Shadowrun5E\SpritesController;
use App\Http\Controllers\Shadowrun5E\TraditionsController;
use App\Http\Controllers\Shadowrun5E\VehicleModificationsController;
use App\Http\Controllers\Shadowrun5E\VehiclesController;
use App\Http\Controllers\Shadowrun5E\WeaponModificationsController;
use App\Http\Controllers\Shadowrun5E\WeaponsController;

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
            ->only(['index', 'show']);
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
