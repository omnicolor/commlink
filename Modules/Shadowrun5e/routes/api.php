<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Shadowrun5e\Http\Controllers\AdeptPowersController;
use Modules\Shadowrun5e\Http\Controllers\AmmunitionController;
use Modules\Shadowrun5e\Http\Controllers\ArmorController;
use Modules\Shadowrun5e\Http\Controllers\ArmorModificationsController;
use Modules\Shadowrun5e\Http\Controllers\CharactersController;
use Modules\Shadowrun5e\Http\Controllers\ComplexFormsController;
use Modules\Shadowrun5e\Http\Controllers\ContactsController;
use Modules\Shadowrun5e\Http\Controllers\CritterPowersController;
use Modules\Shadowrun5e\Http\Controllers\CritterWeaknessesController;
use Modules\Shadowrun5e\Http\Controllers\CrittersController;
use Modules\Shadowrun5e\Http\Controllers\CyberwareController;
use Modules\Shadowrun5e\Http\Controllers\GearController;
use Modules\Shadowrun5e\Http\Controllers\GearModificationsController;
use Modules\Shadowrun5e\Http\Controllers\GruntsController;
use Modules\Shadowrun5e\Http\Controllers\IntrusionCountermeasuresController;
use Modules\Shadowrun5e\Http\Controllers\LifestyleOptionsController;
use Modules\Shadowrun5e\Http\Controllers\LifestyleZonesController;
use Modules\Shadowrun5e\Http\Controllers\LifestylesController;
use Modules\Shadowrun5e\Http\Controllers\MartialArtsStylesController;
use Modules\Shadowrun5e\Http\Controllers\MartialArtsTechniquesController;
use Modules\Shadowrun5e\Http\Controllers\MentorSpiritsController;
use Modules\Shadowrun5e\Http\Controllers\MetamagicsController;
use Modules\Shadowrun5e\Http\Controllers\ProgramsController;
use Modules\Shadowrun5e\Http\Controllers\QualitiesController;
use Modules\Shadowrun5e\Http\Controllers\ResonanceEchoesController;
use Modules\Shadowrun5e\Http\Controllers\RulebooksController;
use Modules\Shadowrun5e\Http\Controllers\SkillGroupsController;
use Modules\Shadowrun5e\Http\Controllers\SkillsController;
use Modules\Shadowrun5e\Http\Controllers\SpellsController;
use Modules\Shadowrun5e\Http\Controllers\SpiritsController;
use Modules\Shadowrun5e\Http\Controllers\SpritesController;
use Modules\Shadowrun5e\Http\Controllers\TraditionsController;
use Modules\Shadowrun5e\Http\Controllers\VehicleModificationsController;
use Modules\Shadowrun5e\Http\Controllers\VehiclesController;
use Modules\Shadowrun5e\Http\Resources\WeaponModificationResource;
use Modules\Shadowrun5e\Http\Resources\WeaponResource;
use Modules\Shadowrun5e\Models\Weapon;
use Modules\Shadowrun5e\Models\WeaponModification;

Route::middleware('auth:sanctum')
    ->prefix('shadowrun5e')
    ->name('shadowrun5e.')->group(function (): void {
        Route::resource('adept-powers', AdeptPowersController::class)
            ->only(['index', 'show']);
        Route::resource('ammunitions', AmmunitionController::class)
            ->only(['index', 'show']);
        Route::resource('armor', ArmorController::class)
            ->only(['index', 'show']);
        Route::resource('armor-modifications', ArmorModificationsController::class)
            ->only(['index', 'show']);
        Route::resource('characters/{character}/contacts', ContactsController::class)
            ->only(['index', 'store']);
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show', 'update']);
        Route::resource('complex-forms', ComplexFormsController::class)
            ->only(['index', 'show']);
        Route::resource('critter-powers', CritterPowersController::class)
            ->only(['index', 'show']);
        Route::resource('critter-weaknesses', CritterWeaknessesController::class)
            ->only(['index', 'show']);
        Route::resource('critters', CrittersController::class)
            ->only(['index', 'show']);
        Route::resource('cyberware', CyberwareController::class)
            ->only(['index', 'show']);
        Route::resource('gear', GearController::class)
            ->only(['index', 'show']);
        Route::resource('gear-modifications', GearModificationsController::class)
            ->only(['index', 'show']);
        Route::resource('grunts', GruntsController::class)
            ->only(['index', 'show']);
        Route::resource('intrusion-countermeasures', IntrusionCountermeasuresController::class)
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
        Route::resource('resonance-echoes', ResonanceEchoesController::class)
            ->only(['index', 'show']);
        Route::resource('rulebooks', RulebooksController::class)
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

        Route::get('weapons', function (): AnonymousResourceCollection {
            return WeaponResource::collection((array)Weapon::all())
                ->additional(['links' => ['self' => route('shadowrun5e.weapons.index')]]);
        })->name('weapons.index');
        Route::get(
            'weapons/{weapon}',
            function (string $weapon): WeaponResource {
                return new WeaponResource(new Weapon($weapon));
            }
        )->name('weapons.show');

        Route::get('weapon-modifications', function (): AnonymousResourceCollection {
            return WeaponModificationResource::collection((array)WeaponModification::all())
                ->additional(['links' => ['self' => route('shadowrun5e.weapon-modifications.index')]]);
        })->name('weapon-modifications.index');
        Route::get(
            'weapon-modifications/{modification}',
            function (string $modification): WeaponModificationResource {
                try {
                    $modification = new WeaponModification($modification);
                } catch (RuntimeException) {
                    abort(404, 'Requested modification not found.');
                }

                return new WeaponModificationResource($modification);
            }
        )->name('weapon-modifications.show');
    });
