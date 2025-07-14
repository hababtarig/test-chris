<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'approved' => true, // Optional: auto-approve new users
        ]);

        return back()->with('status', 'User created successfully.');
    }

    public function index()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    public function approve(User $user)
    {
        $user->approved = true;
        $user->save();
        return back()->with('status', 'User approved successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        $user->delete();
        return back()->with('status', 'User deleted successfully.');
    }
    public function makeAdmin(User $user)
{
    if ($user->is_admin) {
        return back()->with('status', 'User is already an admin.');
    }

    $user->is_admin = true;
    $user->save();

    return back()->with('status', 'User promoted to admin.');
}
}
