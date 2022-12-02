<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Standard user permissions
        Permission::create(['name' => 'view_profile', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit_profile', 'guard_name' => 'sanctum']);
        /** @var Role $standardRole */
        $standardRole = Role::create(['name' => 'standard_user', 'guard_name' => 'sanctum']);
        $standardRole->givePermissionTo(Permission::all());

        // Admin Permissions
        Permission::create(['name' => 'create_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'view_users', 'guard_name' => 'sanctum']);
        /** @var Role $adminRole */
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
        $adminRole->givePermissionTo(Permission::all());

        // System Support Permissions
        $notification_per = Permission::create(['name' => 'receive_system_alerts', 'guard_name' => 'sanctum']);
        /** @var Role $systemSupport */
        $systemSupport = Role::create(['name' => 'system_support', 'guard_name' => 'sanctum']);
        $systemSupport->givePermissionTo($notification_per);

        // Super user permissions
        /** @var Role $superUserRole */
        $superUserRole = Role::create(['name' => 'super_user', 'guard_name' => 'sanctum']);
        $superUserRole->givePermissionTo(Permission::all());
    }
}
