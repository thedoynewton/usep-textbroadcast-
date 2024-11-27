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
             Log::error('Google SSO login failed', ['error' => $e->getMessage()]);
             return redirect('/login')->with('error', 'Login cancelled or failed. Please try again.');
         }
     
         $email = $user->getEmail();
     
         // Validate domain
         $domain = substr(strrchr($email, "@"), 1);
         if ($domain !== 'usep.edu.ph') {
             return redirect('/login')->with('error', 'Access denied. You must use your USeP email.');
         }
     
         // Check if the email exists in the users table
         $existingUser = User::where('email', $email)->first();
     
         if ($existingUser) {
             // Check if user has a role
             if (is_null($existingUser->role)) {
                 Log::warning('Access denied: User has no role.', ['email' => $email]);
                 return redirect('/login')->with('error', 'Access denied. You do not have the required permissions.');
             }
     
             // Update user details with Google data
             $existingUser->update([
                 'name' => $user->getName(),
                 'google_id' => $user->getId(),
                 'avatar' => $user->getAvatar(),
                 'remember_token' => $user->token,
                 'created_at' => $existingUser->created_at ?? now(),
             ]);
     
             // Log the user in
             Auth::login($existingUser, true);
     
             // Redirect to dashboard
             return redirect()->intended('/dashboard');
         }
     
         // If the user does not exist, return an error
         return redirect('/login')->with('error', 'Access denied. You do not have the required permissions.');
     }
     
}
