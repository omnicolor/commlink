<?php

declare(strict_types=1);

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

        Route::get('powers', function () {
            return PowerResource::collection(Power::all());
        })->name('powers.index');
        Route::get('powers/{power}', function (Power $power) {
            return new PowerResource($power);
        })->name('powers.show');

        Route::get('roles', function () {
            return RoleResource::collection(Role::all());
        })->name('roles.index');
        Route::get('roles/{role}', function (Role $role) {
            return new RoleResource($role);
        })->name('roles.show');

        Route::get('species', function () {
            return SpeciesResource::collection(Species::all());
        })->name('species.index');
        Route::get('species/{species}', function (Species $species) {
            return new SpeciesResource($species);
        })->name('species.show');
    });
