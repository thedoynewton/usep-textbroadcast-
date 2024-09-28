<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('user-management.index', compact('users'));
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        $email = $request->input('email');
        $domain = substr(strrchr($email, "@"), 1);

        if ($domain !== 'usep.edu.ph') {
            return redirect()->back()->with('error', 'Access denied. You must use a USeP email.');
        }

        try {
            // Create the user with default data
            $user = User::create([
                'name' => 'New User',
                'email' => $email,
                'google_id' => null,
                'avatar' => 'default-avatar.png',
            ]);

            return redirect()->route('user-management')->with('success', 'User added successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to add user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add user. Please try again.');
        }
    }


    /**
     * Update the user's role.
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('user-management')->with('success', 'User role updated successfully.');
    }

    /**
     * Remove the user's role.
     */
    public function removeRole($id)
    {
        $user = User::findOrFail($id);
        $user->role = null;
        $user->save();

        return redirect()->route('user-management')->with('success', 'User role removed successfully.');
    }
}
