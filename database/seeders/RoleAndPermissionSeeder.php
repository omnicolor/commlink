<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'admin users']);
        $role->givePermissionTo($permission);

        $role = Role::create(['name' => 'trusted']);
        $permission = Permission::create(['name' => 'view data']);
        $role->givePermissionTo($permission);
    }
}
