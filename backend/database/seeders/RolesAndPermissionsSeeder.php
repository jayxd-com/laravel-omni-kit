<?php

namespace Database\Seeders;

use App\Enums\PlatformRole;
use App\Enums\TeamRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Platform Roles (Global)
        foreach (PlatformRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }

        // Team Roles (Scoped)
        foreach (TeamRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }
    }
}
