<?php

namespace Database\Seeders;

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
        Permission::create(['name' => 'update_profile', 'guard_name' => 'sanctum']);
        /** @var Role $standardRole */
        $standardRole = Role::create(['name' => \App\Enums\Role::STANDARD_USER, 'guard_name' => 'sanctum']);
        $standardRole->givePermissionTo(Permission::all());

        // Admin Permissions
        Permission::create(['name' => 'create_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'update_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete_users', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'view_users', 'guard_name' => 'sanctum']);
        /** @var Role $adminRole */
        $adminRole = Role::create(['name' => \App\Enums\Role::ADMIN, 'guard_name' => 'sanctum']);
        $adminRole->givePermissionTo(Permission::all());

        // System Support Permissions
        $notification_per = Permission::create(['name' => 'receive_system_alerts', 'guard_name' => 'sanctum']);
        /** @var Role $systemSupport */
        $systemSupport = Role::create(['name' => \App\Enums\Role::SYSTEM_SUPPORT, 'guard_name' => 'sanctum']);
        $systemSupport->givePermissionTo($notification_per);

        /**
         * Superuser role. We allow all permissions through here
         * @see App\Providers\AuthServiceProvider
         *
         * @var Role $superUserRole
         */
        Role::create(['name' => \App\Enums\Role::SUPER_USER, 'guard_name' => 'sanctum']);
    }
}
