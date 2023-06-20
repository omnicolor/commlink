<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function run(): void
    {
        // Reset cached roles and permissions.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        try {
            Permission::create(['name' => 'view data']);
        } catch (PermissionAlreadyExists) {
            // Ignore.
        }
        try {
            Role::create(['name' => 'trusted'])
                ->givePermissionTo(['view data']);
        } catch (RoleAlreadyExists) {
            // Ignore.
        }
    }
}
