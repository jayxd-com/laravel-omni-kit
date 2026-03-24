<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;

class TeamService
{
    /**
     * Create a new team for the given user.
     */
    public function createTeam(User $user, string $name, ?string $slug = null): Team
    {
        $team = Team::create([
            'name' => $name,
            'slug' => $slug ?? Str::slug($name),
            'owner_id' => $user->id,
        ]);

        $user->teams()->attach($team, ['role' => TeamRole::OWNER->value]);

        if (!$user->current_team_id) {
            $this->switchTeam($user, $team);
        }

        return $team;
    }

    /**
     * Switch the user's current team context.
     */
    public function switchTeam(User $user, Team $team): void
    {
        if (!$user->belongsToTeam($team)) {
            throw new \Exception('User does not belong to this team.');
        }

        $user->forceFill([
            'current_team_id' => $team->id,
        ])->save();
    }
}
