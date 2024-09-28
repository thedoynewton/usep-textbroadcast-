<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
