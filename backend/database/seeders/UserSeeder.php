<?php

namespace Database\Seeders;

use App\Enums\PlatformRole;
use App\Enums\TeamRole;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function __construct(
        protected TeamService $teamService
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Platform Super Admin
        $superAdmin = User::create([
            'name' => 'Platform Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole(PlatformRole::SUPER_ADMIN->value);

        // 2. Create Platform Manager
        $manager = User::create([
            'name' => 'Platform Manager',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole(PlatformRole::MANAGER->value);

        // 3. Create a Team Owner with a Team
        $owner = User::create([
            'name' => 'Team Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);
        // TeamService automatically assigns the OWNER role via attach
        $this->teamService->createTeam($owner, 'Default Team');

        // 4. Create a regular Team Member
        $memberUser = User::create([
            'name' => 'Team Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
        ]);
        
        $team = $owner->ownedTeams()->first();
        $memberUser->teams()->attach($team, ['role' => TeamRole::MEMBER->value]);
        $this->teamService->switchTeam($memberUser, $team);
    }
}
