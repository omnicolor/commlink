<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Stillfleet\Http\Controllers\CharactersController;
use Modules\Stillfleet\Http\Resources\ArmorResource;
use Modules\Stillfleet\Http\Resources\GearResource;
use Modules\Stillfleet\Http\Resources\PowerResource;
use Modules\Stillfleet\Http\Resources\RoleResource;
use Modules\Stillfleet\Http\Resources\SpeciesResource;
use Modules\Stillfleet\Http\Resources\WeaponResource;
use Modules\Stillfleet\Models\Armor;
use Modules\Stillfleet\Models\Gear;
use Modules\Stillfleet\Models\Power;
use Modules\Stillfleet\Models\Role;
use Modules\Stillfleet\Models\Species;
use Modules\Stillfleet\Models\Weapon;

Route::middleware('auth:sanctum')
    ->prefix('stillfleet')
    ->name('stillfleet.')
    ->group(function (): void {
        Route::get('armor', function (): AnonymousResourceCollection {
            return ArmorResource::collection(Armor::all())
                ->additional(['links' => ['self' => route('stillfleet.armor.index')]]);
        })->name('armor.index');
        Route::get('armor/{armor}', function (Armor $armor) {
            return new ArmorResource($armor);
        })->name('armor.show');

        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('classes', function (): AnonymousResourceCollection {
            return RoleResource::collection(Role::all())
                ->additional(['links' => ['self' => route('stillfleet.classes.index')]]);
        })->name('classes.index');
        Route::get('classes/{class}', function (Role $class) {
            return new RoleResource($class);
        })->name('classes.show');

        Route::get('gear', function (): AnonymousResourceCollection {
            return GearResource::collection(Gear::all())
                ->additional(['links' => ['self' => route('stillfleet.gear.index')]]);
        })->name('gear.index');
        Route::get('gear/{gear}', function (Gear $gear) {
            return new GearResource($gear);
        })->name('gear.show');

        Route::get('powers', function (): AnonymousResourceCollection {
            return PowerResource::collection(Power::all())
                ->additional(['links' => ['self' => route('stillfleet.powers.index')]]);
        })->name('powers.index');
        Route::get('powers/{power}', function (Power $power) {
            return new PowerResource($power);
        })->name('powers.show');

        Route::get('species', function (): AnonymousResourceCollection {
            return SpeciesResource::collection(Species::all())
                ->additional(['links' => ['self' => route('stillfleet.species.index')]]);
        })->name('species.index');
        Route::get('species/{species}', function (Species $species) {
            return new SpeciesResource($species);
        })->name('species.show');

        Route::get('weapons', function (): AnonymousResourceCollection {
            return WeaponResource::collection(Weapon::all())
                ->additional(['links' => ['self' => route('stillfleet.weapons.index')]]);
        })->name('weapons.index');
        Route::get('weapons/{weapon}', function (Weapon $weapon) {
            return new WeaponResource($weapon);
        })->name('weapons.show');
    });
