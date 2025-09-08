<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Stillfleet\Http\Controllers\CharactersController;
use Modules\Stillfleet\Http\Resources\PowerResource;
use Modules\Stillfleet\Http\Resources\RoleResource;
use Modules\Stillfleet\Http\Resources\SpeciesResource;
use Modules\Stillfleet\Models\Power;
use Modules\Stillfleet\Models\Role;
use Modules\Stillfleet\Models\Species;

Route::middleware('auth:sanctum')
    ->prefix('stillfleet')
    ->name('stillfleet.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('classes', static function (): AnonymousResourceCollection {
            return RoleResource::collection(Role::all())
                ->additional(['links' => ['self' => route('stillfleet.classes.index')]]);
        })->name('classes.index');
        Route::get('classes/{class}', static function (Role $class): RoleResource {
            return new RoleResource($class);
        })->name('classes.show');

        Route::get('powers', static function (): AnonymousResourceCollection {
            return PowerResource::collection(Power::all())
                ->additional(['links' => ['self' => route('stillfleet.powers.index')]]);
        })->name('powers.index');
        Route::get('powers/{power}', static function (Power $power): PowerResource {
            return new PowerResource($power);
        })->name('powers.show');

        Route::get('species', static function (): AnonymousResourceCollection {
            return SpeciesResource::collection(Species::all())
                ->additional(['links' => ['self' => route('stillfleet.species.index')]]);
        })->name('species.index');
        Route::get('species/{species}', static function (Species $species): SpeciesResource {
            return new SpeciesResource($species);
        })->name('species.show');
    });
