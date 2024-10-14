<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('user-management.index', compact('users'));
    }

    /**
     * Handle user creation.
     */
    public function addUser(Request $request)
    {
        $request->validate([
            'email' => 'required|max:320|email|unique:users,email',
        ]);

        $email = $request->input('email');
        $domain = substr(strrchr($email, "@"), 1);

        if ($domain !== 'usep.edu.ph') {
            return redirect()->back()->with('error', 'Access denied. You must use a USeP email.');
        }

        try {
            $this->userService->createUser($email);
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

        $this->userService->updateUserRole($user, $request->role);

        return redirect()->route('user-management')->with('success', 'User role updated successfully.');
    }

    /**
     * Remove the user's role.
     */
    public function removeRole($id)
    {
        $user = User::findOrFail($id);

        $this->userService->removeUserRole($user);

        return redirect()->route('user-management')->with('success', 'User role removed successfully.');
    }
}
