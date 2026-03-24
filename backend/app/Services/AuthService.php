<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected TeamService $teamService
    ) {}

    /**
     * Register a new user and optionally create a team.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // If team name is provided, create a team
        if (!empty($data['team_name'])) {
            $this->teamService->createTeam($user, $data['team_name']);
        }

        return $user;
    }

    /**
     * Authenticate a user and return a token.
     */
    public function login(string $email, string $password, string $deviceName = 'web'): string
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return $user->createToken($deviceName)->plainTextToken;
    }

    /**
     * Logout the current user by deleting tokens.
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
