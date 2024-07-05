<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Modules\Capers\Http\Controllers\CharactersController;
use Modules\Capers\Http\Resources\GearResource;
use Modules\Capers\Http\Resources\IdentityResource;
use Modules\Capers\Http\Resources\PowerResource;
use Modules\Capers\Http\Resources\SkillResource;
use Modules\Capers\Http\Resources\ViceResource;
use Modules\Capers\Http\Resources\VirtueResource;
use Modules\Capers\Models\Gear;
use Modules\Capers\Models\Identity;
use Modules\Capers\Models\Power;
use Modules\Capers\Models\Skill;
use Modules\Capers\Models\Vice;
use Modules\Capers\Models\Virtue;

Route::middleware('auth:sanctum')
    ->prefix('capers')
    ->name('capers.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);
        Route::get('gear', function () {
            return GearResource::collection(array_values((array)Gear::all()))
                ->additional([
                    'links' => [
                        'self' => route('capers.gear.index'),
                    ],
                ]);
        })->name('gear.index');
        Route::get('identities', function () {
            return IdentityResource::collection(array_values((array)Identity::all()))
                ->additional([
                    'links' => [
                        'self' => route('capers.identities.index'),
                    ],
                ]);
        })->name('identities.index');
        Route::get('powers', function () {
            return PowerResource::collection(array_values((array)Power::all()))
                ->additional([
                    'links' => [
                        'self' => route('capers.powers.index'),
                    ],
                ]);
        })->name('powers.index');
        Route::get('powers/{power}', function (string $power) {
            try {
                $power = new Power($power);
            } catch (RuntimeException) {
                abort(Response::HTTP_NOT_FOUND);
            }
            return (new PowerResource($power))
                ->additional([
                    'links' => [
                        'self' => route('capers.powers.show', $power->id),
                        'collection' => route('capers.powers.index'),
                    ],
                ]);
        })->name('powers.show');
        Route::get('skills', function () {
            return SkillResource::collection(array_values((array)Skill::all()))
                ->additional([
                    'links' => [
                        'self' => route('capers.skills.index'),
                    ],
                ]);
        })->name('skills.index');
        Route::get('vices', function () {
            return ViceResource::collection(array_values((array)Vice::all()))
                ->additional([
                    'links' => [
                        'self' => route('capers.vices.index'),
                    ],
                ]);
        })->name('vices.index');
        Route::get('virtues', function () {
            return VirtueResource::collection(array_values((array)Virtue::all()))
                ->additional([
                    'links' => [
                        'self' => route('capers.virtues.index'),
                    ],
                ]);
        })->name('virtues.index');
    });
