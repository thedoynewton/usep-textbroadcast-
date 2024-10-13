<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Create a new user.
     */
    public function createUser($email)
    {
        return User::create([
            'name' => 'New User',
            'email' => $email,
            'google_id' => null,
            'avatar' => 'default-avatar.png',
        ]);
    }

    /**
     * Update the user's role.
     */
    public function updateUserRole($user, $role)
    {
        $user->role = $role;
        $user->save();
    }

    /**
     * Remove the user's role.
     */
    public function removeUserRole($user)
    {
        $user->role = null;
        $user->save();
    }
}