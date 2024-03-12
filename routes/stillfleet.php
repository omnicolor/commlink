<?php

declare(strict_types=1);

use App\Http\Resources\Stillfleet\PowerResource;
use App\Http\Resources\Stillfleet\RoleResource;
use App\Models\Stillfleet\Power;
use App\Models\Stillfleet\Role;

Route::middleware('auth:sanctum')->prefix('stillfleet')->name('stillfleet.')->group(
    function (): void {
        Route::get('powers', function () {
            return PowerResource::collection(Power::all());
        })->name('powers.index');
        Route::get('powers/{power}', function (string $power) {
            return new PowerResource(new Power($power));
        })->name('powers.show');

        Route::get('roles', function () {
            return RoleResource::collection(Role::all());
        })->name('roles.index');
        Route::get('roles/{role}', function (string $role) {
            return new RoleResource(new Role($role, 1));
        })->name('roles.show');
    }
);
