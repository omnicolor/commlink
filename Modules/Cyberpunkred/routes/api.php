<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Modules\Cyberpunkred\Http\Controllers\CharactersController;
use Modules\Cyberpunkred\Http\Resources\ArmorResource;
use Modules\Cyberpunkred\Http\Resources\SkillResource;
use Modules\Cyberpunkred\Http\Resources\WeaponResource;
use Modules\Cyberpunkred\Models\Armor;
use Modules\Cyberpunkred\Models\Skill;
use Modules\Cyberpunkred\Models\Weapon;

Route::middleware('auth:sanctum')
    ->prefix('cyberpunkred')
    ->name('cyberpunkred.')
    ->group(function (): void {
        Route::get('armor', function () {
            return ArmorResource::collection(Armor::all())
                ->additional(['self' => route('cyberpunkred.armor.index')]);
        })->name('armor.index');
        Route::get('armor/{armor}', function (string $armor) {
            try {
                return new ArmorResource(new Armor($armor));
            } catch (RuntimeException) {
                abort(
                    Response::HTTP_NOT_FOUND,
                    \sprintf('%s is not found', $armor),
                );
            }
        })->name('armor.show');

        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('skills', function () {
            return SkillResource::collection(Skill::all())
                ->additional(['self' => route('cyberpunkred.skills.index')]);
        })->name('skills.index');
        Route::get('skills/{weapon}', function (string $skill) {
            try {
                return new SkillResource(new Skill($skill));
            } catch (RuntimeException) {
                abort(
                    Response::HTTP_NOT_FOUND,
                    \sprintf('%s is not found', $skill),
                );
            }
        })->name('skills.show');

        Route::get('weapons', function () {
            return WeaponResource::collection(Weapon::all())
                ->additional(['self' => route('cyberpunkred.weapons.index')]);
        })->name('weapons.index');
        Route::get('weapons/{weapon}', function (string $weapon) {
            try {
                return new WeaponResource(Weapon::build(['id' => $weapon]));
            } catch (RuntimeException) {
                abort(
                    Response::HTTP_NOT_FOUND,
                    \sprintf('%s is not found', $weapon),
                );
            }
        })->name('weapons.show');
    });
