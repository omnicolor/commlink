<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Stillfleet\Http\Controllers\CharactersController;
use Modules\Stillfleet\Http\Resources\PowerResource;
use Modules\Stillfleet\Http\Resources\RoleResource;
use Modules\Stillfleet\Models\Power;
use Modules\Stillfleet\Models\Role;

Route::middleware('auth:sanctum')
    ->prefix('stillfleet')
    ->name('stillfleet.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('powers', function () {
            return PowerResource::collection(Power::all());
        })->name('powers.index');
        Route::get('powers/{power}', function (string $power) {
            return new PowerResource(new Power($power));
        })->name('powers.show');

        Route::get('roles', function () {
            return RoleResource::collection(Role::all());
        })->name('roles.index');
        Route::get('roles/{role}', function (Role $role) {
            return new RoleResource($role);
        })->name('roles.show');
    });
