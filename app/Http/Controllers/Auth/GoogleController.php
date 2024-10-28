<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */

     public function handleGoogleCallback()
     {
         try {
             $user = Socialite::driver('google')->user();
         } catch (\Exception $e) {
             return redirect('/login')->with('error', 'Login cancelled or failed. Please try again.');
         }
     
         $email = $user->getEmail();
         $existingUser = User::where('email', $email)->first();
     
         if ($existingUser) {
             // Deny access if user doesn't have a role
             if (is_null($existingUser->role)) {
                 Log::warning('Access denied: User has no role.', ['email' => $email]);
                 return redirect('/login')->with('error', 'Access denied. You do not have the required permissions.');
             }
     
             $existingUser->update([
                 'name' => $user->getName(),
                 'google_id' => $user->getId(),
                 'avatar' => $user->getAvatar(),
                 'remember_token' => $user->token,
                 'created_at' => $existingUser->created_at ?? now(), // Set created_at if it's null
             ]);
     
             Auth::login($existingUser, true);
     
             return redirect()->intended('/dashboard');
         }
     
         return redirect('/login')->with('error', 'Access denied. You must use your USeP email.');
     }

}
