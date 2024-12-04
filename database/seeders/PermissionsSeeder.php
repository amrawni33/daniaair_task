<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view tasks',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'assign tasks',
            'view users',
            'edit users',
            'update task status',
            'view number of users and number of tasks',
            'task auto assign by round-robin',
            'view tasks efficiency'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        $adminRole->syncPermissions(Permission::all());

        $managerPermissions = [
            'view tasks',
            'create tasks',
            'edit tasks',
            'assign tasks',
            'task auto assign by round-robin'

        ];
        $managerRole->syncPermissions($managerPermissions);

        $userPermissions = [
            'view tasks',
            'update task status',
        ];
        $userRole->syncPermissions($userPermissions);
    }
}
