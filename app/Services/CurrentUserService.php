<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class CurrentUserService
{
    const SESSION_KEY = 'current_user_id';

    /**
     * Get the current user
     */
    public function getCurrentUser(): ?User
    {
        $userId = Session::get(self::SESSION_KEY);
        
        if (!$userId) {
            // Default to staff user if no user is selected
            $defaultUser = User::where('role', User::ROLE_STAFF)->first();
            if ($defaultUser) {
                $this->setCurrentUser($defaultUser);
                return $defaultUser;
            }
        }

        return User::find($userId);
    }

    /**
     * Set the current user
     */
    public function setCurrentUser(User $user): void
    {
        Session::put(self::SESSION_KEY, $user->id);
    }

    /**
     * Switch user role
     */
    public function switchToRole(string $role): ?User
    {
        $user = User::where('role', $role)->first();
        
        if ($user) {
            $this->setCurrentUser($user);
        }

        return $user;
    }

    /**
     * Get all available users for role switching
     */
    public function getAvailableUsers(): array
    {
        return User::select('id', 'name', 'role', 'email')
            ->orderBy('role')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
} 