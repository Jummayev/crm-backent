<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for API guard
        $permissions = [
            // Customer permissions
            'view-customers',
            'create-customer',
            'edit-customer',
            'delete-customer',

            // Order permissions
            'view-orders',
            'create-order',
            'edit-order',
            'delete-order',

            // Report permissions
            'view-reports',
            'export-reports',

            // User management permissions
            'manage-users',
            'manage-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'api']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::create(['name' => 'Manager', 'guard_name' => 'api']);
        $managerRole->givePermissionTo([
            'view-customers',
            'create-customer',
            'edit-customer',
            'view-orders',
            'create-order',
            'edit-order',
            'delete-order',
            'view-reports',
            'export-reports',
        ]);

        $operatorRole = Role::create(['name' => 'Operator', 'guard_name' => 'api']);
        $operatorRole->givePermissionTo([
            'view-customers',
            'create-customer',
            'edit-customer',
            'view-orders',
            'create-order',
            'edit-order',
        ]);
    }
}
